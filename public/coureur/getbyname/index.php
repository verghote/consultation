<?php
declare(strict_types=1);

use ClasseMetier\Coureur;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();

$page->setTitre("Recherche sur le nom et prénom")
    ->avecJeton()
    ->setDonnee('lesCoureurs', Coureur::getListe())
    ->afficher();
