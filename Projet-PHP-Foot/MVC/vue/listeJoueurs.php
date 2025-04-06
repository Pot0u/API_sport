<?php
require_once '../controleur/joueurControleur.php';
$joueurs = afficherListeJoueurs();

// Get search and filter parameters
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$statut = isset($_GET['statut']) ? htmlspecialchars($_GET['statut']) : '';
include "header.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Joueurs</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>

    <div class="container">
        <h1>Liste des Joueurs</h1>

        <!-- Add Button -->
        <div style="margin: 20px 0;">
            <a href="ajouterJoueur.php" class="button" style="background: #28a745; color: white;">Ajouter un joueur</a>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-box">
            <form method="GET" action="">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Rechercher par nom, prénom ou numéro de licence..."
                    value="<?php echo $search; ?>"
                >
                <select name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="Actif" <?php echo $statut === 'Actif' ? 'selected' : ''; ?>>Actif</option>
                    <option value="Inactif" <?php echo $statut === 'Inactif' ? 'selected' : ''; ?>>Inactif</option>
                    <option value="Blessé" <?php echo $statut === 'Blessé' ? 'selected' : ''; ?>>Blessé</option>
                </select>
                <button type="submit" class="button search-btn">Rechercher</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Numéro de Licence</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Date de Naissance</th>
                    <th>Taille (cm)</th>
                    <th>Poids (kg)</th>
                    <th>Évaluation</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($joueurs): ?>
                    <?php foreach ($joueurs as $joueur): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($joueur['numero_de_licence']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['nom']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['date_naissance']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['taille']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['poids']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['evaluation']); ?></td>
                            <td><?php echo htmlspecialchars($joueur['statut']); ?></td>
                            <td>
                                <a href="modifierJoueur.php?id=<?php echo urlencode($joueur['numero_de_licence']); ?>" 
                                   class="button edit-btn">Modifier</a>
                                <a href="supprimerJoueur.php?id=<?php echo urlencode($joueur['numero_de_licence']); ?>" 
                                   class="button delete-btn"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce joueur ?');">Supprimer</a>
                                <a href="voirNotes.php?id=<?php echo urlencode($joueur['numero_de_licence']); ?>" 
                                   class="button note-btn" style="background: #17a2b8; color: white;">Notes</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">Aucun joueur trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>