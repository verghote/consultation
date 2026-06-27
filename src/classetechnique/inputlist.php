<?php
declare(strict_types=1);

namespace ClasseTechnique;

use InvalidArgumentException;

/**
 * Classe InputList : contrôle une valeur qui doit se trouver dans un ensemble de valeur
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 05/06/2026
 */
class InputList extends Input
{
    // Tableau contenant les valeurs autorisées
    private array $values = [];

    // Indique s'il faut mettre la valeur en majuscule 'U', en minuscule 'L' ou la laisser telle quelle
    private string $casse = '';

    public function setValues(array $values): self
    {
        $this->values = $values;
        return $this;
    }

    public function addValues(array $values): self
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setCasse(string $casse): self
    {
        if (!in_array($casse, ['', 'U', 'L'], true)) {
            throw new InvalidArgumentException("Valeur de casse invalide pour InputList");
        }
        $this->casse = $casse;
        return $this;
    }

    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) return false;

        if ($this->Value !== null) {
            // mise en forme demandée
            if ($this->casse === 'U') {
                $this->Value = strtoupper((string)$this->Value);
            } elseif ($this->casse === 'L') {
                $this->Value = strtolower((string)$this->Value);
            }
            // La valeur fait-elle partie des valeurs de la liste
            if (!in_array($this->Value, $this->values)) {
                $this->validationMessage = "Veuillez entrer une des valeurs acceptées";
                return false;
            }
        }
        return true;
    }
}
