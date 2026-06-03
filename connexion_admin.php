<?php
session_start();

const ADMIN_DB_HOST = 'localhost';
const ADMIN_DB_NAME = 'sae203';
const ADMIN_DB_USER = 'root';
const ADMIN_DB_PASSWORD = '';
const ADMIN_DB_TABLE = 'administrateurs';
const ADMIN_DB_PASSWORD_COLUMN = 'mot_de_passe';

function admin_login_champ(string $nom): string
{
    return trim((string) ($_POST[$nom] ?? ''));
}

function admin_login_identifier(string $identifier): string
{
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
        throw new RuntimeException('Configuration SQL invalide.');
    }

    return '`' . $identifier . '`';
}

function admin_login_pdo(): PDO
{
    $dsn = 'mysql:host=' . ADMIN_DB_HOST . ';dbname=' . ADMIN_DB_NAME . ';charset=utf8mb4';

    return new PDO($dsn, ADMIN_DB_USER, ADMIN_DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function admin_login_password_ok(string $motDePasse): bool
{
    $pdo = admin_login_pdo();
    $table = admin_login_identifier(ADMIN_DB_TABLE);
    $colonneMotDePasse = admin_login_identifier(ADMIN_DB_PASSWORD_COLUMN);
    $requete = $pdo->query("SELECT $colonneMotDePasse AS mot_de_passe FROM $table");

    foreach ($requete as $ligne) {
        $motDePasseBase = (string) ($ligne['mot_de_passe'] ?? '');

        if ($motDePasseBase === '') {
            continue;
        }

        $estHash = password_get_info($motDePasseBase)['algo'] !== 0;
        if (($estHash && password_verify($motDePasse, $motDePasseBase)) || (!$estHash && hash_equals($motDePasseBase, $motDePasse))) {
            return true;
        }
    }

    return false;
}

if (isset($_GET['deconnexion'])) {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
    header('Location: connexion_admin.php?deconnecte=1');
    exit;
}

if (!empty($_SESSION['admin_connecte'])) {
    header('Location: admin/admin.php');
    exit;
}

$erreur = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $motDePasse = admin_login_champ('mot_de_passe');

    if ($motDePasse === '') {
        $erreur = 'Entre le mot de passe administrateur.';
    } else {
        try {
            if (admin_login_password_ok($motDePasse)) {
                session_regenerate_id(true);
                $_SESSION['admin_connecte'] = true;
                header('Location: admin/admin.php');
                exit;
            }

            $erreur = 'Mot de passe incorrect.';
        } catch (Throwable $exception) {
            $erreur = 'Impossible de vérifier le mot de passe dans la base de données.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Connexion admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="commun/reset.css">
    <link rel="stylesheet" href="commun/header.css">
    <link rel="stylesheet" href="commun/footer.css">
    <link rel="stylesheet" href="admin/admin.css">
</head>
<body>
    <header class="header-container-absolute">
        <?php include "commun/header.php"; ?>
    </header>

    <main class="admin-login-page">
        <section class="admin-login-card" aria-labelledby="admin-login-title">
            <p class="admin-eyebrow">Administration</p>
            <h1 id="admin-login-title">Connexion admin</h1>
            <p class="admin-intro">Accès réservé au tableau de bord des inscriptions.</p>

            <?php if (isset($_GET['deconnecte'])): ?>
                <p class="admin-message">Vous êtes bien déconnecté.</p>
            <?php endif; ?>

            <?php if ($erreur !== ''): ?>
                <p class="admin-error" role="alert"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <form class="admin-login-form" action="connexion_admin.php" method="post">
                <label class="admin-login-field">
                    <span>Mot de passe</span>
                    <input type="password" name="mot_de_passe" autocomplete="current-password" required autofocus>
                </label>

                <button class="admin-login-submit" type="submit">Se connecter</button>
            </form>
        </section>
    </main>

    <?php include "commun/footer.php"; ?>
</body>
</html>
