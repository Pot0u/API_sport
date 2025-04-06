<?php
/**
 * Fonction pour obtenir les statistiques des usagers par sexe et par tranche d'âge.
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @return array Les statistiques des usagers.
 */
function getUsagerStats($linkpdo) {
    $stats = [
        'par_sexe' => [],
        'par_tranche_age' => []
    ];

    // Statistiques par sexe
    $querySexe = $linkpdo->query("SELECT sexe, COUNT(*) AS nombre FROM usager GROUP BY sexe");
    $stats['par_sexe'] = $querySexe->fetchAll(PDO::FETCH_ASSOC);

    // Statistiques par tranche d'âge
    $queryAge = $linkpdo->query(
        "SELECT CASE 
            WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) < 25 THEN '<25' 
            WHEN TIMESTAMPDIFF(YEAR, date_nais, CURDATE()) BETWEEN 25 AND 50 THEN '25-50' 
            ELSE '>50' 
        END AS tranche_age, COUNT(*) AS nombre 
        FROM usager 
        GROUP BY tranche_age"
    );
    $stats['par_tranche_age'] = $queryAge->fetchAll(PDO::FETCH_ASSOC);

    return $stats;
}

