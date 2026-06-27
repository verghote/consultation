<?php
use ClasseTechnique\Erreur;

// Initialisation
date_default_timezone_set('Europe/Paris');

// Accès aux variables de session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définition d'une constante indiquant la racine du site
define('WWW', dirname(__DIR__) . DIRECTORY_SEPARATOR . "public");

// Définition d'une constante indiquant la racine du projet (un cran au-dessus de WWW)
define('RACINE', dirname(WWW));

spl_autoload_register(function ($name) {
    $prefixes = [
        'ClasseTechnique\\' => RACINE . '/src/ClasseTechnique/',
        'ClasseMetier\\'    => RACINE . '/src/ClasseMetier/',
    ];

    foreach ($prefixes as $prefix => $dossier) {
        if (str_starts_with($name, $prefix)) {
            $fichier = $dossier . substr($name, strlen($prefix)) . '.php';
            if (is_file($fichier)) {
                require $fichier;
                return;
            }
            throw new Exception("Classe introuvable : $name ($fichier)");
        }
    }
});

// gestion globale des erreurs
Erreur::installerGestionnaire();

