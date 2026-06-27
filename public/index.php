<?php
// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "La consultation et la recherche des données";

// chargement de l'interface
require RACINE . "/view/interface.php";
