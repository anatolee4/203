<?php
$inscriptions = json_decode($_COOKIE['inscriptions'] ?? '[]', true);
if (!is_array($inscriptions)) {
    $inscriptions = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_index'])) {
    $index = (int) $_POST['supprimer_index'];

    if (array_key_exists($index, $inscriptions)) {
        unset($inscriptions[$index]);
        $inscriptions = array_values($inscriptions);
        setcookie('inscriptions', json_encode($inscriptions), time() + 60 * 60 * 24 * 30, '/');
    }

    header('Location: inscription.php?suppression=1');
    exit;
}

function afficher_valeur(?string $valeur, string $defaut = 'Non renseigné'): string
{
    $valeur = trim((string) $valeur);
    return htmlspecialchars($valeur !== '' ? $valeur : $defaut, ENT_QUOTES, 'UTF-8');
}

function libelle_creneau(?string $creneau): string
{
    $libelles = [
        'jeudi-15h' => 'Jeudi 18 juin - 15h',
        'jeudi-15h30' => 'Jeudi 18 juin - 15h30',
        'jeudi-16h' => 'Jeudi 18 juin - 16h',
        'jeudi-16h30' => 'Jeudi 18 juin - 16h30',
        'jeudi-17h' => 'Jeudi 18 juin - 17h',
        'jeudi-17h30' => 'Jeudi 18 juin - 17h30',
        'jeudi-18h' => 'Jeudi 18 juin - 18h',
        'jeudi-18h30' => 'Jeudi 18 juin - 18h30',
        'jeudi-19h' => 'Jeudi 18 juin - 19h',
        'jeudi-19h30' => 'Jeudi 18 juin - 19h30',
        'jeudi-20h' => 'Jeudi 18 juin - 20h',
        'vendredi-9h30' => 'Vendredi 19 juin - 9h30',
        'vendredi-10h' => 'Vendredi 19 juin - 10h',
        'vendredi-10h30' => 'Vendredi 19 juin - 10h30',
        'vendredi-11h' => 'Vendredi 19 juin - 11h',
    ];

    return $libelles[$creneau ?? ''] ?? afficher_valeur($creneau);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-llusion - Mes inscriptions</title>
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
        <section class="inscription-card inscription-card--summary" aria-labelledby="inscription-title">
            <div class="inscription-visual" aria-hidden="true"></div>

            <div class="inscription-content">
                <h1 class="inscription-title" id="inscription-title">Mes inscriptions</h1>

                <?php if (isset($_GET['confirmation'])) : ?>
                    <p class="success-message">Votre inscription a bien été enregistrée.</p>
                <?php endif; ?>

                <?php if (isset($_GET['suppression'])) : ?>
                    <p class="success-message">L'inscription a bien été supprimée.</p>
                <?php endif; ?>

                <?php if (empty($inscriptions)) : ?>
                    <div class="empty-summary">
                        <p>Aucune inscription enregistrée pour le moment.</p>
                        <a class="btn-next" href="inscription1.php">Nouvelle inscription</a>
                    </div>
                <?php else : ?>
                    <div class="summary-list">
                        <?php foreach (array_reverse($inscriptions, true) as $index => $inscription) : ?>
                            <article class="summary-card">
                                <div>
                                    <h2><?= afficher_valeur(($inscription['prenom'] ?? '') . ' ' . ($inscription['nom'] ?? ''), 'Visiteur') ?></h2>
                                    <p><?= afficher_valeur($inscription['email'] ?? '') ?></p>
                                </div>

                                <dl class="summary-details">
                                    <div><dt>Profil</dt><dd><?= afficher_valeur($inscription['profil'] ?? '') ?></dd></div>
                                    <div><dt>Personnes</dt><dd><?= afficher_valeur($inscription['personnes'] ?? '1') ?></dd></div>
                                    <div><dt>Salle</dt><dd>Salle <?= afficher_valeur($inscription['salle'] ?? '') ?></dd></div>
                                    <div><dt>Créneau</dt><dd><?= libelle_creneau($inscription['creneau'] ?? '') ?></dd></div>
                                    <div><dt>Buffet</dt><dd><?= ($inscription['buffet'] ?? 'non') === 'oui' ? 'Oui' : 'Non' ?></dd></div>
                                </dl>

                                <form class="delete-form" action="inscription.php" method="post">
                                    <input type="hidden" name="supprimer_index" value="<?= (int) $index ?>">
                                    <button class="btn-delete" type="submit">Supprimer</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <div class="step-actions">
                        <a class="btn-next" href="inscription1.php">Nouvelle inscription</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
