<?php
// vue/connexion.php
require_once '../controleur/connexionControleur.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../vue/css/style.css">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <div class="container">
        <form method="post" action="">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required <?php echo $encadrerUsername; ?>>

            <label for="motDePasse">Mot de passe:</label>
            <input type="password" id="motDePasse" name="motDePasse" required <?php echo $encadrerMotDePasse; ?>>

            <button type="submit">Se connecter</button>
        </form>

        <p style="color: red;"><?php echo $messageErreur; ?></p>

    </div>
</body>
</html>
