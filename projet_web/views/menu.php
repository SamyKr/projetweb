<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../assets/style_menu.css">
    <!-- Lien vers Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<header>
    <h1><i class="bi bi-joystick"></i> Formula 1: Drive to Escape </h1>
</header>

<div class="content">
    <?php if (isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])): ?>
        <!-- Affichage du pseudo et du statut de connexion si l'utilisateur est connecté -->
        <div class="user-info">
            <p>
                <i class="bi bi-person-circle"></i> 
                <strong><?= htmlspecialchars($_SESSION['pseudo'] ?? $_COOKIE['pseudo']) ?></strong> connecté(e).
            </p>
        </div>
    <?php endif; ?>

    <!-- Bloc de texte pour les règles du jeu -->
    <div class="rules-block mt-4">
        <h2><i class="bi bi-file-earmark-text"></i> Règles du jeu</h2>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed tincidunt libero ac quam elementum, non cursus est iaculis. Fusce nec nunc id felis tincidunt convallis. Integer sollicitudin sit amet purus sit amet fringilla. Curabitur pretium tortor et lectus tempus, a lacinia velit dignissim. Aliquam erat volutpat. Nam quis turpis eget orci placerat fermentum. Fusce malesuada urna vel tortor laoreet, a dapibus felis gravida. Ut viverra turpis quis orci dictum vehicula. Donec vel risus euismod, blandit risus vel, vehicula tortor.
        </p>
    </div>

    <div class="button-group">
        <a href="/jeu" class="btn btn-large">Jouer</a>

        <?php if (isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])): ?>
            <!-- Bouton de déconnexion si l'utilisateur est connecté -->
            <a href="/logout" class="btn btn-small">Se Déconnecter</a>
        <?php endif; ?>
    </div>

    <!-- Hall of Fame intégré -->
    <div id="hallOfFame" class="mt-4">
        <h2 class="text-center"><i class="bi bi-trophy"></i> Hall of Fame</h2>

        <!-- Affichage du dernier score -->
        <?php if (isset($dernier_score) && isset($_SESSION['pseudo'])): ?>
            <div class="alert alert-success text-center">
                <strong>Dernier score :</strong> <?= htmlspecialchars($_SESSION['pseudo']) ?> - <?= htmlspecialchars($dernier_score) ?> secondes
            </div>
        <?php endif; ?>

        <!-- Tableau du Hall of Fame -->
        <div class="table-responsive mt-4">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pseudo</th>
                        <th>Highscore</th>
                    </tr>
                </thead>
                <tbody>
                <!-- Boucle pour afficher les données -->
                <?php if (!empty($hall_of_fame)): ?>
                    <?php foreach ($hall_of_fame as $index => $entry): ?>
                        <tr class="<?= (isset($_SESSION['pseudo']) && $_SESSION['pseudo'] == $entry['pseudo']) ? 'highlight-row' : '' ?>">
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($entry['pseudo']) ?></td>
                            <td><?= htmlspecialchars($entry['highscore']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Aucun résultat trouvé</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>
        </div>
    </div>

    <!-- Bouton pour rejouer uniquement si l'utilisateur est connecté -->
    <div class="text-center mt-4">
        <?php if (isset($_SESSION['pseudo'])): ?>
            <a href="/jeu" class="btn btn-primary">Rejouer</a>
        <?php endif; ?>
    </div>
</div>

<!-- Optionnel : ajout du script Bootstrap pour les fonctionnalités dynamiques -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
