<?php
// activation du chargement dynamique des ressources
require $_SERVER['DOCUMENT_ROOT'] . "/../bootstrap/autoload.php";

// La page erreur/index.php est appelée en cas de détection d'une erreur,
// elle doit charger ses propres ressources.
// Démarrage de la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Protection contre l'accès direct : si aucune erreur n'est en session,
// l'utilisateur a navigué directement vers /erreur → on le renvoie à l'accueil.
if (!isset($_SESSION['erreur']['errors'])) {
    header('Location: /');
    exit;
}

// récupération des informations sur les erreurs
$errors = isset($_SESSION['erreur']['errors']) && is_array($_SESSION['erreur']['errors'])
    ? $_SESSION['erreur']['errors']
    : ['global' => 'Erreur inconnue'];
// destruction de la variable de session
unset($_SESSION['erreur']);
?>
<!DOCTYPE HTML>
<html lang="fr">
<head>
    <title>Erreur</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="data:,">
    <link href="erreur.css" rel="stylesheet">
</head>
<body>
<div class="card">
    <div class="card-header">Avertissement</div>
    <div class="card-body">
        <?php foreach ($errors as $key => $message): ?>
            <p> <?= htmlspecialchars((string)$message) ?></p>
        <?php endforeach; ?>
    </div>
    <div class="card-footer">
        <a href="/">Revenir à la page d'accueil</a>
    </div>
</div>
</body>
</html>
