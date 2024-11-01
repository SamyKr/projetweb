<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'map');
define('DB_USER', 'postgres');
define('DB_PASS', 'postgres');

$connection_string = "host=" . DB_HOST . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS;
$conn = pg_connect($connection_string);

if (!$conn) {
    echo "Erreur de connexion à la base de données : " . pg_last_error() . "\n";
    exit;
} else {
    echo "Connecté à la base de données avec succès.\n"; // Message de succès
}

$query = "SELECT * FROM objet";
$start_time = microtime(true);
$result = pg_query($conn, $query);
$end_time = microtime(true);

if (!$result) {
    echo "Erreur lors de l'exécution de la requête : " . pg_last_error($conn);
    exit;
}

echo "Temps d'exécution de la requête : " . ($end_time - $start_time) . " secondes<br>";

while ($row = pg_fetch_row($result)) {
    echo "Auteur: $row[2]  E-mail: $row[3]";
    echo "<br />\n";
}

  pg_close($conn);
?>
