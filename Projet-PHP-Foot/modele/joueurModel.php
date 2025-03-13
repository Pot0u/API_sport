<?php
// modele/joueurModel.php
require_once '../config/bd.php';

function getJoueurs($search = '', $statut = '') {
    global $linkpdo;
    
    try {
        $sql = "SELECT DISTINCT j.* FROM joueur j WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (j.nom LIKE :search OR j.prenom LIKE :search OR j.numero_de_licence LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if (!empty($statut)) {
            $sql .= " AND j.statut = :statut";
            $params[':statut'] = $statut;
        }
        
        $sql .= " ORDER BY j.nom, j.prenom";
        
        $requete = $linkpdo->prepare($sql);
        $requete->execute($params);
        return $requete->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return [];
    }
}

function ajouterJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $evaluation, $statut) {
    global $linkpdo;
    
    try {
        $sql = "INSERT INTO joueur (numero_de_licence, nom, prenom, date_naissance, taille, poids, evaluation, statut) 
                VALUES (:licence, :nom, :prenom, :naissance, :taille, :poids, :evaluation, :statut)";
        
        $requete = $linkpdo->prepare($sql);
        $result = $requete->execute([
            ':licence' => $numero_licence,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':naissance' => $date_naissance,
            ':taille' => $taille,
            ':poids' => $poids,
            ':evaluation' => $evaluation,
            ':statut' => $statut
        ]);
        
        if (!$result) {
            error_log("Error adding player: " . print_r($requete->errorInfo(), true));
        }
        return $result;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

function getJoueurParLicence($numero_licence) {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT * FROM joueur WHERE numero_de_licence = :licence");
    $requete->execute([':licence' => $numero_licence]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function getJoueurByLicence($numero_licence) {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT * FROM joueur WHERE numero_de_licence = :licence");
    $requete->execute([':licence' => $numero_licence]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function modifierJoueur($numero_licence, $nom, $prenom, $date_naissance, $taille, $poids, $evaluation, $statut) {
    global $linkpdo;
    $requete = $linkpdo->prepare("UPDATE joueur 
                                 SET nom = :nom, 
                                     prenom = :prenom, 
                                     date_naissance = :naissance,
                                     taille = :taille, 
                                     poids = :poids, 
                                     evaluation = :evaluation,
                                     statut = :statut
                                 WHERE numero_de_licence = :licence");
    
    return $requete->execute([
        ':licence' => $numero_licence,
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':naissance' => $date_naissance,
        ':taille' => $taille,
        ':poids' => $poids,
        ':evaluation' => $evaluation,
        ':statut' => $statut
    ]);
}

function hasParticipations($numero_licence) {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT COUNT(*) FROM participation_match WHERE numero_de_licence = :licence");
    $requete->execute([':licence' => $numero_licence]);
    return $requete->fetchColumn() > 0;
}

function supprimerJoueur($numero_licence) {
    global $linkpdo;
    
    if (hasParticipations($numero_licence)) {
        return 'participation_exists';
    }
    
    $requete = $linkpdo->prepare("DELETE FROM joueur WHERE numero_de_licence = :licence");
    return $requete->execute([':licence' => $numero_licence]);
}

function validerDonneesJoueur($data) {
    $errors = [];
    
    if (empty($data['numero_licence'])) {
        $errors[] = "Le numéro de licence est requis";
    }
    if (empty($data['nom'])) {
        $errors[] = "Le nom est requis";
    }
    if (empty($data['prenom'])) {
        $errors[] = "Le prénom est requis";
    }
    if (empty($data['date_naissance'])) {
        $errors[] = "La date de naissance est requise";
    }
    if (!is_numeric($data['taille']) || $data['taille'] <= 0) {
        $errors[] = "La taille doit être un nombre positif";
    }
    if (!is_numeric($data['poids']) || $data['poids'] <= 0) {
        $errors[] = "Le poids doit être un nombre positif";
    }
    if (!in_array($data['evaluation'], ['1', '2', '3', '4', '5'])) {
        $errors[] = "L'évaluation doit être entre 1 et 5";
    }
    if (!in_array($data['statut'], ['Actif', 'Inactif', 'Blessé'])) {
        $errors[] = "Le statut n'est pas valide";
    }
    
    return $errors;
}

function getAllJoueursFromDB() {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT DISTINCT * FROM joueur ORDER BY nom, prenom");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function getJoueursActifs() {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT DISTINCT j.* 
        FROM joueur j 
        WHERE j.statut = 'actif' 
        ORDER BY j.nom, j.prenom
    ");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

?>