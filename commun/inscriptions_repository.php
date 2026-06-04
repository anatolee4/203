<?php
require_once __DIR__ . '/database.php';

function inscriptions_cookie_lire(): array
{
    $inscriptions = json_decode($_COOKIE['inscriptions'] ?? '[]', true);
    return is_array($inscriptions) ? array_values($inscriptions) : [];
}

function inscriptions_cookie_sauver(array $inscriptions): void
{
    setcookie('inscriptions', json_encode(array_values($inscriptions)), time() + 60 * 60 * 24 * 30, '/');
}

function inscriptions_token_utilisateur(): string
{
    $token = (string) ($_COOKIE['token_gestion'] ?? '');

    if (!preg_match('/^[a-f0-9]{32,40}$/', $token)) {
        $token = inscriptions_token();
        setcookie('token_gestion', $token, time() + 60 * 60 * 24 * 365, '/');
        $_COOKIE['token_gestion'] = $token;
    }

    return $token;
}

function inscriptions_colonnes_table(PDO $pdo): array
{
    static $colonnes = null;

    if (is_array($colonnes)) {
        return $colonnes;
    }

    $table = db_identifiant(DB_TABLE_INSCRIPTION);
    $requete = $pdo->query("DESCRIBE $table");
    $colonnes = array_map(static fn (array $ligne): string => (string) $ligne['Field'], $requete->fetchAll());

    return $colonnes;
}

function inscriptions_table_a_colonne(PDO $pdo, string $colonne): bool
{
    return $colonne !== '' && in_array($colonne, inscriptions_colonnes_table($pdo), true);
}

function inscriptions_valeur_creneau(array $inscription): string
{
    return trim((string) ($inscription['creneau'] ?? ''));
}

function inscriptions_token(): string
{
    try {
        return bin2hex(random_bytes(16));
    } catch (Throwable $exception) {
        return sha1(uniqid('', true));
    }
}

function inscriptions_normaliser_texte(string $texte): string
{
    $texte = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texte) ?: $texte;
    return strtolower(preg_replace('/[^a-z0-9]+/', '', $texte) ?? '');
}

function inscriptions_heure_sql(string $creneau): string
{
    if (!preg_match('/(\d{1,2})h(\d{2})?$/', $creneau, $matches)) {
        return '00:00:00';
    }

    return sprintf('%02d:%02d:00', (int) $matches[1], (int) ($matches[2] ?? 0));
}

function inscriptions_code_jour(string $creneau): string
{
    return substr($creneau, 0, 9) === 'vendredi-' ? 'vendredi' : 'jeudi';
}

function inscriptions_creneau_cle(?string $dateVisite, ?string $heureDebut): string
{
    $heure = substr((string) $heureDebut, 0, 5);
    $heure = ltrim(str_replace(':', 'h', $heure), '0');
    $heure = substr($heure, -3) === 'h00' ? substr($heure, 0, -2) : $heure;

    $date = (string) $dateVisite;
    $jour = strpos($date, '-19') !== false ? 'vendredi' : 'jeudi';

    return $jour . '-' . $heure;
}

function inscriptions_dates_creneaux(PDO $pdo): array
{
    static $dates = null;

    if (is_array($dates)) {
        return $dates;
    }

    try {
        $requete = $pdo->query('SELECT DISTINCT date_visite FROM `creneau` ORDER BY date_visite ASC');
        $valeurs = array_values(array_filter(array_map('strval', $requete->fetchAll(PDO::FETCH_COLUMN))));
        $dates = [
            'jeudi' => $valeurs[0] ?? '',
            'vendredi' => $valeurs[1] ?? ($valeurs[0] ?? ''),
        ];
    } catch (Throwable $exception) {
        db_enregistrer_erreur($exception->getMessage());
        $dates = ['jeudi' => '', 'vendredi' => ''];
    }

    return $dates;
}

