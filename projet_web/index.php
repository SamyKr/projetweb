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

Flight::route('GET /objets', function() {
    try {
        $pdo = Flight::get('pdo');

        $idParam = isset($_GET['id']) ? $_GET['id'] : null; // On vérifie si on a demandé un ID spécifique

        if ($idParam !== null) { // On test si l'ID saisi est valide (un entier supérieur à 0)
            if (!is_numeric($idParam) || (int)$idParam <= 0) {
                Flight::json(['error' => 'ID non valide.'], 400);
                return;
            }

            // On récupère les infos de l'ID saisi
            $stmt = $pdo->prepare("SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description, code FROM objet WHERE id = ?");
            $stmt->execute([$idParam]);
            $objet = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($objet === false) {
                Flight::json(['message' => 'Aucun objet trouvé.'], 404);
            } else {
                Flight::json($objet);
            }
        } else {
            // Si aucun ID n'a été saisi alors on renvoi les infos des objets qui permettent d'initialiser le jeu
            $idsDepart = [1, 2, 3];
            $placeholders = rtrim(str_repeat('?,', count($idsDepart)), ','); 
            $stmt = $pdo->prepare("SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description, code FROM objet WHERE id IN ($placeholders)");
            $stmt->execute($idsDepart);
            $objets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($objets)) {
                Flight::json(['message' => 'Aucun objet trouvé.'], 404);
            } else {
                Flight::json($objets);
            }
        }
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur de base de données : ' . $e->getMessage()], 500);
    }
});







Flight::start();

?>
