<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../assets/style_menu.css">
    <!-- Lien vers Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<header>
    <h1><i class="bi bi-joystick"></i> Formula 1: Drive to Escape </h1>
</header>

<div class="content">
    <div class="button-group">
        <a href="/jeu" class="btn btn-large">Jouer</a>
        <a href="/regles" class="btn btn-small">Règles</a>
        <a href="/resultat" class="btn btn-small">Hall of Fame</a>

        <?php if (isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])): ?>
            <!-- Bouton de déconnexion si l'utilisateur est connecté -->
            <a href="/logout" class="btn btn-small">Se Déconnecter</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas connecté, il ne voit pas le bouton de déconnexion -->
        <?php endif; ?>
    </div>
</div>

</body>
</html>
