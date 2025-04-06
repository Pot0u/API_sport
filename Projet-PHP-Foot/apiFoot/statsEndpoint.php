<?php
require_once '../MVC/modele/statistiquesModel.php';
require_once '../MVC/config/functions.php';

$http_method = $_SERVER['REQUEST_METHOD'];

// Vérification du token d'authentification
verifierToken();

switch ($http_method) {
    case "GET":
        if (count($_GET) > 0) {
            send_error(400, "Cette endpoint n'accepte pas de paramètres");
            break;
        }

        // Get match statistics
        $stats_matchs = getStatistiquesMatchs();
        $total_matchs = $stats_matchs['total'];
        
        // Calculate percentages
        $pourcentages = [
            'victoires' => $total_matchs > 0 ? round(($stats_matchs['victoires'] / $total_matchs) * 100, 2) : 0,
            'nuls' => $total_matchs > 0 ? round(($stats_matchs['nuls'] / $total_matchs) * 100, 2) : 0,
            'defaites' => $total_matchs > 0 ? round(($stats_matchs['defaites'] / $total_matchs) * 100, 2) : 0
        ];

        // Get player statistics
        $stats_joueurs = getStatistiquesJoueurs();

        // Format the response
        $statistics = [
            'matchs' => [
                'total' => $stats_matchs['total'],
                'details' => [
                    'victoires' => [
                        'nombre' => $stats_matchs['victoires'],
                        'pourcentage' => $pourcentages['victoires']
                    ],
                    'nuls' => [
                        'nombre' => $stats_matchs['nuls'],
                        'pourcentage' => $pourcentages['nuls']
                    ],
                    'defaites' => [
                        'nombre' => $stats_matchs['defaites'],
                        'pourcentage' => $pourcentages['defaites']
                    ]
                ]
            ],
            'joueurs' => array_map(function($joueur) {
                return [
                    'nom' => $joueur['nom'],
                    'prenom' => $joueur['prenom'],
                    'statut' => $joueur['statut'],
                    'poste_prefere' => $joueur['poste_prefere'],
                    'selections' => [
                        'titulaire' => $joueur['nb_titularisations'],
                        'remplacant' => $joueur['nb_remplacements'],
                        'total' => $joueur['nb_titularisations'] + $joueur['nb_remplacements']
                    ],
                    'evaluation_entraineur' => $joueur['evaluation_entraineur'],
                    'pourcentage_victoires' => $joueur['pourcentage_victoires']
                ];
            }, $stats_joueurs)
        ];

        deliver_response(200, "Statistiques récupérées avec succès", $statistics);
        break;

    case "POST":
    case "PUT":
    case "DELETE":
        send_error(405, "Méthode non autorisée");
        break;

    default:
        send_error(405, "Méthode HTTP non autorisée");
        break;
}