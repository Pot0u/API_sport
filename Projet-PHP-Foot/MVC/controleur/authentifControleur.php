<?php
// Vérifier si l'utilisateur n'est pas connecté
if (!isset($_SESSION['username'])) {
    // Stocker la page actuelle dans la session
    $_SESSION['last_page'] = $_SERVER['PHP_SELF'];

    // Rediriger vers la page de connexion
    header('Location: connexion.php');
    exit;
}
