<?php
// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Recherche sur le nom et prénom";

// chargement tarekraafat-autocomplete.js
//Simple autocomplete pure vanilla Javascript library.
$head = <<<HTML
    <script src="/composant/autocomplete/autocomplete.min.js"></script>
    <link rel="stylesheet" href="/composant/autocomplete/autocomplete.css">
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
