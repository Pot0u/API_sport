<?php
// controleur/connexionControleur.php

require_once '../modele/utilisateurModel.php';
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['username'])) {
    $lastPage = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : '../vue/listeJoueurs.php';
    header("Location: $lastPage");
    exit;
}

$messageErreur = '';
$encadrerUsername = '';
$encadrerMotDePasse = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les informations du formulaire
    $username = htmlspecialchars($_POST["username"]);
    $motDePasse = $_POST["motDePasse"];

    if (!empty($username) && !empty($motDePasse)) {
        $utilisateur = getUtilisateur($username);

        if ($utilisateur) {
            // Vérifier le mot de passe
            if (password_verify($motDePasse, $utilisateur['password'])) {
                $_SESSION['username'] = $username;
                $lastPage = isset($_SESSION['last_page']) ? $_SESSION['last_page'] : '../vue/listeJoueurs.php';
                header("Location: $lastPage");
                exit;
            } else {
                $messageErreur = 'Mot de passe incorrect.';
                $encadrerMotDePasse = 'style="border: 1px solid red;"';
            }
        } else {
            $messageErreur = 'Nom d\'utilisateur non trouvé.';
            $encadrerUsername = 'style="border: 1px solid red;"';
        }
    } else {
        $messageErreur = 'Veuillez saisir un nom d\'utilisateur et un mot de passe.';
        $encadrerUsername = 'style="border: 1px solid red;"';
        $encadrerMotDePasse = 'style="border: 1px solid red;"';
    }
}
