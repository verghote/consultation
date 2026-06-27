<?php
use ClasseMetier\Coureur;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Les coureurs";

// Récupération des coureurs : licence, nom prenom, sexe, dateNaissanceFr au format fr, idCategorie, nomClub
$lesCoureurs = ReponseJson::encoderPourJavascript(Coureur::getAll());

$head = <<<HTML
    <script>
        const lesCoureurs= $lesCoureurs;
    </script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
