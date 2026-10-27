<?php
declare(strict_types=1);

use ClasseMetier\Categorie;
use ClasseMetier\Club;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Recherche sur plusieurs critères")
    ->avecJeton()
    ->setDonnee('lesCategories', Categorie::getListe())
    ->setDonnee('lesClubs', Club::getListe())
    ->afficher();

