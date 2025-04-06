<?php
/**
 * Supprime un usager de la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant de l'usager à supprimer.
 * @return bool Retourne true si la suppression réussit, sinon false.
 */
function deletes($linkpdo, $id) {
    try {
        // Vérification de l'existence de l'usager
        $selectQuery = "SELECT * FROM usager WHERE id_usager = :id";
        $stmt = $linkpdo->prepare($selectQuery);
        $stmt->execute(['id' => $id]);
        
        if ($stmt->rowCount() == 0) {
            // Aucun usager trouvé avec cet ID
            return false;
        }

        // Suppression de l'usager s'il existe
        $queryStr = "DELETE FROM usager WHERE id_usager = :id";
        $query = $linkpdo->prepare($queryStr);
        $query->execute(['id' => $id]);

        // Vérification de la suppression
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        return false;
    }
}

/**
 * Effectue une requête DELETE pour supprimer un usager.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant de l'usager à supprimer.
 * @return void
 */
function requeteDELETE($linkpdo, $id) {
    // Tentative de suppression de l'usager
    $deleted = deletes($linkpdo, $id);

    if ($deleted) {
        // Suppression réussie
        $status_code = 200; // OK
        $status_message = 'Usager successfully deleted.';
    } else {
        // Échec de la suppression
        $status_code = 404; // Not Found ou 500 Internal Server Error selon l'implémentation
        $status_message = 'No matching usager found or error during deletion.';
    }

    deliver_response($status_code, $status_message);
}

/**
 * Récupère les données des usagers depuis la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int|null $id L'identifiant de l'usager à récupérer, null pour tous les usagers.
 * @return array Les données des usagers récupérées.
 */
function reads($linkpdo, $id=null) {
    if ($id == null) {
        try {
            // Lecture de tous les usagers
            $queryStr = "SELECT * FROM usager";
            $query = $linkpdo->prepare($queryStr);
            $query->execute();
        } catch (PDOException $e) {
            send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        }
    } else {
        try {
            // Lecture d'un usager spécifique
            $queryStr = "SELECT * FROM usager WHERE id_usager = :id";
            $query = $linkpdo->prepare($queryStr);
            $query->execute([
                'id' => $id
            ]);
        } catch (PDOException $e) {
            send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        }
    }
    
    // Récupération des résultats sous forme de tableau associatif
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return $results; // Retourne le tableau des résultats
}

/**
 * Effectue une requête GET pour récupérer les données des usagers.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int|null $id L'identifiant de l'usager à récupérer, null pour tous les usagers.
 * @return void
 */
function requeteGET($linkpdo, $id=null) {
    // Appel de la fonction de lecture des usagers
    $matchingData = reads($linkpdo, $id);

    if (!empty($matchingData)) {
        // Données trouvées
        $status_code = 200; // OK
        $status_message = 'Success';
    } else {
        // Aucune donnée trouvée
        $status_code = 404; // Not Found
        $status_message = 'No matching data found';
    }

    deliver_response($status_code, $status_message, $matchingData);
}


/**
 * Met à jour les informations d'un usager dans la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant de l'usager à mettre à jour.
 * @param array $data Les données à mettre à jour pour l'usager.
 * @return array|bool Retourne un tableau contenant l'ID et les champs mis à jour en cas de succès, sinon false.
 */
function updates($linkpdo, $id, $data) {
    // Début de la requête UPDATE
    $queryStr = "UPDATE usager SET ";

    // Initialisation d'un tableau pour les parties de la requête et un autre pour les paramètres
    $setParts = [];
    $queryParams = [];

    // Construction dynamique de la partie SET de la requête
    foreach ($data as $key => $value) {
        $setParts[] = "$key = :$key";
        $queryParams[$key] = $value; // Ajout du paramètre et sa valeur au tableau des paramètres
    }

    // Ajout de l'ID de le usager à mettre à jour dans les paramètres
    $queryParams['id'] = $id;

    // Finalisation de la chaîne de la requête en ajoutant la partie SET et la condition WHERE
    $queryStr .= implode(', ', $setParts) . " WHERE id_usager = :id";

    try {
        $query = $linkpdo->prepare($queryStr);

        // Exécution de la requête avec les paramètres dynamiques
        $success = $query->execute($queryParams);

        if ($success) {
            return ['id' => $id, 'updated_fields' => array_keys($data)]; // Retourne l'ID et les champs mis à jour
        } else {
            return false; // En cas d'échec de la requête
        }
    } catch (PDOException $e) {
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
    }
}

