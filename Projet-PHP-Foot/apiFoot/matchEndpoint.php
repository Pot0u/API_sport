<?php
require_once '../MVC/modele/matchModel.php';
require_once '../MVC/config/functions.php';
require_once '../MVC/modele/participationModel.php';

$postedData = file_get_contents('php://input');
$data = json_decode($postedData, true);

// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

// Vérification du token d'authentification
verifierToken();

// Define allowed results
$allowed_results = ['Non joué', 'Victoire', 'Défaite', 'Nul'];

switch ($http_method) {
    case "GET":
        if (count($_GET) == 0) {
            // Get all matches
            $matchs = getAllMatchs();
            deliver_response(200, "Liste des matchs récupérée", $matchs);
        } elseif (isset($_GET['id']) && count($_GET) == 1) {
            // Get a single match by ID
            $id_match = htmlspecialchars($_GET['id']);
            $match = getMatchById($id_match);

            if ($match) {
                deliver_response(200, "Match trouvé", $match);
            } else {
                deliver_response(404, "Match non trouvé");
            }
        } else {
            send_error(400, "Requête invalide. Les paramètres valides sont : id ou rien.");
        }
        break;

    case "POST":
        // Validate required fields
        $required_fields = ['date_match', 'heure_match', 'equipe_adverse', 'lieu'];
        $is_valid = true;

        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                send_error(400, "Le champ '$field' est requis");
                $is_valid = false;
            }
        }

        // Validate constraints
        if (isset($data['equipe_adverse']) && strlen($data['equipe_adverse']) > 50) {
            send_error(400, "Le nom de l'équipe adverse ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }

        // Validate date (not in the past)
        $match_date = new DateTime($data['date_match'] . ' ' . $data['heure_match']);
        $now = new DateTime();
        if ($match_date < $now) {
            send_error(400, "La date du match ne peut pas être dans le passé");
            $is_valid = false;
        }

        // Validate lieu (0 or 1)
        if (!in_array($data['lieu'], [0, 1])) {
            send_error(400, "Le lieu doit être 0 (Extérieur) ou 1 (Domicile)");
            $is_valid = false;
        }

        // Check for existing match on same date and time
        $all_matches = getAllMatchs();
        foreach ($all_matches as $existing_match) {
            $existing_datetime = new DateTime($existing_match['Date_match'] . ' ' . $existing_match['Heure_match']);
            $new_datetime = new DateTime($data['date_match'] . ' ' . $data['heure_match']);
            
            if ($existing_datetime == $new_datetime) {
                send_error(400, "Un match est déjà prévu à cette date et heure");
                $is_valid = false;
                break;
            }
        }

        if ($is_valid) {
            $result = ajouterMatch(
                $data['date_match'],
                $data['heure_match'],
                $data['equipe_adverse'],
                $data['lieu']
            );

            if ($result) {
                deliver_response(201, "Match créé avec succès", $result);
            } else {
                send_error(500, "Erreur lors de la création du match");
            }
        }
        break;

    case "PUT":
        if (!isset($_GET['id'])) {
            send_error(400, "ID du match non spécifié");
            break;
        }

        $id_match = htmlspecialchars($_GET['id']);
        $is_valid = true;

        // Check if match exists
        $current_match = getMatchById($id_match);
        if (!$current_match) {
            send_error(404, "Match non trouvé");
            break;
        }

        // Check if match is in the past
        $match_datetime = new DateTime($current_match['Date_match'] . ' ' . $current_match['Heure_match']);
        $now = new DateTime();
        $is_past_match = $match_datetime < $now;

        // Check if match already has a result other than "Non joué"
        $has_result = $current_match['Resultat_match'] !== 'Non joué';

        if ($is_past_match) {
            // Past match handling
            if ($has_result) {
                send_error(400, "Impossible de modifier un match passé qui a déjà un résultat");
                $is_valid = false;
            } else {
                // Only allow result updates for past matches without result
                if (isset($data['date_match']) || isset($data['heure_match']) || 
                    isset($data['equipe_adverse']) || isset($data['lieu'])) {
                    send_error(400, "Seul le résultat peut être modifié pour un match passé");
                    $is_valid = false;
                }
                if (!isset($data['resultat'])) {
                    send_error(400, "Le résultat est requis pour un match passé");
                    $is_valid = false;
                }
                if (!in_array($data['resultat'], $allowed_results)) {
                    send_error(400, "Le résultat doit être l'un des suivants : " . implode(', ', $allowed_results));
                    $is_valid = false;
                }
                if ($data['resultat'] === 'Non joué') {
                    send_error(400, "Un match passé ne peut pas avoir le statut 'Non joué'");
                    $is_valid = false;
                }
            }
        } else {
            // Future match handling
            if (isset($data['resultat'])) {
                send_error(400, "Impossible de définir le résultat d'un match futur");
                $is_valid = false;
            }

            // Validate date modification for future matches
            if (isset($data['date_match']) || isset($data['heure_match'])) {
                $new_date = isset($data['date_match']) ? $data['date_match'] : $current_match['Date_match'];
                $new_time = isset($data['heure_match']) ? $data['heure_match'] : $current_match['Heure_match'];
                $new_datetime = new DateTime($new_date . ' ' . $new_time);
                
                if ($new_datetime < $now) {
                    send_error(400, "La nouvelle date du match ne peut pas être dans le passé");
                    $is_valid = false;
                }

                // Check for existing match on same date and time
                $all_matches = getAllMatchs();
                foreach ($all_matches as $existing_match) {
                    // Skip current match when checking
                    if ($existing_match['Id_match'] === $id_match) {
                        continue;
                    }
                    
                    $existing_datetime = new DateTime($existing_match['Date_match'] . ' ' . $existing_match['Heure_match']);
                    
                    if ($existing_datetime == $new_datetime) {
                        send_error(400, "Un match est déjà prévu à cette date et heure");
                        $is_valid = false;
                        break;
                    }
                }
            }

            // Validate other fields for future matches
            if (isset($data['equipe_adverse']) && strlen($data['equipe_adverse']) > 50) {
                send_error(400, "Le nom de l'équipe adverse ne doit pas dépasser 50 caractères");
                $is_valid = false;
            }
            if (isset($data['lieu']) && !in_array($data['lieu'], [0, 1])) {
                send_error(400, "Le lieu doit être 0 (Extérieur) ou 1 (Domicile)");
                $is_valid = false;
            }
        }

        if ($is_valid) {
            $result = modifierMatch(
                $id_match,
                $data['date_match'] ?? $current_match['Date_match'],
                $data['heure_match'] ?? $current_match['Heure_match'],
                $data['equipe_adverse'] ?? $current_match['Nom_equipe_adverse'],
                $data['lieu'] ?? $current_match['Domicile_externe'],
                $data['resultat'] ?? $current_match['Resultat_match']
            );

            if ($result) {
                $updated_match = getMatchById($id_match);
                deliver_response(200, "Match mis à jour avec succès", $updated_match);
            } else {
                send_error(500, "Erreur lors de la mise à jour du match");
            }
        }
        break;

    case "DELETE":
        if (!isset($_GET['id'])) {
            send_error(400, "ID du match non spécifié");
            break;
        }

        $id_match = htmlspecialchars($_GET['id']);

        // Check if match exists
        $match = getMatchById($id_match);
        if (!$match) {
            send_error(404, "Match non trouvé");
            break;
        }

        // Check if match is in the past
        $match_datetime = new DateTime($match['Date_match'] . ' ' . $match['Heure_match']);
        $now = new DateTime();
        if ($match_datetime < $now) {
            send_error(400, "Impossible de supprimer un match passé");
            break;
        }

        // Check for existing participations
        $participations = getParticipations($id_match);
        if ($participations && count($participations) > 0) {
            send_error(400, "Impossible de supprimer un match qui a des participations");
            break;
        }

        $result = supprimerMatch($id_match);

        if ($result) {
            deliver_response(200, "Match supprimé avec succès");
        } else {
            send_error(500, "Erreur lors de la suppression du match");
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