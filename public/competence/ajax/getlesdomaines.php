<?php
use ClasseMetier\Competence;
use ClasseTechnique\ReponseJson;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// vérification de la transmission du paramètre idBloc
if (!isset($_POST['idBloc'])) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant du bloc n'a pas été transmis."], 400);
}

// récupération du bloc
$idBloc = $_POST['idBloc'];

// S'il n'est pas numérique, on envoie une erreur
if (!preg_match("/^[0-9]+$/", $idBloc)) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant du bloc n'est pas valide."], 400);
}

// envoi de la réponse
ReponseJson::envoyerLesDonnees(Competence::getLesDomaines($idBloc));
