<?php
require_once '../controleur/matchControleur.php';
$matchs = afficherListeMatchs();
include "header.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Matchs</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Liste des Matchs</h1>
        
        <div style="margin: 20px 0;">
            <a href="ajouterMatch.php" class="button" style="background: #28a745; color: white;">Ajouter un match</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID Match</th>
                    <th>Date</th>
                    <th>Heure</th>
                    <th>Équipe adverse</th>
                    <th>Lieu</th>
                    <th>Résultat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($matchs): ?>
                    <?php foreach ($matchs as $match): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($match['Id_match']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($match['Date_match']))); ?></td>
                            <td><?php echo htmlspecialchars(date('H:i', strtotime($match['Heure_match']))); ?></td>
                            <td><?php echo htmlspecialchars($match['Nom_equipe_adverse']); ?></td>
                            <td><?php echo formatLieuMatch($match['Domicile_externe']); ?></td>
                            <td><?php echo formatResultat($match['Resultat_match']); ?></td>
                            <td>
                                <?php
                                $match_datetime = new DateTime($match['Date_match'] . ' ' . $match['Heure_match']);
                                $now = new DateTime();
                                if ($match_datetime > $now): 
                                ?>
                                    <a href="modifierMatch.php?id=<?php echo urlencode($match['Id_match']); ?>" 
                                       class="button edit-btn">Modifier</a>
                                    <a href="supprimerMatch.php?id=<?php echo urlencode($match['Id_match']); ?>" 
                                       class="button delete-btn"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce match ?');">Supprimer</a>
                                <?php elseif ($match['Resultat_match'] === 'Non joué'): ?>
                                    <a href="modifierMatch.php?id=<?php echo urlencode($match['Id_match']); ?>" 
                                       class="button edit-btn">Modifier résultat</a>
                                <?php else: ?>
                                    <span class="text-muted">Match passé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Aucun match trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>