<?php
use ClasseMetier\Projet;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\UserException;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// vérification du paramètre idProjet
if (!isset($_POST['idProjet'])) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant du projet n'est pas transmis."], 400);
}

// récupération du paramètre
$idProjet = $_POST['idProjet'];

// contrôle du format du paramètre idProjet : seulement des chiffres
if (!preg_match('/^\d+$/', $idProjet)) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant du projet n'est pas valide."], 400);
}

// Si le projet n'existe pas, on envoie une erreur
if (!Projet::getById($idProjet)) {
    ReponseJson::envoyerLesErreurs(['global' => "Ce projet n'existe pas."], 200);
}

// récupération des compétences du projet et envoi de la réponse au format json
ReponseJson::envoyerLesDonnees(Projet::getLesCompetences($idProjet));
