<?php
// vue/inscription.php
require_once '../controleur/inscriptionControleur.php';

// Appelle la fonction du contrôleur pour traiter l'inscription
$message = traiterInscription();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Inscription</title>
</head>
<body>
    <h1>Inscription</h1>
    
    <form method="post" action="">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" id="username" name="username" required>

        <label for="motDePasse">Mot de passe:</label>
        <input type="password" id="motDePasse" name="motDePasse" required>

        <button type="submit">S'inscrire</button>
    </form>

    <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>

    <p>Déjà un compte ? <a href="connexion.php">Connectez-vous ici</a>.</p>
</body>
</html>
