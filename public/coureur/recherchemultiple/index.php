<?php

use ClasseMetier\Categorie;
use ClasseMetier\Club;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Recherche sur plusieurs critères";

// alimentation des listes déroulantes
$lesCategories = ReponseJson::encoderPourJavascript(Categorie::getListe());
$lesClubs = ReponseJson::encoderPourJavascript(Club::getListe());

$head = <<<HTML
<script>
       const lesCategories = $lesCategories;
       const lesClubs = $lesClubs;
</script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";