<?php
require_once '../modele/matchModel.php';

function afficherListeMatchs() {
    return getMatchs();
}

function formatLieuMatch($domicile) {
    return $domicile ? 'Domicile' : 'Extérieur';
}

function formatResultat($resultat) {
    return $resultat ?? 'Non joué';
}

function validerMatch($date_match, $heure_match, $equipe_adverse, $lieu) {
    $errors = [];
    
    // Validate date (not in the past)
    $match_date = new DateTime($date_match);
    $today = new DateTime('today');
    
    if ($match_date < $today) {
        $errors[] = "La date du match ne peut pas être dans le passé";
    }
    
    // Validate team name
    if (empty($equipe_adverse)) {
        $errors[] = "Le nom de l'équipe adverse est requis";
    }
    
    return $errors;
}

function ajouterNouveauMatch() {
    $errors = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date_match = filter_input(INPUT_POST, 'date_match', FILTER_SANITIZE_STRING);
        $heure_match = filter_input(INPUT_POST, 'heure_match', FILTER_SANITIZE_STRING);
        $equipe_adverse = filter_input(INPUT_POST, 'equipe_adverse', FILTER_SANITIZE_STRING);
        $lieu = filter_input(INPUT_POST, 'lieu', FILTER_SANITIZE_STRING);
        
        $errors = validerMatch($date_match, $heure_match, $equipe_adverse, $lieu);
        
        if (empty($errors)) {
            if (ajouterMatch($date_match, $heure_match, $equipe_adverse, $lieu)) {
                header('Location: listeMatchs.php');
                exit;
            } else {
                $errors[] = "Erreur lors de l'ajout du match";
            }
        }
    }
    
    return $errors;
}

function modifierMatchExistant($id) {
    $match = getMatchById($id);
    
    if (!$match) {
        header('Location: listeMatchs.php');
        exit;
    }

    $match_datetime = new DateTime($match['Date_match'] . ' ' . $match['Heure_match']);
    $now = new DateTime();
    $is_past_match = $match_datetime < $now;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($is_past_match) {
            // Only allow result update for past matches
            $resultat = isset($_POST['Resultat_match']) ? htmlspecialchars($_POST['Resultat_match']) : null;
            
            if ($resultat && modifierResultatMatch($id, $resultat)) {
                header('Location: listeMatchs.php');
                exit;
            } else {
                return ['match' => $match, 'errors' => ["Erreur lors de la modification du résultat"], 'is_past_match' => true];
            }
        } else {
            // Allow full modification for future matches
            $date_match = filter_input(INPUT_POST, 'date_match', FILTER_SANITIZE_STRING);
            $heure_match = filter_input(INPUT_POST, 'heure_match', FILTER_SANITIZE_STRING);
            $equipe_adverse = filter_input(INPUT_POST, 'equipe_adverse', FILTER_SANITIZE_STRING);
            $lieu = filter_input(INPUT_POST, 'lieu', FILTER_SANITIZE_STRING);
            
            $errors = validerMatch($date_match, $heure_match, $equipe_adverse, $lieu);
            
            if (empty($errors)) {
                if (modifierMatch($id, $date_match, $heure_match, $equipe_adverse, $lieu, $resultat ?? 'Non joué')) {
                    header('Location: listeMatchs.php');
                    exit;
                } else {
                    $errors[] = "Erreur lors de la modification du match";
                }
            }
            return ['match' => $_POST, 'errors' => $errors, 'is_past_match' => false];
        }
    }
    
    return ['match' => $match, 'errors' => [], 'is_past_match' => $is_past_match];
}

function supprimerMatchExistant($id) {
    $match = getMatchById($id);
    
    if (!$match) {
        header('Location: listeMatchs.php');
        exit;
    }

    $match_datetime = new DateTime($match['Date_match'] . ' ' . $match['Heure_match']);
    $now = new DateTime();
    
    if ($match_datetime < $now) {
        $_SESSION['error'] = "Impossible de supprimer un match passé";
        header('Location: listeMatchs.php');
        exit;
    }

    if (supprimerMatch($id)) {
        header('Location: listeMatchs.php');
    } else {
        $_SESSION['error'] = "Erreur lors de la suppression du match";
        header('Location: listeMatchs.php');
    }
    exit;
}