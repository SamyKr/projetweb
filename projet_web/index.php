<?php

declare(strict_types=1);

require_once 'flight/Flight.php';

define('DB_HOST', 'localhost'); 
define('DB_NAME', 'map'); 
define('DB_USER', 'postgres'); 
define('DB_PASS', 'postgres'); 

$connection_string = "host=" . DB_HOST . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;
$conn = pg_connect($connection_string);

if (!$conn) {
    exit("Erreur de connexion à la base de données : " . pg_last_error());
} else {

}

session_start();

Flight::set('conn', $conn);

// Example route using global 'conn' service
Flight::route('GET /', function() {
    Flight::render('jeu');
});

Flight::route('/login', function() use ($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $mail = $_POST['mail'];
        $password = $_POST['password'];

        // Access the global 'conn' service
        $result = pg_query_params($conn, "SELECT * FROM joueurs WHERE mail = $1", [$mail]);
        $user = pg_fetch_assoc($result);

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

Flight::route('/register', function() use ($conn) {
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

        $result = pg_query_params($conn, "SELECT * FROM joueurs WHERE mail = $1", [$mail]);
        if (pg_fetch_assoc($result)) {
            $_SESSION['error'] = 'Cette adresse e-mail est déjà utilisée.';
            Flight::redirect('/register');
            return;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = pg_prepare($conn, "insert_joueur", "INSERT INTO joueurs (pseudo, mail, password) VALUES ($1, $2, $3)");

        if ($stmt && pg_execute($conn, "insert_joueur", [$pseudo, $mail, $hashed_password])) {
            $_SESSION['success'] = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            Flight::redirect('/login');
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'inscription. ' . pg_last_error($conn);
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
        // Récupérer la connexion depuis Flight
        $conn = Flight::get('conn');

        // ON RECUPERE LES IDS DEPUIS LA REQUETE GET 
        $ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

        // VERIFICATION SI LE ID EST VIDE
        if (empty($ids)) {
            $query= "SELECT id, nom_objet, ST_X(position) as longitude, ST_Y(position) as latitude, zoom, depart, fin, type, block, ajout, code, description, indice
            FROM objet
            WHERE depart = true";
            $result = pg_query($conn, $query);
            $objets = pg_fetch_all($result);
            Flight::json($objets); // Envoie la réponse et arrête l'exécution
            return;
        }

        // ON PREPARE LA REQUETE SQL AVEC PLACEHOLDERS
        $placeholders = implode(',', array_map(fn($index) => '$' . ($index + 1), array_keys($ids)));
        $query = "SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, depart, fin, type, block, ajout, code, description, indice
                  FROM objet
                  WHERE id IN ($placeholders)";

        // Exécuter la requête avec les IDs comme paramètres
        $result = pg_query_params($conn, $query, $ids);
        
        if (!$result) {
            throw new Exception(pg_last_error($conn));  // Ajoute un message d'erreur détaillé
        }

        $objets = pg_fetch_all($result);

        if (empty($objets)) {
            Flight::json(['message' => 'Aucun objet trouvé.'], 404);
            return;
        } else {
            Flight::json($objets);
            return;
        }
    } catch (Exception $e) {
        Flight::json(['error' => 'Erreur lors de la requête : ' . $e->getMessage()], 500);
    }
});



Flight::start();



?>
