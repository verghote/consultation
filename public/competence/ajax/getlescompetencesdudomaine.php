<?php
declare(strict_types=1);

use ClasseMetier\Competence;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// récupérer en vérifiant la présence et le type des paramètres attendus
$idBloc = Requete::postInt('idBloc');
$idDomaine = Requete::postInt('idDomaine');

// envoi de la réponse
ReponseJson::envoyerLesDonnees(Competence::getLesCompetencesduDomaine($idBloc, $idDomaine));

