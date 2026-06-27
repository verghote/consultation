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

// contrôle de la transmission du paramètre idClub
if (!isset($_POST['idClub'])) {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant du club n'est pas transmis"], 400);
}

// contrôle de la transmission du paramètre sexe
if (!isset($_POST['sexe'])) {
    ReponseJson::envoyerLesErreurs(['global' => "Le sexe n'est pas transmis"], 400);
}

// récupération des trois paramètres
$idCategorie = $_POST['idCategorie'];
$idClub = $_POST['idClub'];
$sexe = strtoupper($_POST['sexe']);

// vérification du format du paramètre idCategorie : une lettre majuscule suivie d'une lettre majuscule ou d'un chiffre ou de 10 ou *
if (!preg_match("/^[A-Z](?:[A-Z]|\d|10)$/", $idCategorie) && $idCategorie != '*') {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant de la catégorie n'est pas conforme"], 400);
}

// vérification du format du paramètre idClub : 6 chiffres exactement commençant par 080 ou *
if (!preg_match("/^080\d{3}$/", $idClub) && $idClub != '*') {
    ReponseJson::envoyerLesErreurs(['global' => "L'identifiant du club n'est pas conforme"], 400);
}

// vérification du format du paramètre sexe : M ou F ou *
if (!preg_match("/^[MF*]$/", $sexe)) {
    ReponseJson::envoyerLesErreurs(['global' => "Le sexe n'est pas conforme"], 400);
}

// récupération et envoi des licenciés dans cette catégorie
ReponseJson::envoyerLesDonnees(Coureur::getBySexeClubCategorie($sexe, $idClub, $idCategorie));
