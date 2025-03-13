<?php
// vue/ajouterJoueur.php
require_once '../controleur/joueurControleur.php';

// Initialize error array
$errors = [];

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Call controller function to handle form submission
    $errors = traiterFormulaireJoueur('ajouter');
}
include "header.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Joueur</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .error-message {
            color: red;
            margin-bottom: 10px;
        }
        .button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn {
            background: #28a745;
            color: white;
        }
        .cancel-btn {
            background: #dc3545;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Ajouter un Joueur</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="numero_licence">Numéro de Licence</label>
                <input type="text" id="numero_licence" name="numero_licence" required maxlength="50" pattern="[A-Za-z0-9]+"
                value="<?php echo isset($_POST['numero_licence']) ? htmlspecialchars($_POST['numero_licence']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required maxlength="50" pattern="[A-Za-zÀ-ÿ\s-]+"
                       value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required maxlength="50" pattern="[A-Za-zÀ-ÿ\s-]+"
                       value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="date_naissance">Date de Naissance</label>
                <input type="date" id="date_naissance" name="date_naissance" required
                       min="1900-01-01" max="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo isset($_POST['date_naissance']) ? htmlspecialchars($_POST['date_naissance']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="taille">Taille (cm)</label>
                <input type="number" id="taille" name="taille" required min="50" max="300"
                       value="<?php echo isset($_POST['taille']) ? htmlspecialchars($_POST['taille']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="poids">Poids (kg)</label>
                <input type="number" id="poids" name="poids" required min="20" max="500"
                       value="<?php echo isset($_POST['poids']) ? htmlspecialchars($_POST['poids']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="evaluation">Évaluation (1-5)</label>
                <select id="evaluation" name="evaluation" required>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo (isset($_POST['evaluation']) && $_POST['evaluation'] == $i) ? 'selected' : ''; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="statut">Statut</label>
                <select id="statut" name="statut" required>
                    <option value="Actif" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'Actif') ? 'selected' : ''; ?>>Actif</option>
                    <option value="Inactif" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'Inactif') ? 'selected' : ''; ?>>Inactif</option>
                    <option value="Blessé" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'Blessé') ? 'selected' : ''; ?>>Blessé</option>
                    <option value="Suspendu" <?php echo (isset($_POST['statut']) && $_POST['statut'] == 'Suspendu') ? 'selected' : ''; ?>>Suspendu</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="button submit-btn">Ajouter le Joueur</button>
                <a href="listeJoueurs.php" class="button cancel-btn">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>