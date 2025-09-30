<?php
declare(strict_types=1);

class Projet
{
    /**
     * retourne les projets du portfolio de l'utilisateur
     * @return array
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
            select id, nom
            from projet
            order by nom;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * retourne les compétences du projet
     * @param int $idProjet
     * @return array
     */
    public static function getLesCompetences(int $idProjet): array
    {
        $sql = <<<SQL
            select competence.id, concat('C.', idBloc, '.', idDomaine, '.', competence.idCompetence) as code, libelle
            from competenceprojet 
                 join competence on competence.id = competenceprojet.idCompetence  
            where idProjet = :idProjet
            order by libelle;
SQL;
        $select = new Select();
        return $select->getRows($sql, ['idProjet' => $idProjet]);
    }

    /**
     * retourne un projet par son id
     * @param int $id
     * @return mixed
     */
    public static function getById(int $id): mixed
    {
        $sql = "select id, nom from projet where id = :id;";
        $select = new Select();
        return $select->getRow($sql, ['id' => $id]);
    }

}