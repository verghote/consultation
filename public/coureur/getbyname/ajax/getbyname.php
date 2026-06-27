<?php

use ClasseMetier\Coureur;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Std;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// vérification de l'emploi de la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ReponseJson::envoyerLesErreurs(['global' => "La méthode POST doit être utilisée pour cette ressource."], 405);
}

// vérification de la transmission des données attendues
if (!empty($_POST['search'])) {
    ReponseJson::envoyerLesErreurs(['global' => "Le paramètre 'search' est absent ou vide."], 400);
}

$search = $_GET['search'];

// Vérification du format de l'identifiant
if (!preg_match('/^[a-zA-Z]+$/', $search)) {
    ReponseJson::envoyerLesErreurs(['global' => "Le format du paramètre 'search' n'est pas valide."], 400);
}


// Récupération des coureurs correspondant à la recherche
ReponseJson::envoyerLesDonnees(Coureur::getByNomPrenom($search));
