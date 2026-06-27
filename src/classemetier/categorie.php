<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\Select;

// définition de la table catégorie : id, nom, ageMin, ageMax

class Categorie
{
    /**
     * retourne les catégories en y ajoutant l'intervalle des dates de naissance pour les catégories
     * @return array
     */
    public static function getAll(): array
    {
        $annee = date('Y');
        $mois = intval(Date('m'));
        if ($mois >= 9) {
            $annee++;
        }
        $sql = <<<SQL
            select categorie.id, categorie.nom, ageMin, ageMax, concat(ageMin, '-', ageMax) as age, concat($annee - ageMax, '-' ,$annee - ageMin) as annee,
                   count(*) as nb
            from categorie
               join coureur on coureur.idCategorie = categorie.id
            group by categorie.id, categorie.nom, categorie.ageMin, categorie.ageMax
            order by ageMin;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Retourner l'ensemble des catégories ayant des coureurs
     * Champs à retourner : id, nom
     * @return array
     */
    public static function getListe() : array {
        $sql = <<<SQL
             select id, nom
             from categorie
             order by ageMin;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

}