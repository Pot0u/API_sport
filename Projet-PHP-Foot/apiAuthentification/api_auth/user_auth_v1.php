<?php
require_once 'bd_auth.php';
require_once 'jwt_utils.php';
require_once "functions.php";

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        
        $jwt = get_bearer_token();

        if ($jwt && is_jwt_valid(   $jwt, 'your_secret_key')) {
            $payload = json_decode(base64url_decode(explode('.', $jwt)[1]), true);
            
            deliver_response(200,'Authentification verified.',['payload' => $payload] );
        } else {
            deliver_response(401,'Accès non autorisé. Jeton invalide ou manquant.');
        }
        break;

    case 'POST':
        // Récupération du login et mot de passe depuis les données reçues
        $input = json_decode(file_get_contents('php://input'), true);
        $login = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        // Vérifier l'utilisateur dans la base de données
        $user = verify_user($login, $password);

        if ($user) {
            // L'utilisateur est valide, générer et renvoyer un JWT
            $headers = ['alg' => 'HS256', 'typ' => 'JWT'];
            $payload = [
                'user' => $user['username'],
                'exp' => (time() + 60 * 60) // Expiration après 1 heure
            ];
            $jwt = generate_jwt($headers, $payload, 'your_secret_key'); 

            deliver_response(200,'Authentification successful.',['token' => $jwt] );
        } else {
            http_response_code(401); // Non autorisé
            
            deliver_response(401,'Authentification unsucessfull');
        }
        break;

    default:
        http_response_code(405); // Méthode non autorisée
        
        deliver_response(405,'Méthode non autorisée');
        echo json_encode(['error' => 'Méthode non autorisée']);
        break;
}
