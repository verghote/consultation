<?php
declare(strict_types=1);

use ClasseMetier\Competence;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
// Requete::exigerPost();

// récupérer la valeur des paramètres transmis en vérifiant leur présence et leur type
$idBloc = Requete::postInt('idBloc');

// envoi de la réponse
ReponseJson::envoyerLesDonnees(Competence::getLesDomaines($idBloc));
