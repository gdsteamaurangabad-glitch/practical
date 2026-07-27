options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);
$conn->ssl_set(NULL, NULL, NULL, NULL, NULL);

$success = $conn->real_connect($host, $user, $pass, $dbname, (int)$port, NULL, MYSQLI_CLIENT_SSL);

if (!$success) {
    die("Connection failed: " . mysqli_connect_error());
}
?>