<?php

declare(strict_types=1);

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

use ClasseTechnique\ReponseJson;
use ClasseTechnique\Requete;
use ClasseMetier\Coureur;


// ============================================================================
// Vérification de la requête
// ============================================================================

Requete::exigerPost();


// ============================================================================
// Récupération des paramètres
// ============================================================================

$idCategorie = Requete::postString('idCategorie');
$idClub = Requete::postString('idClub');
$sexe = strtoupper(Requete::postString('sexe'));


// ============================================================================
// Tableau des erreurs
// ============================================================================

$lesErreurs = [];


// ============================================================================
// Validation du sexe
// ============================================================================

if (!preg_match('/^[MF*]$/', $sexe)) {
    $lesErreurs['sexe'] = "Le sexe n'est pas conforme.";
}


// ============================================================================
// Validation du club
// ============================================================================

if ($idClub !== '*' && !preg_match('/^080\d{3}$/', $idClub)) {
    $lesErreurs['idClub'] = "L'identifiant du club n'est pas conforme.";
}


// ============================================================================
// Validation de la catégorie
// ============================================================================

if ($idCategorie !== '*' && !preg_match('/^[A-Z](?:[A-Z]|\d|10)$/', $idCategorie)) {
    $lesErreurs['idCategorie'] = "L'identifiant de la catégorie n'est pas conforme.";
}


// ============================================================================
// Vérification d'au moins un critère
// ============================================================================

if ($sexe === '*' && $idClub === '*' && $idCategorie === '*') {
    $lesErreurs['recherche'] = "Au moins un critère de recherche doit être sélectionné.";
}


// ============================================================================
// Transmission des erreurs
// ============================================================================

if (!empty($lesErreurs)) {
    ReponseJson::envoyerLesErreurs($lesErreurs);
}


// ============================================================================
// Recherche
// ============================================================================

ReponseJson::envoyerLesDonnees(
    Coureur::getBySexeClubCategorie($sexe, $idClub, $idCategorie)
);
