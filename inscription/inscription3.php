<?php
$capaciteMax = 12;
$salleChoisie = preg_match('/(001|002|005|021)/', (string) ($_GET['salle'] ?? ''), $matches) ? $matches[1] : '';
$personnesDemandees = max(1, min($capaciteMax, (int) ($_GET['personnes'] ?? 1)));
$creneauSelectionne = trim((string) ($_GET['creneau'] ?? ''));
$retourEtape2 = 'inscription2.php?' . http_build_query([
    'nom' => $_GET['nom'] ?? '',
    'prenom' => $_GET['prenom'] ?? '',
    'email' => $_GET['email'] ?? '',
    'profil' => $_GET['profil'] ?? '',
    'personnes' => $_GET['personnes'] ?? '1',
    'salle' => $_GET['salle'] ?? '',
    'creneau' => $_GET['creneau'] ?? '',
    'buffet' => $_GET['buffet'] ?? '',
]);

$creneaux = [
    'Jeudi 18' => [
        '15h' => 'jeudi-15h',
        '15h30' => 'jeudi-15h30',
        '16h' => 'jeudi-16h',
        '16h30' => 'jeudi-16h30',
        '17h' => 'jeudi-17h',
        '17h30' => 'jeudi-17h30',
        '18h' => 'jeudi-18h',
        '18h30' => 'jeudi-18h30',
        '19h' => 'jeudi-19h',
        '19h30' => 'jeudi-19h30',
        '20h' => 'jeudi-20h',
    ],
    'Vendredi 19' => [
        '9h30' => 'vendredi-9h30',
        '10h' => 'vendredi-10h',
        '10h30' => 'vendredi-10h30',
        '11h' => 'vendredi-11h',
    ],
];

$inscriptions = json_decode($_COOKIE['inscriptions'] ?? '[]', true);
if (!is_array($inscriptions)) {
    $inscriptions = [];
}

function code_salle_inscription(?string $salle): string
{
    if (preg_match('/(001|002|005|021)/', (string) $salle, $matches)) {
        return $matches[1];
    }

    return '';
}

function places_prises_creneau(array $inscriptions, string $salle, string $creneau): int
{
    $places = 0;

    foreach ($inscriptions as $inscription) {
        if (
            code_salle_inscription($inscription['salle'] ?? '') === $salle
            && ($inscription['creneau'] ?? '') === $creneau
        ) {
            $places += max(1, (int) ($inscription['personnes'] ?? 1));
        }
    }

    return $places;
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
    <link rel="stylesheet" href="inscription.css?v=4">
</head>
<body>
    <header class="header-container-absolute">
        <?php include "../commun/header.php"; ?>
    </header>

    <main class="inscription-page">
        <section class="inscription-card" aria-labelledby="inscription-title">
            <div class="inscription-visual" aria-hidden="true"></div>

            <div class="inscription-content">
                <h1 class="inscription-title" id="inscription-title">Créneaux de la visite</h1>

                <form class="inscription-form" action="inscription4.php" method="get" data-validate-form data-error-message="Veuillez choisir un créneau avant de continuer." novalidate>
                    <p class="form-alert" data-form-alert role="alert" aria-live="polite" hidden></p>

                    <input type="hidden" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="prenom" value="<?= htmlspecialchars($_GET['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="profil" value="<?= htmlspecialchars($_GET['profil'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="personnes" value="<?= htmlspecialchars($_GET['personnes'] ?? '1', ENT_QUOTES, 'UTF-8') ?>" required>
                    <input type="hidden" name="salle" value="<?= htmlspecialchars($_GET['salle'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    <?php if (trim((string) ($_GET['buffet'] ?? '')) !== ''): ?>
                        <input type="hidden" name="buffet" value="<?= htmlspecialchars($_GET['buffet'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>

                    <fieldset class="form-fieldset slot-panel">
                        <?php foreach ($creneaux as $jour => $horaires): ?>
                            <p class="slot-day"><?= htmlspecialchars($jour, ENT_QUOTES, 'UTF-8') ?></p>

                            <div class="slot-grid">
                                <?php foreach ($horaires as $horaire => $creneau): ?>
                                    <?php
                                        $placesPrises = places_prises_creneau($inscriptions, $salleChoisie, $creneau);
                                        $placesRestantes = max(0, $capaciteMax - $placesPrises);
                                        $estComplet = $placesRestantes < $personnesDemandees;
                                        $statutPlaces = $placesRestantes === 0
                                            ? 'Complet'
                                            : $placesRestantes . ' place' . ($placesRestantes > 1 ? 's' : '') . ' restante' . ($placesRestantes > 1 ? 's' : '');
                                    ?>
                                    <label class="slot-option <?= $estComplet ? 'slot-option--disabled' : '' ?>" title="<?= $estComplet ? htmlspecialchars($statutPlaces, ENT_QUOTES, 'UTF-8') : '' ?>" <?= $estComplet ? 'aria-disabled="true"' : '' ?>>
                                        <input type="radio" name="creneau" value="<?= htmlspecialchars($creneau, ENT_QUOTES, 'UTF-8') ?>" required <?= $estComplet ? 'disabled' : '' ?> <?= !$estComplet && $creneauSelectionne === $creneau ? 'checked' : '' ?>>
                                        <span class="slot-card">
                                            <span><?= htmlspecialchars($horaire, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if ($estComplet): ?>
                                                <span class="slot-status"><?= htmlspecialchars($statutPlaces, ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>

                    <div class="step-actions">
                        <a class="btn-secondary" href="<?= htmlspecialchars($retourEtape2, ENT_QUOTES, 'UTF-8') ?>">Retour</a>
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
