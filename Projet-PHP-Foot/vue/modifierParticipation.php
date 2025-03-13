<?php
require_once '../controleur/participationControleur.php';
include "header.php";

$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$id) {
    header('Location: listeParticipations.php');
    exit;
}

$result = modifierParticipationExistante($id);
$participation = $result['participation'];
$errors = $result['errors'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une Participation</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier une Participation</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Match: <?php echo htmlspecialchars($participation['Date_match'] . ' - ' . $participation['Nom_equipe_adverse']); ?></label>
            </div>

            <div class="form-group">
                <label>Joueur: <?php echo htmlspecialchars($participation['nom'] . ' ' . $participation['prenom']); ?></label>
            </div>

            <div class="form-group">
                <label for="poste">Poste</label>
                <select id="poste" name="poste" required>
                    <option value="Gardien" <?php echo $participation['Poste'] == 'Gardien' ? 'selected' : ''; ?>>Gardien</option>
                    <option value="Défenseur" <?php echo $participation['Poste'] == 'Défenseur' ? 'selected' : ''; ?>>Défenseur</option>
                    <option value="Milieu" <?php echo $participation['Poste'] == 'Milieu' ? 'selected' : ''; ?>>Milieu</option>
                    <option value="Attaquant" <?php echo $participation['Poste'] == 'Attaquant' ? 'selected' : ''; ?>>Attaquant</option>
                </select>
            </div>

            <div class="form-group">
                <label for="titulaire">Statut</label>
                <select id="titulaire" name="titulaire" required>
                    <option value="1" <?php echo $participation['Titulaire'] == 1 ? 'selected' : ''; ?>>Titulaire</option>
                    <option value="0" <?php echo $participation['Titulaire'] == 0 ? 'selected' : ''; ?>>Remplaçant</option>
                </select>
            </div>

            <button type="submit" class="button" style="background: #28a745; color: white;">Modifier</button>
            <a href="listeParticipations.php" class="button" style="background: #6c757d; color: white;">Annuler</a>
        </form>
    </div>
</body>
</html>