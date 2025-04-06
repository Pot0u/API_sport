<?php
/**
 * Fonction pour obtenir la durée totale des consultations effectuées par chaque médecin.
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @return array La durée totale des consultations par médecin en nombre d'heures.
 */
function getMedecinConsultationStats($linkpdo) {
    $stats = [];

    // Requête pour obtenir la durée totale des consultations par médecin
    $query = $linkpdo->query(
        "SELECT m.nom, m.prenom, SUM(c.duree_consult) AS duree_total
         FROM medecin m
         JOIN consultation c ON m.id_medecin = c.id_medecin
         GROUP BY m.id_medecin"
    );

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $row['duree_total'] = round($row['duree_total'] / 60, 2);
        $stats[] = $row;
    }

    return $stats;
}
