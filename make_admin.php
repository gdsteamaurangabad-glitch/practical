<?php
require 'config.php';

// The admin details you want to set:
$admin_user = 'admin';
$admin_pass = '808722';

// Hash the password properly using your server's native algorithm
$hashed_password = password_hash($admin_pass, PASSWORD_DEFAULT);

try {
    // Delete old admin if exists
    $pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$admin_user]);

    // Insert new admin account
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
    $stmt->execute([$admin_user, $hashed_password]);

    echo "<h2 style='color:green;'>SUCCESS! Admin account created/reset.</h2>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> admin123</p>";
    echo "<a href='index.php'>Go to Login Page</a>";
} catch (PDOException $e) {
    echo "<h2 style='color:red;'>ERROR: " . $e->getMessage() . "</h2>";
}
?>