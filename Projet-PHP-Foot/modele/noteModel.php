<?php
require_once '../config/bd.php';

function getNotesJoueur($numero_licence) {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT DISTINCT n.*, j.nom, j.prenom 
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
    
    // Generate note ID
    $requete = $linkpdo->query("SELECT COUNT(*) as count FROM note_personnelle");
    $count = $requete->fetch(PDO::FETCH_ASSOC)['count'] + 1;
    $id_note = 'NOTE' . str_pad($count, 3, '0', STR_PAD_LEFT);
    
    $requete = $linkpdo->prepare("
        INSERT INTO note_personnelle (Id_note, Commentaire, numero_de_licence) 
        VALUES (:id, :commentaire, :licence)
    ");
    
    return $requete->execute([
        ':id' => $id_note,
        ':commentaire' => $commentaire,
        ':licence' => $numero_licence
    ]);
}