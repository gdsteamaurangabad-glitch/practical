<?php
require 'config.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$admin_msg = '';

// Handle Adding New Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_admin') {
    $new_username = trim($_POST['new_admin_username']);
    $new_password = $_POST['new_admin_password'];

    if (!empty($new_username) && !empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'admin')");
            $stmt->execute([$new_username, $hashed_password]);
            $admin_msg = "<span style='color:green;'>✓ New admin account created!</span>";
        } catch (PDOException $e) {
            $admin_msg = "<span style='color:red;'>✗ Admin username already exists.</span>";
        }
    }
}

// Fetch All Submissions
$stmt = $pdo->query("SELECT * FROM submissions ORDER BY submitted_at DESC");
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Counts
$total = count($submissions);
$approved = 0; $rejected = 0; $pending = 0;
foreach ($submissions as $s) {
    if ($s['status'] === 'Approved') $approved++;
    elseif ($s['status'] === 'Rejected') $rejected++;
    else $pending++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .stats { display: flex; gap: 15px; margin: 20px 0; }
        .stat-card { background: white; padding: 15px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center; }
        .stat-card h3 { margin: 0; font-size: 24px; }
        .card { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; text-decoration: none; }
        .btn-approve { background: #28a745; }
        .btn-reject { background: #dc3545; }
        .btn-logout { background: #6c757d; }
        .status-Approved { color: green; font-weight: bold; }
        .status-Rejected { color: red; font-weight: bold; }
        .status-Pending { color: orange; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Admin Management Portal</h2>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>

    <!-- Summary Stats -->
    <div class="stats">
        <div class="stat-card"><h3 style="color:#007bff;"><?= $total ?></h3><p>Total Submissions</p></div>
        <div class="stat-card"><h3 style="color:green;"><?= $approved ?></h3><p>Approved</p></div>
        <div class="stat-card"><h3 style="color:red;"><?= $rejected ?></h3><p>Rejected</p></div>
        <div class="stat-card"><h3 style="color:orange;"><?= $pending ?></h3><p>Pending</p></div>
    </div>

    <!-- Add Admin Section -->
    <div class="card">
        <h3>➕ Add New Admin</h3>
        <?php if ($admin_msg): ?><p><?= $admin_msg ?></p><?php endif; ?>
        <form method="POST" style="display: flex; gap: 15px; align-items: flex-end;">
            <input type="hidden" name="action" value="add_admin">
            <div>
                <label>Username</label><br>
                <input type="text" name="new_admin_username" required style="padding:8px;">
            </div>
            <div>
                <label>Password</label><br>
                <input type="password" name="new_admin_password" required style="padding:8px;">
            </div>
            <button type="submit" class="btn" style="background:#007bff; padding: 9px 15px;">Create Admin</button>
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="card">
        <h3>📋 All Student Submissions</h3>
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Roll No</th>
                    <th>Practical Name</th>
                    <th>Submitted File</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($submissions) > 0): ?>
                    <?php foreach ($submissions as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['student_name']) ?></td>
                        <td><?= htmlspecialchars($s['class_name']) ?></td>
                        <td><?= htmlspecialchars($s['roll_no']) ?></td>
                        <td><?= htmlspecialchars($s['practical_name']) ?></td>
                        <td><a href="<?= htmlspecialchars($s['file_path']) ?>" target="_blank">📁 View File</a></td>
                        <td><span class="status-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                        <td>
                            <form action="action.php" method="POST" style="display:inline;">
                                <input type="hidden" name="submission_id" value="<?= $s['id'] ?>">
                                <button type="submit" name="status" value="Approved" class="btn btn-approve">Approve</button>
                                <button type="submit" name="status" value="Rejected" class="btn btn-reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align: center;">No student submissions found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>