function inscriptions_id_creneau(PDO $pdo, array $inscription): string
{
    $creneau = inscriptions_valeur_creneau($inscription);
    $salle = preg_match('/(001|002|005|021)/', (string) ($inscription['salle'] ?? ''), $matches) ? $matches[1] : '';
    $dates = inscriptions_dates_creneaux($pdo);
    $dateVisite = $dates[inscriptions_code_jour($creneau)] ?? '';
    $heureDebut = inscriptions_heure_sql($creneau);

    if ($salle === '' || $dateVisite === '' || $heureDebut === '00:00:00') {
        return $creneau;
    }

    $requete = $pdo->prepare(
        "SELECT c.`id_creneau`
         FROM `creneau` c
         INNER JOIN `salle` s ON s.`id_salle` = c.`id_salle`
         WHERE s.`numero_salle` = :salle
           AND c.`date_visite` = :date_visite
           AND c.`heure_debut` = :heure_debut
         LIMIT 1"
    );
    $requete->execute([
        'salle' => $salle,
        'date_visite' => $dateVisite,
        'heure_debut' => $heureDebut,
    ]);

    $id = $requete->fetchColumn();
    return $id !== false ? (string) $id : $creneau;
}

function inscriptions_id_profil(PDO $pdo, string $profil): string
{
    static $profils = null;

    if ($profils === null) {
        $profils = [];

        try {
            $requete = $pdo->query('SELECT `id`, `nom_profil` FROM `profils`');
            foreach ($requete as $ligne) {
                $profils[inscriptions_normaliser_texte((string) $ligne['nom_profil'])] = (string) $ligne['id'];
            }
        } catch (Throwable $exception) {
            db_enregistrer_erreur($exception->getMessage());
        }
    }

    $alias = [
        'enseignant' => ['enseignant', 'enseignante'],
        'etudiant-mmi' => ['etudiantmmi', 'etudiantemmi', 'mmi'],
        'personnel-usmb' => ['personnelusmb', 'personnel'],
        'professionnel' => ['professionnel', 'professionnelle', 'partenaire'],
        'visiteur' => ['visiteur', 'visiteurexterieur', 'visiteurseeexterieure'],
    ];
    $profilNormalise = inscriptions_normaliser_texte($profil);
    $candidats = array_merge([$profilNormalise], $alias[$profil] ?? []);

    foreach ($profils as $nom => $id) {
        foreach ($candidats as $candidat) {
            if ($candidat !== '' && (strpos($nom, $candidat) !== false || strpos($candidat, $nom) !== false)) {
                return $id;
            }
        }
    }

    return $profil;
}

function inscriptions_lire_toutes(?string $tokenGestion = null): array
{
    $pdo = db_connexion();

    if (!$pdo) {
        return inscriptions_cookie_lire();
    }

    try {
        $filtrerToken = $tokenGestion !== null && inscriptions_table_a_colonne($pdo, DB_COL_INSCRIPTION_TOKEN);
        $where = $filtrerToken ? 'WHERE i.`token_gestion` = :token_gestion' : '';
        $requete = $pdo->prepare(
            "SELECT i.`id_inscription` AS id, i.`nom` AS nom, i.`prenom` AS prenom, i.`email` AS email,
                    COALESCE(p.`nom_profil`, i.`profil_visiteur`) AS profil,
                    i.`nb_personnes` AS personnes,
                    COALESCE(s.`numero_salle`, '') AS salle,
                    c.`date_visite` AS date_visite,
                    c.`heure_debut` AS heure_debut,
                    i.`participe_buffet` AS buffet,
                    i.`date_inscription` AS date_creation
             FROM `inscription` i
             LEFT JOIN `creneau` c ON c.`id_creneau` = i.`id_creneau`
             LEFT JOIN `salle` s ON s.`id_salle` = c.`id_salle`
             LEFT JOIN `profils` p ON p.`id` = i.`profil_visiteur`
             $where
             ORDER BY i.`id_inscription` ASC"
        );
        $requete->execute($filtrerToken ? ['token_gestion' => $tokenGestion] : []);
        $inscriptions = [];

        foreach ($requete as $ligne) {
            $ligne['_id'] = (string) $ligne['id'];
            $ligne['creneau'] = inscriptions_creneau_cle($ligne['date_visite'] ?? '', $ligne['heure_debut'] ?? '');
            $ligne['buffet'] = in_array((string) $ligne['buffet'], ['1', 'oui', 'true'], true) ? 'oui' : 'non';
            unset($ligne['id']);
            $inscriptions[] = $ligne;
        }

        return $inscriptions;
    } catch (Throwable $exception) {
        db_enregistrer_erreur($exception->getMessage());
        return inscriptions_cookie_lire();
    }
}

