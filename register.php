<?php
require 'config.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')");
            $stmt->execute([$username, $hashed_password]);
            $message = "Registration successful! You can now <a href='index.php'>Login</a>.";
            $success = true;
        } catch (PDOException $e) {
            $message = "Username already taken. Choose another.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Student Portal</title>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 320px; }
        h2 { margin-top: 0; text-align: center; color: #333; }
        input { width: 100%; padding: 10px; margin: 8px 0 16px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .msg { text-align: center; margin-bottom: 15px; color: <?= $success ? 'green' : 'red' ?>; }
        .link { text-align: center; margin-top: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Student Register</h2>
        <?php if ($message): ?><p class="msg"><?= $message ?></p><?php endif; ?>
        <form method="POST">
            <label>Choose User ID / Username</label>
            <input type="text" name="username" required>
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <button type="submit">Create Account</button>
        </form>
        <div class="link">
            Already registered? <a href="index.php">Login Here</a>
        </div>
    </div>
</body>
</html>