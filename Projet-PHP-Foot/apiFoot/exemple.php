<?php
require '../v2/functions.php';
require '../v1/functions.php';

// Ajout des en-têtes CORS pour accepter les requêtes de n'importe quelle origine
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'OPTIONS':
        deliver_response(200, "OK");
        break;

    case 'GET':
        if (isset($_GET['id'])) {
            $sentence = getSentenceById($_GET['id']);
            if ($sentence) {
                deliver_response(200, "Phrase récupérée avec succès", $sentence);
            } else {
                deliver_response(404, "Phrase non trouvée");
            }
        } elseif (isset($_GET['lastN'])) {
            $n = $_GET['lastN'];
            $phrases = getLastNPhrases($n);
            deliver_response(200, "Dernières phrases récupérées avec succès", $phrases);
        } elseif (isset($_GET['topN'])) {
            $n = $_GET['topN'];
            $phrases = getTopNPhrases($n);
            deliver_response(200, "Phrases les mieux notées récupérées avec succès", $phrases);
        } elseif (isset($_GET['reported'])) {
            $phrases = getAllReportedSentences();
            deliver_response(200, "Phrases signalées récupérées avec succès", $phrases);
        } else {
            $sentences = getAllSentences();
            deliver_response(200, "Toutes les phrases récupérées avec succès", $sentences);
        }
        break;


    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        $phrase = $data['phrase'];
        $vote = $data['vote'] ?? 0;
        $faute = $data['faute'] ?? 0;
        $signalement = $data['signalement'] ?? 0;

        $result = addSentence($phrase, $vote, $faute, $signalement);
        if ($result) {
            deliver_response(201, "Phrase créée avec succès", ["id" => $result]);
        } else {
            deliver_response(500, "Erreur lors de la création de la phrase");
        }
        break;

        case 'PUT':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $action = isset($_GET['action']) ? $_GET['action'] : (isset($_GET['incrementVote']) ? 'incrementVote' : (isset($_GET['decrementVote']) ? 'decrementVote' : (isset($_GET['markReported']) ? 'markReported' : (isset($_GET['unmarkReported']) ? 'unmarkReported' : ''))));
        
            if (!empty($id) && !empty($action)) {
                switch ($action) {
                    case 'incrementVote':
                        $result = incrementVote($id);
                        $message = $result ? "Vote incrémenté avec succès" : "Erreur lors de l'incrémentation du vote";
                        break;
                    case 'decrementVote':
                        $result = decrementVote($id);
                        $message = $result ? "Vote décrémenté avec succès" : "Erreur lors de la décrémentation du vote";
                        break;
                    case 'markReported':
                        $result = markSentenceAsReported($id);
                        $message = $result ? "Phrase marquée comme signalée avec succès" : "Phrase déjà marqué comme signalée";
                        break;
                    case 'unmarkReported':
                        $result = unmarkSentenceAsReported($id);
                        $message = $result ? "Phrase marquée comme non signalée avec succès" : "Phrase déjà marqué comme non signalée";
                        break;
                    default:
                        $message = "Action non spécifiée pour la mise à jour";
                        $result = false;
                }
                deliver_response($result ? 200 : 400, $message, ["updatedRows" => $result]);
            } else {
                deliver_response(400, "ID non spécifié pour la mise à jour");
            }
            break;
        



    case 'DELETE':
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        if (!empty($id)) {
            $result = deleteSentence($id);
            if ($result) {
                deliver_response(200, "Phrase supprimée avec succès", ["deletedRows" => $result]);
            } else {
                deliver_response(500, "Erreur lors de la suppression de la phrase");
            }
        } else {
            deliver_response(400, "ID non spécifié pour la suppression");
        }
        break;

    default:
        deliver_response(405, "Méthode HTTP non prise en charge");
        break;
}