function inscriptions_lire_utilisateur(): array
{
    return inscriptions_lire_toutes(inscriptions_token_utilisateur());
}

function inscriptions_ajouter(array $inscription): void
{
    $pdo = db_connexion();

    if (!$pdo) {
        $inscriptions = inscriptions_cookie_lire();
        $inscriptions[] = $inscription;
        inscriptions_cookie_sauver($inscriptions);
        return;
    }

    try {
        $table = db_identifiant(DB_TABLE_INSCRIPTION);
        $valeurs = [
            'nom' => $inscription['nom'] ?? '',
            'prenom' => $inscription['prenom'] ?? '',
            'email' => $inscription['email'] ?? '',
            'profil' => inscriptions_id_profil($pdo, (string) ($inscription['profil'] ?? '')),
            'personnes' => max(1, (int) ($inscription['personnes'] ?? 1)),
            'creneau' => inscriptions_id_creneau($pdo, $inscription),
            'buffet' => ($inscription['buffet'] ?? 'non') === 'oui' ? 1 : 0,
            'date_creation' => $inscription['date_creation'] ?? date('Y-m-d H:i:s'),
        ];

        $colonnes = [
            DB_COL_INSCRIPTION_NOM => 'nom',
            DB_COL_INSCRIPTION_PRENOM => 'prenom',
            DB_COL_INSCRIPTION_EMAIL => 'email',
            DB_COL_INSCRIPTION_PROFIL => 'profil',
            DB_COL_INSCRIPTION_PERSONNES => 'personnes',
            DB_COL_INSCRIPTION_CRENEAU => 'creneau',
            DB_COL_INSCRIPTION_BUFFET => 'buffet',
            DB_COL_INSCRIPTION_DATE => 'date_creation',
        ];

        if (inscriptions_table_a_colonne($pdo, DB_COL_INSCRIPTION_SALLE)) {
            $colonnes[DB_COL_INSCRIPTION_SALLE] = 'salle';
            $valeurs['salle'] = $inscription['salle'] ?? '';
        }

        if (inscriptions_table_a_colonne($pdo, DB_COL_INSCRIPTION_TOKEN)) {
            $colonnes[DB_COL_INSCRIPTION_TOKEN] = 'token_gestion';
            $valeurs['token_gestion'] = inscriptions_token_utilisateur();
        }

        $colonnesSql = implode(', ', array_map('db_identifiant', array_keys($colonnes)));
        $parametresSql = ':' . implode(', :', array_values($colonnes));
        $requete = $pdo->prepare("INSERT INTO $table ($colonnesSql) VALUES ($parametresSql)");
        $requete->execute($valeurs);
    } catch (Throwable $exception) {
        db_enregistrer_erreur($exception->getMessage());
        $inscriptions = inscriptions_cookie_lire();
        $inscriptions[] = $inscription;
        inscriptions_cookie_sauver($inscriptions);
    }
}

function inscriptions_identifiant(array $inscription, int $index): string
{
    return (string) ($inscription['_id'] ?? $index);
}

function inscriptions_index_par_identifiant(array $inscriptions, string $identifiant): ?int
{
    foreach ($inscriptions as $index => $inscription) {
        if (inscriptions_identifiant($inscription, (int) $index) === $identifiant) {
            return (int) $index;
        }
    }

    return null;
}

