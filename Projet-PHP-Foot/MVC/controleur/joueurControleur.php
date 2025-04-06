<?php
// controleur/joueurControleur.php

require_once dirname(__DIR__) . '/modele/joueurModel.php';

function afficherListeJoueurs() {
    $search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
    $statut = isset($_GET['statut']) ? htmlspecialchars($_GET['statut']) : '';
    return getJoueurs($search, $statut);
}

function ajouterNouveauJoueur() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer et valider les données du formulaire
        $numero_licence = filter_input(INPUT_POST, 'numero_licence', FILTER_SANITIZE_STRING);
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
        $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
        $date_naissance_valid = DateTime::createFromFormat('Y-m-d', $date_naissance) !== false;
        $taille = filter_input(INPUT_POST, 'taille', FILTER_VALIDATE_FLOAT);
        $poids = filter_input(INPUT_POST, 'poids', FILTER_VALIDATE_FLOAT);
        $evaluation = filter_input(INPUT_POST, 'evaluation', FILTER_VALIDATE_INT);
        $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);

        // Vérifier que toutes les données sont valides
        if ($numero_licence && $nom && $prenom && $date_naissance_valid && $taille && $poids && $evaluation !== false && $statut) {
            if (ajouterJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $evaluation, $statut)) {
                header('Location: listeJoueurs.php');
                exit;
            } else {
                $error = "Erreur lors de l'ajout du joueur";
            }
        } else {
            $error = "Veuillez remplir tous les champs correctement";
        }
    }
    
    // Afficher le formulaire (avec message d'erreur si nécessaire)
    include '../vue/ajouterJoueurs.php';
}

function modifierJoueurExistant($numero_licence) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
        $date_naissance = filter_input(INPUT_POST, 'date_naissance', FILTER_SANITIZE_STRING);
        $taille = filter_input(INPUT_POST, 'taille', FILTER_VALIDATE_FLOAT);
        $poids = filter_input(INPUT_POST, 'poids', FILTER_VALIDATE_FLOAT);
        $evaluation = filter_input(INPUT_POST, 'evaluation', FILTER_VALIDATE_INT);
        $statut = filter_input(INPUT_POST, 'statut', FILTER_SANITIZE_STRING);

        echo $date_naissance;

        $data = [
            'numero_licence' => $numero_licence,
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $date_naissance,
            'taille' => $taille,
            'poids' => $poids,
            'evaluation' => $evaluation,
            'statut' => $statut
        ];

        $errors = validerDonneesJoueur($data);

        if (empty($errors)) {
            if (modifierJoueur(
                $numero_licence, 
                $nom, 
                $prenom, 
                $date_naissance, 
                $taille, 
                $poids, 
                $evaluation, 
                $statut
            )) {
                header('Location: listeJoueurs.php');
                exit;
            } else {
                $errors[] = "Erreur lors de la modification du joueur.";
            }
        }

        return ['joueur' => $data, 'errors' => $errors];
    } else {
        $joueur = getJoueurParLicence($numero_licence);
        if (!$joueur) {
            header('Location: listeJoueurs.php');
            exit;
        }
        return ['joueur' => $joueur, 'errors' => []];
    }
}

function supprimerJoueurExistant($numero_licence) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (supprimerJoueur($numero_licence)) {
            header('Location: listeJoueurs.php');
            exit;
        } else {
            $error = "Erreur lors de la suppression du joueur";
        }
    }
    
    $joueur = getJoueurParLicence($numero_licence);
    if (!$joueur) {
        header('Location: listeJoueurs.php');
        exit;
    }
    
    include '../vue/supprimerJoueur.php';
}

function traiterFormulaireJoueur($action) {
    $errors = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero_licence = isset($_POST['numero_licence']) ? htmlspecialchars($_POST['numero_licence']) : '';
        $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '';
        $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '';
        $date_naissance = isset($_POST['date_naissance']) ? htmlspecialchars($_POST['date_naissance']) : '';
        $taille = isset($_POST['taille']) ? (int)$_POST['taille'] : 0;
        $poids = isset($_POST['poids']) ? (int)$_POST['poids'] : 0;
        $evaluation = isset($_POST['evaluation']) ? (int)$_POST['evaluation'] : 0;
        $statut = isset($_POST['statut']) ? htmlspecialchars($_POST['statut']) : '';
        
        if (empty($numero_licence)) $errors[] = "Le numéro de licence est requis";
        if (empty($nom)) $errors[] = "Le nom est requis";
        if (empty($prenom)) $errors[] = "Le prénom est requis";
        
        if (empty($errors)) {
            $result = ($action === 'ajouter') ? 
                ajouterJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $evaluation, $statut) :
                modifierJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $evaluation, $statut);
            
            if ($result) {
                header('Location: listeJoueurs.php');
                exit;
            } else {
                $errors[] = "Erreur lors de l'opération";
            }
        }
    }
    
    return $errors;
}

?>