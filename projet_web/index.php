<?php

declare(strict_types=1);

require_once 'flight/Flight.php';
require 'config.php'; // Include your database configuration
session_start();

Flight::route('GET /', function() use ($pdo) { // Use $pdo in the route

    Flight::render('jeu');  
});

Flight::route('/login', function() use ($pdo) {
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mail = $_POST['mail'];
        $password = $_POST['password'];

        // Rechercher l'utilisateur par mail
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE mail = ?");
        $stmt->execute([$mail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Authentification réussie
            $_SESSION['user_id'] = $user['id']; // Stocker l'ID de l'utilisateur dans la session
            $_SESSION['pseudo'] = $user['pseudo']; // Stocker le pseudo dans la session
            Flight::redirect('/jeu'); // Rediriger vers la page de bienvenue
        } else {
            // Échec de la connexion
            $_SESSION['error'] = 'Adresse e-mail ou mot de passe incorrect.';
            Flight::redirect('/login');
        }
    } else {
        include 'views/login.php'; // Afficher le formulaire
    }
});



Flight::route('/register', function() use ($pdo) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pseudo = $_POST['pseudo'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Vérifier si les mots de passe correspondent
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
            Flight::redirect('/register'); // Redirige vers le formulaire d'inscription
            return;
        }

        // Vérifiez si l'adresse e-mail existe déjà
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE mail = ?");
        $stmt->execute([$mail]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Cette adresse e-mail est déjà utilisée.';
            Flight::redirect('/register'); // Redirige vers le formulaire d'inscription
            return;
        }

        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        try {
            $stmt = $pdo->prepare("INSERT INTO joueurs (pseudo, mail, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$pseudo, $mail, $hashed_password])) {
                $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                Flight::redirect('/login'); // Redirige vers la page de connexion
            } else {
                $_SESSION['error'] = 'Erreur lors de l\'inscription. ' . implode(', ', $stmt->errorInfo());
                Flight::redirect('/register'); 
            }

        } catch (PDOException $e) {
            $_SESSION['error'] = 'Erreur lors de l\'insertion : ' . $e->getMessage();
            Flight::redirect('/register'); 
        }
    } else {
        include 'views/register.php'; 
    }
});




Flight::route('GET /jeu', function() {
    Flight::render('jeu');  
});

Flight::route('GET /map', function() {
    Flight::render('map');
});

Flight::start();

?>
