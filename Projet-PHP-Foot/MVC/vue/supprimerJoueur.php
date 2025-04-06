<?php
// /vue/supprimerJoueur.php
require_once '../controleur/joueurControleur.php';

$numero_licence = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$numero_licence) {
    header('Location: listeJoueurs.php');
    exit;
}

$result = supprimerJoueur($numero_licence);

if ($result === 'participation_exists') {
    echo "Impossible de supprimer ce joueur car il a participé à des matchs";
    echo "<br><a href='listeJoueurs.php'>Retour à la liste</a>";
} elseif ($result) {
    header('Location: listeJoueurs.php');
} else {
    echo "Erreur lors de la suppression du joueur";
    echo "<br><a href='listeJoueurs.php'>Retour à la liste</a>";
}
?>