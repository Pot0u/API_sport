<?php
// controleur/inscriptionControleur.php
require_once '../modele/utilisateurModel.php';

function traiterInscription() {
    $message = '';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer et valider les données du formulaire
        $username = htmlspecialchars($_POST["username"]);
        $motDePasse = $_POST["motDePasse"];

        if (!empty($username) && !empty($motDePasse)) {
            // Hacher le mot de passe
            $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);

            // Vérifier si l'utilisateur existe déjà
            if (!getUtilisateur($username)) {
                // Ajouter le nouvel utilisateur
                if (ajouterUtilisateur($username, $motDePasseHache)) {
                    $message = "Inscription réussie !";
                } else {
                    $message = "Erreur lors de l'inscription. Veuillez réessayer.";
                }
            } else {
                $message = "Ce nom d'utilisateur est déjà pris.";
            }
        } else {
            $message = "Veuillez saisir un nom d'utilisateur et un mot de passe.";
        }
    }

    // Retourne le message pour l'afficher dans la vue
    return $message;
}
