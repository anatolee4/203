<?php
// Remplis ces valeurs avec les informations de connexion de ta base MySQL existante.
// D'apres ta capture phpMyAdmin, la base s'appelle palisslu.
const DB_HOST = '192.168.135.113';
const DB_NAME = 'palisslu';
const DB_USER = 'palisslu';
const DB_PASSWORD = 'Petit-lulu.091';
const DB_CHARSET = 'utf8mb4';

// Noms des tables existantes dans ta base.
const DB_TABLE_INSCRIPTION = 'inscription';
const DB_TABLE_ADMIN = 'config_admin';

// Colonnes attendues dans la table inscription.
// Si tes colonnes n'ont pas exactement ces noms, change seulement ces constantes.
const DB_COL_INSCRIPTION_ID = 'id_inscription';
const DB_COL_INSCRIPTION_NOM = 'nom';
const DB_COL_INSCRIPTION_PRENOM = 'prenom';
const DB_COL_INSCRIPTION_EMAIL = 'email';
const DB_COL_INSCRIPTION_PROFIL = 'profil_visiteur';
const DB_COL_INSCRIPTION_PERSONNES = 'nb_personnes';
const DB_COL_INSCRIPTION_SALLE = '';
const DB_COL_INSCRIPTION_CRENEAU = 'id_creneau';
const DB_COL_INSCRIPTION_BUFFET = 'participe_buffet';
const DB_COL_INSCRIPTION_TOKEN = 'token_gestion';
const DB_COL_INSCRIPTION_DATE = 'date_inscription';

// Colonne contenant le mot de passe dans config_admin.
const DB_COL_ADMIN_PASSWORD = 'valeur';

$db_derniere_erreur = null;

function db_est_configuree(): bool
{
    return DB_NAME !== '' && DB_USER !== '';
}

function db_derniere_erreur(): ?string
{
    global $db_derniere_erreur;
    return $db_derniere_erreur;
}

function db_enregistrer_erreur(string $message): void
{
    global $db_derniere_erreur;
    $db_derniere_erreur = $message;
}

function db_connexion(): ?PDO
{
    static $pdo = null;
    global $db_derniere_erreur;

    if (!db_est_configuree()) {
        $db_derniere_erreur = 'Configuration de base de donnees incomplete.';
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
        $db_derniere_erreur = $exception->getMessage();
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
