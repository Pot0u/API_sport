<?php
require_once 'fonctionMedecins.php';
require_once "../../Ressource/connexionDB.php";
require_once "../../Ressource/functions.php";

$linkpdo = $conn;
$http_method = $_SERVER['REQUEST_METHOD'];

// Vérification du token d'authentification
verifierToken();

switch ($http_method) {
    case "GET":
        if (count($_GET) == 0)  {
            $stats = getMedecinConsultationStats($linkpdo);
            header('Content-Type: application/json');
            echo json_encode([
                'status_code' => 200,
                'status_message' => 'Statistiques des consultations par médecin récupérées avec succès',
                'data' => $stats
            ]);
        } else {
            deliver_response(400, "Paramètre 'stats' manquant ou incorrect.");
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode(['status_code' => 405, 'status_message' => 'Méthode non autorisée']);
        break;
}
