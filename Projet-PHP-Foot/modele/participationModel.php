<?php
require_once '../config/bd.php';

function getParticipations($id_match = null) {
    global $linkpdo;
    
    $sql = "SELECT DISTINCT p.*, j.nom, j.prenom, m.Date_match, m.Nom_equipe_adverse 
            FROM participation_match p
            LEFT JOIN joueur j ON p.numero_de_licence = j.numero_de_licence
            LEFT JOIN match_foot m ON p.Id_match = m.Id_match";
    
    if ($id_match) {
        $sql .= " WHERE p.Id_match = :id_match";
        $stmt = $linkpdo->prepare($sql);
        $stmt->execute([':id_match' => $id_match]);
    } else {
        $stmt = $linkpdo->prepare($sql);
        $stmt->execute();
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getParticipationById($id) {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT p.*, j.nom, j.prenom, m.Date_match, m.Nom_equipe_adverse 
        FROM participation_match p
        LEFT JOIN joueur j ON p.numero_de_licence = j.numero_de_licence
        LEFT JOIN match_foot m ON p.Id_match = m.Id_match
        WHERE p.Id_Participation_match = :id
    ");
    $requete->execute([':id' => $id]);
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function modifierParticipation($id, $titulaire, $poste) {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        UPDATE participation_match 
        SET Titulaire = :titulaire,
            Poste = :poste
        WHERE Id_Participation_match = :id
    ");
    return $requete->execute([
        ':id' => $id,
        ':titulaire' => $titulaire,
        ':poste' => $poste
    ]);
}

function supprimerParticipation($id) {
    global $linkpdo;
    $requete = $linkpdo->prepare("DELETE FROM participation_match WHERE Id_Participation_match = :id");
    return $requete->execute([':id' => $id]);
}

function getParticipationsFromDB($match_id = null, $search = '') {
    global $linkpdo;
    $sql = "SELECT DISTINCT p.*, j.nom, j.prenom, m.Date_match, m.Nom_equipe_adverse 
            FROM participation_match p
            LEFT JOIN joueur j ON p.numero_de_licence = j.numero_de_licence
            LEFT JOIN match_foot m ON p.Id_match = m.Id_match
            WHERE 1=1";
    $params = [];
    
    if ($match_id) {
        $sql .= " AND p.Id_match = :match_id";
        $params[':match_id'] = $match_id;
    }
    
    if ($search) {
        $sql .= " AND (j.nom LIKE :search OR j.prenom LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $sql .= " ORDER BY m.Date_match DESC, j.nom ASC";
    
    $stmt = $linkpdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function genererIdParticipation() {
    global $linkpdo;
    $requete = $linkpdo->query("SELECT COUNT(*) as count FROM participation_match");
    $count = $requete->fetch(PDO::FETCH_ASSOC)['count'];
    return $count + 1;
}

function ajouterParticipation($numero_licence, $id_match, $poste, $titulaire) {
    global $linkpdo;
    
    try {
        $id_participation = genererIdParticipation();
        
        $requete = $linkpdo->prepare("
            INSERT INTO participation_match 
            (Id_Participation_match, numero_de_licence, Id_match, Poste, Titulaire) 
            VALUES 
            (:id, :licence, :match, :poste, :titulaire)
        ");
        
        return $requete->execute([
            ':id' => $id_participation,
            ':licence' => $numero_licence,
            ':match' => $id_match,
            ':poste' => $poste,
            ':titulaire' => $titulaire
        ]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return false;
    }
}