<?php
$currentDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$currentFolder = basename($currentDirectory);
$sectionFolders = ['accueil', 'inscription', 'salles', 'oeuvres', 'admin'];
$siteRoot = in_array($currentFolder, $sectionFolders, true) ? dirname($currentDirectory) : $currentDirectory;
$siteRoot = rtrim($siteRoot, '/');
?>
<nav class="site-header" aria-label="Navigation principale">
    <ul class="site-header__nav">
        <li><a href="<?= htmlspecialchars($siteRoot . '/inscription/inscription.php', ENT_QUOTES, 'UTF-8') ?>" class="site-header__link">Inscription</a></li>
        <li><a href="<?= htmlspecialchars($siteRoot . '/salles/salles.php', ENT_QUOTES, 'UTF-8') ?>" class="site-header__link">Les salles</a></li>
        <li><a href="<?= htmlspecialchars($siteRoot . '/oeuvres/oeuvres.php', ENT_QUOTES, 'UTF-8') ?>" class="site-header__link">Les oeuvres</a></li>
    </ul>

    <a href="<?= htmlspecialchars($siteRoot . '/accueil/menu.php', ENT_QUOTES, 'UTF-8') ?>" class="site-header__logo" aria-label="Accueil E-llusion">
        <span class="site-header__logo-e">E</span>-llusion
    </a>

    <a href="<?= htmlspecialchars($siteRoot . '/connexion_admin.php', ENT_QUOTES, 'UTF-8') ?>" class="site-header__admin">Admin</a>
</nav>
