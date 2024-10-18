<?php

declare(strict_types=1);

require_once 'flight/Flight.php';
// require 'flight/autoload.php';


session_start(); // Démarre la session

Flight::route('GET /', function() {
    Flight::render('menu.php');  // Page par défaut
});


Flight::route('GET /regles', function() {
    Flight::render('regles');  
});

Flight::route('GET /jeu', function() {
    Flight::render('jeu');
});


Flight::route('GET /map', function() {
    Flight::render('map');
});




Flight::start();

?>