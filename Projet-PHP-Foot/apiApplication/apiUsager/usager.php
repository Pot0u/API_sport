    <?php
    require_once "../Ressource/connexionDB.php";
    require_once "../Ressource/functions.php";
    require_once "fonctionUsager.php";

    $postedData = file_get_contents('php://input'); // Récupère le contenu du corps de la requête
    $data = json_decode($postedData, true); // Décrypte le JSON en tableau associatif

    /// Identification du type de méthode HTTP envoyée par le client
    $http_method = $_SERVER['REQUEST_METHOD'];

    // Connexion à la base de donnée
    $linkpdo = $conn;

    // Vérification du token d'authentification
    verifierToken();

    switch ($http_method){

        case "GET" :

            if (count($_GET) == 0) {
                requeteGET($linkpdo, $id=null);
            }
            // Vérifie si 'id' est le seul paramètre reçu dans l'URL
            else if (isset($_GET['id']) && is_numeric($_GET['id']) && count($_GET) == 1) {
                $id = (int) htmlspecialchars($_GET['id']); // Conversion en entier pour s'assurer de la validité
                requeteGET($linkpdo, $id);

            } else {
                deliver_response(400, "Requête invalide. Les paramètres valides sont : id ou rien.");
                exit; // Arrête l'exécution du script
            }
            break;

        case "POST" :

            // Valider les données ici
            if (isValid($data)) {
                // Exécuter l'opération de création
                $result = requetePOST($linkpdo, $data);
                if ($result) {
                    // Opération réussie, renvoyer un code de succès et des données (si nécessaire)
                    deliver_response(201, "Resource created successfully.", $result);
                } else {
                    // Échec de l'opération, renvoyer un code d'erreur
                    deliver_response(500, "Internal Server Error.");
                }
            } else {
                deliver_response(400, "Invalid data provided.");
            }
            break;

        case "PATCH" :

                $donneesMiseAJour = isValidPatch($data);
                
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $id = (int) htmlspecialchars($_GET['id']);
                    // Appel de la fonction de mise à jour
                    requetePATCH($linkpdo, $id, $donneesMiseAJour);
                } else {
                    deliver_response(400, "ID de ressource manquant ou invalide.");
                    exit;
                }
            break;

        case "DELETE" :
            if (isset($_GET['id']) && is_numeric($_GET['id']) && count($_GET) == 1) { 
                $id = (int) htmlspecialchars($_GET['id']); // Conversion en entier pour s'assurer de la validité
                requeteDelete($linkpdo, $id);
            } else {
                deliver_response(400, "Requête invalide. Il faut rajouter un id.");
                exit;
            }
            break;

        case "OPTIONS" :
            // Gestion de la requête OPTIONS pour CORS
            set_cors_headers();
            deliver_response(204, "CORS preflight request accepted."); // 204 No Content
            break;
        default:
            deliver_response(501, "Requete non reconnu");
            break;
    }

    ?>