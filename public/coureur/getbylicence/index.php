<?php
// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Rechercher sur le numéro de licence d'un coureur";
$dureeToken = 10;

// chargement de l'interface
require RACINE . "/view/interface.php";
