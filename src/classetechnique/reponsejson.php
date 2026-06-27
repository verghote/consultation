<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe ReponseJson : permet de produire des réponses JSON
 * et d'encoder des données destinées à être injectées dans du JavaScript.
 *
 * @Author : Guy Verghote
 * @Version 2026.3
 * @Date : 05/06/2026
 */
final class ReponseJson
{
    /**
     * Constructeur privé : classe utilitaire statique.
     */
    private function __construct()
    {
    }

    /**
     * Envoie les en-têtes HTTP JSON.
     */
    private static function headers(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }

    /**
     * Options JSON utilisées pour les réponses AJAX.
     */
    private static function jsonOptions(): int
    {
        return JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;
    }

    /**
     * Envoie une réponse JSON contenant des données.
     *
     * @param array $donnees Données à transmettre
     * @param int $status Code HTTP
     */
    public static function envoyerLesDonnees(array $donnees = [], int $status = 200): never
    {
        http_response_code($status);
        self::headers();

        echo json_encode($donnees, self::jsonOptions());

        exit;
    }

    /**
     * Envoie une réponse JSON contenant un message.
     *
     * Si le message est numérique (par exemple un identifiant auto-incrémenté),
     * le code HTTP 201 est envoyé.
     *
     * @param string|int $message
     */
    public static function envoyerMessage(string|int $message = 'Opération réalisée avec succès'): never
    {
        $status = is_numeric((string)$message) ? 201 : 200;

        http_response_code($status);
        self::headers();

        echo json_encode(['message' => $message], self::jsonOptions());

        exit;
    }

    /**
     * Envoie une réponse JSON contenant une liste d'erreurs.
     *
     * @param array $lesErreurs
     * @param int $status
     */
    public static function envoyerLesErreurs(array $lesErreurs, int $status = 400): never
    {
        http_response_code($status);
        self::headers();

        echo json_encode(['errors' => $lesErreurs], self::jsonOptions());

        exit;
    }

    /**
     * Encode des données JSON destinées à être injectées
     * dans une balise <script>.
     *
     * Exemple :
     *
     * <script>
     *     const lesClubs = <?= ReponseJson::encoderPourJavascript($lesClubs) ?>;
     * </script>
     *
     * Les caractères pouvant provoquer une attaque XSS
     * sont neutralisés.
     *
     * @param mixed $valeur
     * @return string
     */
    public static function encoderPourJavascript(mixed $valeur): string
    {
        return json_encode($valeur, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}