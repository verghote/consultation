<?php

use ClasseMetier\Coureur;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\UserException;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// contrôle de la transmission du paramètre idCategorie
if (!isset($_POST['idCategorie'])) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant de la catégorie n'est pas transmis"], 400);
}

// récupération du paramètre
$idCategorie = $_POST['idCategorie'];

// vérification du format du paramètre
if (!preg_match("/^[A-Z](?:[A-Z]|\d|10)$/", $idCategorie)) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant de la catégorie n'est pas conforme"], 400);
}

// récupération et envoi des licenciés dans cette catégorie
ReponseJson::envoyerLesDonnees(Coureur::getByCategorie($idCategorie));





