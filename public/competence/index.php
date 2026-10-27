<?php
declare(strict_types=1);

use ClasseMetier\Competence;
use ClasseTechnique\Page;

/** @noinspection PhpIncludeInspection */
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/bootstrap.php";

// alimentation et affichage de l'interface
$page = new Page();
$page->setTitre("Compétences par bloc et domaine")
    ->setDonnee('lesBlocs', Competence::getLesBlocs())
    ->avecJeton()
    ->afficher();
