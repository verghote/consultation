<?php
// utilisation d'un tableau associatif pour permettre de découpler le nom reçu du client du vrai nom PHP de la classe
// L'utilisation d'un tableau simple est aussi possible mais moins évolutif

return [
    'categorie' => \ClasseMetier\Categorie::class,
    'coureur' => \ClasseMetier\Coureur::class,
    'club' => \ClasseMetier\Club::class,
    'annonce' => \ClasseMetier\Annonce::class,
    'projet' => \ClasseMetier\Projet::class,
];
