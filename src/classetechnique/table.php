<?php
declare(strict_types=1);

namespace ClasseTechnique;

use PDO;

/**
 * Classe Table : représente une table SQL
 * Cette classe est une classe abstraite donc non instantiable.
 * Elle met en facteur tous les attributs et toutes les méthodes communes aux classes dérivées
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 05/06/2026
 *
 */
abstract class Table
{
    private PDO $db;
    private string $tableName;
    protected string $idName = 'id';
    protected array $columns;
    protected InputList $listOfColumns;
    private array $lesErreurs = [];
    private bool|string $lastInsertId = false;

    protected function __construct(string $nomTable)
    {
        $this->tableName = $nomTable;
        $this->columns = [];
        $this->listOfColumns = new InputList();
        $this->db = Database::getInstance();
    }

    public function getColonne(string $colonne): Input
    {
        return $this->columns[$colonne];
    }

    public function getLesErreurs(): array
    {
        return $this->lesErreurs;
    }

    public function getLastInsertId(): bool|string
    {
        return $this->lastInsertId;
    }

    public function setValue(string $colonne, mixed $valeur): void
    {
        if (isset($this->columns[$colonne])) {
            $this->columns[$colonne]->Value = $valeur;
        }
    }

    private function prepareAndExecute(string $sql): void
    {
        $cmd = $this->db->prepare($sql);
        foreach ($this->columns as $cle => $input) {
            if ($input->Value === null) {
                continue;
            }
            $cmd->bindValue($cle, $input->Value);
        }
        $cmd->execute();
    }

    /**
     * Alimente les valeurs des objets Input à partir de $_POST
     * Vérifie que tous les champs obligatoires sont renseignés
     */
    public function donneesTransmises(): bool
    {
        // Réinitialisation avant une nouvelle validation d'ajout
        $this->lesErreurs = [];

        // Alimentation depuis $_POST
        foreach ($_POST as $cle => $valeur) {
            $valeur = trim($valeur);
            if (isset($this->columns[$cle])) {
                $input = $this->columns[$cle];

                // Un `valueAsNumber` vide peut arriver côté JS sous forme de "NaN"
                if ($input instanceof InputInt && strtolower($valeur) === 'nan') {
                    continue;
                }

                if ($valeur !== '') {
                    $input->Value = $valeur;
                }
            }
        }

        // Vérification des champs obligatoires
        $ok = true;
        foreach ($this->columns as $cle => $input) {
            if ($input->Require && $input->Value === null) {
                $this->lesErreurs[$cle] = "Veuillez renseigner ce champ.";
                $ok = false;
            }
        }
        return $ok;
    }

    public function checkAll(): bool
    {
        $correct = true;
        foreach ($this->columns as $cle => $input) {
            // Une erreur déjà trouvée (ex: champ obligatoire manquant) est conservée
            if (isset($this->lesErreurs[$cle])) {
                $correct = false;
                continue;
            }

            if (!$input->checkValidity()) {
                $this->lesErreurs[$cle] = $input->getValidationMessage();
                $correct = false;
            }
        }
        return $correct;
    }

    public function insert(): void
    {
        $set = "";
        foreach ($this->columns as $cle => $input) {
            if ($input->Value !== null) {
                $set .= "$cle = :$cle, ";
            }
        }
        $set = substr($set, 0, -2);
        $sql = "insert into $this->tableName set $set";
        $this->prepareAndExecute($sql);
        $this->lastInsertId = $this->db->lastInsertId();
    }

    public function delete(int|string $id): void
    {
        // Suppression de l'enregistrement
        $sql = "DELETE FROM $this->tableName WHERE $this->idName = :id";
        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->execute();
    }

    protected function existe($id): bool
    {
        $sql = "SELECT 1 FROM $this->tableName WHERE $this->idName = :id";
        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->execute();
        $ligne = $cmd->fetch();
        $cmd->closeCursor();
        return (bool)$ligne;
    }

    public function update(int|string $id, array $lesValeurs): void
    {
        if (!$this->existe($id)) {
            throw new UserException("Enregistrement inexistant.");
        }

        // 1. Alimenter les valeurs depuis $_POST
        foreach ($lesValeurs as $cle => $valeur) {
            if (!isset($this->columns[$cle])) {
                throw new UserException("Requête mal formulée : colonne $cle inexistante.");
            }
            $this->columns[$cle]->Value = $valeur;
        }

        // 2. Validation de toutes les valeurs
        $erreur = false;
        $set = "";

        foreach ($this->columns as $cle => $input) {
            if ($input->Value !== null) {
                if (!$input->checkValidity()) {
                    $this->lesErreurs[$cle] = $input->getValidationMessage();
                    $erreur = true;
                } else {
                    $set .= "$cle = :$cle, ";
                }
            }
        }

        if ($erreur) {
            ReponseJson::envoyerLesErreurs($this->lesErreurs, 422);
        }

        if (empty($set)) {
            throw new UserException("Aucune modification à effectuer.");
        }

        $set = substr($set, 0, -2);

        // 3. Mise à jour en base de données
        $sql = "UPDATE $this->tableName SET $set WHERE $this->idName = :id";

        $cmd = $this->db->prepare($sql);
        foreach ($this->columns as $cle => $input) {
            if ($input->Value !== null) {
                $cmd->bindValue($cle, $input->Value);
            }
        }
        $cmd->bindValue('id', $id);
        $cmd->execute();
    }

    public function modifierColonne(string $colonne, string|int $valeur, string|int $id): void
    {
        $this->listOfColumns->Value = $colonne;
        if (!$this->listOfColumns->checkValidity()) {
            throw new UserException("La colonne $colonne n'est pas modifiable.");
        }

        if (!$this->existe($id)) {
            throw new UserException("L'enregistrement à modifier n'existe pas.");
        }

        $input = $this->columns[$colonne];
        $input->Value = $valeur;
        if (!$input->checkValidity()) {
            throw new UserException("La valeur pour la colonne $colonne n'est pas acceptée : " . $input->getValidationMessage());
        }

        $sql = "UPDATE $this->tableName SET $colonne = :valeur WHERE $this->idName = :id";
        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('valeur', $valeur);
        $cmd->bindValue('id', $id);
        $cmd->execute();
    }

    public function setNull(string $colonne, string|int $id): void
    {
        if (!isset($this->columns[$colonne])) {
            throw new UserException("La colonne $colonne n'existe pas.");
        }

        if (!$this->existe($id)) {
            throw new UserException("L'enregistrement à modifier n'existe pas.");
        }

        $sql = "UPDATE $this->tableName SET $colonne = null WHERE $this->idName = :id";
        $cmd = $this->db->prepare($sql);
        $cmd->bindValue('id', $id);
        $cmd->execute();
    }
}
