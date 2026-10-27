<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\Select;

// Classe gérant les données de la table annonce : id, nom, description, date, actif

class Annonce
{
    // Répertoire où sont stockées les affiches

    /**
     * retourne les annonces actives et dont la date n'est pas dépassée
     * @return array
     */
    public static function getLesAnnoncesActives(): array
    {
        $sql = <<<SQL
          Select nom, description, date, date_format(date, '%d/%m/%Y') as dateFr, affiche, url
          from annonce 
          where date >= curdate()
          order by date;
SQL;
        $select = new Select();
        $lesLignes = $select->getRows($sql);

        // Ajout d'une colonne permettant de vérifier l'existence réelle de l'affiche
        foreach ($lesLignes as &$ligne) {
            $ligne['present'] = ($ligne['affiche'] !== null && $ligne['affiche'] !== '' && is_file(DOSSIER_ANNONCE . $ligne['affiche']));
        }
        unset($ligne);

        return $lesLignes;
    }

}

