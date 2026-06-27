<?php
use ClasseMetier\Club;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Liste des clubs";

// récupération des clubs
$lesClubs = ReponseJson::encoderPourJavascript(Club::getAll());

$head = <<<HTML
    <script>
        const lesClubs = $lesClubs ;
    </script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
