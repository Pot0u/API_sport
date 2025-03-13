<?php
require_once '../controleur/participationControleur.php';
include "header.php";

$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$match_id = isset($_GET['match_id']) ? htmlspecialchars($_GET['match_id']) : '';
$matchs = getMatchsDisponibles();
$participations = afficherListeParticipations($match_id, $search);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Participations</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Participations</h1>
        
        <!-- Search and Filter Form -->
        <div class="search-box">
            <form method="GET" action="">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Rechercher un joueur..."
                    value="<?php echo $search; ?>"
                >
                <select name="match_id">
                    <option value="">Tous les matchs</option>
                    <?php foreach ($matchs as $match): ?>
                        <option value="<?php echo htmlspecialchars($match['Id_match']); ?>"
                                <?php echo $match_id == $match['Id_match'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($match['Date_match'] . ' - ' . $match['Nom_equipe_adverse']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button search-btn">Filtrer</button>
                <a href="listeParticipations.php" class="button reset-btn" style="background: #6c757d; color: white;">Réinitialiser</a>
            </form>
        </div>

        <!-- Add Button -->
        <div style="margin: 20px 0;">
            <a href="ajouterParticipation.php" class="button" style="background: #28a745; color: white;">Ajouter une participation</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Date</th>
                    <th>Joueur</th>
                    <th>Poste</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($participations): ?>
                    <?php foreach ($participations as $participation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participation['Nom_equipe_adverse']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($participation['Date_match']))); ?></td>
                            <td><?php echo htmlspecialchars($participation['nom'] . ' ' . $participation['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($participation['Poste']); ?></td>
                            <td><?php echo formatTitulaire($participation['Titulaire']); ?></td>
                            <td>
                                <a href="modifierParticipation.php?id=<?php echo urlencode($participation['Id_Participation_match']); ?>" 
                                   class="button edit-btn">Modifier</a>
                                <a href="supprimerParticipation.php?id=<?php echo urlencode($participation['Id_Participation_match']); ?>" 
                                   class="button delete-btn"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette participation ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Aucune participation trouvée</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>