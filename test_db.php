<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/commun/database.php';

function diagnostic_ok(string $message): void
{
    echo '<p style="color:#146c2e">OK - ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}

function diagnostic_ko(string $message): void
{
    echo '<p style="color:#a40000">ERREUR - ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}

function diagnostic_info(string $message): void
{
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
}

function diagnostic_colonnes(PDO $pdo, string $table): array
{
    $requete = $pdo->query('DESCRIBE ' . db_identifiant($table));
    return array_map(static fn (array $ligne): string => (string) $ligne['Field'], $requete->fetchAll());
}

function diagnostic_table(PDO $pdo, string $table, array $colonnesAttendues): void
{
    try {
        $colonnes = diagnostic_colonnes($pdo, $table);
        diagnostic_ok("Table `$table` trouvee.");
        diagnostic_info("Colonnes dans `$table` : " . implode(', ', $colonnes));

        $manquantes = array_values(array_diff($colonnesAttendues, $colonnes));
        if ($manquantes === []) {
            diagnostic_ok("Toutes les colonnes attendues existent dans `$table`.");
        } else {
            diagnostic_ko("Colonnes manquantes dans `$table` : " . implode(', ', $manquantes));
        }
    } catch (Throwable $exception) {
        diagnostic_ko("Impossible de lire la table `$table` : " . $exception->getMessage());
    }
}

function diagnostic_est_hash(string $motDePasse): bool
{
    return (password_get_info($motDePasse)['algoName'] ?? 'unknown') !== 'unknown';
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diagnostic base de donnees</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.4; padding: 24px;">
    <h1>Diagnostic base de donnees</h1>

    <h2>Connexion</h2>
    <?php
    $pdo = db_connexion();

    if (!$pdo) {
        diagnostic_ko('Connexion impossible avec les identifiants de commun/database.php.');
        diagnostic_info('Verifie DB_HOST, DB_NAME, DB_USER et DB_PASSWORD dans commun/database.php.');
        exit;
    }

    diagnostic_ok('Connexion PDO reussie.');
    diagnostic_info('Base configuree : ' . DB_NAME);
    diagnostic_info('Serveur configure : ' . DB_HOST);

    try {
        $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        diagnostic_info('Tables visibles : ' . implode(', ', $tables));
    } catch (Throwable $exception) {
        diagnostic_ko('Impossible de lister les tables : ' . $exception->getMessage());
    }
    ?>

    <h2>Table des inscriptions</h2>
    <?php
    diagnostic_table($pdo, DB_TABLE_INSCRIPTION, [
        DB_COL_INSCRIPTION_ID,
        DB_COL_INSCRIPTION_NOM,
        DB_COL_INSCRIPTION_PRENOM,
        DB_COL_INSCRIPTION_EMAIL,
        DB_COL_INSCRIPTION_PROFIL,
        DB_COL_INSCRIPTION_PERSONNES,
        DB_COL_INSCRIPTION_CRENEAU,
        DB_COL_INSCRIPTION_BUFFET,
        DB_COL_INSCRIPTION_TOKEN,
        DB_COL_INSCRIPTION_DATE,
    ]);

    try {
        $table = db_identifiant(DB_TABLE_INSCRIPTION);
        $total = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        diagnostic_info('Nombre d inscriptions dans la table : ' . $total);
    } catch (Throwable $exception) {
        diagnostic_ko('Impossible de compter les inscriptions : ' . $exception->getMessage());
    }
    ?>

    <h2>Tables liees</h2>
    <?php
    foreach (['creneau', 'salle', 'profils'] as $tableLiee) {
        try {
            $colonnes = diagnostic_colonnes($pdo, $tableLiee);
            diagnostic_ok("Table `$tableLiee` trouvee.");
            diagnostic_info("Colonnes dans `$tableLiee` : " . implode(', ', $colonnes));

            $requeteExemples = $pdo->query('SELECT * FROM ' . db_identifiant($tableLiee) . ' LIMIT 5');
            $exemples = $requeteExemples->fetchAll(PDO::FETCH_ASSOC);
            foreach ($exemples as $index => $ligne) {
                diagnostic_info("Exemple `$tableLiee` #" . ($index + 1) . ' : ' . json_encode($ligne, JSON_UNESCAPED_UNICODE));
            }
        } catch (Throwable $exception) {
            diagnostic_ko("Impossible de lire la table `$tableLiee` : " . $exception->getMessage());
        }
    }
    ?>

    <h2>Table admin</h2>
    <?php
    diagnostic_table($pdo, DB_TABLE_ADMIN, [
        DB_COL_ADMIN_PASSWORD,
    ]);

    try {
        $table = db_identifiant(DB_TABLE_ADMIN);
        $colonneMotDePasse = db_identifiant(DB_COL_ADMIN_PASSWORD);
        $nouveauMotDePasse = trim((string) ($_GET['admin_set'] ?? ''));

        if ($nouveauMotDePasse !== '') {
            $requeteMaj = $pdo->prepare("UPDATE $table SET $colonneMotDePasse = :mot_de_passe LIMIT 1");
            $requeteMaj->execute(['mot_de_passe' => $nouveauMotDePasse]);
            diagnostic_ok('Le mot de passe admin a ete remplace par la valeur fournie dans admin_set.');
        }

        $lignes = $pdo->query("SELECT $colonneMotDePasse AS mot_de_passe FROM $table")->fetchAll();
        diagnostic_info('Nombre de valeurs admin trouvees : ' . count($lignes));
        foreach ($lignes as $index => $ligne) {
            $motDePasseBase = (string) ($ligne['mot_de_passe'] ?? '');
            $estHash = diagnostic_est_hash($motDePasseBase);
            diagnostic_info(
                'Valeur admin #' . ($index + 1) . ' : longueur ' . strlen($motDePasseBase)
                . ', format ' . ($estHash ? 'hash' : 'texte simple')
            );
        }

        $test = trim((string) ($_GET['admin_test'] ?? ''));
        if ($test !== '') {
            $correspond = false;
            foreach ($lignes as $ligne) {
                $motDePasseBase = (string) ($ligne['mot_de_passe'] ?? '');
                $estHash = diagnostic_est_hash($motDePasseBase);
                if (($estHash && password_verify($test, $motDePasseBase)) || (!$estHash && hash_equals($motDePasseBase, $test))) {
                    $correspond = true;
                    break;
                }
            }

            if ($correspond) {
                diagnostic_ok('Le mot de passe teste correspond a une valeur admin.');
            } else {
                diagnostic_ko('Le mot de passe teste ne correspond a aucune valeur admin.');
            }
        } else {
            diagnostic_info('Pour tester le mot de passe admin sans l afficher, ajoute ?admin_test=TON_MOT_DE_PASSE a l URL.');
            diagnostic_info('Pour definir un nouveau mot de passe temporairement, ajoute ?admin_set=NOUVEAU_MOT_DE_PASSE a l URL, puis supprime ce fichier.');
        }
    } catch (Throwable $exception) {
        diagnostic_ko('Impossible de verifier la table admin : ' . $exception->getMessage());
    }
    ?>

    <p><strong>Important :</strong> supprime ce fichier du serveur apres le diagnostic.</p>
</body>
</html>
