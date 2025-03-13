<?php
// modele/utilisateurModel.php
require_once '../config/bd.php';

//Vérifie si un utilisateur existe déjà dans la base.
function getUtilisateur($username) {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT * FROM utilisateur WHERE username = :username");
    $requete->bindParam(':username', $username);
    $requete->execute();
    return $requete->fetch(PDO::FETCH_ASSOC);
}

// Insère un nouvel utilisateur dans la base de données.
function ajouterUtilisateur($username, $motDePasseHache) {
    global $linkpdo;
    $requete = $linkpdo->prepare("INSERT INTO utilisateur (username, password) VALUES (:username, :password)");
    $requete->bindParam(':username', $username);
    $requete->bindParam(':password', $motDePasseHache);
    return $requete->execute();
}
?>