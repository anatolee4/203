<?php
require_once __DIR__ . '/../commun/inscriptions_repository.php';

$capaciteMax = 12;
$personnesInitiales = max(1, min($capaciteMax, (int) ($_GET['personnes'] ?? 1)));
$salleSelectionnee = code_salle_disponibilite($_GET['salle'] ?? '');
$retourEtape1 = 'inscription1.php?' . http_build_query([
    'nom' => $_GET['nom'] ?? '',
    'prenom' => $_GET['prenom'] ?? '',
    'email' => $_GET['email'] ?? '',
    'profil' => $_GET['profil'] ?? '',
    'personnes' => $_GET['personnes'] ?? '1',
    'salle' => $_GET['salle'] ?? '',
    'creneau' => $_GET['creneau'] ?? '',
    'buffet' => $_GET['buffet'] ?? '',
]);
$salles = [
    '002' => 'Salle 002',
    '001' => 'Salle 001',
    '021' => 'Salle 021',
    '005' => 'Salle 005',
];
$creneaux = [
    'jeudi-15h',
    'jeudi-15h30',
    'jeudi-16h',
    'jeudi-16h30',
    'jeudi-17h',
    'jeudi-17h30',
    'jeudi-18h',
    'jeudi-18h30',
    'jeudi-19h',
    'jeudi-19h30',
    'jeudi-20h',
    'vendredi-9h30',
    'vendredi-10h',
    'vendredi-10h30',
    'vendredi-11h',
];

$inscriptions = inscriptions_lire_toutes();

function code_salle_disponibilite(?string $salle): string
{
    if (preg_match('/(001|002|005|021)/', (string) $salle, $matches)) {
        return $matches[1];
    }

    return '';
}

function places_restantes_max(array $inscriptions, string $salle, array $creneaux, int $capaciteMax): int
{
    $placesParCreneau = array_fill_keys($creneaux, 0);

    foreach ($inscriptions as $inscription) {
        $creneau = (string) ($inscription['creneau'] ?? '');

        if (
            code_salle_disponibilite($inscription['salle'] ?? '') === $salle
            && array_key_exists($creneau, $placesParCreneau)
        ) {
            $placesParCreneau[$creneau] += max(1, (int) ($inscription['personnes'] ?? 1));
        }
    }

    $meilleureDisponibilite = 0;
    foreach ($placesParCreneau as $placesPrises) {
        $meilleureDisponibilite = max($meilleureDisponibilite, $capaciteMax - $placesPrises);
    }

    return max(0, $meilleureDisponibilite);
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
    <link rel="stylesheet" href="inscription.css?v=5">
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
                    <?php if (trim((string) ($_GET['creneau'] ?? '')) !== ''): ?>
                        <input type="hidden" name="creneau" value="<?= htmlspecialchars($_GET['creneau'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <?php if (trim((string) ($_GET['buffet'] ?? '')) !== ''): ?>
                        <input type="hidden" name="buffet" value="<?= htmlspecialchars($_GET['buffet'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="form-label" for="personnes">Nombre de personnes</label>
                        <div class="counter-control" aria-label="Nombre de personnes">
                            <button class="counter-button" type="button" data-counter="minus" aria-label="Diminuer">-</button>
                            <input class="counter-value" type="number" id="personnes" name="personnes" min="1" max="12" value="<?= $personnesInitiales ?>" required>
                            <button class="counter-button" type="button" data-counter="plus" aria-label="Augmenter">+</button>
                        </div>
                    </div>

                    <fieldset class="form-fieldset">
                        <legend class="fieldset-title">Choix de la salle</legend>

                        <div class="room-grid">
                            <?php foreach ($salles as $code => $nomSalle): ?>
                                <?php
                                    $placesRestantes = places_restantes_max($inscriptions, $code, $creneaux, $capaciteMax);
                                    $salleIndisponible = $placesRestantes < $personnesInitiales;
                                ?>
                                <label class="room-option <?= $salleIndisponible ? 'room-option--disabled' : '' ?>" data-room-option data-max-remaining="<?= $placesRestantes ?>" <?= $salleIndisponible ? 'aria-disabled="true"' : '' ?>>
                                    <input type="radio" name="salle" value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" required <?= $salleIndisponible ? 'disabled' : '' ?> <?= !$salleIndisponible && $salleSelectionnee === $code ? 'checked' : '' ?>>
                                    <span class="room-card">
                                        <span class="room-name"><?= htmlspecialchars($nomSalle, ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="room-capacity" data-room-capacity <?= $salleIndisponible ? 'hidden' : '' ?>><?= $placesRestantes ?> places disponibles</span>
                                        <span class="room-status" data-room-status <?= $salleIndisponible ? '' : 'hidden' ?>>Pas assez de places</span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>

                    <div class="step-actions">
                        <a class="btn-secondary" href="<?= htmlspecialchars($retourEtape1, ENT_QUOTES, 'UTF-8') ?>">Retour</a>
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
        const roomOptions = Array.from(document.querySelectorAll("[data-room-option]"));

        const updateRoomAvailability = () => {
            const requestedPlaces = Number(counterInput.value) || 1;

            roomOptions.forEach((option) => {
                const maxRemaining = Number(option.dataset.maxRemaining) || 0;
                const isUnavailable = maxRemaining < requestedPlaces;
                const input = option.querySelector('input[name="salle"]');
                const capacity = option.querySelector("[data-room-capacity]");
                const status = option.querySelector("[data-room-status]");

                option.classList.toggle("room-option--disabled", isUnavailable);
                option.setAttribute("aria-disabled", String(isUnavailable));
                input.disabled = isUnavailable;

                if (input.checked && isUnavailable) {
                    input.checked = false;
                }

                if (capacity) {
                    capacity.hidden = isUnavailable;
                }

                if (status) {
                    status.hidden = !isUnavailable;
                }
            });
        };

        document.querySelectorAll("[data-counter]").forEach((button) => {
            button.addEventListener("click", () => {
                const direction = button.dataset.counter === "plus" ? 1 : -1;
                const min = Number(counterInput.min);
                const max = Number(counterInput.max);
                const nextValue = Number(counterInput.value) + direction;

                counterInput.value = Math.min(max, Math.max(min, nextValue));
                updateRoomAvailability();
            });
        });

        counterInput.addEventListener("input", updateRoomAvailability);
        updateRoomAvailability();
    </script>
</body>
</html>
