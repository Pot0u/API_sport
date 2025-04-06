<?php
/**
 * Supprime un Medecin de la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant du Medecin à supprimer.
 * @return bool Retourne true si la suppression réussit, sinon false.
 */
function deletes($linkpdo, $id) {
    try {
        // Vérification de l'existence du medecin
        $selectQuery = "SELECT * FROM medecin WHERE id_medecin = :id";
        $stmt = $linkpdo->prepare($selectQuery);
        $stmt->execute(['id' => $id]);
        
        // Si aucun médecin correspondant n'est trouvé, renvoie false
        if ($stmt->rowCount() == 0) {
            // Aucun médecin trouvé avec cet ID
            return false;
        }

        // Si le médecin existe, procède à la suppression
        $queryStr = "DELETE FROM medecin WHERE id_medecin = :id";
        $query = $linkpdo->prepare($queryStr);
        $query->execute(['id' => $id]);

        // Vérifie si la suppression a été effectuée
        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        // En cas d'erreur PDO, envoie une erreur serveur interne
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        return false;
    }
}

/**
 * Effectue une requête DELETE pour supprimer un medecin.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant du medecin à supprimer.
 * @return void
 */
function requeteDELETE($linkpdo, $id) {
    // Tente de supprimer le médecin
    $deleted = deletes($linkpdo, $id);

    if ($deleted) {
        // Suppression réussie
        $status_code = 200; // OK
        $status_message = 'Médecin supprimé avec succès.';
    } else {
        // Échec de la suppression
        $status_code = 404; // Not Found ou 500 Internal Server Error selon l'implémentation
        $status_message = 'Aucun médecin correspondant trouvé ou erreur lors de la suppression.';
    }

    // Renvoie la réponse avec le code de statut et le message appropriés
    deliver_response($status_code, $status_message);
}


/**
 * Récupère les données des medecins depuis la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int|null $id L'identifiant du medecin à récupérer, null pour tous les medecins.
 * @return array Les données des medecins récupérées.
 */
function reads($linkpdo, $id=null) {
    if ($id == null) {
        // Lecture de tous les usagers
        try {
            $queryStr = "SELECT * FROM medecin";
            $query = $linkpdo->prepare($queryStr);
            $query->execute();
        } catch (PDOException $e) {
            // En cas d'erreur PDO, envoie une erreur serveur interne
            send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        }
    } else {
        // Si un ID est spécifié, récupère le médecin correspondant
        try {
            $queryStr = "SELECT * FROM medecin WHERE id_medecin = :id";
            $query = $linkpdo->prepare($queryStr);
            $query->execute([
                'id' => $id
            ]);
        } catch (PDOException $e) {
            // En cas d'erreur PDO, envoie une erreur serveur interne
            send_error('500', 'Erreur de base de données : ' . $e->getMessage());
        }
    }

    // Récupère tous les enregistrements sous forme de tableau associatif
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    return $results; // Retourne le tableau des résultats
}

/**
 * Effectue une requête GET pour récupérer les données des medecins.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int|null $id L'identifiant du medecin à récupérer, null pour tous les medecins.
 * @return void
 */
function requeteGET($linkpdo, $id=null) {
    // Appel de la fonction de lecture des médecins
    $matchingData = reads($linkpdo, $id);

    // Si des données correspondantes sont trouvées, renvoie un code de succès, sinon renvoie un code d'erreur
    if (!empty($matchingData)) {
        $status_code = 200; // OK
        $status_message = 'Success';
    } else {
        $status_code = 404; // Non trouvé
        $status_message = 'No matching data found';
    }

    deliver_response($status_code, $status_message, $matchingData);
}

/**
 * Met à jour les informations d'un medecin dans la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant du medecin à mettre à jour.
 * @param array $data Les données à mettre à jour pour le medecin.
 * @return array|bool Retourne un tableau contenant l'ID et les champs mis à jour en cas de succès, sinon false.
 */
