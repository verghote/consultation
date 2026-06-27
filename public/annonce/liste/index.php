<?php
use ClasseMetier\Annonce;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Liste des annonces";

// Récupération des catégories avec l'intervalle des années
$lesAnnonces = ReponseJson::encoderPourJavascript(Annonce::getLesAnnoncesActives());

$head = <<<HTML
    <script>
        const lesAnnonces = $lesAnnonces;
    </script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
