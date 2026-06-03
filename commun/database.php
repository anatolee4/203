<?php
// Remplis ces valeurs avec les informations de ta base MySQL.
// Tant que DB_NAME ou DB_USER est vide, le site garde le stockage local en secours.
const DB_HOST = 'localhost';
const DB_NAME = '';
const DB_USER = '';
const DB_PASSWORD = '';
const DB_CHARSET = 'utf8mb4';

function db_est_configuree(): bool
{
    return DB_NAME !== '' && DB_USER !== '';
}

function db_connexion(): ?PDO
{
    static $pdo = null;

    if (!db_est_configuree()) {
        return null;
    }

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Throwable $exception) {
        return null;
    }

    return $pdo;
}

function db_identifiant(string $nom): string
{
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nom)) {
        throw new RuntimeException('Identifiant SQL invalide.');
    }

    return '`' . $nom . '`';
}
