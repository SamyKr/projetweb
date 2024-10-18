<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="text-center">Connexion</h2>
                        
                        <!-- Affichage des erreurs -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['error']; ?>
                                <?php unset($_SESSION['error']); // Effacez le message après l'affichage ?>
                            </div>
                        <?php endif; ?>

                        <form action="/login" method="post">
                            <div class="form-group">
                                <label for="mail">Adresse e-mail</label>
                                <input type="email" class="form-control" id="mail" name="mail" placeholder="Adresse e-mail" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                        </form>
                        <div class="text-center mt-3">
                            <p>Pas de compte ?</p>
                            <a href="/register" class="btn btn-secondary">S'inscrire</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