function updates($linkpdo, $id, $data) {
    // Début de la requête UPDATE
    $queryStr = "UPDATE medecin SET ";

    // Initialisation d'un tableau pour les parties de la requête et un autre pour les paramètres
    $setParts = [];
    $queryParams = [];

    // Construction dynamique de la partie SET de la requête
    foreach ($data as $key => $value) {
        $setParts[] = "$key = :$key";
        $queryParams[$key] = $value; // Ajout du paramètre et sa valeur au tableau des paramètres
    }

    // Ajout de l'ID du médecin à mettre à jour dans les paramètres
    $queryParams['id'] = $id;

    // Finalisation de la requête en ajoutant la partie SET et la condition WHERE
    $queryStr .= implode(', ', $setParts) . " WHERE id_medecin = :id";

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
 * Effectue une requête PATCH pour mettre à jour les informations d'un medecin.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param int $id L'identifiant du medecin à mettre à jour.
 * @param array $data Les nouvelles données du medecin.
 * @return array|null Retourne les données du medecin mises à jour s'il y a réussite, sinon null.
 */
function requetePATCH($linkpdo, $id, $data) {
    //Traitement des données
    $matchingData = updates($linkpdo, $id, $data);

    if (!empty($matchingData)) {
        deliver_response(201, 'Médecin modifié avec succès.', $matchingData);  // 201 Created
        return $matchingData;
    }
    // En cas d'échec de la mise à jour
    return null;
}

/**
 * Valide les données pour une requête PATCH.
 *
 * @param array $data Les données à valider.
 * @return array Les données valides à mettre à jour.
 */
function isValidPatch($data){

    // Initialise un tableau pour les données valides à mettre à jour
    $donneesMiseAJour = [];

    // Liste des champs autorisés pour la mise à jour
    $champsAutorises = ['civilite', 'nom', 'prenom'];

    // Validation des champs présents dans la requête
    foreach ($data as $champ => $valeur) {
        if (in_array($champ, $champsAutorises)) {
            // Ajout du champ et de sa nouvelle valeur au tableau des données de mise à jour
            $donneesMiseAJour[$champ] = $valeur;
        } else {
            // Renvoie une erreur si l'un des champs est invalide
            deliver_response(400, "L'un des champs est invalide, champs autorisés : " . implode(', ', $champsAutorises));
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

/**
 * Insère un nouveau medecin dans la base de données.
 *
 * @param PDO $linkpdo L'objet PDO représentant la connexion à la base de données.
 * @param array $data Les données du medecin à insérer.
 * @return array Un tableau contenant l'ID de la nouvelle entrée.
 */
function creates($linkpdo, $data) {
    try {
        // Requête pour insérer un nouveau médecin
        $queryStrPOST = "INSERT INTO medecin (civilite, nom, prenom) VALUES (:civilite, :nom, :prenom)";
        $query = $linkpdo->prepare($queryStrPOST);

        // Exécution de la requête avec les données fournies
        $exec = [
            'civilite' => $data["civilite"],
            'nom' => $data["nom"],
            'prenom' => $data["prenom"]
        ];
        $success = $query->execute($exec);

        // Récupération de l'ID du nouveau médecin inséré
        $newId = $linkpdo->lastInsertId();
        return ['id' => $newId];  // Retourne l'ID de la nouvelle entrée
    } catch (PDOException $e) {
        send_error('500', 'Erreur de base de données : ' . $e->getMessage());
    }
}

/**
 * Traite les requêtes POST pour créer un nouveau médecin.
 *
 * @param PDO $linkpdo Lien vers la base de données.
 * @param array $data Les données du nouveau médecin à insérer.
 * @return array|null Les données du nouveau médecin s'il est créé avec succès, sinon null.
 */
// Fonction pour traiter les requêtes POST de médecin
function requetePOST($linkpdo, $data) {
    // Appel de la fonction de création d'un nouveau médecin
    $matchingData = creates($linkpdo, $data);

    // Si les données du nouveau médecin sont insérées avec succès, renvoie un code de succès et les données du nouveau médecin
    if (!empty($matchingData)) {
        deliver_response(201, 'Nouveau médecin créé avec succès.', $matchingData);  // 201 Created
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
    // Liste des champs autorisés pour la création d'un médecin
    $champsAutorises = ['civilite', 'nom', 'prenom'];

    // Vérifie si les champs fournis sont autorisés
    foreach ($data as $champ => $valeur) {
        if (!in_array($champ, $champsAutorises)) {
            return false;
        }
    }
    return true; // Retourne vrai si les données sont valides, sinon faux
}

?>