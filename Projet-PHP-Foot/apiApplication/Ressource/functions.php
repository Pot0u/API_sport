<?php 
/// Fonction pour envoyer la réponse au client
function deliver_response($status_code, $status_message, $data=null){
    /// Configure le code de statut HTTP avec un message standard associé
    http_response_code($status_code); 
    // Personnalise le message associé au code HTTP
    header("HTTP/1.1 $status_code $status_message"); 
    // Indique au client que la réponse est au format JSON
    header("Content-Type:application/json; charset=utf-8");
    // Configure les en-têtes CORS pour autoriser toutes les origines
    set_cors_headers(); 
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    /// Convertit la réponse en JSON
    $json_response = json_encode($response);
    if($json_response===false)
     die('Erreur encodage JSON : '.json_last_error_msg());
    /// Affiche la réponse (renvoyée au client)
    echo $json_response;
}

// Fonction pour envoyer une erreur au client
function send_error($status_code, $message) {
    deliver_response($status_code, "Erreur: " . $message, null);
}

// Fonction pour définir les en-têtes CORS
function set_cors_headers() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

// Fonction pour vérifier le token d'authentification
function verifierToken() {
    // Vérifie si l'en-tête d'autorisation est présent dans la requête
    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        deliver_response(400, "Requête invalide. Il n'y a pas d'en-tête d'autorisation.");
        exit;
    }

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    $token = str_replace("Bearer ", "", $authHeader);

    $ch = curl_init("https://cabinetherite.alwaysdata.net/PROJCABI/apiAuthentification/api_auth/user_auth_v1.php");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Autorise jusqu'à 10 redirections
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    if ($response === false) {
        deliver_response(500, "Erreur interne du serveur: " . curl_error($ch));
        exit;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Vérifie le code HTTP de la réponse
    if ($httpCode != 200) {
        deliver_response(401, "Non autorisé: Le token est invalide ou expiré.");
        exit;
    }

    return true;
}

?>