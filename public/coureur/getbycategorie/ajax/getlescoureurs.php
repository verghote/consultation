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
$idCategorie = Requete::postString('idCategorie');

if (!preg_match('/^[A-Z](?:[A-Z]|\d|10)$/', $idCategorie)) {
    ReponseJson::envoyerLesErreurs(['idCategorie' => "L'identifiant de la catégorie n'est pas conforme."]);
}

// envoi de la réponse
ReponseJson::envoyerLesDonnees(Coureur::getByCategorie($idCategorie));
