<?php
$salles = [
    ['nom' => 'Salle 001', 'inscrits' => 8],
    ['nom' => 'Salle 002', 'inscrits' => 5],
    ['nom' => 'Salle 005', 'inscrits' => 12],
    ['nom' => 'Salle 021', 'inscrits' => 3],
];

$capaciteMax = 12;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tableau de bord</title>
    <link rel="stylesheet" href="../commun/reset.css">
    <link rel="stylesheet" href="../commun/header.css">
    <link rel="stylesheet" href="../commun/footer.css?v=2">
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container-absolute">
        <?php include "../commun/header.php"; ?>
    </header>

    <main class="admin-page">
        <section class="admin-hero">
            <p class="admin-eyebrow">Administration</p>
            <h1>Tableau de bord des salles</h1>
            <p class="admin-intro">Suivi visuel du nombre de personnes inscrites par salle.</p>
        </section>

        <section class="dashboard" aria-label="Compteurs des salles">
            <?php foreach ($salles as $salle): ?>
                <?php
                $inscrits = max(0, min($capaciteMax, (int) $salle['inscrits']));
                $pourcentage = ($inscrits / $capaciteMax) * 100;
                ?>
                <article class="room-card">
                    <div class="room-card__header">
                        <h2><?= htmlspecialchars($salle['nom'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="room-card__counter">
                            <span><?= $inscrits ?></span>/<?= $capaciteMax ?>
                        </p>
                    </div>

                    <div class="gauge" aria-label="<?= htmlspecialchars($salle['nom'], ENT_QUOTES, 'UTF-8') ?> : <?= $inscrits ?> inscrits sur <?= $capaciteMax ?>">
                        <div class="gauge__fill" style="width: <?= $pourcentage ?>%;"></div>
                    </div>

                    <p class="room-card__status"><?= round($pourcentage) ?>% rempli</p>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

    <?php include "../commun/footer.php"; ?>
</body>
</html>
