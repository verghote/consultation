<?php
declare(strict_types=1);

namespace ClasseMetier;

use ClasseTechnique\Select;

class Competence
{
    /**
     * retourne les projets du portfolio de l'utilisateur
     * @return array
     */
    public static function getAll(): array
    {
        $sql = <<<SQL
            select concat('C.', idBloc, '.', idDomaine, '.', idCompetence) as code, libelle
            from competence
            order by code;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * retourne les compétences d'un bloc
     * @param int $idBloc
     * @return array
     */
    public static function getLesCompetencesDuBloc(int $idBloc): array
    {
        $sql = <<<SQL
            select id, concat('C.', idBloc, '.', idDomaine, '.', idCompetence) as code, libelle
            from competence
            where idBloc = :idBloc
SQL;

        $select = new Select();
        return $select->getRows($sql, ['idBloc' => $idBloc]);
    }

    /**
     * retourne les compétences d'un domaine
     * @param int $idBloc
     * @param int $idDomaine
     * @return array
     */
    public static function getLesCompetencesDuDomaine(int $idBloc, int $idDomaine): array
    {
        $sql = <<<SQL
            select id, concat('C.', idBloc, '.', idDomaine, '.', idCompetence) as code, libelle
            from competence
            where idBloc = :idBloc
            and idDomaine = :idDomaine
SQL;
        $select = new Select();
        return $select->getRows($sql, ['idBloc' => $idBloc, 'idDomaine' => $idDomaine]);
    }

    /**
     * retourne les blocs
     * @return array
     */
    public static function getLesBlocs(): array
    {
        $sql = <<<SQL
            select id, libelle
            from bloc
            order by libelle;
SQL;
        $select = new Select();
        return $select->getRows($sql);
    }

    /**
     * retourne les domaines du bloc
     * @param int $idBloc
     * @return array
     */
    public static function getLesDomaines(int $idBloc): array
    {
        $sql = <<<SQL
            select idDomaine, libelle
            from domaine
            where idBloc = :idBloc
            order by libelle;   
SQL;
        $select = new Select();
        return $select->getRows($sql, ['idBloc' => $idBloc]);
    }
}
