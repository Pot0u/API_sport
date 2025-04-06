<?php
// Inclusion des fichiers nécessaires
require_once "../Ressource/connexionDB.php";
require_once "../Ressource/functions.php";
require_once "fonctionMedecin.php";

// Récupération des données envoyées dans le corps de la requête
$postedData = file_get_contents('php://input'); // Récupère le contenu du corps de la requête
$data = json_decode($postedData, true); // Décrypte le JSON en tableau associatif

/// Identification du type de méthode HTTP envoyée par le client
$http_method = $_SERVER['REQUEST_METHOD'];

// Connexion à la base de données
$linkpdo = $conn;

// Vérification du token d'authentification
verifierToken();

switch ($http_method){

    case "GET" :

        // Vérification des paramètres de la requête GET
        if (count($_GET) == 0) {
            // Si aucun paramètre n'est présent dans l'URL, exécuter la requête GET sans ID
            requeteGET($linkpdo, $id=null);
        }
        // Vérifie si 'id' est le seul paramètre reçu dans l'URL
        else if (isset($_GET['id']) && is_numeric($_GET['id']) && count($_GET) == 1) {
            $id = (int) htmlspecialchars($_GET['id']); // Conversion en entier pour s'assurer de la validité
            // Si seul l'ID est présent dans l'URL, exécuter la requête GET avec cet ID
            requeteGET($linkpdo, $id);
        } else {
            // Si des paramètres invalides sont présents dans l'URL, renvoyer une erreur
            deliver_response(400, "Requête invalide. Les paramètres valides sont : id ou rien.");
            exit; // Arrête l'exécution du script
        }
        break;

    case "POST" :

         // Valider les données avant de procéder
        if (isValid($data)) {
            // Si les données sont valides, exécuter l'opération de création
            $result = requetePOST($linkpdo, $data);
            if ($result) {
                // Si l'opération réussie, renvoyer un code de succès et des données (si nécessaire)
                deliver_response(201, "Resource created successfully.", $result);
            } else {
                // En cas d'échec de l'opération, renvoyer un code d'erreur
                deliver_response(500, "Internal Server Error.");
            }
        } else {
            // Si les données sont invalides, renvoyer une erreur
            deliver_response(400, "Invalid data provided.");
        }
        break;

    case "PATCH" :

            // Validation des données à mettre à jour
            $donneesMiseAJour = isValidPatch($data);
            
            if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                $id = (int) htmlspecialchars($_GET['id']);
                // Si l'ID est valide, appeler la fonction de mise à jour avec les données validées
                requetePATCH($linkpdo, $id, $donneesMiseAJour);
            } else {
                // Si l'ID est manquant ou invalide, renvoyer une erreur
                deliver_response(400, "ID de ressource manquant ou invalide.");
                exit;
            }
        break;

    case "DELETE" :
        // Vérification de l'ID fourni dans la requête DELETE
        if (isset($_GET['id']) && is_numeric($_GET['id']) && count($_GET) == 1) { 
            $id = (int) htmlspecialchars($_GET['id']); // Conversion en entier pour s'assurer de la validité
            // Si l'ID est valide, exécuter la requête DELETE
            requeteDelete($linkpdo, $id);
        } else {
            // Si l'ID est manquant ou invalide, renvoyer une erreur
            deliver_response(400, "Requête invalide. Il faut rajouter un id.");
            exit;
        }
        break;

    case "OPTIONS" :
        // Gestion de la requête OPTIONS pour CORS
        set_cors_headers();
        // Réponse avec un code 204 No Content
        deliver_response(204, "CORS preflight request accepted.");
        break;
    default:
        // Si la méthode HTTP n'est pas reconnue, renvoyer une erreur
        deliver_response(501, "Requete non reconnu");
        break;
}

?>
