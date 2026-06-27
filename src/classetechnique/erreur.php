<?php
declare(strict_types=1);

namespace ClasseTechnique;

use PDOException;
use Throwable;

/**
 * Classe Erreur : gestion centralisée des erreurs applicatives et SQL.
 *
 * STRATÉGIE :
 * - Toute exception est journalisée avec ses détails techniques complets
 * - Réponse utilisateur en deux cas seulement :
 *   • UserException → le message réel
 *   • Tout autre Throwable → message générique
 *
 * @author Guy Verghote
 * @Version 2026.2
 * @Date : 05/06/2026
 */
enum TypeReponse: string
{
    // Réponse HTML classique (redirection vers page d'erreur)
    case HTML = 'html';
    // Réponse JSON (API clients)
    case JSON = 'json';
}

class Erreur
{
    // Message générique affiché pour toute erreur non-applicative
    private const MSG_SYSTEME = "Une erreur technique est survenue, veuillez réessayer ultérieurement.";

    // Mapping des codes d'erreur MySQL/MariaDB connus vers un message lisible
    // Ces messages sont présentés comme UserException après journalisation
    private static array $lesCodesSql = [
        1062 => "Enregistrement déjà existant.",
        1048 => "Une information obligatoire est manquante.",
        1406 => "Une information est trop longue.",
        1366 => "Format de donnée invalide.",
        1452 => "Donnée invalide (référence inexistante).",
        1451 => "Suppression impossible : donnée utilisée.",
        3819 => "Une valeur saisie ne respecte pas une règle de validation.",
        4025 => "Une valeur saisie ne respecte pas une règle de validation.",
    ];

    /**
     * Installe le handler global ; c'est le seul point d'entrée public.
     */
    public static function installerGestionnaire(): void
    {
        set_exception_handler(static function (Throwable $e): void {
            self::traiterReponse($e);
        });
    }

    /**
     * Traitement interne de toutes les exceptions.
     *
     * 1. Journalisation systématique des détails techniques
     * 2. Construction de la réponse utilisateur :
     *    - UserException => message réel
     *    - PDOException  => message lisible si code connu, sinon générique
     *    - Throwable     => message générique
     */
    private static function traiterReponse(Throwable $e): void
    {
        self::journaliser($e);

        if ($e instanceof UserException) {
            $errors = ['global' => $e->getMessage()];
        } elseif ($e instanceof PDOException) {
            $errors = ['global' => self::resoudreMessageSQL($e)];
        } else {
            $errors = ['global' => self::MSG_SYSTEME];
        }

        self::rendreReponse($errors);
    }

    /**
     * Journalisation systématique avec tous les détails techniques de l'exception.
     */
    private static function journaliser(Throwable $e): void
    {
        $detail = sprintf(
            '[%s] %s | code : %s | fichier : %s | ligne : %d',
            get_class($e),
            $e->getMessage(),
            $e->getCode(),
            $e->getFile(),
            $e->getLine()
        );
        Journal::enregistrer($detail, 'erreur');
    }

    /**
     * Résout un message lisible à partir d'une PDOException.
     *
     * PDO fournit errorInfo : [SQLSTATE, driverCode, driverMessage]
     * - SQLSTATE '45000' => message du trigger (lisible, exposé tel quel)
     * - Contrainte CHECK  => message générique de validation
     * - Code connu dans $lesCodesSql => message mappé
     * - Autre => message système générique
     */
    private static function resoudreMessageSQL(PDOException $e): string
    {
        $errorInfo = $e->errorInfo ?? [];
        [$sqlState, $codeErreur, $message] = array_pad($errorInfo, 3, null);

        if ($sqlState === '45000') {
            return (string)$message;
        }

        if (self::estErreurCheckConstraint($codeErreur, $message)) {
            return "Une valeur saisie ne respecte pas une règle de validation.";
        }

        if (isset(self::$lesCodesSql[(int)$codeErreur])) {
            return self::$lesCodesSql[(int)$codeErreur];
        }

        return self::MSG_SYSTEME;
    }

    /**
     * Détecte les violations de contraintes CHECK (MySQL code 3819, MariaDB code 4025).
     * Fallback sur le message texte si le code n'est pas exploitable.
     */
    private static function estErreurCheckConstraint(mixed $codeErreur, mixed $message): bool
    {
        if (in_array((int)$codeErreur, [3819, 4025], true)) {
            return true;
        }

        if (!is_string($message) || $message === '') {
            return false;
        }

        return (bool)preg_match('/check constraint|constraint .* is violated|violated/i', $message);
    }

    /**
     * Envoie la réponse dans le format attendu (JSON ou redirection HTML).
     */
    private static function rendreReponse(array $errors): void
    {
        if (self::getTypeReponse() === TypeReponse::JSON) {
            ReponseJson::envoyerLesErreurs($errors);
        } else {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['erreur'] = [];
            $_SESSION['erreur']['page']   = $_SERVER['PHP_SELF'];
            $_SESSION['erreur']['errors'] = $errors;
            header('Location: /erreur');
        }
    }

    /**
     * Détermine le type de réponse attendu : JSON si requête AJAX ou Accept contient application/json.
     */
    private static function getTypeReponse(): TypeReponse
    {
        if (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            return TypeReponse::JSON;
        }

        if (
            isset($_SERVER['HTTP_ACCEPT']) &&
            str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
        ) {
            return TypeReponse::JSON;
        }

        return TypeReponse::HTML;
    }

    /**
     * Bloque un visiteur jugé malveillant.
     */
    public static function bloquerVisiteur(?string $message = null): void
    {
        if ($message === null) {
            $message = "Votre requête a été jugée malveillante, votre session a été désactivée et votre adresse IP a été enregistrée";
        }

        Journal::enregistrer($_SERVER['REQUEST_URI'], 'menace');

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['disable'] = true;
        $_SESSION['erreur'] = [];
        $_SESSION['erreur']['errors'] = ['global' => $message];

        header('Location: /erreur');
        exit;
    }

    /**
     * Retourne un libellé lisible pour un code HTTP donné.
     */
    public static function getErreurHttp(int|string|null $codeHttp): string
    {
        if ($codeHttp === null) {
            return "Erreur HTTP : code inconnu";
        }

        $libelles = [
            400 => "Requête incorrecte",
            401 => "Erreur d'authentification",
            403 => "Demande interdite",
            404 => "Page non trouvée",
            405 => "Méthode non autorisée",
            408 => "Temps d'attente d'une requête dépassé",
            500 => "Erreur interne du serveur",
            502 => "Mauvaise passerelle",
            503 => "Service indisponible",
            504 => "Temps d'attente de la passerelle dépassé"
        ];

        return $libelles[$codeHttp] ?? "Erreur HTTP : " . $codeHttp;
    }
}

