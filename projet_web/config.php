<?php
// config.php

// Database connection constants
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'map'); 
define('DB_USER', 'postgres'); 
define('DB_PASS', 'postgres'); 

try {
    
    $dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // Set attributes for error mode and fetch mode
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Optionally, you can set this for emulating prepared statements
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Handle connection errors
    echo 'Connection failed: ' . $e->getMessage();
    exit; // Stop execution if the connection fails
}
?>
