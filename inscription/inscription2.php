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
    <link rel="stylesheet" href="../commun/footer.css">
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
                <h1 class="inscription-title" id="inscription-title">Votre visite</h1>

                <form class="inscription-form" action="inscription3.php" method="get" data-validate-form data-error-message="Veuillez choisir le nombre de personnes et une salle avant de continuer." novalidate>
                    <p class="form-alert" data-form-alert role="alert" aria-live="polite" hidden></p>

                    <input type="hidden" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="prenom" value="<?= htmlspecialchars($_GET['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="profil" value="<?= htmlspecialchars($_GET['profil'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>

                    <div class="form-group">
                        <label class="form-label" for="personnes">Nombre de personnes</label>
                        <div class="counter-control" aria-label="Nombre de personnes">
                            <button class="counter-button" type="button" data-counter="minus" aria-label="Diminuer">-</button>
                            <input class="counter-value" type="number" id="personnes" name="personnes" min="1" max="12" value="1" required>
                            <button class="counter-button" type="button" data-counter="plus" aria-label="Augmenter">+</button>
                        </div>
                    </div>

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Choix de la salle</legend>

                        <div class="room-grid">
                            <label class="room-option">
                                <input type="radio" name="salle" value="002" required>
                                <span class="room-card">
                                    <span class="room-name">Salle 002</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                            <label class="room-option">
                                <input type="radio" name="salle" value="001" required>
                                <span class="room-card">
                                    <span class="room-name">Salle 001</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                            <label class="room-option">
                                <input type="radio" name="salle" value="021" required>
                                <span class="room-card">
                                    <span class="room-name">Salle 021</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                            <label class="room-option">
                                <input type="radio" name="salle" value="005" required>
                                <span class="room-card">
                                    <span class="room-name">Salle 005</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                        </div>
                    </fieldset>

                    <div class="step-actions">
                        <a class="btn-secondary" href="inscription1.php">Retour</a>
                        <button class="btn-next" type="submit">Suivant</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include "../commun/footer.php"; ?>
    <script src="inscription-validation.js"></script>
    <script>
        const counterInput = document.querySelector("#personnes");

        document.querySelectorAll("[data-counter]").forEach((button) => {
            button.addEventListener("click", () => {
                const direction = button.dataset.counter === "plus" ? 1 : -1;
                const min = Number(counterInput.min);
                const max = Number(counterInput.max);
                const nextValue = Number(counterInput.value) + direction;

                counterInput.value = Math.min(max, Math.max(min, nextValue));
            });
        });
    </script>
</body>
</html>
