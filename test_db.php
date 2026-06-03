<?php
require_once __DIR__ . '/commun/database.php';
$pdo = db_connexion();
if ($pdo) {
    echo "✅ db_connexion() fonctionne";
} else {
    echo "❌ db_connexion() retourne null";
}
?>