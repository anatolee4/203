<?php
$currentDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$currentFolder = basename($currentDirectory);
$sectionFolders = ['accueil', 'inscription', 'salles', 'oeuvres', 'admin'];
$siteRoot = in_array($currentFolder, $sectionFolders, true) ? dirname($currentDirectory) : $currentDirectory;
$siteRoot = rtrim($siteRoot, '/');
?>
<footer class="footer-section">
        
        <div class="footer-main">
            <h2 class="footer-logo">E-llusion</h2>
            
            
            <div class="footer-content">
                <div class="footer-brand-left">
                    <img src="<?= htmlspecialchars($siteRoot . '/img/logo_MMI.png', ENT_QUOTES, 'UTF-8') ?>" alt="MMI Chambéry" class="partner-logo">
                </div>

                <div class="footer-center">
                    <p class="contact-mail">nous contacter : <a href="mailto:aaaa@univ-smb.fr">aaaa@univ-smb.fr</a></p>
                    <a href="https://www.instagram.com/mmichambery/" target="_blank" class="instagram-link">
                        <img src="<?= htmlspecialchars($siteRoot . '/img/logo_insta.png?v=2', ENT_QUOTES, 'UTF-8') ?>" alt="" class="insta-icon"> Instagram
                    </a>
                </div>

                <div class="footer-brand-right">
                    <img src="<?= htmlspecialchars($siteRoot . '/img/logo_IUT.png', ENT_QUOTES, 'UTF-8') ?>" alt="IUT Chambéry" class="partner-logo">
                </div>
            </div>
        </div>
        
        <div class="footer-sub">
            <a href="https://mmi.univ-smb.fr" target="_blank">https://mmi.univ-smb.fr/</a>
        </div>
    </footer>
