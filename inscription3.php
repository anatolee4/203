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
                <h1 class="inscription-title" id="inscription-title">Créneaux de la visite</h1>

                <form class="inscription-form" action="inscription4.php" method="get">
                    <input type="hidden" name="nom" value="<?= htmlspecialchars($_GET['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="prenom" value="<?= htmlspecialchars($_GET['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="profil" value="<?= htmlspecialchars($_GET['profil'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="personnes" value="<?= htmlspecialchars($_GET['personnes'] ?? '1', ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="salle" value="<?= htmlspecialchars($_GET['salle'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <fieldset class="form-fieldset slot-panel">
                        <p class="slot-day">Jeudi 18</p>

                        <div class="slot-grid">
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-15h"><span class="slot-card">15h</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-15h30"><span class="slot-card">15h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-16h"><span class="slot-card">16h</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-16h30"><span class="slot-card">16h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-17h"><span class="slot-card">17h</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-17h30"><span class="slot-card">17h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-18h"><span class="slot-card">18h</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-18h30"><span class="slot-card">18h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-19h"><span class="slot-card">19h</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-19h30"><span class="slot-card">19h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="jeudi-20h"><span class="slot-card">20h</span></label>
                        </div>

                        <p class="slot-day">Vendredi 19</p>

                        <div class="slot-grid">
                            <label class="slot-option"><input type="radio" name="creneau" value="vendredi-9h30"><span class="slot-card">9h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="vendredi-10h"><span class="slot-card">10h</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="vendredi-10h30"><span class="slot-card">10h30</span></label>
                            <label class="slot-option"><input type="radio" name="creneau" value="vendredi-11h"><span class="slot-card">11h</span></label>
                        </div>
                    </fieldset>

                    <div class="step-actions">
                        <a class="btn-secondary" href="inscription2.php">Retour</a>
                        <button class="btn-next" type="submit">Suivant</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
