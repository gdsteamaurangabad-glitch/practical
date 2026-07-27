<?php
$host   = getenv('DB_HOST');
$user   = getenv('DB_USER');
$pass   = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port   = getenv('DB_PORT');

$conn = mysqli_init();

// Required SSL settings for Aiven
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

$success = $conn->real_connect($host, $user, $pass, $dbname, (int)$port, NULL, MYSQLI_CLIENT_SSL);

if (!$success) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
