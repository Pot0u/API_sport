<?php
/**
* Supprime une consultation existante dans la base de données.
*
* @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
* @param int $id L'identifiant de la consultation à supprimer.
*/
// Fonction pour supprimer une consultation existante dans la base de données
function deletes($linkpdo, $id) {
    try {
        // Vérifie d'abord si une consultation correspondant à l'ID fourni existe
        $selectQuery = "SELECT * FROM consultation WHERE id_consult = :id";
        $stmt = $linkpdo->prepare($selectQuery);
        $stmt->execute(['id' => $id]);
        
        // Si aucune consultation correspondante n'est trouvée, renvoie false
        if ($stmt->rowCount() == 0) {
            return false;
        }

        // Supprime la consultation correspondant à l'ID fourni
        $queryStr = "DELETE FROM consultation WHERE id_consult = :id";
        $query = $linkpdo->prepare($queryStr);
        $query->execute(['id' => $id]);

        // Vérifie si la suppression a été effectuée avec succès
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // En cas d'erreur PDO, renvoie une erreur serveur interne
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        return false;
    }
}

// Fonction pour traiter les requêtes DELETE de consultation
function requeteDELETE($linkpdo, $id) {
    // Tente de supprimer la consultation
    $deleted = deletes($linkpdo, $id);

    // Si la suppression réussit, renvoie un code de succès, sinon renvoie un code d'erreur
    if ($deleted) {
        $status_code = 200; // OK
        $status_message = 'Consultation supprimée avec succès.';
    } else {
        $status_code = 404; // Not Found ou 500 Internal Server Error selon le cas
        $status_message = 'Aucune consultation trouvée correspondante ou erreur lors de la suppression.';
    }

    // Renvoie la réponse avec le code de statut et le message appropriés
    deliver_response($status_code, $status_message);
}

// Fonction pour récupérer les données des consultations depuis la base de données
function reads($linkpdo, $id=null) {
    if ($id == null) {
        // Si aucun ID spécifié, récupère toutes les consultations
        try {
            $queryStr = "SELECT * FROM consultation";
            $query = $linkpdo->prepare($queryStr);
            $query->execute();
        } catch (PDOException $e) {
            // En cas d'erreur PDO, renvoie une erreur serveur interne
            send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        }
    } else {
        // Si un ID est spécifié, récupère la consultation correspondante
        try {
            $queryStr = "SELECT * FROM consultation WHERE id_consult = :id";
            $query = $linkpdo->prepare($queryStr);
            $query->execute([
                'id' => $id
            ]);
        } catch (PDOException $e) {
            // En cas d'erreur PDO, renvoie une erreur serveur interne
            send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        }
    }

    // Récupère tous les enregistrements sous forme de tableau associatif
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return $results; // Retourne le tableau des résultats
}

// Fonction pour traiter les requêtes GET de consultation
function requeteGET($linkpdo, $id=null) {
    // Appel de la fonction de lecture des consultations
    $matchingData = reads($linkpdo, $id);

    // Si des données correspondantes sont trouvées, renvoie un code de succès, sinon renvoie un code d'erreur
    if (!empty($matchingData)) {
        $status_code = 200; // OK
        $status_message = 'Succès';
    } else {
        $status_code = 404; // Not Found
        $status_message = 'Aucune donnée correspondante trouvée';
    }

    // Renvoie la réponse avec le code de statut, le message et les données appropriés
    deliver_response($status_code, $status_message, $matchingData);
}

