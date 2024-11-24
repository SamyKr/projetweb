<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte</title>
    <link rel="stylesheet" href="assets/general.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
 body{
    background-image: url('data/image/flou_bg.jpg') !important;
}
</style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-5">
                    <div class="card-body">
                        <h2 class="text-center">Créer un compte</h2>
                        
                        <!-- Affichage des erreurs -->
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $_SESSION['error']; ?>
                                <?php unset($_SESSION['error']); // Effacez le message après l'affichage ?>
                            </div>
                        <?php endif; ?>

                        <!-- Affichage des succès -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $_SESSION['success']; ?>
                                <?php unset($_SESSION['success']); // Effacez le message après l'affichage ?>
                            </div>
                        <?php endif; ?>

                        <form action="/register" method="post">
                            <div class="form-group">
                                <label for="pseudo">Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="pseudo" name="pseudo" placeholder="Nom d'utilisateur" required>
                            </div>
                            <div class="form-group">
                                <label for="mail">Adresse e-mail</label>
                                <input type="email" class="form-control" id="mail" name="mail" placeholder="Adresse e-mail" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Déjà un compte ?</p>
                            <a href="/login" class="btn btn-secondary">Se connecter</a>
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
