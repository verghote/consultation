<?php

use ClasseMetier\Competence;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// alimentation de l'interface
$titre = "Recherche sur des critères imbriqués";


$lesBlocs = ReponseJson::encoderPourJavascript(Competence::getLesBlocs());

$head = <<<HTML
<script>
    const lesBlocs = $lesBlocs;
</script>
HTML;

// chargement de l'interface
require RACINE . "/view/interface.php";
