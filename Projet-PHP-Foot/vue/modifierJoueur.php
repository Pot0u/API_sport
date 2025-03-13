<!-- vue/modifierJoueur.php -->
<?php
require_once '../controleur/joueurControleur.php';

// Replace deprecated filter with htmlspecialchars
$numero_licence = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$numero_licence) {
    header('Location: listeJoueurs.php');
    exit;
}

$result = modifierJoueurExistant($numero_licence);
$joueur = $result['joueur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    // Redirect to avoid form resubmission
    header('Location: listeJoueurs.php');
    exit;
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Joueur</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="container mt-4">
    <h2>Modifier un joueur</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="modifierJoueur.php?id=<?php echo htmlspecialchars($joueur['numero_de_licence']); ?>">
        <div class="form-group">
            <label>Numéro de licence</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($joueur['numero_de_licence']); ?>" disabled>
        </div>
        
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($joueur['nom'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($joueur['prenom'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="date_naissance">Date de naissance</label>
            <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($joueur['date_naissance'] ?? ''); ?>" max='9999-12-31' required>
        </div>
        
        <div class="form-group">
            <label for="taille">Taille (cm)</label>
            <input type="number" class="form-control" id="taille" name="taille" value="<?php echo htmlspecialchars($joueur['taille'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="poids">Poids (kg)</label>
            <input type="number" class="form-control" id="poids" name="poids" value="<?php echo htmlspecialchars($joueur['poids'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="evaluation">Évaluation</label>
            <select class="form-control" id="evaluation" name="evaluation" required>
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo (isset($joueur['evaluation']) && $joueur['evaluation'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="statut">Statut</label>
            <select class="form-control" id="statut" name="statut" required>
                <?php $statuts = ['Actif', 'Inactif', 'Blessé', 'Suspendu']; ?>
                <?php foreach($statuts as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo (isset($joueur['statut']) && $joueur['statut'] == $s) ? 'selected' : ''; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Modifier</button>
        <a href="listeJoueurs.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>
</body>
</html>