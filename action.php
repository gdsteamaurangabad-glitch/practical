<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $submission_id = $_POST['submission_id'];
    $status = $_POST['status'];

    if (in_array($status, ['Approved', 'Rejected'])) {
        $stmt = $pdo->prepare("UPDATE submissions SET status = ? WHERE id = ?");
        $stmt->execute([$status, $submission_id]);
    }
}

header('Location: admin_dashboard.php');
exit;
?>