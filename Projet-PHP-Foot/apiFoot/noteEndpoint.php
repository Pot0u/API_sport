<?php
require_once '../MVC/modele/noteModel.php';
require_once '../MVC/modele/joueurModel.php';
require_once '../MVC/config/functions.php';


$postedData = file_get_contents('php://input');
$data = json_decode($postedData, true);

// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

// Vérification du token d'authentification
verifierToken();

switch ($http_method) {
    case "GET":
        if (!isset($_GET['id'])) {
            send_error(400, "L'ID du joueur (numéro de licence) est requis");
            break;
        }

        $numero_licence = htmlspecialchars($_GET['id']);
        
        // Check if player exists
        $joueur = getJoueurByLicence($numero_licence);
        if (!$joueur) {
            send_error(404, "Joueur non trouvé");
            break;
        }

        // Get notes for this player
        $notes = getNotesJoueur($numero_licence);
        if ($notes) {
            deliver_response(200, "Notes trouvées pour le joueur", $notes);
        } else {
            deliver_response(404, "Aucune note trouvée pour ce joueur");
        }
        break;

    case "POST":
        // Validate required fields
        $required_fields = ['numero_licence', 'commentaire'];
        $is_valid = true;

        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                send_error(400, "Le champ '$field' est requis");
                $is_valid = false;
            }
        }

        // Check if player exists
        $joueur = getJoueurByLicence($data['numero_licence']);
        if (!$joueur) {
            send_error(404, "Joueur non trouvé");
            $is_valid = false;
        }

        // Validate comment length
        if (isset($data['commentaire']) && strlen($data['commentaire']) > 50) {
            send_error(400, "Le commentaire ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }

        if ($is_valid) {
            $result = ajouterNote(
                $data['numero_licence'],
                $data['commentaire']
            );

            if ($result) {
                $new_note = getNoteById($result);
                if ($new_note) {
                    deliver_response(201, "Note créée avec succès", $new_note);
                } else {
                    send_error(500, "La note a été créée mais impossible de la récupérer");
                }
            } else {
                // Check error log for details
                send_error(500, "Erreur lors de la création de la note. Vérifiez les logs pour plus de détails.");
            }
        }
        break;

    case "PUT":
        send_error(405, "La modification des notes n'est pas autorisée");
        break;

    case "DELETE":
        send_error(405, "La suppression des notes n'est pas autorisée");
        break;

    default:
        send_error(405, "Méthode HTTP non autorisée");
        break;
}