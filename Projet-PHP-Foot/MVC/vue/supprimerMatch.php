<?php
require_once '../controleur/matchControleur.php';

$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
if (!$id) {
    header('Location: listeMatchs.php');
    exit;
}

supprimerMatchExistant($id);
?>