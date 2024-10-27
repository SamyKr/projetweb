<?php
require 'flight/autoload.php'; // Assure-toi que le chemin est correct
include('config.php'); // Inclure le fichier de configuration pour la connexion à la base de données

// Gérer les requêtes OPTIONS pour CORS
Flight::route('OPTIONS /*', function() {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
});

header("Access-Control-Allow-Origin: *"); // Autoriser toutes les origines
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Autoriser les méthodes HTTP
header("Access-Control-Allow-Headers: Content-Type"); // Autoriser les en-têtes spécifiques

use Flight;

// Route pour récupérer tous les objets
Flight::route('GET /objets', function() use ($dbconn) {
    $sql = "SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description 
            FROM objet";
    $result = pg_query($dbconn, $sql);
    
    // Vérifie si la requête a échoué
    if (!$result) {
        // Retourne une réponse JSON avec un code d'erreur
        Flight::halt(500, json_encode(['error' => 'Erreur lors de l\'exécution de la requête à la base de données.']));
    }

    $objects = [];
    while ($row = pg_fetch_assoc($result)) {
        $objects[] = [
            'id' => $row['id'],
            'name' => $row['nom_objet'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'zoom' => $row['zoom'],
            'block' => $row['block'],
            'description' => $row['description']
        ];
    }

    // Si aucun objet n'est trouvé, retourne un tableau vide
    if (empty($objects)) {
        Flight::halt(204, json_encode([]));  // Renvoie un code 204 si aucun objet trouvé
    } else {
        Flight::json($objects);
    }
});

// Route pour récupérer un objet spécifique par ID
Flight::route('GET /objets/@id', function($id) use ($dbconn) {
    $id = intval($id);

    $sql = "SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description 
            FROM objet WHERE id = $1";
    $result = pg_query_params($dbconn, $sql, array($id));

    // Vérifie si l'objet existe
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $object = [
            'id' => $row['id'],
            'name' => $row['nom_objet'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'zoom' => $row['zoom'],
            'block' => $row['block'],
            'description' => $row['description']
        ];
        Flight::json($object);
    } else {
        // Retourne une réponse JSON pour un objet non trouvé
        Flight::halt(404, json_encode(['error' => 'Objet non trouvé.']));
    }
});

// Démarrer l'application Flight
Flight::start();
?>
