<?php
require_once '../controleur/participationControleur.php';

$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$id) {
    header('Location: listeParticipations.php');
    exit;
}

if (supprimerParticipation($id)) {
    header('Location: listeParticipations.php');
} else {
    echo "Erreur lors de la suppression de la participation";
    echo "<br><a href='listeParticipations.php'>Retour à la liste</a>";
}
?>