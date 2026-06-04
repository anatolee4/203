<?php
require_once __DIR__ . '/../commun/inscriptions_repository.php';

function champ_inscription(string $nom, string $defaut = ''): string
{
    return trim((string) ($_POST[$nom] ?? $_GET[$nom] ?? $defaut));
}

$retourEtape3 = 'inscription3.php?' . http_build_query([
    'nom' => champ_inscription('nom'),
    'prenom' => champ_inscription('prenom'),
    'email' => champ_inscription('email'),
    'profil' => champ_inscription('profil'),
    'personnes' => champ_inscription('personnes', '1'),
    'salle' => champ_inscription('salle'),
    'creneau' => champ_inscription('creneau'),
    'buffet' => champ_inscription('buffet'),
]);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
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

    $tokenGestion = inscriptions_ajouter($inscription);
    header('Location: inscription.php?' . http_build_query([
        'confirmation' => '1',
        'token_gestion' => $tokenGestion,
    ]));
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../commun/reset.css">
    <link rel="stylesheet" href="../accueil/menu.css">
    <link rel="stylesheet" href="../commun/header.css">
    <link rel="stylesheet" href="../commun/footer.css?v=2">
    <link rel="stylesheet" href="inscription.css">
</head>
<body>
    <header class="header-container-absolute">
        <?php include "../commun/header.php"; ?>
    </header>

    <main class="inscription-page">
        <section class="inscription-card" aria-labelledby="inscription-title">
            <div class="inscription-visual" aria-hidden="true"></div>

            <div class="inscription-content">
                <h1 class="inscription-title" id="inscription-title">Derniers détails</h1>

                <form class="inscription-form" action="inscription4.php" method="post" data-validate-form data-error-message="Certaines informations obligatoires sont manquantes. Revenez aux étapes précédentes pour compléter l'inscription." novalidate>
                    <p class="form-alert" data-form-alert role="alert" aria-live="polite" hidden></p>

                    <input type="hidden" name="nom" value="<?= htmlspecialchars(champ_inscription('nom'), ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="prenom" value="<?= htmlspecialchars(champ_inscription('prenom'), ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="email" value="<?= htmlspecialchars(champ_inscription('email'), ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="profil" value="<?= htmlspecialchars(champ_inscription('profil'), ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="personnes" value="<?= htmlspecialchars(champ_inscription('personnes', '1'), ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="salle" value="<?= htmlspecialchars(champ_inscription('salle'), ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="creneau" value="<?= htmlspecialchars(champ_inscription('creneau'), ENT_QUOTES, 'UTF-8') ?>" required>

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Buffet</legend>

                        <div class="radio-list">
                            <label class="radio-option">
                                <input type="checkbox" name="buffet" value="oui" <?= champ_inscription('buffet') === 'oui' ? 'checked' : '' ?>>
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
                        <a class="btn-secondary" id="back-to-slots" href="<?= htmlspecialchars($retourEtape3, ENT_QUOTES, 'UTF-8') ?>">Retour</a>
                        <button class="btn-next" type="submit">Confirmer l'inscription</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include "../commun/footer.php"; ?>
    <script src="inscription-validation.js"></script>
    <script>
        const buffetInput = document.querySelector('input[name="buffet"]');
        const backLink = document.querySelector("#back-to-slots");

        if (buffetInput && backLink) {
            backLink.addEventListener("click", () => {
                const url = new URL(backLink.href, window.location.href);
                if (buffetInput.checked) {
                    url.searchParams.set("buffet", "oui");
                } else {
                    url.searchParams.delete("buffet");
                }
                backLink.href = url.toString();
            });
        }
    </script>
</body>
</html>
