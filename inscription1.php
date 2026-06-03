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
                <h1 class="inscription-title" id="inscription-title">Inscription</h1>

                <form class="inscription-form" action="inscription2.php" method="get" data-validate-form data-error-message="Veuillez remplir tous les champs avant de continuer." novalidate>
                    <p class="form-alert" data-form-alert role="alert" aria-live="polite" hidden></p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="nom">Nom</label>
                            <input class="form-input" type="text" id="nom" name="nom" autocomplete="family-name" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="prenom">Prénom</label>
                            <input class="form-input" type="text" id="prenom" name="prenom" autocomplete="given-name" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input class="form-input" type="email" id="email" name="email" autocomplete="email" required>
                    </div>

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Qui êtes-vous ?</legend>

                        <div class="radio-list">
                            <label class="radio-option">
                                <input type="radio" name="profil" value="enseignant" required>
                                <span>Enseignant·e</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="etudiant-mmi" required>
                                <span>Étudiant·e MMI 2 ou 3</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="personnel-usmb" required>
                                <span>Personnel USMB</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="professionnel" required>
                                <span>Professionnel·le / partenaire de la formation</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="profil" value="visiteur" required>
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

    <?php include 'footer.php'; ?>
    <script src="inscription-validation.js"></script>
</body>
</html>
