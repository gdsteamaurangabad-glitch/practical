<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');

$conn = mysqli_init();

// Ensure SSL is enabled
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL); 
$conn->real_connect($host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