function inscriptions_supprimer(string $identifiant, ?string $tokenGestion = null): void
{
    $pdo = db_connexion();

    if ($pdo && ctype_digit($identifiant)) {
        try {
            $table = db_identifiant(DB_TABLE_INSCRIPTION);
            $id = db_identifiant(DB_COL_INSCRIPTION_ID);
            $filtrerToken = $tokenGestion !== null && inscriptions_table_a_colonne($pdo, DB_COL_INSCRIPTION_TOKEN);

            if ($filtrerToken) {
                $token = db_identifiant(DB_COL_INSCRIPTION_TOKEN);
                $requete = $pdo->prepare("DELETE FROM $table WHERE $id = :id AND $token = :token_gestion");
                $requete->execute(['id' => (int) $identifiant, 'token_gestion' => $tokenGestion]);
            } else {
                $requete = $pdo->prepare("DELETE FROM $table WHERE $id = :id");
                $requete->execute(['id' => (int) $identifiant]);
            }
            return;
        } catch (Throwable $exception) {
            db_enregistrer_erreur($exception->getMessage());
            // On tente le stockage local juste en dessous.
        }
    }

    $inscriptions = inscriptions_cookie_lire();
    $index = inscriptions_index_par_identifiant($inscriptions, $identifiant);

    if ($index !== null) {
        unset($inscriptions[$index]);
        inscriptions_cookie_sauver($inscriptions);
    }
}

function inscriptions_modifier(string $identifiant, array $inscription): void
{
    $pdo = db_connexion();

    if ($pdo && ctype_digit($identifiant)) {
        try {
            $table = db_identifiant(DB_TABLE_INSCRIPTION);
            $id = db_identifiant(DB_COL_INSCRIPTION_ID);
            $nom = db_identifiant(DB_COL_INSCRIPTION_NOM);
            $prenom = db_identifiant(DB_COL_INSCRIPTION_PRENOM);
            $email = db_identifiant(DB_COL_INSCRIPTION_EMAIL);
            $profil = db_identifiant(DB_COL_INSCRIPTION_PROFIL);
            $personnes = db_identifiant(DB_COL_INSCRIPTION_PERSONNES);
            $creneau = db_identifiant(DB_COL_INSCRIPTION_CRENEAU);
            $buffet = db_identifiant(DB_COL_INSCRIPTION_BUFFET);
            $valeurs = [
                'id' => (int) $identifiant,
                'nom' => $inscription['nom'] ?? '',
                'prenom' => $inscription['prenom'] ?? '',
                'email' => $inscription['email'] ?? '',
                'profil' => inscriptions_id_profil($pdo, (string) ($inscription['profil'] ?? '')),
                'personnes' => max(1, (int) ($inscription['personnes'] ?? 1)),
                'creneau' => inscriptions_id_creneau($pdo, $inscription),
                'buffet' => ($inscription['buffet'] ?? 'non') === 'oui' ? 1 : 0,
            ];
            $assignations = [
                "$nom = :nom",
                "$prenom = :prenom",
                "$email = :email",
                "$profil = :profil",
                "$personnes = :personnes",
                "$creneau = :creneau",
                "$buffet = :buffet",
            ];

            if (inscriptions_table_a_colonne($pdo, DB_COL_INSCRIPTION_SALLE)) {
                $salle = db_identifiant(DB_COL_INSCRIPTION_SALLE);
                $assignations[] = "$salle = :salle";
                $valeurs['salle'] = $inscription['salle'] ?? '';
            }

            $requete = $pdo->prepare("UPDATE $table SET " . implode(', ', $assignations) . " WHERE $id = :id");
            $requete->execute($valeurs);
            return;
        } catch (Throwable $exception) {
            db_enregistrer_erreur($exception->getMessage());
            // On tente le stockage local juste en dessous.
        }
    }

    $inscriptions = inscriptions_cookie_lire();
    $index = inscriptions_index_par_identifiant($inscriptions, $identifiant);

    if ($index !== null) {
        $inscriptions[$index] = array_merge($inscriptions[$index], $inscription);
        inscriptions_cookie_sauver($inscriptions);
    }
}
