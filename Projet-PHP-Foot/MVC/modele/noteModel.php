<?php
require_once dirname(__DIR__) . '/config/bd.php';

function getNotesJoueur($numero_licence) {
    global $linkpdo;
    
    $requete = $linkpdo->prepare("
        SELECT n.*, j.Nom as nom, j.Prenom as prenom 
        FROM note_personnelle n
        JOIN joueur j ON n.numero_de_licence = j.numero_de_licence
        WHERE n.numero_de_licence = :licence
        ORDER BY n.Id_note DESC
    ");
    $requete->execute([':licence' => $numero_licence]);
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}

function ajouterNote($numero_licence, $commentaire) {
    global $linkpdo;
    
    try {
        $id_note = genererIdNote();
        if (!$id_note) {
            error_log("Failed to generate note ID");
            return false;
        }
        
        $requete = $linkpdo->prepare("
            INSERT INTO note_personnelle (Id_note, Commentaire, numero_de_licence) 
            VALUES (:id, :commentaire, :licence)
        ");
        
        $success = $requete->execute([
            ':id' => $id_note,
            ':commentaire' => $commentaire,
            ':licence' => $numero_licence
        ]);
        
        return $success ? $id_note : false;
    } catch (PDOException $e) {
        error_log("PDO Exception in ajouterNote: " . $e->getMessage());
        return false;
    }
}

function getNoteById($id_note) {
    global $linkpdo;
    
    $requete = $linkpdo->prepare("
        SELECT n.*, j.Nom as nom, j.Prenom as prenom 
        FROM note_personnelle n 
        JOIN joueur j ON n.numero_de_licence = j.numero_de_licence 
        WHERE n.Id_note = :id
    ");
    $requete->execute([':id' => $id_note]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function genererIdNote() {
    global $linkpdo;
    $prefix = "NOTE";
    
    try {
        $requete = $linkpdo->query("SELECT Id_note FROM note_personnelle ORDER BY Id_note DESC LIMIT 1");
        if (!$requete) {
            error_log("Query failed in genererIdNote");
            return false;
        }
        
        $lastNote = $requete->fetch(PDO::FETCH_ASSOC);
        
        if ($lastNote) {
            $lastNumber = intval(substr($lastNote['Id_note'], 4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    } catch (PDOException $e) {
        error_log("Error in genererIdNote: " . $e->getMessage());
        return false;
    }
}