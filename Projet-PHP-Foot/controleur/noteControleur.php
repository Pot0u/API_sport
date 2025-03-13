<?php
require_once '../modele/noteModel.php';
require_once '../modele/joueurModel.php';

function afficherNotesJoueur($numero_licence) {
    return [
        'joueur' => getJoueurByLicence($numero_licence),
        'notes' => getNotesJoueur($numero_licence)
    ];
}

function ajouterNouvelleNote() {
    $errors = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero_licence = isset($_POST['numero_licence']) ? htmlspecialchars($_POST['numero_licence']) : '';
        $commentaire = isset($_POST['commentaire']) ? htmlspecialchars($_POST['commentaire']) : '';
        
        if (empty($commentaire)) {
            $errors[] = "Le commentaire est requis";
        }
        
        if (empty($errors)) {
            if (ajouterNote($numero_licence, $commentaire)) {
                header('Location: voirNotes.php?id=' . urlencode($numero_licence));
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout de la note";
            }
        }
    }
    
    return $errors;
}