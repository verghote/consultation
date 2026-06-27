<?php
use ClasseMetier\Categorie;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Liste des catégories";

// Récupération des catégories avec l'intervalle des années
$lesCategories = ReponseJson::encoderPourJavascript(Categorie::getAll());

$head = <<<HTML
    <script src="/composant/html2pdf/html2pdf.bundle.min.js"></script>
    <script>
        const lesCategories = $lesCategories;
    </script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
