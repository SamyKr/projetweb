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
    <!-- Titre du jeu avec une icône joystick -->
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
        <h2><i class="bi bi-file-earmark-text"></i> But du jeu</h2>
        <p>
        Bienvenue à Drive to Escape ! Le trophée de F1 a été volé il y a quelques jours. Le gala de remise des prix a lieu aujourd’hui et tu es chargé de ramener le trophée à Jeddah le plus vite possible. Pour commencer ta mission, rends-toi chez le dernier champion du monde de Formule 1.
        </p>
        <h2><i class="bi bi-controller"></i> Comment jouer ?</h2>
        <p>
        Tu vas te balader de popup en popup à travers le monde. N'oublie pas de lire tous les textes. Pour débloquer certains objets, il te faudra sélectionner un objet dans ton inventaire puis cliquer sur 'Débloquer' sur l'objet qui est bloqué. Certains codes peuvent également débloquer un objet, même s'il n'est pas juste à côté. Si jamais tu rencontres des difficultés, tu peux activer le mode triche. Le temps passera deux fois plus vite, mais tu pourras voir où se trouvent les objets à chercher.
Bon jeu !
        </p>
    </div>

    <!-- Groupe de boutons pour démarrer ou rejouer et se déconnecter -->
    <div class="button-group">
        <?php if (isset($_SESSION['pseudo'])): ?>
            <!-- Bouton Rejouer si l'utilisateur est connecté -->
            <a href="/jeu" class="btn btn-primary">Rejouer</a>
        <?php else: ?>
            <!-- Bouton Jouer si l'utilisateur n'est pas encore connecté -->
            <a href="/jeu" class="btn btn-lg btn-secondary">Jouer</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user_id']) || isset($_COOKIE['user_id'])): ?>
            <!-- Bouton de déconnexion si l'utilisateur est connecté -->
            <a href="/logout" class="btn btn-small">Se Déconnecter</a>
        <?php endif; ?>
    </div>

    <!-- Section Hall of Fame -->
    <div id="hallOfFame" class="mt-4">
        <h2 class="text-center"><i class="bi bi-trophy"></i> Hall of Fame</h2>

        <!-- Affichage du dernier score (si disponible) -->
        <?php if (isset($dernier_score) && isset($_SESSION['pseudo'])): ?>
            <div class="alert alert-success text-center">
                <strong>Dernier score :</strong> <?= htmlspecialchars($_SESSION['pseudo']) ?> - <?= htmlspecialchars($dernier_score) ?> secondes
            </div>
        <?php endif; ?>

        <!-- Tableau des scores du Hall of Fame -->
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

    
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
