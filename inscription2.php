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
                <h1 class="inscription-title" id="inscription-title">Votre visite</h1>

                <form class="inscription-form" action="inscription3.php" method="get">
                    <div class="form-group">
                        <label class="form-label" for="personnes">Nombre de personnes</label>
                        <div class="counter-control" aria-label="Nombre de personnes">
                            <button class="counter-button" type="button" data-counter="minus" aria-label="Diminuer">-</button>
                            <input class="counter-value" type="number" id="personnes" name="personnes" min="1" max="12" value="1">
                            <button class="counter-button" type="button" data-counter="plus" aria-label="Augmenter">+</button>
                        </div>
                    </div>

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Choix de la salle</legend>

                        <div class="room-grid">
                            <label class="room-option">
                                <input type="radio" name="salle" value="002">
                                <span class="room-card">
                                    <span class="room-name">Salle 002</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                            <label class="room-option">
                                <input type="radio" name="salle" value="001">
                                <span class="room-card">
                                    <span class="room-name">Salle 001</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                            <label class="room-option">
                                <input type="radio" name="salle" value="021">
                                <span class="room-card">
                                    <span class="room-name">Salle 021</span>
                                    <span class="room-capacity">12 places</span>
                                </span>
                            </label>
                            <label class="room-option">
                                <input type="radio" name="salle" value="005">
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

    <?php include 'footer.php'; ?>
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
