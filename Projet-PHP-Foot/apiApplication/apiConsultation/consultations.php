<?php
// Inclusion des fichiers nécessaires
require_once "../Ressource/connexionDB.php";
require_once "../Ressource/functions.php";
require_once "fonctionConsultations.php";

// Récupération des données envoyées par le client
$postedData = file_get_contents('php://input');
$data = json_decode($postedData, true);

// Identification de la méthode HTTP utilisée
$http_method = $_SERVER['REQUEST_METHOD'];

// Connexion à la base de donnée
$linkpdo = $conn;

// Vérification du token d'authentification
verifierToken();

// Gestion des différentes méthodes HTTP
switch ($http_method){

    // Traitement des requêtes GET
    case "GET" :
        if (count($_GET) == 0) {
            // Si aucun paramètre GET n'est fourni, effectue une requête GET sans spécifier d'ID
            requeteGET($linkpdo, $id=null);
        }
        // Vérifie si 'id' est le seul paramètre reçu dans l'URL
        else if (isset($_GET['id']) && is_numeric($_GET['id']) && count($_GET) == 1) {
            $id = (int) htmlspecialchars($_GET['id']); // Conversion en entier pour s'assurer de la validité
            requeteGET($linkpdo, $id); // Effectue une requête GET en spécifiant l'ID fourni
        } else {
            // Renvoie une réponse d'erreur 400 si les paramètres de la requête ne sont pas valides
            deliver_response(400, "Requête invalide. Les paramètres valides sont : id ou rien.");
            exit; // Arrête l'exécution du script
        }
        break;

    // Traitement des requêtes POST
    case "POST" :
        if (isValid($data)) {
            // Si les données fournies sont valides, exécute l'opération de création
            $result = requetePOST($linkpdo, $data);
            if ($result) {
                // Renvoie une réponse de succès 201 avec les données créées
                deliver_response(201, "Resource created successfully.", $result);
            } else {
                // Renvoie une réponse d'erreur 500 en cas d'échec de l'opération
                deliver_response(500, "Internal Server Error.");
            }
        } else {
            // Renvoie une réponse d'erreur 400 si les données fournies ne sont pas valides
            deliver_response(400, "Invalid data provided.");
        }
        break;

    // Traitement des requêtes PATCH
    case "PATCH" :
        $donneesMiseAJour = isValidPatch($data); // Valide les données de mise à jour
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $id = (int) htmlspecialchars($_GET['id']); // Obtient l'ID de la ressource à mettre à jour
            requetePATCH($linkpdo, $id, $donneesMiseAJour); // Effectue une requête PATCH pour mettre à jour la ressource spécifiée par l'ID
        } else {
            // Renvoie une réponse d'erreur 400 si l'ID de la ressource est manquant ou invalide
            deliver_response(400, "ID de ressource manquant ou invalide.");
            exit;
        }
        break;

    // Traitement des requêtes DELETE
    case "DELETE" :
        if (isset($_GET['id']) && is_numeric($_GET['id']) && count($_GET) == 1) { 
            $id = (int) htmlspecialchars($_GET['id']); // Conversion en entier pour s'assurer de la validité
            requeteDelete($linkpdo, $id); // Effectue une requête DELETE pour supprimer la ressource spécifiée par l'ID
        } else {
            // Renvoie une réponse d'erreur 400 si l'ID de la ressource est manquant ou invalide
            deliver_response(400, "Requête invalide. Il faut rajouter un id.");
            exit;
        }
        break;

    // Traitement des requêtes OPTIONS
    case "OPTIONS" :
        // Gestion de la requête OPTIONS pour CORS
        set_cors_headers(); // Définit les en-têtes CORS pour permettre les requêtes cross-origin
        deliver_response(204, "CORS preflight request accepted."); // 204 No Content
        break;

    // Gestion des autres cas de requêtes non supportées
    default:
        // Renvoie une réponse d'erreur 501 si la méthode de requête n'est pas reconnue
        deliver_response(501, "Requete non reconnu");
        break;
}

?>