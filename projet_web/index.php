<?php

declare(strict_types=1);

require_once 'flight/Flight.php';

define('DB_HOST', 'localhost'); 
define('DB_NAME', 'map'); 
define('DB_PORT', '5433')
define('DB_USER', 'postgres'); 
define('DB_PASS', 'postgres'); 

$connection_string = "host=" . DB_HOST . "port=" . DB_PORT ." dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;
$conn = pg_connect($connection_string);

if (!$conn) {
    exit("Erreur de connexion à la base de données : " . pg_last_error());
} else {

}

session_start();



Flight::set('conn', $conn);

Flight::route('GET /', function() {
    Flight::render('menu');
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

            // Définir un cookie pour une connexion persistante (validité de 1 jour)
            setcookie('user_id', $user['id'], time() + 86400, '/'); // Expire dans 1 jour
            setcookie('pseudo', $user['pseudo'], time() + 86400, '/');

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

Flight::route('POST /save-time', function() use ($conn) {
    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        Flight::json(['error' => 'Utilisateur non connecté.'], 401);
        return;
    }

    $time = $_POST['time'];

    // Convertir le temps en minutes et secondes
    $minutes = floor($time / 60);
    $seconds = floor($time % 60); // Partie entière des secondes
    $tenths = floor(($time - floor($time)) * 10); // Extraire les dixièmes

    // Formater en chaîne "MM:SS.t"
    $formatted_time = sprintf('%02d:%02d.%d', $minutes, $seconds, $tenths);

    // Enregistrer le score dans la base de données
    $query = "UPDATE joueurs SET score = $1 WHERE id = $2";
    pg_query_params($conn, $query, [$formatted_time, $_SESSION['user_id']]);

    // Vérifiez si le nouveau score est meilleur que le highscore actuel
    $query = "SELECT highscore FROM joueurs WHERE id = $1";
    $result = pg_query_params($conn, $query, [$_SESSION['user_id']]);
    $currentHighscoreData = pg_fetch_assoc($result);
    $currentHighscore = $currentHighscoreData['highscore'];

    // Comparer les temps pour savoir si le nouveau score est meilleur
    // Convertir les temps en secondes pour faciliter la comparaison
    $newScoreInSeconds = $minutes * 60 + $seconds + $tenths / 10;

    if ($currentHighscore) {
        // Convertir le highscore existant en secondes
        preg_match('/(\d+):(\d+)\.(\d+)/', $currentHighscore, $matches);
        $currentHighscoreInSeconds = $matches[1] * 60 + $matches[2] + $matches[3] / 10;
        
        // Si le nouveau score est meilleur, on le met à jour
        if ($newScoreInSeconds < $currentHighscoreInSeconds) {
            $query = "UPDATE joueurs SET highscore = $1 WHERE id = $2";
            pg_query_params($conn, $query, [$formatted_time, $_SESSION['user_id']]);
        }
    } else {
        // Si aucun highscore n'existe, on l'ajoute
        $query = "UPDATE joueurs SET highscore = $1 WHERE id = $2";
        pg_query_params($conn, $query, [$formatted_time, $_SESSION['user_id']]);
    }

    // Récupérer les données pour le Hall of Fame
    $query = "SELECT id, pseudo, highscore FROM joueurs WHERE highscore IS NOT NULL ORDER BY highscore ASC LIMIT 10";
    $result = pg_query($conn, $query);
    $hallOfFame = pg_fetch_all($result);

    // Récupérer le score actuel de l'utilisateur
    $queryScoreActuel = "SELECT score FROM joueurs WHERE id = $1";
    $resultScoreActuel = pg_query_params($conn, $queryScoreActuel, [$_SESSION['user_id']]);
    $scoreActuelData = pg_fetch_assoc($resultScoreActuel);
    $dernier_score = $scoreActuelData['score'] ?? null;

    // Rendu de la page de résultats avec le Hall of Fame et le dernier score
    Flight::render('resultat', ['hall_of_fame' => $hallOfFame, 'dernier_score' => $dernier_score]);
});






Flight::route('GET /jeu', function() {
    if (!isset($_SESSION['user_id']) && !isset($_COOKIE['user_id'])) {
        // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
        Flight::redirect('/login');
    } else {
        // Si connecté, afficher le jeu
        Flight::render('jeu');
    }
});



Flight::route('GET /regles', function() {
    Flight::render('regles');
});

Flight::route('GET /login', function() {
    Flight::render('login');
});

Flight::route('GET /menu', function() {
    Flight::render('menu');
});



Flight::route('GET /resultat', function(){
    try {
        // Connexion à la base de données
        $conn = Flight::get('conn');
        
        // Préparer la requête pour récupérer les données du Hall of Fame
        $query = "SELECT id, pseudo, highscore FROM joueurs ORDER BY highscore ASC LIMIT 10"; // Limite les 10 meilleurs scores

        // Exécuter la requête
        $result = pg_query($conn, $query);
        
        if (!$result) {
            throw new Exception('Erreur lors de l\'exécution de la requête : ' . pg_last_error($conn));
        }

        // Récupérer les résultats sous forme de tableau
        $hallOfFame = pg_fetch_all($result);

        // Récupérer le dernier score ajouté (le score actuel)
        $queryScoreActuel = "SELECT highscore FROM joueurs ORDER BY id ASC LIMIT 1"; // On récupère le dernier score ajouté
        $resultScoreActuel = pg_query($conn, $queryScoreActuel);
        
        if (!$resultScoreActuel) {
            throw new Exception('Erreur lors de la récupération du score actuel : ' . pg_last_error($conn));
        }

        $scoreActuelData = pg_fetch_assoc($resultScoreActuel);
        $dernier_score = $scoreActuelData['highscore'] ?? null;

        // Si le Hall of Fame est vide
        if (empty($hallOfFame)) {
            Flight::render('resultat', ['message' => 'Aucun résultat trouvé dans le Hall of Fame.']);
            return;
        }

        // Afficher la page HTML avec les données du Hall of Fame et le dernier score
        Flight::render('resultat', ['hall_of_fame' => $hallOfFame, 'dernier_score' => $dernier_score]);
    } catch (Exception $e) {
        // Gestion des erreurs
        Flight::render('resultat', ['error' => 'Erreur lors de la récupération des données : ' . $e->getMessage()]);
    }
});



Flight::route('/logout', function() {
    // Supprimer la session et les cookies
    session_destroy();
    setcookie('user_id', '', time() - 3600, '/'); // Supprimer le cookie
    setcookie('pseudo', '', time() - 3600, '/'); // Supprimer le cookie
    Flight::redirect('/menu');
});

Flight::route('/objets', function() {
    try {
        // On récupère la connexion depuis Flight
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

        // On éxécute la requête avec les IDs comme paramètres
        $result = pg_query_params($conn, $query, $ids);
        
        if (!$result) {
            throw new Exception(pg_last_error($conn));  // On ajoute un message d'erreur détaillé
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
