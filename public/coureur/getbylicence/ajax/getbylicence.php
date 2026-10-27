<?php
declare(strict_types=1);

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;
use ClasseMetier\Coureur;

// Chargement automatique des classes
require $_SERVER['DOCUMENT_ROOT'] . '/../bootstrap/bootstrap.php';

// Vérification d'un appel AJAX par la méthode POST sécurisé par un jeton
Requete::exigerPost();

// récupération du paramètre transmis
$licence = Requete::postString('licence');

if (!preg_match('/^\d{6,7}$/', $licence)) {
    ReponseJson::envoyerLesErreurs(['licence' => 'Numéro de licence invalide.']);
}

$coureur = Coureur::getByLicence($licence);

if (!$coureur) {
    ReponseJson::envoyerLesErreurs(['licence' => 'Numéro de licence inexistant.']);
}

// envoi de la réponse
ReponseJson::envoyerLesDonnees($coureur);
