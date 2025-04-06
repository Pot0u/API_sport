<?php
require_once '../controleur/noteControleur.php';
include "header.php";

$numero_licence = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$numero_licence) {
    header('Location: listeJoueurs.php');
    exit;
}

$joueur = getJoueurByLicence($numero_licence);
if (!$joueur) {
    header('Location: listeJoueurs.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = ajouterNouvelleNote();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Note</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter une Note pour <?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="numero_licence" value="<?php echo htmlspecialchars($numero_licence); ?>">
            
            <div class="form-group">
                <label for="commentaire">Commentaire</label>
                <textarea id="commentaire" 
                         name="commentaire" 
                         required 
                         maxlength="50" 
                         rows="3" 
                         class="form-control"></textarea>
            </div>

            <button type="submit" class="button" style="background: #28a745; color: white;">Ajouter</button>
            <a href="voirNotes.php?id=<?php echo urlencode($numero_licence); ?>" 
               class="button" 
               style="background: #6c757d; color: white;">Annuler</a>
        </form>
    </div>
</body>
</html>