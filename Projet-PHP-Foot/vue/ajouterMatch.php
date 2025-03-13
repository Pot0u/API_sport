<?php
require_once '../controleur/matchControleur.php';
include "header.php";

$errors = ajouterNouveauMatch();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Match</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un Match</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="date_match">Date du match</label>
                <input type="date" 
                       id="date_match" 
                       name="date_match" 
                       required 
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo isset($_POST['date_match']) ? htmlspecialchars($_POST['date_match']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="heure_match">Heure du match</label>
                <input type="time" 
                       id="heure_match" 
                       name="heure_match" 
                       required
                       value="<?php echo isset($_POST['heure_match']) ? htmlspecialchars($_POST['heure_match']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="equipe_adverse">Équipe adverse</label>
                <input type="text" 
                       id="equipe_adverse" 
                       name="equipe_adverse" 
                       required
                       maxlength="50"
                       value="<?php echo isset($_POST['equipe_adverse']) ? htmlspecialchars($_POST['equipe_adverse']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="lieu">Lieu</label>
                <select id="lieu" name="lieu" required>
                    <option value="1" <?php echo (isset($_POST['lieu']) && $_POST['lieu'] == '1') ? 'selected' : ''; ?>>Domicile</option>
                    <option value="0" <?php echo (isset($_POST['lieu']) && $_POST['lieu'] == '0') ? 'selected' : ''; ?>>Extérieur</option>
                </select>
            </div>

            <button type="submit" class="button" style="background: #28a745; color: white;">Ajouter le match</button>
            <a href="listeMatchs.php" class="button" style="background: #6c757d; color: white;">Annuler</a>
        </form>
    </div>
</body>
</html>