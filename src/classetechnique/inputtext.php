<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Gestion de la casse.
 */
enum TextCase
{
    case None;
    case Upper;
    case Lower;
    case Word;
    case First;
}

/**
 * Classe InputText : contrôle une chaîne de caractères.
 *
 * @Author : Guy Verghote
 * @Version 2026.2
 * @Date : 05/06/2026
 */
class InputText extends Input
{
    /**
     * Expression régulière complète.
     * Exemple : '/^[A-Z]+$/u'
     */
    public ?string $Pattern = null;

    /**
     * Longueur minimale.
     */
    public ?int $MinLength = null;

    /**
     * Longueur maximale.
     */
    public ?int $MaxLength = null;

    /**
     * Gestion de la casse.
     */
    public TextCase $Casse = TextCase::None;

    /**
     * Supprimer les accents.
     */
    public bool $SupprimerAccent = false;

    /**
     * Supprimer les espaces multiples.
     */
    public bool $SupprimerEspaceSuperflu = false;

    /**
     * Transforme un motif métier en pattern PCRE complet.
     */
    private function getPcrePattern(string $pattern): string
    {
        $pattern = trim($pattern);

        if ($pattern === '') {
            return '';
        }

        // Motif déjà délimité (ex: '/^...$/u').
        if (preg_match('/^([^\\w\\s\\\\]).+\\1[imsxADSUXJu]*$/', $pattern) === 1) {
            return $pattern;
        }

        // Motif brut (ex: '^...$') : encapsulation avec '/'.
        return '/'.str_replace('/', '\\/', $pattern).'/u';
    }

    /**
     * Suppression des accents.
     */
    private function sansAccent(string $valeur): string
    {
        if (function_exists('transliterator_transliterate')) {
            return transliterator_transliterate('Any-Latin; Latin-ASCII', $valeur);
        }

        return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valeur) ?: $valeur;
    }

    /**
     * Validation.
     */
    public function checkValidity(): bool
    {
        if (!parent::checkValidity()) {
            return false;
        }

        if ($this->Value === null) {
            return true;
        }

        $valeur = trim((string)$this->Value);

        // Suppression des accents
        if ($this->SupprimerAccent) {
            $valeur = $this->sansAccent($valeur);
        }

        // Suppression des espaces multiples
        if ($this->SupprimerEspaceSuperflu) {
            $valeur = preg_replace('/\s+/u', ' ', $valeur) ?? $valeur;
        }

        // Gestion de la casse
        switch ($this->Casse) {
            case TextCase::Upper:
                $valeur = mb_strtoupper($valeur, 'UTF-8');
                break;

            case TextCase::Lower:
                $valeur = mb_strtolower($valeur, 'UTF-8');
                break;

            case TextCase::Word:
                $valeur = mb_convert_case(mb_strtolower($valeur, 'UTF-8'), MB_CASE_TITLE,'UTF-8');
                break;

            case TextCase::First:
                $premiere = mb_substr($valeur, 0, 1, 'UTF-8');
                $reste = mb_substr($valeur, 1, null, 'UTF-8');

                $valeur =
                    mb_strtoupper($premiere, 'UTF-8')
                    . mb_strtolower($reste, 'UTF-8');
                break;

            case TextCase::None:
                break;
        }

        // Contrôle du pattern
        if ($this->Pattern !== null) {
            $pattern = $this->getPcrePattern($this->Pattern);

            // Pattern invalide: on évite d'exposer un warning preg_match.
            if ($pattern === '' || @preg_match($pattern, '') === false) {
                $this->validationMessage = 'Le format de validation est invalide.';
                return false;
            }

            if (@preg_match($pattern, $valeur) !== 1) {
                $this->validationMessage = 'La valeur transmise n\'est pas valide.';
                return false;
            }
        }

        $nbCar = mb_strlen($valeur, 'UTF-8');

        // Longueur minimale
        if ( $this->MinLength !== null && $nbCar < $this->MinLength) {
            $this->validationMessage = "Veuillez allonger ce texte pour qu'il comporte au moins {$this->MinLength} caractères. Il en compte actuellement {$nbCar}.";
            return false;
        }

        // Longueur maximale
        if ($this->MaxLength !== null && $nbCar > $this->MaxLength ) {
            $this->validationMessage = "Veuillez réduire ce texte afin de ne pas dépasser {$this->MaxLength} caractères.";
            return false;
        }

        $this->Value = $valeur;

        return true;
    }
}