// Fonction pour mettre à jour une consultation existante dans la base de données
function updates($linkpdo, $id, $data) {
    // Début de la requête UPDATE
    $queryStr = "UPDATE consultation SET ";

    // Initialisation de tableaux pour les parties de la requête et les paramètres
    $setParts = [];
    $queryParams = [];

    // Construction dynamique de la partie SET de la requête
    foreach ($data as $key => $value) {
        $setParts[] = "$key = :$key";
        $queryParams[$key] = $value; // Ajout du paramètre et sa valeur au tableau des paramètres
    }

    // Ajout de l'ID de la consultation à mettre à jour dans les paramètres
    $queryParams['id'] = $id;

    // Finalisation de la requête en ajoutant la partie SET et la condition WHERE
    $queryStr .= implode(', ', $setParts) . " WHERE id_consult = :id";

    try {
        $query = $linkpdo->prepare($queryStr);

        // Exécution de la requête avec les paramètres dynamiques
        $success = $query->execute($queryParams);

        // Si la requête est exécutée avec succès, retourne l'ID et les champs mis à jour, sinon retourne false
        if ($success) {
            return ['id' => $id, 'updated_fields' => array_keys($data)];
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // En cas d'erreur PDO, renvoie une erreur serveur interne
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
    }
}

// Fonction pour traiter les requêtes PATCH de consultation
function requetePATCH($linkpdo, $id, $data) {
    // Appel de la fonction de mise à jour de la consultation
    $matchingData = updates($linkpdo, $id, $data);

    // Si les données correspondantes sont trouvées, renvoie un code de succès et les données mises à jour
    if (!empty($matchingData)) {
        deliver_response(201, 'Consultation modifiée avec succès.', $matchingData);
        return $matchingData;
    }
    // En cas d'échec de la mise à jour
    return null;
} 

// Fonction pour valider les données à mettre à jour lors des requêtes PATCH
function isValidPatch($data) {
    // Liste des champs autorisés pour la mise à jour
    $champsAutorises = ['date_consult', 'heure_consult', 'duree_consult','id_medecin','id_usager'];

    // Initialise un tableau pour les données valides à mettre à jour
    $donneesMiseAJour = [];

    // Vérifie les champs présents dans la requête
    foreach ($data as $champ => $valeur) {
        if (in_array($champ, $champsAutorises)) {
            // Ajoute le champ et sa nouvelle valeur au tableau des données de mise à jour
            $donneesMiseAJour[$champ] = $valeur;
        } else {
            // Renvoie une erreur si l'un des champs est invalide
            deliver_response(400, "L'un des champs est invalide, champs autorisé : ." . $champsAutorises);
            exit;
        }
    }

    // Renvoie une erreur si aucun champ valide n'est fourni pour la mise à jour
    if (empty($donneesMiseAJour)) {
        deliver_response(400, "Aucun champ valide fourni pour la mise à jour.");
        exit;
    }

    return $donneesMiseAJour; // Retourne les données valides à mettre à jour
}

// Fonction pour créer une nouvelle consultation dans la base de données
function creates($linkpdo, $data) {
    try {
        // Requête pour insérer une nouvelle consultation
        $queryStrPOST = "INSERT INTO consultation(date_consult, heure_consult, duree_consult,id_medecin,id_usager) VALUES (:date_consult, :heure_consult, :duree_consult, :id_medecin, :id_usager)";
        $query = $linkpdo->prepare($queryStrPOST);

        // Exécution de la requête avec les données fournies
        $exec = [
            'date_consult' => $data["date_consult"],
            'heure_consult' => $data["heure_consult"],
            'duree_consult' => $data["duree_consult"],
            'id_medecin' => $data["id_medecin"],
            'id_usager' => $data["id_usager"],
        ];
        $success = $query->execute($exec);

        // Récupère l'ID de la nouvelle consultation insérée
        $newId = $linkpdo->lastInsertId();
        return ['id' => $newId];  // Retourne l'ID de la nouvelle entrée
    } catch (PDOException $e) {
        // En cas d'erreur PDO, renvoie une erreur serveur interne
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
    }
}

// Fonction pour traiter les requêtes POST de consultation
function requetePOST($linkpdo, $data) {
    // Appel de la fonction de création d'une nouvelle consultation
    $matchingData = creates($linkpdo, $data);

    // Si les données de la nouvelle consultation sont insérées avec succès, renvoie un code de succès et les données de la nouvelle consultation
    if (!empty($matchingData)) {
        deliver_response(201, 'Nouvelle consultation créée avec succès.', $matchingData);  // 201 Created
        return $matchingData;
    }
} 

// Fonction pour valider les données lors des requêtes POST
function isValid($data) {
    // Liste des champs autorisés pour la création d'une consultation
    $champsAutorises = ['date_consult', 'heure_consult', 'duree_consult','id_medecin','id_usager'];

    // Vérifie si les champs fournis sont autorisés
    foreach ($data as $champ => $valeur) {
        if (!in_array($champ, $champsAutorises)) {
            return false;
        }
    }
    return true; // Retourne vrai si les données sont valides, sinon faux
}

?>
