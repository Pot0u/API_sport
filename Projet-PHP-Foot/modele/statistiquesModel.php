<?php
require_once '../config/bd.php';

function getStatistiquesMatchs() {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT 
            COUNT(DISTINCT Id_match) as total,
            COUNT(DISTINCT CASE WHEN Resultat_match = 'Victoire' THEN Id_match END) as victoires,
            COUNT(DISTINCT CASE WHEN Resultat_match = 'Nul' THEN Id_match END) as nuls,
            COUNT(DISTINCT CASE WHEN Resultat_match = 'Défaite' THEN Id_match END) as defaites
        FROM match_foot
        WHERE Resultat_match != 'Non joué'
    ");
    $requete->execute();
    return $requete->fetch(PDO::FETCH_ASSOC);
}

function getStatistiquesJoueurs() {
    global $linkpdo;
    $sql = "
        SELECT 
            j.numero_de_licence,
            j.nom,
            j.prenom,
            j.statut,
            j.evaluation as evaluation_entraineur,
            COUNT(DISTINCT CASE WHEN p.Titulaire = 1 THEN p.Id_match END) as nb_titularisations,
            COUNT(DISTINCT CASE WHEN p.Titulaire = 0 THEN p.Id_match END) as nb_remplacements,
            MAX(p.Poste) as poste_prefere,
            COUNT(DISTINCT CASE WHEN m.Resultat_match != 'Non joué' THEN m.Id_match END) as total_matchs,
            COUNT(DISTINCT CASE WHEN m.Resultat_match = 'Victoire' THEN m.Id_match END) as matchs_gagnes
        FROM joueur j
        LEFT JOIN participation_match p ON j.numero_de_licence = p.numero_de_licence
        LEFT JOIN match_foot m ON p.Id_match = m.Id_match AND m.Resultat_match != 'Non joué'
        GROUP BY j.numero_de_licence, j.nom, j.prenom, j.statut, j.evaluation
    ";
    
    try {
        $requete = $linkpdo->prepare($sql);
        $requete->execute();
        $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultats as &$joueur) {
            $joueur['pourcentage_victoires'] = $joueur['total_matchs'] > 0 ? 
                round(($joueur['matchs_gagnes'] / $joueur['total_matchs']) * 100, 2) : 0;
        }
        
        return $resultats;
    } catch (PDOException $e) {
        error_log("Error in getStatistiquesJoueurs: " . $e->getMessage());
        return [];
    }
}

function getSelectionsConsecutives($numero_licence) {
    global $linkpdo;
    $requete = $linkpdo->prepare("
        SELECT m.Date_match, p.Titulaire
        FROM participation_match p
        JOIN match_foot m ON p.Id_match = m.Id_match
        WHERE p.numero_de_licence = :licence
        ORDER BY m.Date_match DESC
    ");
    $requete->execute([':licence' => $numero_licence]);
    return $requete->fetchAll(PDO::FETCH_ASSOC);
}