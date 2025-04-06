<?php
require_once '../MVC/modele/participationModel.php';
require_once '../MVC/config/functions.php';
require_once '../MVC/modele/joueurModel.php';  // Add this line
require_once '../MVC/modele/matchModel.php';    // Add this line too for getMatchById()

$postedData = file_get_contents('php://input');
$data = json_decode($postedData, true);

// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

// Vérification du token d'authentification
verifierToken();

// Define allowed postes
$allowed_postes = ['Attaquant', 'Défenseur', 'Gardien', 'Milieu'];

function hasStartingGoalkeeper($match_id, $current_participation_id = null) {
    $participations = getParticipations($match_id);
    foreach ($participations as $p) {
        // Skip current participation when updating
        if ($current_participation_id && $p['Id_Participation_match'] == $current_participation_id) {
            continue;
        }
        
        // Check if there's already a starting goalkeeper
        if ($p['Poste'] === 'Gardien' && (int)$p['Titulaire'] === 1) {
            return true;
        }
    }
    return false;
}

switch ($http_method) {
    case "GET":
        if (count($_GET) == 0) {
            // Get all participations
            $participations = getParticipations();
            deliver_response(200, "Liste des participations récupérée", $participations);
        } elseif (isset($_GET['id']) && count($_GET) == 1) {
            $id = htmlspecialchars($_GET['id']);
            
            // Check if it's a match ID (starts with 'MATCH')
            if (strpos($id, 'MATCH') === 0) {
                // Get all participations for this match
                $participations = getParticipations($id);
                if ($participations) {
                    deliver_response(200, "Participations trouvées pour le match " . $id, $participations);
                } else {
                    deliver_response(404, "Aucune participation trouvée pour ce match");
                }
            } else {
                // Get a single participation by ID
                $participation = getParticipationById($id);
                if ($participation) {
                    deliver_response(200, "Participation trouvée", $participation);
                } else {
                    deliver_response(404, "Participation non trouvée");
                }
            }
        } else {
            send_error(400, "Requête invalide. Les paramètres valides sont : id ou rien.");
        }
        break;

    case "POST":
        // Validate required fields
        $required_fields = ['numero_licence', 'id_match', 'poste', 'titulaire'];
        $is_valid = true;

        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                send_error(400, "Le champ '$field' est requis");
                $is_valid = false;
            }
        }

        // Validate constraints
        if (!in_array($data['poste'], $allowed_postes)) {
            send_error(400, "Le poste doit être l'un des suivants : " . implode(', ', $allowed_postes));
            $is_valid = false;
        }

        if (!in_array($data['titulaire'], [0, 1])) {
            send_error(400, "Le statut titulaire doit être 0 (Remplaçant) ou 1 (Titulaire)");
            $is_valid = false;
        }

        // Check if player exists and is active
        $joueur = getJoueurParLicence($data['numero_licence']);
        if (!$joueur) {
            send_error(404, "Joueur non trouvé");
            $is_valid = false;
        } elseif ($joueur['statut'] !== 'Actif') {
            send_error(400, "Le joueur doit être actif pour participer à un match");
            $is_valid = false;
        }

        // Check if match exists and is in the future
        $match = getMatchById($data['id_match']);
        if (!$match) {
            send_error(404, "Match non trouvé");
            $is_valid = false;
        } else {
            // Check if match is in the past
            $match_datetime = new DateTime($match['Date_match'] . ' ' . $match['Heure_match']);
            $now = new DateTime();
            if ($match_datetime < $now) {
                send_error(400, "Impossible d'ajouter une participation pour un match passé");
                $is_valid = false;
            }
        }

        // Check if player already participates in this match
        $participations = getParticipations($data['id_match']);
        foreach ($participations as $p) {
            if ($p['numero_de_licence'] === $data['numero_licence']) {
                send_error(400, "Ce joueur participe déjà à ce match");
                $is_valid = false;
                break;
            }
        }

        // Check goalkeeper limit
        if ($data['poste'] === 'Gardien' && $data['titulaire'] === 1) {
            if (hasStartingGoalkeeper($data['id_match'])) {
                send_error(400, "Il y a déjà un gardien titulaire pour ce match");
                $is_valid = false;
            }
        }

        if ($is_valid) {
            $result = ajouterParticipation(
                $data['numero_licence'],
                $data['id_match'],
                $data['poste'],
                $data['titulaire']
            );

            if ($result) {
                // Get the newly created participation
                $new_participation = getParticipationById(genererIdParticipation() - 1);
                deliver_response(201, "Participation créée avec succès", $new_participation);
            } else {
                send_error(500, "Erreur lors de la création de la participation");
            }
        }
        break;

    case "PUT":
        if (!isset($_GET['id'])) {
            send_error(400, "ID de la participation non spécifié");
            break;
        }

        $id_participation = htmlspecialchars($_GET['id']);
        $is_valid = true;

        // Check if participation exists
        $current_participation = getParticipationById($id_participation);
        if (!$current_participation) {
            send_error(404, "Participation non trouvée");
            break;
        }

        // Validate poste if provided
        if (isset($data['poste']) && !in_array($data['poste'], $allowed_postes)) {
            send_error(400, "Le poste doit être l'un des suivants : " . implode(', ', $allowed_postes));
            $is_valid = false;
        }

        // Validate titulaire if provided
        if (isset($data['titulaire']) && !in_array($data['titulaire'], [0, 1])) {
            send_error(400, "Le statut titulaire doit être 0 (Remplaçant) ou 1 (Titulaire)");
            $is_valid = false;
        }

        if ($is_valid) {
            // Check goalkeeper limit when updating
            $new_poste = $data['poste'] ?? $current_participation['Poste'];
            $new_titulaire = isset($data['titulaire']) ? (int)$data['titulaire'] : (int)$current_participation['Titulaire'];

            if ($new_poste === 'Gardien' && $new_titulaire === 1) {
                if (hasStartingGoalkeeper($current_participation['Id_match'], $id_participation)) {
                    send_error(400, "Il y a déjà un gardien titulaire pour ce match");
                    $is_valid = false;
                }
            }
        }

        if ($is_valid) {
            $result = modifierParticipation(
                $id_participation,
                $data['titulaire'] ?? $current_participation['Titulaire'],
                $data['poste'] ?? $current_participation['Poste']
            );

            if ($result) {
                $updated_participation = getParticipationById($id_participation);
                deliver_response(200, "Participation mise à jour avec succès", $updated_participation);
            } else {
                send_error(500, "Erreur lors de la mise à jour de la participation");
            }
        }
        break;

    case "DELETE":
        if (!isset($_GET['id'])) {
            send_error(400, "ID de la participation non spécifié");
            break;
        }

        $id_participation = htmlspecialchars($_GET['id']);

        // Check if participation exists
        $participation = getParticipationById($id_participation);
        if (!$participation) {
            send_error(404, "Participation non trouvée");
            break;
        }

        // Get match details
        $match = getMatchById($participation['Id_match']);
        if (!$match) {
            send_error(404, "Match associé non trouvé");
            break;
        }

        // Check if match is in the past
        $match_datetime = new DateTime($match['Date_match'] . ' ' . $match['Heure_match']);
        $now = new DateTime();
        if ($match_datetime < $now) {
            send_error(400, "Impossible de supprimer une participation pour un match passé");
            break;
        }

        $result = supprimerParticipation($id_participation);

        if ($result) {
            deliver_response(200, "Participation supprimée avec succès");
        } else {
            send_error(500, "Erreur lors de la suppression de la participation");
        }
        break;

    case "OPTIONS":
        set_cors_headers();
        deliver_response(204, "CORS preflight request accepted.");
        break;

    default:
        send_error(405, "Méthode HTTP non autorisée");
        break;
}