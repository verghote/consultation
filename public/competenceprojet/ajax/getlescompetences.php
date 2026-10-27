<?php
use ClasseMetier\Projet;

use ClasseTechnique\Requete;
use ClasseTechnique\ReponseJson;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// récupérer en vérifiant la présence et le type des paramètres attendus
$idProjet = Requete::postInt('idProjet');


// Si le projet n'existe pas, on envoie une erreur
if (!Projet::getById($idProjet)) {
    ReponseJson::envoyerLesErreurs(['global' => "Ce projet n'existe pas."]);
}

// récupération des compétences du projet et envoi de la réponse au format json
ReponseJson::envoyerLesDonnees(Projet::getLesCompetences($idProjet));
