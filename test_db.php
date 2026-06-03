<?php
$host = '192.168.135.113';
$dbname = 'palisslu';
$user = 'palisslu';
$password = 'Petit-lulu.091';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    echo "✅ Connexion réussie !";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>