/**
 * Effectue une requête PATCH pour mettre à jour les informations d'un usager.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant de l'usager à mettre à jour.
 * @param array $data Les nouvelles données de l'usager.
 * @return array|null Retourne les données de l'usager mises à jour s'il y a réussite, sinon null.
 */
function requetePATCH($linkpdo, $id, $data) {
    //Appel de la fonction de création d’une phrase
    $matchingData = updates($linkpdo, $id, $data);

    
    if (!empty($matchingData)) {
        deliver_response(201, 'Medecin modifiée avec succès.', $matchingData);  // 201 Created
        return $matchingData;
    }
} 

/**
 * Valide les données pour une requête PATCH.
 *
 * @param array $data Les données à valider.
 * @return array Les données valides à mettre à jour.
 */
function isValidPatch($data){

    // Initialisation d'un tableau pour les données valides à mettre à jour
    $donneesMiseAJour = [];

    $champsAutorises = ['civilite', 'nom', 'prenom', 'sexe', 'adresse', 'code_postal', 'ville', 'date_nais', 'lieu_nais', 'num_secu', 'id_medecin'];

    // Validation des champs présents dans la requête
    foreach ($data as $champ => $valeur) {
        if (in_array($champ, $champsAutorises)) {
            // Ajout du champ et de sa nouvelle valeur au tableau des données de mise à jour
            $donneesMiseAJour[$champ] = $valeur;
        } else {
            deliver_response(400, "L'un des champs est invalide, champs autorisé : ." . $champsAutorises);
            exit;
        }
    }

    if (empty($donneesMiseAJour)) {
        deliver_response(400, "Aucun champ valide fourni pour la mise à jour.");
        exit;
    }

    return $donneesMiseAJour;
}

/**
 * Insère un nouvel usager dans la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param array $data Les données de l'usager à insérer.
 * @return array Un tableau contenant l'ID de la nouvelle entrée.
 */
function creates($linkpdo, $data) {
    try {
        $queryStrPOST = "INSERT INTO usager (civilite, nom, prenom, sexe, adresse, code_postal, ville, date_nais, lieu_nais, num_secu, id_medecin) 
                            VALUES (:civilite, :nom, :prenom, :sexe, :adresse, :code_postal, :ville, :date_nais, :lieu_nais, :num_secu, :id_medecin)";
        $query = $linkpdo->prepare($queryStrPOST);

        // Exécution de la requête
        $exec = [
            'civilite' => $data["civilite"],
            'nom' => $data["nom"],
            'prenom' => $data["prenom"],
            'sexe' => $data["sexe"],
            'adresse' => $data["adresse"],
            'code_postal' => $data["code_postal"],
            'ville' => $data["ville"],
            'date_nais' => $data["date_nais"],
            'lieu_nais' => $data["lieu_nais"],
            'num_secu' => $data["num_secu"],
            'id_medecin' => $data["id_medecin"]
        ];
        $success = $query->execute($exec);

        // Récupération de l'ID de la nouvelle phrase insérée
        $newId = $linkpdo->lastInsertId();
        return ['id' => $newId];  // Retourner l'ID de la nouvelle entrée
    } catch (PDOException $e) {
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
    }
}

/**
 * Envoie une requête POST pour créer un nouvel usager.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param array $data Les données de l'usager à insérer.
 * @return array|null Les données de l'usager créé, ou null si aucune donnée correspondante n'est trouvée.
 */
function requetePOST($linkpdo, $data) {
    // Appel de la fonction de création d'un nouvel usager
    $matchingData = creates($linkpdo, $data);

    if (!empty($matchingData)) {
        deliver_response(201, 'Nouvelle usager créée avec succès.', $matchingData);  // 201 Created
        return $matchingData;
    }
} 

/**
 * Vérifie si les champs fournis sont autorisés.
 *
 * @param array $data Les données à vérifier.
 * @return bool True si les champs sont autorisés, sinon false.
 */
function isValid($data) {
    $champsAutorises = ['civilite', 'nom', 'prenom', 'sexe', 'adresse', 'code_postal', 'ville', 'date_nais', 'lieu_nais', 'num_secu', 'id_medecin'];

    foreach ($data as $champ => $valeur) {
        if (!in_array($champ, $champsAutorises)) {
            return false;
        }
    }
    return true;
}

?>