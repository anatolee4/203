<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Exposition Interactive</title>
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="salles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container-absolute">
        <?php include 'header.php'; ?>
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
                <a href="/203/203/oeuvres.php" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
            <img src="img/imgsalle001.png" alt="Salle 001" class="salle-image">
        </section>

        <section class="salle-section salle-section--image-left">
            <img src="img/imgsalle002.png" alt="Salle 002" class="salle-image">
            <div class="salle-content">
                <h2>Salle 002</h2>
                <p class="salle-tp">TP : ...</p>
                <p class="salle-label">PRESENTATION</p>
                <div class="salle-presentation-box">
                    <p>Texte de presentation de la salle a completer.</p>
                </div>
                <a href="/203/203/oeuvres.php" class="salle-oeuvres-button">Voir les oeuvres</a>
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
                <a href="/203/203/oeuvres.php" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
            <img src="img/imgsalle005.png" alt="Salle 005" class="salle-image">
        </section>

        <section class="salle-section salle-section--image-left">
            <img src="img/imgsalle021.png" alt="Salle 021" class="salle-image">
            <div class="salle-content">
                <h2>Salle 021</h2>
                <p class="salle-tp">TP : ...</p>
                <p class="salle-label">PRESENTATION</p>
                <div class="salle-presentation-box">
                    <p>Texte de presentation de la salle a completer.</p>
                </div>
                <a href="/203/203/oeuvres.php" class="salle-oeuvres-button">Voir les oeuvres</a>
            </div>
        </section>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
