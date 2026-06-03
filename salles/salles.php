<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Exposition Interactive</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../commun/reset.css">
    <link rel="stylesheet" href="../accueil/menu.css">
    <link rel="stylesheet" href="../commun/header.css">
    <link rel="stylesheet" href="../commun/footer.css?v=2">
    <link rel="stylesheet" href="salles.css">
</head>
<body>
    <header class="header-container-absolute">
        <?php include "../commun/header.php"; ?>
    </header>

    <main class="salles-page">
        <section class="salle-section salle-section--image-right">
            <div class="salle-content">
                <h1>Salle 001</h1>
                <p class="salle-tp">TP : ...</p>
                <p class="salle-label">PRESENTATION</p>
                <div class="salle-presentation-box">
                    <p>Texte de presentation de la salle a completer.</p>
                </div>
                <a href="../oeuvres/oeuvres.php#salle-001" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
            <img src="../img/imgsalle001.png" alt="Salle 001" class="salle-image">
        </section>

        <section class="salle-section salle-section--image-left">
            <img src="../img/imgsalle002.png" alt="Salle 002" class="salle-image">
            <div class="salle-content">
                <h2>Salle 002</h2>
                <p class="salle-tp">TP : ...</p>
                <p class="salle-label">PRESENTATION</p>
                <div class="salle-presentation-box">
                    <p>Texte de presentation de la salle a completer.</p>
                </div>
                <a href="../oeuvres/oeuvres.php#salle-002" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
        </section>

        <section class="salle-section salle-section--image-right">
            <div class="salle-content">
                <h2>Salle 005</h2>
                <p class="salle-tp">TP : ...</p>
                <p class="salle-label">PRESENTATION</p>
                <div class="salle-presentation-box">
                    <p>Texte de presentation de la salle a completer.</p>
                </div>
                <a href="../oeuvres/oeuvres.php#salle-005" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
            <img src="../img/imgsalle005.png" alt="Salle 005" class="salle-image">
        </section>

        <section class="salle-section salle-section--image-left">
            <img src="../img/imgsalle021.png" alt="Salle 021" class="salle-image">
            <div class="salle-content">
                <h2>Salle 021</h2>
                <p class="salle-tp">TP : ...</p>
                <p class="salle-label">PRESENTATION</p>
                <div class="salle-presentation-box">
                    <p>Texte de presentation de la salle a completer.</p>
                </div>
                <a href="../oeuvres/oeuvres.php#salle-021" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
        </section>
    </main>
    <?php include "../commun/footer.php"; ?>
</body>
</html>
