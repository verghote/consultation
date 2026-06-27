<?php
use ClasseMetier\Coureur;
use ClasseTechnique\ReponseJson;
use ClasseTechnique\UserException;
use ClasseTechnique\Jeton;

// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// vérifier la méthode utilisée et le token CSRF
Jeton::verifierRequete();


// vérification de la transmission du paramètre attendu : licence
if (!isset($_POST['licence'])) {
    throw new Exception("Numéro de licence non transmis");
}

// Récupération du coureur
$licence = $_POST['licence'];

// vérification du format de la licence
if (!preg_match('/^[0-9]{6,7}$/', $licence)) {
    throw new UserException("Numéro de licence invalide");
    // ReponseJson::envoyerLesErreurs(['licence' => "Numéro de licence invalide"], 200);
}

// Récupération du coureur
$ligne = Coureur::getByLicence($licence);
// Si le coureur n'existe pas, on renvoie une erreur métier attendue en 200
// pour éviter un bruit inutile dans la console navigateur.
if ($ligne) {
    ReponseJson::envoyerLesDonnees($ligne);
} else {
    ReponseJson::envoyerLesErreurs(['licence' => "Numéro de licence inexistant"], 200);
}
