<?php
declare(strict_types=1);

// Classe gérant les données de la table annonce : id, nom, description, date, actif

class Annonce
{
    /**
     * retourne les annonces
     * @return array
     */
    public static function getAll() {
        $sql = <<<SQL
          Select id, nom, description, date, actif, date_format(date, '%d/%m/%Y') as dateFr
          from annonce 
          order by date;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * retourne les annonces actives et dont la date n'est pas dépassée
     * @return array
     */
    public static function getLesAnnoncesActives() {
        $sql = <<<SQL
          Select id, nom, description, date, actif, date_format(date, '%d/%m/%Y') as dateFr
          from annonce 
          where actif = 1
          and date >= curdate()
          order by date;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * Retourne l'annonce dont l'id est passé en paramètre
     *
     * @param int $id L'id doit être contrôlé en amont
     * @return mixed
     */
    public static function getById(int $id): mixed
    {
        $sql = <<<SQL
            select id, nom, description, date
            from  annonce
            where id = :id;
SQL;
        $select = new Select();
        return $select->getRow($sql, ['id' => $id]);
    }

    /**
     * Suppression des ancinnes annonces : date dépassée
     * @return array tableau contenant les id des annonces supprimées
     */
    public static function deleteOld() : array {
        $sql = <<<SQL
            select id
            from annonce 
            where date <= curdate();
SQL;
        $select = new Select();
        $lesAnnoncesSupprimees = $select->getRows($sql);

        $sql = <<<SQL
	        delete from annonce 
	        where date <= curdate() 
SQL;
        $db = Database::getInstance();
        $db->exec($sql);
        return $lesAnnoncesSupprimees;
    }
}

