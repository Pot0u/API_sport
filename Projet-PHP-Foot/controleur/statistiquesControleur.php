<?php
require_once '../modele/statistiquesModel.php';

function calculerStatistiques() {
    $stats_matchs = getStatistiquesMatchs();
    $stats_joueurs = getStatistiquesJoueurs();
    
    $total_matchs = $stats_matchs['total'];
    
    $pourcentages = [
        'victoires' => $total_matchs > 0 ? round(($stats_matchs['victoires'] / $total_matchs) * 100, 2) : 0,
        'nuls' => $total_matchs > 0 ? round(($stats_matchs['nuls'] / $total_matchs) * 100, 2) : 0,
        'defaites' => $total_matchs > 0 ? round(($stats_matchs['defaites'] / $total_matchs) * 100, 2) : 0
    ];
    
    foreach ($stats_joueurs as &$joueur) {
        $joueur['pourcentage_victoires'] = $joueur['total_matchs'] > 0 ? 
            round(($joueur['matchs_gagnes'] / $joueur['total_matchs']) * 100, 2) : 0;
    }
    
    return [
        'matchs' => $stats_matchs,
        'pourcentages' => $pourcentages,
        'joueurs' => $stats_joueurs
    ];
}