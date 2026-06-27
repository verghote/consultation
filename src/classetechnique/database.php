<?php
declare(strict_types=1);

namespace ClasseTechnique;

use Exception;
use PDO;

/**
 * Classe Database : gestion centralisée de la connexion PDO
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 05/06/2026
 */

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $lesParametres = self::getLesParametres();
            $host = $lesParametres['host'];
            $database = $lesParametres['database'];
            $user = $lesParametres['user'];
            $password = $lesParametres['password'];
            $port = $lesParametres['port'];
            $charset = 'utf8mb4';
            $chaine = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
            $db = new PDO($chaine, $user, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $db->exec("SET sql_mode = 'STRICT_ALL_TABLES,ONLY_FULL_GROUP_BY'");
            self::$instance = $db;
        }
        return self::$instance;
    }

    /**
     * Lecture du fichier de configuration config/database.php
     */
    public static function getLesParametres(): array
    {
        $lesParametres = Config::charger('database');

        foreach (['host', 'database', 'user', 'password'] as $key) {
            if (!isset($lesParametres[$key])) {
                throw new Exception("Clé manquante dans le fichier de configuration /config/database.php : $key");
            }
        }

        $lesParametres['port'] = $lesParametres['port'] ?? 3306;
        return $lesParametres;
    }
}

