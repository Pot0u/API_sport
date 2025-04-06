<?php
require_once '../controleur/noteControleur.php';
include "header.php";

$numero_licence = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$numero_licence) {
    header('Location: listeJoueurs.php');
    exit;
}

$result = afficherNotesJoueur($numero_licence);
$joueur = $result['joueur'];
$notes = $result['notes'];

if (!$joueur) {
    header('Location: listeJoueurs.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes du Joueur</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Notes pour <?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></h1>
        
        <div style="margin: 20px 0;">
            <a href="ajouterNote.php?id=<?php echo urlencode($numero_licence); ?>" 
               class="button add-btn">Ajouter une note</a>
        </div>

        <?php if (empty($notes)): ?>
            <div class="alert alert-info">Aucune note n'a été ajoutée pour ce joueur.</div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Note</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($note['Id_note']); ?></td>
                            <td><?php echo htmlspecialchars($note['Commentaire']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="listeJoueurs.php" class="button back-btn">Retour</a>
        </div>
    </div>
</body>
</html>