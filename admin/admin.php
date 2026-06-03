<?php
session_start();

if (empty($_SESSION['admin_connecte'])) {
    header('Location: ../connexion_admin.php');
    exit;
}

$salles = ['001' => 'Salle 001', '002' => 'Salle 002', '005' => 'Salle 005', '021' => 'Salle 021'];
$capaciteMax = 12;

$creneaux = [
    'jeudi' => [
        'label' => 'Jeudi 18',
        'horaires' => [
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
        ]
    ],
    'vendredi' => [
        'label' => 'Vendredi 19',
        'horaires' => [
            '9h30' => 'vendredi-9h30',
            '10h' => 'vendredi-10h',
            '10h30' => 'vendredi-10h30',
            '11h' => 'vendredi-11h',
        ]
    ],
];

$creneauLibelles = [];
foreach ($creneaux as $jourData) {
    foreach ($jourData['horaires'] as $horaire => $creneauKey) {
        $creneauLibelles[$creneauKey] = $jourData['label'] . ' juin - ' . $horaire;
    }
}

function admin_lire_inscriptions(): array
{
    $inscriptions = json_decode($_COOKIE['inscriptions'] ?? '[]', true);
    return is_array($inscriptions) ? array_values($inscriptions) : [];
}

function admin_sauver_inscriptions(array $inscriptions): void
{
    setcookie('inscriptions', json_encode(array_values($inscriptions)), time() + 60 * 60 * 24 * 30, '/');
}

function admin_champ(string $nom, string $defaut = ''): string
{
    return trim((string) ($_POST[$nom] ?? $defaut));
}

function admin_code_salle(?string $salle): string
{
    if (preg_match('/(001|002|005|021)/', (string) $salle, $matches)) {
        return $matches[1];
    }

    return '';
}

function admin_libelle_salle(?string $salle, array $salles): string
{
    $code = admin_code_salle($salle);
    return $salles[$code] ?? 'Non renseigné';
}

function admin_libelle_creneau(?string $creneau, array $creneauLibelles): string
{
    $creneau = trim((string) $creneau);
    return $creneauLibelles[$creneau] ?? ($creneau !== '' ? $creneau : 'Non renseigné');
}

$inscriptions = admin_lire_inscriptions();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = admin_champ('action');
    $index = (int) admin_champ('index', '-1');

    if (array_key_exists($index, $inscriptions)) {
        if ($action === 'delete') {
            unset($inscriptions[$index]);
            admin_sauver_inscriptions($inscriptions);
            header('Location: admin.php?suppression=1');
            exit;
        }

        if ($action === 'update') {
            $inscriptions[$index] = [
                'nom' => admin_champ('nom'),
                'prenom' => admin_champ('prenom'),
                'email' => admin_champ('email'),
                'profil' => admin_champ('profil'),
                'personnes' => max(1, min($capaciteMax, (int) admin_champ('personnes', '1'))),
                'salle' => admin_code_salle(admin_champ('salle')),
                'creneau' => admin_champ('creneau'),
                'buffet' => isset($_POST['buffet']) ? 'oui' : 'non',
                'date_creation' => $inscriptions[$index]['date_creation'] ?? date('Y-m-d H:i:s'),
            ];

            admin_sauver_inscriptions($inscriptions);
            header('Location: admin.php?modification=1');
            exit;
        }
    }
}

$editIndex = isset($_GET['edit']) ? (int) $_GET['edit'] : null;
if ($editIndex !== null && !array_key_exists($editIndex, $inscriptions)) {
    $editIndex = null;
}

$inscritsData = [];
foreach ($creneaux as $jour => $data) {
    foreach ($data['horaires'] as $h => $creneauKey) {
        foreach ($salles as $code => $s) {
            $inscritsData[$jour][$h][$s] = 0;
        }
    }
}

