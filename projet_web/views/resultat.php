<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hall of Fame</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Hall of Fame</h1>

        <!-- Affichage du dernier score -->
        <?php if (isset($dernier_score) && isset($_SESSION['pseudo'])): ?>
            <div class="alert alert-success">
                <strong>Dernier score :</strong> <?= htmlspecialchars($_SESSION['pseudo']) ?> - <?= htmlspecialchars($dernier_score) ?> secondes
            </div>
        <?php endif; ?>

        <!-- Affichage du Hall of Fame -->
        <div id="hallOfFame" class="mt-4">
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
                            <tr>
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

        <!-- Bouton pour retourner au menu -->
        <div class="text-center mt-4">
            <a href="menu" class="btn btn-primary">Retour au menu</a>
            <!-- Affichage du bouton "Rejouer" uniquement si l'utilisateur est connecté -->
            <?php if (isset($_SESSION['pseudo'])): ?>
                <a href="jeu" class="btn btn-primary">Rejouer</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Optionnel : ajout du script Bootstrap pour les fonctionnalités dynamiques -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
