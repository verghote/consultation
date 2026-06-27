<?php
use ClasseMetier\Projet;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Consultation des projets";

// récupération de tous les projets
$lesProjets = ReponseJson::encoderPourJavascript( Projet::getAll());

$head = <<<HTML
    <script>
        const lesProjets = $lesProjets;
    </script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
