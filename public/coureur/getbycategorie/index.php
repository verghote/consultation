<?php
declare(strict_types=1);

use ClasseMetier\Categorie;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Les coureurs d'une catégorie")
    ->avecJeton()
    ->setDonnee('lesCategories', Categorie::getListe())
    ->afficher();

