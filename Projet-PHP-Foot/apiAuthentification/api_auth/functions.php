<?php

function deliver_response($status_code, $status_message, $data=null){
    // Configuration de l'en-tête de réponse HTTP avec le code de statut
    http_response_code($status_code);
    // Définition du type de contenu de la réponse en JSON
    header("Content-Type:application/json; charset=utf-8");
    // Configuration des en-têtes CORS pour autoriser les requêtes cross-origin
    set_cors_headers();

    // Construction de la structure de réponse
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;

    // Conversion de la réponse en format JSON
    $json_response = json_encode($response);
    // Vérification d'erreurs lors de la conversion en JSON
    if($json_response === false)
        die('Erreur de conversion JSON : '.json_last_error_msg());

    // Envoi de la réponse JSON au client
    echo $json_response;
}

/**
 * Configure les en-têtes CORS pour les réponses HTTP, autorisant les requêtes cross-origin.
 */
function set_cors_headers() {
    // Autorise toutes les origines avec le joker "*"
    header("Access-Control-Allow-Origin: *");
    // Spécifie les méthodes HTTP autorisées pour les requêtes cross-origin
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    // Indique les en-têtes autorisés dans les requêtes cross-origin
    header("Access-Control-Allow-Headers: Content-Type");
}