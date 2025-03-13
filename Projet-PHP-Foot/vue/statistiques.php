<?php
require_once '../controleur/statistiquesControleur.php';
include "header.php";

$stats = calculerStatistiques();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <div class="container">
        <h1>Statistiques</h1>
        
        <section>
            <h2>Statistiques des matchs</h2>
            <div class="stats-grid">
                <div class="stat-box">
                    <h3>Victoires</h3>
                    <p><?php echo $stats['pourcentages']['victoires']; ?>%</p>
                    <p>(<?php echo $stats['matchs']['victoires']; ?> matchs)</p>
                </div>
                <div class="stat-box">
                    <h3>Nuls</h3>
                    <p><?php echo $stats['pourcentages']['nuls']; ?>%</p>
                    <p>(<?php echo $stats['matchs']['nuls']; ?> matchs)</p>
                </div>
                <div class="stat-box">
                    <h3>Défaites</h3>
                    <p><?php echo $stats['pourcentages']['defaites']; ?>%</p>
                    <p>(<?php echo $stats['matchs']['defaites']; ?> matchs)</p>
                </div>
            </div>
        </section>

        <section>
            <h2>Statistiques des joueurs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Joueur</th>
                        <th>Statut</th>
                        <th>Poste préféré</th>
                        <th>Titularisations</th>
                        <th>Remplacements</th>
                        <th>Évaluation moyenne</th>
                        <th>% Victoires</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats['joueurs'] as $joueur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['statut']); ?></td>
                        <td><?php echo htmlspecialchars($joueur['poste_prefere']); ?></td>
                        <td><?php echo $joueur['nb_titularisations']; ?></td>
                        <td><?php echo $joueur['nb_remplacements']; ?></td>
                        <td><?php echo $joueur['evaluation_entraineur']; ?>/5</td>
                        <td><?php echo $joueur['pourcentage_victoires']; ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>