<?php
require_once '../modele/joueurModel.php';
require_once '../config/functions.php';

$postedData = file_get_contents('php://input'); // Récupère le contenu du corps de la requête
$data = json_decode($postedData, true); // Décrypte le JSON en tableau associatif

// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

// Vérification du token d'authentification
verifierToken();

// Define allowed statuses
$allowed_statuses = ['Actif', 'Inactif', 'Blessé', 'Suspendu'];

switch ($http_method) {
    case "GET":
        if (count($_GET) == 0) {
            // Get all players
            $joueurs = getJoueurs();
            deliver_response(200, "Liste des joueurs récupérée", $joueurs);
        } elseif (isset($_GET['id']) && count($_GET) == 1) {
            // Get a single player by license number
            $numero_licence = htmlspecialchars($_GET['id']);
            $joueur = getJoueurParLicence($numero_licence);

            if ($joueur) {
                deliver_response(200, "Joueur trouvé", $joueur);
            } else {
                deliver_response(404, "Joueur non trouvé");
            }
        } else {
            send_error(400, "Requête invalide. Les paramètres valides sont : id ou rien.");
        }
        break;

    case "POST":
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        $required_fields = ['numero_de_licence', 'nom', 'prenom', 'date_naissance', 'taille', 'poids', 'evaluation', 'statut'];
        $is_valid = true;

        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                send_error(400, "Le champ '$field' est requis");
                $is_valid = false;
            }
        }

        // Validate constraints
        if (isset($data['numero_de_licence']) && strlen($data['numero_de_licence']) > 50) {
            send_error(400, "Le numéro de licence ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }
        if (isset($data['nom']) && strlen($data['nom']) > 50) {
            send_error(400, "Le nom ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }
        if (isset($data['prenom']) && strlen($data['prenom']) > 50) {
            send_error(400, "Le prénom ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }
        if (isset($data['taille']) && ($data['taille'] < 50 || $data['taille'] > 300)) {
            send_error(400, "La taille doit être comprise entre 50 et 300");
            $is_valid = false;
        }
        if (isset($data['poids']) && ($data['poids'] < 20 || $data['poids'] > 500)) {
            send_error(400, "Le poids doit être compris entre 20 et 500");
            $is_valid = false;
        }
        if (isset($data['evaluation']) && ($data['evaluation'] < 1 || $data['evaluation'] > 5)) {
            send_error(400, "L'évaluation doit être comprise entre 1 et 5");
            $is_valid = false;
        }
        if (!in_array($data['statut'], $allowed_statuses)) {
            send_error(400, "Le statut doit être l'un des suivants : " . implode(', ', $allowed_statuses));
            $is_valid = false;
        }

        // Check if numero_de_licence already exists
        if (getJoueurParLicence($data['numero_de_licence'])) {
            send_error(400, "Un joueur avec ce numéro de licence existe déjà");
            $is_valid = false;
        }

        // Proceed only if all validations pass
        if ($is_valid) {
            $result = ajouterJoueur(
                $data['numero_de_licence'],
                $data['nom'],
                $data['prenom'],
                $data['date_naissance'],
                $data['taille'],
                $data['poids'],
                $data['evaluation'],
                $data['statut']
            );

            if ($result) {
                // Fetch the newly created player data
                $new_player = getJoueurParLicence($data['numero_de_licence']);
                deliver_response(201, "Joueur créé avec succès", $new_player);
            } else {
                send_error(500, "Erreur lors de la création du joueur");
            }
        }
        break;

    case "PUT":
        if (!isset($_GET['id'])) {
            send_error(400, "ID du joueur non spécifié");
            break;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $numero_licence = htmlspecialchars($_GET['id']);
        $is_valid = true;

        // Fetch the current player data
        $current_player = getJoueurParLicence($numero_licence);
        if (!$current_player) {
            send_error(404, "Joueur non trouvé");
            break;
        }

        // Validate constraints
        if (isset($data['numero_de_licence']) && strlen($data['numero_de_licence']) > 50) {
            send_error(400, "Le numéro de licence ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }
        if (isset($data['nom']) && strlen($data['nom']) > 50) {
            send_error(400, "Le nom ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }
        if (isset($data['prenom']) && strlen($data['prenom']) > 50) {
            send_error(400, "Le prénom ne doit pas dépasser 50 caractères");
            $is_valid = false;
        }
        if (isset($data['taille']) && ($data['taille'] < 50 || $data['taille'] > 300)) {
            send_error(400, "La taille doit être comprise entre 50 et 300");
            $is_valid = false;
        }
        if (isset($data['poids']) && ($data['poids'] < 20 || $data['poids'] > 500)) {
            send_error(400, "Le poids doit être compris entre 20 et 500");
            $is_valid = false;
        }
        if (isset($data['evaluation']) && ($data['evaluation'] < 1 || $data['evaluation'] > 5)) {
            send_error(400, "L'évaluation doit être comprise entre 1 et 5");
            $is_valid = false;
        }
        if (isset($data['statut']) && !in_array($data['statut'], $allowed_statuses)) {
            send_error(400, "Le statut doit être l'un des suivants : " . implode(', ', $allowed_statuses));
            $is_valid = false;
        }

        // Proceed only if all validations pass
        if ($is_valid) {
            // Merge the current player data with the new data
            $updated_data = array_merge($current_player, $data);

            $result = modifierJoueur(
                $numero_licence,
                $updated_data['nom'] ?? null,
                $updated_data['prenom'] ?? null,
                $updated_data['date_naissance'] ?? null,
                $updated_data['taille'] ?? null,
                $updated_data['poids'] ?? null,
                $updated_data['evaluation'] ?? null,
                $updated_data['statut'] ?? null
            );

            if ($result) {
                // Fetch the updated player data
                $updated_player = getJoueurParLicence($numero_licence);
                deliver_response(200, "Joueur mis à jour avec succès", $updated_player);
            } else {
                send_error(500, "Erreur lors de la mise à jour du joueur");
            }
        }
        break;

    case "DELETE":
        if (!isset($_GET['id'])) {
            send_error(400, "ID du joueur non spécifié");
            break;
        }

        $numero_licence = htmlspecialchars($_GET['id']);

        if (hasParticipations($numero_licence)) {
            send_error(400, "Impossible de supprimer le joueur car il a des participations existantes");
            break;
        }

        $result = supprimerJoueur($numero_licence);

        if ($result) {
            deliver_response(200, "Joueur supprimé avec succès");
        } else {
            send_error(500, "Erreur lors de la suppression du joueur");
        }
        break;

    case "OPTIONS":
        // Gestion de la requête OPTIONS pour CORS
        set_cors_headers();
        deliver_response(204, "CORS preflight request accepted.");
        break;

    default:
        send_error(405, "Méthode HTTP non autorisée");
        break;
}