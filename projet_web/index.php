<?php

declare(strict_types=1);

require_once 'flight/Flight.php';

define('DB_HOST', 'localhost'); 
define('DB_NAME', 'map'); 
define('DB_USER', 'postgres'); 
define('DB_PASS', 'postgres'); 

try {
    $dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Set $pdo as a global service with Flight
    Flight::set('pdo', $pdo);

    
} catch (PDOException $e) {
    
    exit;
}

session_start();

// Example route using global 'pdo' service
Flight::route('GET /', function() {
    Flight::render('jeu');
});

Flight::route('/login', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mail = $_POST['mail'];
        $password = $_POST['password'];

        // Access the global 'pdo' service
        $pdo = Flight::get('pdo');
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE mail = ?");
        $stmt->execute([$mail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['pseudo'] = $user['pseudo'];
            Flight::redirect('/jeu');
        } else {
            $_SESSION['error'] = 'Adresse e-mail ou mot de passe incorrect.';
            Flight::redirect('/login');
        }
    } else {
        include 'views/login.php';
    }
});

Flight::route('/register', function() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pseudo = $_POST['pseudo'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas.';
            Flight::redirect('/register');
            return;
        }

        // Access the global 'pdo' service
        $pdo = Flight::get('pdo');
        $stmt = $pdo->prepare("SELECT * FROM joueurs WHERE mail = ?");
        $stmt->execute([$mail]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Cette adresse e-mail est déjà utilisée.';
            Flight::redirect('/register');
            return;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO joueurs (pseudo, mail, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$pseudo, $mail, $hashed_password])) {
                $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                Flight::redirect('/login');
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

Flight::route('GET /logout', function() {
    session_destroy();
    Flight::redirect('/login');
});

Flight::route('/objets', function() {
    try {
        $pdo = Flight::get('pdo');

        // ON RECUPERE LE ID DEPUIS LA REQUETE GET 
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];
        // VERIFICATION SI LE ID EST VIDE
        if (empty($ids)) {
            Flight::json(['error' => 'Aucun ID fourni.'], 400);
            return;
        }

        // ON PREPARE LA REQUETE SQL AVEC PLACEHOLDERS
        $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
        $stmt = $pdo->prepare("SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description, code FROM objet WHERE id IN ($placeholders)");
        // ON LA LANCE
        $stmt->execute($ids);
        $objets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($objets)) {
            Flight::json(['message' => 'Aucun objet trouvé.'], 404);
        } else {
            Flight::json($objets);
        }
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur de base de données : ' . $e->getMessage()], 500);
    }
});





Flight::start();

?>
