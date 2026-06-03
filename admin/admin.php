<?php
$salles = ['Salle 001', 'Salle 002', 'Salle 005', 'Salle 021'];
$capaciteMax = 12;

$creneaux = [
    'jeudi' => [
        'label' => 'Jeudi 18',
        'horaires' => ['15h', '15h30', '16h', '16h30', '17h', '17h30', '18h', '18h30', '19h', '19h30', '20h']
    ],
    'vendredi' => [
        'label' => 'Vendredi 19',
        'horaires' => ['9h30', '10h', '10h30', '11h']
    ],
];

// Données fictives — à remplacer par vos requêtes BDD
// Structure: $inscritsData['jeudi']['15h']['Salle 001'] = 4;
$inscritsData = [];
foreach ($creneaux as $jour => $data) {
    foreach ($data['horaires'] as $h) {
        foreach ($salles as $s) {
            $inscritsData[$jour][$h][$s] = 0;
        }
    }
}
// Exemple avec quelques valeurs
$inscritsData['jeudi']['15h']['Salle 001'] = 8;
$inscritsData['jeudi']['16h']['Salle 002'] = 5;
$inscritsData['jeudi']['17h30']['Salle 005'] = 12;
$inscritsData['vendredi']['10h']['Salle 021'] = 3;

// Liste des inscriptions — à remplacer par requête BDD
// Structure: chaque inscription a: nom, prenom, email, profil, personnes, salle, creneau, buffet
$inscriptions = [
    ['nom' => 'Martin',   'prenom' => 'Anatole',  'email' => 'anatolemartin123@gmail.com', 'profil' => 'Enseignant',  'personnes' => 4, 'salle' => 'Salle 001', 'creneau' => 'Vendredi 19 juin - 11h',  'buffet' => true],
    ['nom' => 'Dupont',   'prenom' => 'Claire',   'email' => 'claire.dupont@example.com',  'profil' => 'Étudiant',    'personnes' => 2, 'salle' => 'Salle 002', 'creneau' => 'Jeudi 18 juin - 15h',   'buffet' => false],
    ['nom' => 'Bernard',  'prenom' => 'Lucas',    'email' => 'lucas.bernard@example.com',  'profil' => 'Enseignant',  'personnes' => 1, 'salle' => 'Salle 005', 'creneau' => 'Jeudi 18 juin - 17h30', 'buffet' => true],
    ['nom' => 'Leroy',    'prenom' => 'Sophie',   'email' => 'sophie.leroy@example.com',   'profil' => 'Personnel',   'personnes' => 3, 'salle' => 'Salle 021', 'creneau' => 'Vendredi 19 juin - 10h', 'buffet' => false],
    ['nom' => 'Moreau',   'prenom' => 'Thomas',   'email' => 'thomas.moreau@example.com',  'profil' => 'Étudiant',    'personnes' => 2, 'salle' => 'Salle 001', 'creneau' => 'Jeudi 18 juin - 16h',   'buffet' => true],
    ['nom' => 'Petit',    'prenom' => 'Emma',     'email' => 'emma.petit@example.com',     'profil' => 'Enseignant',  'personnes' => 5, 'salle' => 'Salle 002', 'creneau' => 'Jeudi 18 juin - 18h',   'buffet' => false],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tableau de bord</title>
    <link rel="stylesheet" href="../commun/reset.css">
    <link rel="stylesheet" href="../commun/header.css">
    <link rel="stylesheet" href="../commun/footer.css">
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
            <p class="admin-intro">Suivi des inscriptions par salle et par créneau.</p>
        </section>

        <section class="schedule-section" aria-label="Planning des salles">

            <!-- Onglets -->
            <div class="tabs" role="tablist">
                <?php foreach ($creneaux as $jourKey => $jourData): ?>
                    <button
                        class="tab-btn <?= $jourKey === 'jeudi' ? 'tab-btn--active' : '' ?>"
                        role="tab"
                        aria-selected="<?= $jourKey === 'jeudi' ? 'true' : 'false' ?>"
                        aria-controls="panel-<?= $jourKey ?>"
                        data-tab="<?= $jourKey ?>"
                    >
                        <?= htmlspecialchars($jourData['label'], ENT_QUOTES, 'UTF-8') ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Panels -->
            <?php foreach ($creneaux as $jourKey => $jourData): ?>
                <div
                    class="tab-panel <?= $jourKey === 'jeudi' ? 'tab-panel--active' : '' ?>"
                    id="panel-<?= $jourKey ?>"
                    role="tabpanel"
                >
                    <div class="schedule-table-wrapper">
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th class="th-empty"></th>
                                    <?php foreach ($salles as $salle): ?>
                                        <th><?= htmlspecialchars($salle, ENT_QUOTES, 'UTF-8') ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jourData['horaires'] as $horaire): ?>
                                    <tr>
                                        <td class="td-horaire"><?= htmlspecialchars($horaire, ENT_QUOTES, 'UTF-8') ?></td>
                                        <?php foreach ($salles as $salle): ?>
                                            <?php
                                                $inscrits = $inscritsData[$jourKey][$horaire][$salle] ?? 0;
                                                $inscrits = max(0, min($capaciteMax, (int)$inscrits));
                                                $plein = $inscrits >= $capaciteMax;
                                                $presque = $inscrits >= ceil($capaciteMax * 0.75);
                                            ?>
                                            <td class="td-cell <?= $plein ? 'td-cell--full' : ($presque ? 'td-cell--almost' : '') ?>">
                                                <span class="cell-count"><?= $inscrits ?></span><span class="cell-sep">/</span><span class="cell-max"><?= $capaciteMax ?></span>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

        </section>

        <!-- ── Gestion des inscriptions ── -->
        <section class="inscriptions-section" aria-label="Gestion des inscriptions">
            <div class="inscriptions-hero">
                <p class="admin-eyebrow">Inscriptions</p>
                <h2 class="inscriptions-title">Gestion des inscriptions</h2>
            </div>

            <div class="inscriptions-box">
                <div class="inscriptions-search-row">
                    <input
                        type="search"
                        id="inscriptions-search"
                        class="inscriptions-search"
                        placeholder="Recherche (nom, prénom, email…)"
                        aria-label="Rechercher une inscription"
                    >
                </div>
                <hr class="inscriptions-divider">

                <ul class="inscriptions-list" id="inscriptions-list">
                    <?php foreach ($inscriptions as $i => $insc): ?>
                        <?php
                            $id = 'insc-' . $i;
                            $nomComplet = htmlspecialchars($insc['prenom'] . ' ' . $insc['nom'], ENT_QUOTES, 'UTF-8');
                            $email      = htmlspecialchars($insc['email'],    ENT_QUOTES, 'UTF-8');
                            $profil     = htmlspecialchars($insc['profil'],   ENT_QUOTES, 'UTF-8');
                            $salle      = htmlspecialchars($insc['salle'],    ENT_QUOTES, 'UTF-8');
                            $creneau    = htmlspecialchars($insc['creneau'],  ENT_QUOTES, 'UTF-8');
                            $personnes  = (int)$insc['personnes'];
                            $buffet     = $insc['buffet'] ? 'Oui' : 'Non';
                        ?>
                        <li
                            class="insc-item"
                            data-search="<?= strtolower(htmlspecialchars($insc['prenom'] . ' ' . $insc['nom'] . ' ' . $insc['email'], ENT_QUOTES, 'UTF-8')) ?>"
                        >
                            <button
                                class="insc-row"
                                aria-expanded="false"
                                aria-controls="<?= $id ?>-detail"
                                onclick="toggleDetail(this)"
                            >
                                <span class="insc-name"><?= $nomComplet ?></span>
                                <span class="insc-email"><?= $email ?></span>
                                <span class="insc-actions">
                                    <span class="insc-chevron" aria-hidden="true">&#8250;</span>
                                    <span class="insc-icon-edit" title="Modifier" aria-label="Modifier">&#9998;</span>
                                    <span class="insc-icon-del"  title="Supprimer" aria-label="Supprimer">&#128465;</span>
                                </span>
                            </button>

                            <div class="insc-detail" id="<?= $id ?>-detail" hidden>
                                <div class="insc-detail-inner">
                                    <div class="insc-detail-header">
                                        <div>
                                            <p class="insc-detail-fullname"><?= $nomComplet ?></p>
                                            <p class="insc-detail-email"><?= $email ?></p>
                                        </div>
                                        <button class="insc-btn-delete" type="button">Supprimer</button>
                                    </div>
                                    <div class="insc-detail-grid">
                                        <div class="insc-detail-field">
                                            <span class="insc-field-label">Profil</span>
                                            <span class="insc-field-value"><?= $profil ?></span>
                                        </div>
                                        <div class="insc-detail-field">
                                            <span class="insc-field-label">Personnes</span>
                                            <span class="insc-field-value"><?= $personnes ?></span>
                                        </div>
                                        <div class="insc-detail-field">
                                            <span class="insc-field-label">Salle</span>
                                            <span class="insc-field-value"><?= $salle ?></span>
                                        </div>
                                        <div class="insc-detail-field">
                                            <span class="insc-field-label">Créneau</span>
                                            <span class="insc-field-value"><?= $creneau ?></span>
                                        </div>
                                        <div class="insc-detail-field">
                                            <span class="insc-field-label">Buffet</span>
                                            <span class="insc-field-value"><?= $buffet ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="inscriptions-empty" id="inscriptions-empty" hidden>Aucun résultat trouvé.</p>
            </div>
        </section>

    </main>

    <?php include "../commun/footer.php"; ?>

    <script>
        /* ── Onglets planning ── */
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.tab;
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('tab-btn--active');
                    b.setAttribute('aria-selected', 'false');
                });
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('tab-panel--active'));
                btn.classList.add('tab-btn--active');
                btn.setAttribute('aria-selected', 'true');
                document.getElementById('panel-' + target).classList.add('tab-panel--active');
            });
        });

        /* ── Accordéon inscriptions ── */
        function toggleDetail(btn) {
            const item    = btn.closest('.insc-item');
            const detail  = item.querySelector('.insc-detail');
            const isOpen  = btn.getAttribute('aria-expanded') === 'true';

            // Fermer tous les autres
            document.querySelectorAll('.insc-row[aria-expanded="true"]').forEach(b => {
                if (b !== btn) {
                    b.setAttribute('aria-expanded', 'false');
                    b.closest('.insc-item').querySelector('.insc-detail').hidden = true;
                    b.closest('.insc-item').classList.remove('insc-item--open');
                }
            });

            btn.setAttribute('aria-expanded', String(!isOpen));
            detail.hidden = isOpen;
            item.classList.toggle('insc-item--open', !isOpen);
        }

        /* ── Recherche ── */
        document.getElementById('inscriptions-search').addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            document.querySelectorAll('.insc-item').forEach(item => {
                const match = !q || item.dataset.search.includes(q);
                item.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            document.getElementById('inscriptions-empty').hidden = visible > 0;
        });
    </script>
</body>
</html>
