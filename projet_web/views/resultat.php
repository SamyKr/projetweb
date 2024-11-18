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
        <?php if (isset($dernier_score)): ?>
            <div class="alert alert-success">
                <strong>Dernier score : </strong> <?= htmlspecialchars($dernier_score) ?> secondes
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
                            <td colspan="4" class="text-center">Aucun résultat trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
