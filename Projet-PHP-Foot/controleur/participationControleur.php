<?php
require_once '../modele/participationModel.php';
require_once '../modele/joueurModel.php';
require_once '../modele/matchModel.php';

function getJoueursDisponibles() {
    return getJoueursActifs();
}

function getMatchsDisponibles() {
    return getAllMatchs(); 
}

function afficherListeParticipations($match_id = null, $search = '') {
    return getParticipationsFromDB($match_id, $search);
}

function formatTitulaire($titulaire) {
    return $titulaire ? 'Titulaire' : 'Remplaçant';
}

function ajouterNouvelleParticipation() {
    $errors = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero_licence = isset($_POST['numero_licence']) ? htmlspecialchars($_POST['numero_licence']) : '';
        $id_match = isset($_POST['id_match']) ? htmlspecialchars($_POST['id_match']) : '';
        $poste = isset($_POST['poste']) ? htmlspecialchars($_POST['poste']) : '';
        $titulaire = isset($_POST['titulaire']) ? (int)$_POST['titulaire'] : 0;
        
        if (empty($numero_licence)) $errors[] = "Veuillez sélectionner un joueur";
        if (empty($id_match)) $errors[] = "Veuillez sélectionner un match";
        if (empty($poste)) $errors[] = "Veuillez sélectionner un poste";
        
        if (empty($errors)) {
            if (ajouterParticipation($numero_licence, $id_match, $poste, $titulaire)) {
                header('Location: listeParticipations.php');
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout de la participation";
            }
        }
    }
    return $errors;
}

function modifierParticipationExistante($id) {
    $participation = getParticipationById($id);
    
    if (!$participation) {
        header('Location: listeParticipations.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulaire = isset($_POST['titulaire']) ? htmlspecialchars($_POST['titulaire']) : null;
        $poste = isset($_POST['poste']) ? htmlspecialchars($_POST['poste']) : null;
        
        if (modifierParticipation($id, $titulaire, $poste)) {
            header('Location: listeParticipations.php');
            exit;
        } else {
            return ['participation' => $participation, 'errors' => ["Erreur lors de la modification"]];
        }
    }
    
    return ['participation' => $participation, 'errors' => []];
}