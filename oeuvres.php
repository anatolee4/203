<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Les oeuvres</title>
    <link rel="stylesheet" href="menu.css">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="oeuvres.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header-container-absolute">
        <?php include 'header.php'; ?>
    </header>

    <main class="oeuvres-page">
        <section class="oeuvres-room">
            <div class="oeuvres-heading">
                <h1>Salle 001</h1>
                <p>TP : ...</p>
            </div>

            <div class="oeuvres-grid">
                <article class="oeuvre-card oeuvre-card--one">
                    <img src="img/imgsalle001.png" alt="Oeuvre 1" class="oeuvre-image">
                    <div class="oeuvre-panel">
                        <h2>Oeuvre 1</h2>
                        <p>Presentation courte de l'oeuvre, de son intention et de l'experience proposee au visiteur.</p>
                    </div>
                </article>

                <article class="oeuvre-card oeuvre-card--two">
                    <img src="img/imgsalle001.png" alt="Oeuvre 2" class="oeuvre-image">
                    <div class="oeuvre-panel">
                        <h2>Oeuvre 2</h2>
                        <p>Informations sur les formes, les sons ou les interactions qui composent cette installation.</p>
                    </div>
                </article>

                <article class="oeuvre-card oeuvre-card--three">
                    <img src="img/imgsalle001.png" alt="Oeuvre 3" class="oeuvre-image">
                    <div class="oeuvre-panel">
                        <h2>Oeuvre 3</h2>
                        <p>Texte a completer avec le nom du projet, les auteurs et les elements importants a observer.</p>
                    </div>
                </article>

                <article class="oeuvre-card oeuvre-card--four">
                    <img src="img/imgsalle001.png" alt="Oeuvre 4" class="oeuvre-image">
                    <div class="oeuvre-panel">
                        <h2>Oeuvre 4</h2>
                        <p>Details supplementaires sur le dispositif, le theme et le lien avec l'exposition E-llusion.</p>
                    </div>
                </article>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
