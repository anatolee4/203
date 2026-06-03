<?php
require_once __DIR__ . '/database.php';

const INSCRIPTIONS_TABLE = 'inscriptions';

function inscriptions_cookie_lire(): array
{
    $inscriptions = json_decode($_COOKIE['inscriptions'] ?? '[]', true);
    return is_array($inscriptions) ? array_values($inscriptions) : [];
}

function inscriptions_cookie_sauver(array $inscriptions): void
{
    setcookie('inscriptions', json_encode(array_values($inscriptions)), time() + 60 * 60 * 24 * 30, '/');
}

function inscriptions_lire_toutes(): array
{
    $pdo = db_connexion();

    if (!$pdo) {
        return inscriptions_cookie_lire();
    }

    try {
        $table = db_identifiant(INSCRIPTIONS_TABLE);
        $requete = $pdo->query("SELECT id, nom, prenom, email, profil, personnes, salle, creneau, buffet, date_creation FROM $table ORDER BY id ASC");
        $inscriptions = [];

        foreach ($requete as $ligne) {
            $ligne['_id'] = (string) $ligne['id'];
            unset($ligne['id']);
            $inscriptions[] = $ligne;
        }

        return $inscriptions;
    } catch (Throwable $exception) {
        return inscriptions_cookie_lire();
    }
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
        $table = db_identifiant(INSCRIPTIONS_TABLE);
        $requete = $pdo->prepare(
            "INSERT INTO $table (nom, prenom, email, profil, personnes, salle, creneau, buffet, date_creation)
             VALUES (:nom, :prenom, :email, :profil, :personnes, :salle, :creneau, :buffet, :date_creation)"
        );
        $requete->execute([
            'nom' => $inscription['nom'] ?? '',
            'prenom' => $inscription['prenom'] ?? '',
            'email' => $inscription['email'] ?? '',
            'profil' => $inscription['profil'] ?? '',
            'personnes' => max(1, (int) ($inscription['personnes'] ?? 1)),
            'salle' => $inscription['salle'] ?? '',
            'creneau' => $inscription['creneau'] ?? '',
            'buffet' => $inscription['buffet'] ?? 'non',
            'date_creation' => $inscription['date_creation'] ?? date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $exception) {
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

function inscriptions_supprimer(string $identifiant): void
{
    $pdo = db_connexion();

    if ($pdo && ctype_digit($identifiant)) {
        try {
            $table = db_identifiant(INSCRIPTIONS_TABLE);
            $requete = $pdo->prepare("DELETE FROM $table WHERE id = :id");
            $requete->execute(['id' => (int) $identifiant]);
            return;
        } catch (Throwable $exception) {
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
            $table = db_identifiant(INSCRIPTIONS_TABLE);
            $requete = $pdo->prepare(
                "UPDATE $table
                 SET nom = :nom, prenom = :prenom, email = :email, profil = :profil, personnes = :personnes,
                     salle = :salle, creneau = :creneau, buffet = :buffet
                 WHERE id = :id"
            );
            $requete->execute([
                'id' => (int) $identifiant,
                'nom' => $inscription['nom'] ?? '',
                'prenom' => $inscription['prenom'] ?? '',
                'email' => $inscription['email'] ?? '',
                'profil' => $inscription['profil'] ?? '',
                'personnes' => max(1, (int) ($inscription['personnes'] ?? 1)),
                'salle' => $inscription['salle'] ?? '',
                'creneau' => $inscription['creneau'] ?? '',
                'buffet' => $inscription['buffet'] ?? 'non',
            ]);
            return;
        } catch (Throwable $exception) {
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
