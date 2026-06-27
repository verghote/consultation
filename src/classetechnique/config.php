<?php
declare(strict_types=1);

namespace ClasseTechnique;

use Exception;

/**
 * Classe Config
 *
 * Chargement simple des fichiers de configuration.
 * Chaque fichier doit retourner une valeur via `return`.
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 05/06/2026
 */
final class Config
{
    /**
     * Empêche l'instanciation
     */
    private function __construct()
    {
    }

    /**
     * Charge un fichier de configuration
     *
     * @param string $nom Nom du fichier sans extension
     * @return mixed
     * @throws Exception
     */
    public static function charger(string $nom): mixed
    {
        // Sécurisation du nom de fichier
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $nom)) {
            throw new Exception("Nom de configuration invalide : $nom");
        }

        // Chemin du fichier
        $fichier = RACINE . '/config/' . $nom . '.php';

        // Vérification existence
        if (!file_exists($fichier)) {
            throw new Exception("Fichier de configuration introuvable : $nom");
        }

        // Chargement du fichier
        $config = require $fichier;

        // Vérification minimale
        if ($config === null) {
            throw new Exception("Le fichier de configuration '$nom' ne retourne aucune donnée");
        }

        return $config;
    }
}
