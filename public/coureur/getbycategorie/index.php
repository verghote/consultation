<?php

use ClasseMetier\Categorie;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Les coureurs d'une catégorie";

// récupération des catégories afin d'alimenter la liste déroulante
$lesCategories = ReponseJson::encoderPourJavascript(Categorie::getListe());

$head = <<<HTML
<script>
       const lesCategories = $lesCategories;
</script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
