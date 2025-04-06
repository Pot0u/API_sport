<?php
require_once dirname(__DIR__) . '/config/bd.php';

function getMatchs() {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT DISTINCT * FROM match_foot ORDER BY Date_match DESC, Heure_match DESC");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchById($id) {
    global $linkpdo;
    $requete = $linkpdo->prepare("SELECT * FROM match_foot WHERE Id_match = :id");
    $requete->execute([':id' => $id]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function genererIdMatch() {
    global $linkpdo;
    $prefix = "MATCH";
    
    // Get the highest existing ID number
    $requete = $linkpdo->query("SELECT Id_match FROM match_foot ORDER BY Id_match DESC LIMIT 1");
    $lastMatch = $requete->fetch(PDO::FETCH_ASSOC);
    
    if ($lastMatch) {
        // Extract the numeric part of the last ID
        $lastNumber = intval(substr($lastMatch['Id_match'], 5));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
}

function ajouterMatch($date_match, $heure_match, $equipe_adverse, $lieu) {
    global $linkpdo;
    
    try {
        $linkpdo->beginTransaction();
        
        $id_match = genererIdMatch();
        
        $requete = $linkpdo->prepare("INSERT INTO match_foot 
            (Id_match, Date_match, Heure_match, Nom_equipe_adverse, Domicile_externe, Resultat_match) 
            VALUES (:id, :date, :heure, :equipe, :lieu, 'Non joué')");
            
        $success = $requete->execute([
            ':id' => $id_match,
            ':date' => $date_match,
            ':heure' => $heure_match,
            ':equipe' => $equipe_adverse,
            ':lieu' => $lieu
        ]);
        
        if (!$success) {
            throw new Exception("Échec de l'insertion du match");
        }
        
        $linkpdo->commit();
        
        // Return the newly created match data
        return getMatchById($id_match);
        
    } catch (Exception $e) {
        $linkpdo->rollBack();
        error_log("Erreur lors de l'ajout du match: " . $e->getMessage());
        return false;
    }
}

function modifierMatch($id, $date_match, $heure_match, $equipe_adverse, $lieu, $resultat = 'Non joué') {
    global $linkpdo;
    
    $requete = $linkpdo->prepare("UPDATE match_foot 
        SET Date_match = :date,
            Heure_match = :heure,
            Nom_equipe_adverse = :equipe,
            Domicile_externe = :lieu,
            Resultat_match = :resultat
        WHERE Id_match = :id");
        
    return $requete->execute([
        ':id' => $id,
        ':date' => $date_match,
        ':heure' => $heure_match,
        ':equipe' => $equipe_adverse,
        ':lieu' => $lieu,
        ':resultat' => $resultat
    ]);
}

function supprimerMatch($id) {
    global $linkpdo;
    $requete = $linkpdo->prepare("DELETE FROM match_foot WHERE Id_match = :id");
    return $requete->execute([':id' => $id]);
}

function modifierResultatMatch($id, $resultat) {
    global $linkpdo;
    
    $requete = $linkpdo->prepare("
        UPDATE match_foot 
        SET Resultat_match = :resultat
        WHERE Id_match = :id
    ");
    
    return $requete->execute([
        ':id' => $id,
        ':resultat' => $resultat
    ]);
}

function getAllMatchs() {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT DISTINCT * FROM match_foot 
        ORDER BY Date_match DESC, Heure_match DESC
    ");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function getMatchsFuturs() {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT DISTINCT * FROM match_foot 
        WHERE Date_match >= CURDATE() 
        OR (Date_match = CURDATE() AND Heure_match > CURTIME())
        ORDER BY Date_match ASC, Heure_match ASC
    ");
    $requete->execute();
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}