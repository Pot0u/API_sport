<?php
require_once '../controleur/participationControleur.php';
include "header.php";

$joueurs = getJoueursDisponibles();
$matchs = getMatchsDisponibles();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = ajouterNouvelleParticipation();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Participation</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter une Participation</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="joueur">Joueur</label>
                <select id="joueur" name="numero_licence" required>
                    <option value="">Sélectionner un joueur</option>
                    <?php foreach ($joueurs as $joueur): ?>
                        <option value="<?php echo htmlspecialchars($joueur['numero_de_licence']); ?>">
                            <?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="match">Match</label>
                <select id="match" name="id_match" required>
                    <option value="">Sélectionner un match</option>
                    <?php foreach ($matchs as $match): ?>
                        <option value="<?php echo htmlspecialchars($match['Id_match']); ?>">
                            <?php echo htmlspecialchars($match['Date_match'] . ' - ' . $match['Nom_equipe_adverse']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="poste">Poste</label>
                <select id="poste" name="poste" required>
                    <option value="Gardien">Gardien</option>
                    <option value="Défenseur">Défenseur</option>
                    <option value="Milieu">Milieu</option>
                    <option value="Attaquant">Attaquant</option>
                </select>
            </div>

            <div class="form-group">
                <label for="titulaire">Statut</label>
                <select id="titulaire" name="titulaire" required>
                    <option value="1">Titulaire</option>
                    <option value="0">Remplaçant</option>
                </select>
            </div>

            <button type="submit" class="button" style="background: #28a745; color: white;">Ajouter</button>
            <a href="listeParticipations.php" class="button" style="background: #6c757d; color: white;">Annuler</a>
        </form>
    </div>
</body>
</html>