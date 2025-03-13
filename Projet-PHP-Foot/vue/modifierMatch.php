<?php
require_once '../controleur/matchControleur.php';
include "header.php";

// Replace deprecated filter with htmlspecialchars
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$id) {
    header('Location: listeMatchs.php');
    exit;
}

$result = modifierMatchExistant($id);
$match = $result['match'];
$errors = $result['errors'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Match</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier un Match</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="modifierMatch.php?id=<?php echo htmlspecialchars($id); ?>">
            <div class="form-group">
                <label for="date_match">Date du match</label>
                <input type="date" 
                       id="date_match" 
                       name="date_match" 
                       required 
                       min="<?php echo date('Y-m-d'); ?>"
                       value="<?php echo htmlspecialchars($match['Date_match']); ?>">
            </div>

            <div class="form-group">
                <label for="heure_match">Heure du match</label>
                <input type="time" 
                       id="heure_match" 
                       name="heure_match" 
                       required
                       value="<?php echo htmlspecialchars($match['Heure_match']); ?>">
            </div>

            <div class="form-group">
                <label for="equipe_adverse">Équipe adverse</label>
                <input type="text" 
                       id="equipe_adverse" 
                       name="equipe_adverse" 
                       required
                       maxlength="50"
                       value="<?php echo htmlspecialchars($match['Nom_equipe_adverse']); ?>">
            </div>

            <div class="form-group">
                <label for="lieu">Lieu</label>
                <select id="lieu" name="lieu" required>
                    <option value="1" <?php echo $match['Domicile_externe'] == 1 ? 'selected' : ''; ?>>Domicile</option>
                    <option value="0" <?php echo $match['Domicile_externe'] == 0 ? 'selected' : ''; ?>>Extérieur</option>
                </select>
            </div>

            <div class="form-group">
                <label for="resultat">Résultat</label>
                <select id="resultat" name="Resultat_match" required>
                    <option value="Non joué" <?php echo ($match['Resultat_match'] == 'Non joué' ? 'selected' : ''); ?>>Non joué</option>
                    <option value="Victoire" <?php echo ($match['Resultat_match'] == 'Victoire' ? 'selected' : ''); ?>>Victoire</option>
                    <option value="Défaite" <?php echo ($match['Resultat_match'] == 'Défaite' ? 'selected' : ''); ?>>Défaite</option>
                    <option value="Nul" <?php echo ($match['Resultat_match'] == 'Nul' ? 'selected' : ''); ?>>Match nul</option>
                </select>
            </div>

            <button type="submit" class="button" style="background: #28a745; color: white;">Modifier le match</button>
            <a href="listeMatchs.php" class="button" style="background: #6c757d; color: white;">Annuler</a>
        </form>
    </div>
</body>
</html>