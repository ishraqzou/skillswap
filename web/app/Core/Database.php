<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Database - Connexion PDO unique (singleton) à la base MySQL SkillSwap.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                if (APP_ENV === 'development') {
                    die('Erreur de connexion à la base de données : ' . $e->getMessage());
                }
                die('Le service est temporairement indisponible. Veuillez réessayer plus tard.');
            }
        }
        return self::$instance;
    }

    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception('Impossible de désérialiser un singleton Database.');
    }
}
