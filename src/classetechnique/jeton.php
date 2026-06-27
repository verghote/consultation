<?php
declare(strict_types=1);

namespace ClasseTechnique;

/**
 * Classe Jeton : gestion des jetons CSRF
 *
 * -------------------------------------------------------------------------
 * OBJECTIF
 * -------------------------------------------------------------------------
 * Empêcher les attaques CSRF (Cross-Site Request Forgery).
 *
 * Une attaque CSRF consiste à amener le navigateur d'un utilisateur déjà
 * authentifié à exécuter une action à son insu sur notre application.
 *
 * Exemple :
 * - l'utilisateur est connecté à notre site ;
 * - il visite ensuite un site malveillant ;
 * - ce site tente d'envoyer une requête POST vers notre application ;
 * - le navigateur enverra automatiquement les cookies de session ;
 * - sans protection CSRF, l'action pourrait être exécutée.
 *
 * -------------------------------------------------------------------------
 * MÉCANISME UTILISÉ
 * -------------------------------------------------------------------------
 * Pattern : Synchronizer Token via header HTTP.
 *
 * 1. Le serveur génère un jeton aléatoire cryptographiquement sûr.
 * 2. Le jeton est stocké en session.
 * 3. Le jeton est injecté dans la page HTML :
 *
 *      <meta name="csrf-token" content="...">
 *
 * 4. Le JavaScript lit cette valeur.
 * 5. Chaque requête AJAX POST transmet le jeton dans :
 *
 *      X-CSRF-Token: ...
 *
 * 6. Le serveur compare :
 *      - le jeton stocké en session
 *      - le jeton reçu dans le header HTTP
 *
 * Si les deux valeurs sont identiques, la requête est considérée comme
 * légitime.
 *
 * -------------------------------------------------------------------------
 * POURQUOI UN HEADER HTTP ?
 * -------------------------------------------------------------------------
 * Un site tiers peut soumettre un formulaire HTML vers notre application,
 * mais il ne peut pas ajouter arbitrairement un header personnalisé comme :
 *
 *      X-CSRF-Token
 *
 * Cette contrainte du navigateur constitue une protection efficace contre
 * les attaques CSRF classiques.
 *
 * -------------------------------------------------------------------------
 * POURQUOI PAS UN COOKIE ?
 * -------------------------------------------------------------------------
 * - Un cookie HttpOnly n'est pas lisible par JavaScript.
 * - Un cookie non HttpOnly est exposé à tout script injecté (XSS).
 * - Le pattern Synchronizer Token est plus simple et plus robuste ici.
 *
 * -------------------------------------------------------------------------
 * DURÉE DE VIE DU JETON
 * -------------------------------------------------------------------------
 * Le jeton est conservé tant qu'il reste valide.
 *
 * Lorsqu'un utilisateur ouvre plusieurs onglets, on évite de régénérer
 * systématiquement le jeton afin de ne pas invalider les pages déjà ouvertes.
 *
 * -------------------------------------------------------------------------
 * UTILISATION
 * -------------------------------------------------------------------------
 * Dans une page :
 *
 *      $token = Jeton::creer();
 *
 * Dans un endpoint AJAX :
 *
 *      Jeton::verifierRequete();
 *
 * ou
 *
 *      Jeton::verifierRequete('POST');
 *
 * ou
 *
 *      Jeton::verifierRequete('DELETE');
 *
 * -------------------------------------------------------------------------
 *
 * @Author  Guy Verghote
 * @Version 2026.3
 * @Date    18/06/2026
 */
class Jeton
{
    /**
     * Refuse immédiatement la requête.
     *
     * Toutes les erreurs CSRF doivent retourner :
     *   HTTP 403 Forbidden
     *
     * Une exception est ensuite levée afin que le mécanisme de gestion
     * des erreurs de l'application prenne le relais.
     *
     * @param string $message Message destiné à l'utilisateur.
     *
     * @return void
     *
     * @throws UserException
     */
    private static function refuser(string $message): void
    {
        http_response_code(403);
        throw new UserException($message);
    }

