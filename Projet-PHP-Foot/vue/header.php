<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header>
        <nav class="main-nav">
            <div class="nav-logo">
                <h1>Gestion Équipe</h1>
            </div>
            <ul class="nav-links">
                <li><a href="listeJoueurs.php">Liste des Joueurs</a></li>
                <li><a href="listeMatchs.php">Liste des Matchs</a></li>
                <li><a href="listeParticipations.php">Liste des Participations</a></li>
                <li><a href="statistiques.php">Statistiques</a></li>
            </ul>
            <div class="nav-user">
                <a href="deconnexion.php" class="button logout-btn">Déconnexion</a>
            </div>
        </nav>
    </header>

    <style>
    .main-nav {
        background-color: #333;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .nav-logo h1 {
        margin: 0;
        font-size: 1.5rem;
        color: white;
    }

    .nav-links {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 1rem;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .nav-links a:hover {
        background-color: #555;
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .logout-btn {
        background-color: #dc3545;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }
    </style>
</body>
</html>