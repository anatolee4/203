<?php
function valeur_retour(string $nom, string $defaut = ''): string
{
    return trim((string) ($_GET[$nom] ?? $defaut));
}

$profilSelectionne = valeur_retour('profil');
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
                <h1 class="inscription-title" id="inscription-title">Inscription</h1>

                <form class="inscription-form" action="inscription2.php" method="get" data-validate-form data-error-message="Veuillez remplir tous les champs avant de continuer." novalidate>
                    <p class="form-alert" data-form-alert role="alert" aria-live="polite" hidden></p>

                    <?php foreach (['personnes', 'salle', 'creneau', 'buffet'] as $champCache): ?>
                        <?php if (valeur_retour($champCache) !== ''): ?>
                            <input type="hidden" name="<?= htmlspecialchars($champCache, ENT_QUOTES, 'UTF-8') ?>" value="<?= htmlspecialchars(valeur_retour($champCache), ENT_QUOTES, 'UTF-8') ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom</label>
                            <input class="form-input" type="text" id="nom" name="nom" autocomplete="family-name" value="<?= htmlspecialchars(valeur_retour('nom'), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="prenom">Prénom</label>
                            <input class="form-input" type="text" id="prenom" name="prenom" autocomplete="given-name" value="<?= htmlspecialchars(valeur_retour('prenom'), ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input class="form-input" type="email" id="email" name="email" autocomplete="email" value="<?= htmlspecialchars(valeur_retour('email'), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Qui êtes-vous ?</legend>

                        <div class="radio-list">
                            <label class="radio-option">
                                <input type="radio" name="profil" value="enseignant" required <?= $profilSelectionne === 'enseignant' ? 'checked' : '' ?>>
                                <span>Enseignant·e</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="etudiant-mmi" required <?= $profilSelectionne === 'etudiant-mmi' ? 'checked' : '' ?>>
                                <span>Étudiant·e MMI 2 ou 3</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="personnel-usmb" required <?= $profilSelectionne === 'personnel-usmb' ? 'checked' : '' ?>>
                                <span>Personnel USMB</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="professionnel" required <?= $profilSelectionne === 'professionnel' ? 'checked' : '' ?>>
                                <span>Professionnel·le / partenaire de la formation</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="visiteur" required <?= $profilSelectionne === 'visiteur' ? 'checked' : '' ?>>
                                <span>Visiteur·se extérieur·e</span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="step-actions">
                        <button class="btn-next" type="submit">Suivant</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include "../commun/footer.php"; ?>
    <script src="inscription-validation.js"></script>
</body>
</html>
