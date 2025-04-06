<?php
require_once 'fonctionUsagers.php';
require_once "../../Ressource/connexionDB.php";
require_once "../../Ressource/functions.php";

$http_method = $_SERVER['REQUEST_METHOD'];
$linkpdo = $conn;

// Vérification du token d'authentification
verifierToken();

switch ($http_method) {
    case "GET":
        if (count($_GET) == 0)  {
            // Si la requête GET inclut le paramètre 'stats=true', fournir les statistiques des usagers
            $stats = getUsagerStats($linkpdo);
            header('Content-Type: application/json');
            echo json_encode([
                'status_code' => 200,
                'status_message' => 'Statistiques des usagers récupérées avec succès',
                'data' => $stats
            ]);
        } else {
            // Si la requête GET ne concerne pas les statistiques, renvoyer une erreur
                deliver_response(400, "Paramètre 'stats' manquant ou incorrect.");
        }
        break;

    default:
        // Si la méthode n'est pas GET, renvoyer une erreur
        deliver_response(405, "Méthode non autorisée. Seule la méthode GET est autorisée pour cette API.");
        break;
}

