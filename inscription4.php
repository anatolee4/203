<?php
function champ_inscription(string $nom, string $defaut = ''): string
{
    return trim((string) ($_POST[$nom] ?? $_GET[$nom] ?? $defaut));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inscription = [
        'nom' => champ_inscription('nom'),
        'prenom' => champ_inscription('prenom'),
        'email' => champ_inscription('email'),
        'profil' => champ_inscription('profil'),
        'personnes' => champ_inscription('personnes', '1'),
        'salle' => champ_inscription('salle'),
        'creneau' => champ_inscription('creneau'),
        'buffet' => isset($_POST['buffet']) ? 'oui' : 'non',
        'date_creation' => date('Y-m-d H:i:s'),
    ];

    $inscriptions = json_decode($_COOKIE['inscriptions'] ?? '[]', true);
    if (!is_array($inscriptions)) {
        $inscriptions = [];
    }

    $inscriptions[] = $inscription;

    /*
     * Ici, tu pourras remplacer ou compléter le cookie avec une insertion SQL.
     * Exemple de logique :
     * 1. inclure ton fichier de connexion PDO ;
     * 2. préparer un INSERT INTO inscriptions (...) VALUES (...) ;
     * 3. exécuter la requête avec les valeurs de $inscription.
     */
    setcookie('inscriptions', json_encode($inscriptions), time() + 60 * 60 * 24 * 30, '/');
    header('Location: inscription.php?confirmation=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Inscription</title>
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="inscription.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container-absolute">
        <?php include 'header.php'; ?>
    </header>

    <main class="inscription-page">
        <section class="inscription-card" aria-labelledby="inscription-title">
            <div class="inscription-visual" aria-hidden="true"></div>

            <div class="inscription-content">
                <h1 class="inscription-title" id="inscription-title">Derniers détails</h1>

                <form class="inscription-form" action="inscription4.php" method="post">
                    <input type="hidden" name="nom" value="<?= htmlspecialchars(champ_inscription('nom'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="prenom" value="<?= htmlspecialchars(champ_inscription('prenom'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars(champ_inscription('email'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="profil" value="<?= htmlspecialchars(champ_inscription('profil'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="personnes" value="<?= htmlspecialchars(champ_inscription('personnes', '1'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="salle" value="<?= htmlspecialchars(champ_inscription('salle'), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="creneau" value="<?= htmlspecialchars(champ_inscription('creneau'), ENT_QUOTES, 'UTF-8') ?>">

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Buffet</legend>

                        <div class="radio-list">
                            <label class="radio-option">
                                <input type="checkbox" name="buffet" value="oui">
                                <span>Je participe au buffet du jeudi 18 juin à 18h30</span>
                            </label>
                        </div>
                    </fieldset>

                    <section aria-labelledby="question-title">
                        <h2 class="fieldset-title" id="question-title">Une question ou un cas particulier</h2>
                        <p class="radio-option">Contacter le référent des inscriptions</p>

                        <ul class="contact-list">
                            <li><span>Salle 002</span><a href="mailto:aaaa@etu.univ-smb.fr">aaaa@etu.univ-smb.fr</a></li>
                            <li><span>Salle 001</span><a href="mailto:bbbb@etu.univ-smb.fr">bbbb@etu.univ-smb.fr</a></li>
                            <li><span>Salle 021</span><a href="mailto:cccc@etu.univ-smb.fr">cccc@etu.univ-smb.fr</a></li>
                            <li><span>Salle 005</span><a href="mailto:dddd@etu.univ-smb.fr">dddd@etu.univ-smb.fr</a></li>
                        </ul>
                    </section>

                    <div class="step-actions">
                        <a class="btn-secondary" href="inscription3.php">Retour</a>
                        <button class="btn-next" type="submit">Confirmer l'inscription</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