foreach ($inscriptions as $inscription) {
    $salle = admin_libelle_salle($inscription['salle'] ?? '', $salles);
    $personnes = max(1, (int) ($inscription['personnes'] ?? 1));
    foreach ($creneaux as $jour => $data) {
        $horaire = array_search($inscription['creneau'] ?? '', $data['horaires'], true);
        if ($horaire !== false && isset($inscritsData[$jour][$horaire][$salle])) {
            $inscritsData[$jour][$horaire][$salle] += $personnes;
        }
    }
}
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
            <a class="admin-logout" href="../connexion_admin.php?deconnexion=1">Se déconnecter</a>
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
                                <?php foreach ($jourData['horaires'] as $horaire => $creneauKey): ?>
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
                <?php if (isset($_GET['suppression'])): ?>
                    <p class="admin-message">L'inscription a bien été supprimée.</p>
                <?php endif; ?>
                <?php if (isset($_GET['modification'])): ?>
                    <p class="admin-message">L'inscription a bien été modifiée.</p>
                <?php endif; ?>

                <hr class="inscriptions-divider">

                <ul class="inscriptions-list" id="inscriptions-list">
                    <?php foreach ($inscriptions as $i => $insc): ?>
                        <?php
                            $id = 'insc-' . $i;
                            $nomCompletTexte = trim(($insc['prenom'] ?? '') . ' ' . ($insc['nom'] ?? '')) ?: 'Visiteur';
                            $nomComplet = htmlspecialchars($nomCompletTexte, ENT_QUOTES, 'UTF-8');
                            $email      = htmlspecialchars($insc['email'] ?? '', ENT_QUOTES, 'UTF-8');
                            $profil     = htmlspecialchars($insc['profil'] ?? 'Non renseigné', ENT_QUOTES, 'UTF-8');
                            $salleCode  = admin_code_salle($insc['salle'] ?? '');
                            $salle      = htmlspecialchars(admin_libelle_salle($insc['salle'] ?? '', $salles), ENT_QUOTES, 'UTF-8');
                            $creneauKey = $insc['creneau'] ?? '';
                            $creneau    = htmlspecialchars(admin_libelle_creneau($creneauKey, $creneauLibelles), ENT_QUOTES, 'UTF-8');
                            $personnes  = max(1, (int)($insc['personnes'] ?? 1));
                            $buffetOui  = ($insc['buffet'] ?? 'non') === 'oui' || ($insc['buffet'] ?? false) === true;
                            $buffet     = $buffetOui ? 'Oui' : 'Non';
                            $isEditing  = $editIndex === $i;
                        ?>
                        <li
                            class="insc-item <?= $isEditing ? 'insc-item--open' : '' ?>"
                            data-search="<?= strtolower(htmlspecialchars($nomCompletTexte . ' ' . ($insc['email'] ?? ''), ENT_QUOTES, 'UTF-8')) ?>"
                        >
                            <div class="insc-row">
                                <button
                                    class="insc-toggle"
                                    type="button"
                                    aria-expanded="<?= $isEditing ? 'true' : 'false' ?>"
                                    aria-controls="<?= $id ?>-detail"
                                    onclick="toggleDetail(this)"
                                >
                                    <span class="insc-name"><?= $nomComplet ?></span>
                                    <span class="insc-email"><?= $email ?></span>
                                    <span class="insc-chevron" aria-hidden="true">&#8250;</span>
                                </button>
                                <span class="insc-actions">
                                    <a class="insc-icon-edit" href="admin.php?edit=<?= (int) $i ?>#<?= $id ?>-detail" title="Modifier" aria-label="Modifier">&#9998;</a>
                                    <form class="insc-action-form" action="admin.php" method="post" onsubmit="return confirm('Supprimer cette inscription ?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="index" value="<?= (int) $i ?>">
                                        <button class="insc-icon-del" type="submit" title="Supprimer" aria-label="Supprimer">&#128465;</button>
                                    </form>
                                </span>
                            </div>

                            <div class="insc-detail" id="<?= $id ?>-detail" <?= $isEditing ? '' : 'hidden' ?>>
                                <div class="insc-detail-inner">
                                    <?php if ($isEditing): ?>
                                        <form class="insc-edit-form" action="admin.php" method="post">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="index" value="<?= (int) $i ?>">

                                            <div class="insc-edit-grid">
                                                <label class="insc-edit-field">
                                                    <span>Prénom</span>
                                                    <input type="text" name="prenom" value="<?= htmlspecialchars($insc['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                </label>
                                                <label class="insc-edit-field">
                                                    <span>Nom</span>
                                                    <input type="text" name="nom" value="<?= htmlspecialchars($insc['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                </label>
                                                <label class="insc-edit-field">
                                                    <span>Email</span>
                                                    <input type="email" name="email" value="<?= htmlspecialchars($insc['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                </label>
                                                <label class="insc-edit-field">
                                                    <span>Profil</span>
                                                    <input type="text" name="profil" value="<?= htmlspecialchars($insc['profil'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                                </label>
                                                <label class="insc-edit-field">
                                                    <span>Personnes</span>
                                                    <input type="number" name="personnes" min="1" max="<?= $capaciteMax ?>" value="<?= $personnes ?>" required>
                                                </label>
                                                <label class="insc-edit-field">
                                                    <span>Salle</span>
                                                    <select name="salle" required>
                                                        <?php foreach ($salles as $code => $libelle): ?>
                                                            <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" <?= $salleCode === $code ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label class="insc-edit-field">
                                                    <span>Créneau</span>
                                                    <select name="creneau" required>
                                                        <?php foreach ($creneauLibelles as $value => $label): ?>
                                                            <option value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" <?= $creneauKey === $value ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </label>
                                                <label class="insc-edit-check">
                                                    <input type="checkbox" name="buffet" value="oui" <?= $buffetOui ? 'checked' : '' ?>>
                                                    <span>Buffet</span>
                                                </label>
                                            </div>

                                            <div class="insc-edit-actions">
                                                <a class="insc-btn-cancel" href="admin.php#<?= $id ?>-detail">Annuler</a>
                                                <button class="insc-btn-save" type="submit">Enregistrer</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="insc-detail-header">
                                            <div>
                                                <p class="insc-detail-fullname"><?= $nomComplet ?></p>
                                                <p class="insc-detail-email"><?= $email ?></p>
                                            </div>
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
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <p class="inscriptions-empty" id="inscriptions-empty" <?= empty($inscriptions) ? '' : 'hidden' ?>>Aucune inscription trouvée.</p>
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
            document.querySelectorAll('.insc-toggle[aria-expanded="true"]').forEach(b => {
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
