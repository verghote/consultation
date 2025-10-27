<?php
declare(strict_types=1);

class Coureur
{
    /**
     * retourne l'ensemble des informations sur les coureurs
     * @return array
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
    Select licence, coureur.nom, prenom ,  concat(coureur.nom, ' ', prenom) as nomPrenom, sexe, date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, 
           idCategorie , club.nom AS nomClub, idClub, email, telephone, ffa
    from coureur
    join club on coureur.idClub = club.id 
    order by nom, prenom;
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
    public static function getByLicence(string $licence): mixed
    {
        $sql = <<<SQL
              Select licence, coureur.nom, prenom , sexe, date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, dateNaissance,
                     idCategorie , club.nom AS nomClub, ffa, telephone, email, idClub
             from coureur
                join club on coureur.idClub = club.id 
             where licence = :licence
SQL;
        $select = new Select();
        return $select->getRow($sql, ['licence' => $licence]);
    }

    /**
     * Retourne l'ensemble des coureurs d'une catégorie
     * @param string $idCategorie
     * @return array
     */
    public static function getByCategorie(string $idCategorie): array
    {
        $sql = <<<SQL
            Select licence, coureur.nom, prenom , concat(coureur.nom, ' ', prenom) as nomPrenom,  sexe, 
                       date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, 
                       club.nom AS nomClub, idCategorie
            from coureur
                join club on coureur.idClub = club.id
            where idCategorie = :idCategorie
            order by coureur.nom, prenom;   
SQL;
        $select = new Select();
        return $select->getRows($sql, ['idCategorie' => $idCategorie]);
    }

    public static function getBySexeClubCategorie($sexe = '*', $idClub = '*', $idCategorie = '*'): array
    {
        $lesParametres = [];
        $sql = <<<SQL
            Select licence, coureur.nom, prenom , concat(coureur.nom, ' ', prenom) as nomPrenom, sexe, 
                       date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, 
                       club.nom AS nomClub, idCategorie
            from coureur , club
            where coureur.idClub = club.id
SQL;
        if ($sexe !== '*') {
            $sql .= " and sexe = :sexe";
            $lesParametres['sexe'] = $sexe;
        }
        if ($idClub !== '*') {
            $sql .= " and idClub = :idClub";
            $lesParametres['idClub'] = $idClub;
        }
        if ($idCategorie !== '*') {
            $sql .= " and idCategorie = :idCategorie";
            $lesParametres['idCategorie'] = $idCategorie;
        }
        $sql .= " order by coureur.nom, prenom;";

        $select = new Select();
        return $select->getRows($sql, $lesParametres);
    }


    public static function getByNomPrenom(string $nomPrenom): array
    {
        // récupération des informations sur l'étudiant
        $sql = <<<SQL
              Select licence, coureur.nom, prenom,  concat(coureur.nom, ' ', prenom) AS nomPrenom, sexe, date_format(dateNaissance, '%d/%m/%Y') as dateNaissanceFr, dateNaissance,
                     idCategorie , club.nom AS nomClub, ffa, telephone, email, idClub
             from coureur
                join club on coureur.idClub = club.id 
            where concat(coureur.nom, ' ', prenom) like :terme 
            order by coureur.nom, prenom
            limit 10
SQL;
        $select = new Select();
        // on ajoute % pour la recherche
        $terme = "%$nomPrenom%";
        return $select->getRows($sql,['terme' => $terme]);
    }

}