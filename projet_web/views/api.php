<?php
require 'flight/autoload.php'; // Assure-toi que le chemin est correct
include('config.php'); // Inclure le fichier de configuration pour la connexion à la base de données

use Flight;

// Route pour récupérer tous les objets
Flight::route('GET /objets', function() use ($dbconn) {
    // Requête SQL pour récupérer tous les objets
    $sql = "SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description 
            FROM objet";
    $result = pg_query($dbconn, $sql);
    
    if (!$result) {
        Flight::halt(500, json_encode(['error' => 'Erreur lors de l\'exécution de la requête à la base de données.']));
    }

    // Préparer les données pour tous les objets
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

    // Renvoyer tous les objets sous forme de JSON
    Flight::json($objects);
});

// Route pour récupérer un objet spécifique par ID
Flight::route('GET /objets/@id', function($id) use ($dbconn) {
    // Récupérer l'ID
    $id = intval($id);  // Assurer que l'ID soit bien un entier

    // Requête SQL pour récupérer un objet spécifique par ID
    $sql = "SELECT id, nom_objet, ST_X(position) AS longitude, ST_Y(position) AS latitude, zoom, block, description 
            FROM objet WHERE id = $1";
    $result = pg_query_params($dbconn, $sql, array($id));

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        // Préparer les données de l'objet
        $object = [
            'id' => $row['id'],
            'name' => $row['nom_objet'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'zoom' => $row['zoom'],
            'block' => $row['block'],
            'description' => $row['description']
        ];
        // Renvoyer l'objet sous forme de JSON
        Flight::json($object);
    } else {
        // Renvoyer une erreur si l'objet n'est pas trouvé
        Flight::halt(404, json_encode(['error' => 'Objet non trouvé.']));
    }
});

// Démarrer l'application Flight
Flight::start();
?>