    /**
     * Crée ou réutilise un jeton CSRF.
     *
     * Si un jeton valide existe déjà en session, il est réutilisé.
     *
     * Pourquoi ?
     * ----------
     * Un utilisateur peut ouvrir plusieurs onglets simultanément.
     *
     * Si un nouveau jeton était généré à chaque affichage de page :
     * - l'onglet A recevrait le jeton A ;
     * - l'onglet B recevrait le jeton B ;
     * - le jeton A deviendrait invalide ;
     * - l'utilisateur obtiendrait des erreurs CSRF inattendues.
     *
     * La réutilisation évite ce problème.
     *
     * @param int $dureeVie
     *      Durée de vie en secondes.
     *      0 = valable tant que la session existe.
     *
     * @return string
     *      Jeton CSRF.
     */
    public static function creer(int $dureeVie = 0): string
    {
        if (isset($_SESSION['csrf_token']['value'], $_SESSION['csrf_token']['expires'])) {
            $value = (string) $_SESSION['csrf_token']['value'];
            $expires = (int) $_SESSION['csrf_token']['expires'];

            if ($value !== '' && ($expires === 0 || $expires >= time())) {
                return $value;
            }
        }

        $token = bin2hex(random_bytes(32));

        $expires = $dureeVie > 0
            ? time() + $dureeVie
            : 0;

        $_SESSION['csrf_token'] = [
            'value'   => $token,
            'expires' => $expires,
        ];

        return $token;
    }

    /**
     * Vérifie qu'une requête respecte :
     *
     * 1. la méthode HTTP attendue ;
     * 2. les règles CSRF si nécessaire.
     *
     * Les requêtes GET sont considérées comme non mutantes :
     * elles ne modifient pas l'état de l'application.
     *
     * Par conséquent, aucun contrôle CSRF n'est réalisé pour GET.
     *
     * Les méthodes suivantes sont considérées comme mutantes :
     * - POST
     * - PUT
     * - PATCH
     * - DELETE
     *
     * Elles nécessitent un contrôle CSRF.
     *
     * @param string $methodeAttendue
     *      Méthode HTTP attendue.
     *
     * @return void
     *
     * @throws UserException
     */
    public static function verifierRequete(string $methodeAttendue = 'POST'): void
    {
        $methodeAttendue = strtoupper($methodeAttendue);

        if ($_SERVER['REQUEST_METHOD'] !== $methodeAttendue) {
            header("Allow: $methodeAttendue");
            http_response_code(405);

            throw new UserException(
                Erreur::getErreurHttp(405)
            );
        }

        if ($methodeAttendue !== 'GET') {
            self::verifier();
        }
    }

    /**
     * Vérifie le jeton CSRF.
     *
     * Vérifications effectuées :
     *
     * 1. Présence du jeton en session.
     * 2. Présence du header HTTP.
     * 3. Non-expiration du jeton.
     * 4. Correspondance exacte des valeurs.
     *
     * Chaque étape possède son propre message afin de faciliter
     * le diagnostic et l'assistance utilisateur.
     *
     * @return void
     *
     * @throws UserException
     */
    public static function verifier(): void
    {
        /**
         * Cas n°1
         * --------
         * Aucun jeton disponible en session.
         *
         * Causes possibles :
         * - session expirée ;
         * - déconnexion ;
         * - serveur redémarré ;
         * - ancienne page encore ouverte ;
         * - problème technique.
         *
         * Nous décrivons uniquement le fait observé :
         * les informations de sécurité ne sont plus disponibles.
         */
        if (!isset($_SESSION['csrf_token']['value'], $_SESSION['csrf_token']['expires'])) {
            self::refuser(
                "Les informations de sécurité associées à cette page ne sont plus disponibles. Veuillez recharger la page puis recommencer l'opération."
            );
        }

        /**
         * Cas n°2
         * --------
         * Le navigateur n'a pas transmis le header attendu.
         *
         * Cela indique généralement :
         * - un problème JavaScript ;
         * - une requête construite manuellement ;
         * - une tentative de contournement.
         */
        $tokenRecu = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if ($tokenRecu === '') {
            self::refuser(
                "Le jeton de sécurité est absent de la requête. Veuillez recharger la page et recommencer."
            );
        }

        /**
         * Cas n°3
         * --------
         * Le jeton existe mais sa durée de validité est dépassée.
         */
        $expires = (int) $_SESSION['csrf_token']['expires'];

        if ($expires > 0 && $expires < time()) {
            self::refuser(
                "Le délai autorisé est dépassé. Veuillez recharger la page et recommencer l'opération."
            );
        }

        /**
         * Cas n°4
         * --------
         * Le jeton reçu ne correspond pas au jeton stocké.
         *
         * hash_equals() évite les attaques par analyse temporelle
         * (timing attacks).
         */
        if (!hash_equals($_SESSION['csrf_token']['value'], $tokenRecu)) {
            self::refuser(
                "La vérification de sécurité a échoué. Veuillez recharger la page et recommencer l'opération."
            );
        }
    }
}
