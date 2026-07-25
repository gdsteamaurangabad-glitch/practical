<?php
require 'config.php';

// Auth Guard
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$total_required_practicals = 10; // Change target total as needed
$upload_msg = '';

// Handle Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['practical_file'])) {
    $student_name   = trim($_POST['student_name']);
    $class_name     = trim($_POST['class_name']);
    $roll_no        = trim($_POST['roll_no']);
    $practical_name = trim($_POST['practical_name']);

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_basename = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", basename($_FILES["practical_file"]["name"]));
    $target_file = $target_dir . $file_basename;

    if (move_uploaded_file($_FILES["practical_file"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO submissions (user_id, student_name, class_name, roll_no, practical_name, file_path) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $student_name, $class_name, $roll_no, $practical_name, $target_file]);
        $upload_msg = "<span style='color:green;'>✓ Work submitted successfully!</span>";
    } else {
        $upload_msg = "<span style='color:red;'>✗ File upload failed. Try again.</span>";
    }
}

// Fetch Student Submissions
$stmt = $pdo->prepare("SELECT * FROM submissions WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->execute([$user_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate Completion Percentage
$approved_count = 0;
foreach ($submissions as $sub) {
    if ($sub['status'] === 'Approved') $approved_count++;
}
$completion_percentage = min(100, round(($approved_count / $total_required_practicals) * 100));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 20px; color: #333; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .progress-bar-bg { background: #e0e0e0; border-radius: 10px; height: 24px; width: 100%; overflow: hidden; margin: 10px 0; }
        .progress-bar-fill { background: #28a745; height: 100%; text-align: center; color: white; font-weight: bold; line-height: 24px; transition: width 0.4s; }
        input, select { width: 100%; padding: 8px; margin: 6px 0 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #007bff; color: white; }
        .status-Approved { color: green; font-weight: bold; }
        .status-Rejected { color: red; font-weight: bold; }
        .status-Pending { color: orange; font-weight: bold; }
        .btn-logout { background: #6c757d; text-decoration: none; padding: 8px 15px; color: white; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Student Dashboard</h2>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <!-- Progress Tracker -->
    <div class="card">
        <h3>Work Completion: <?= $completion_percentage ?>%</h3>
        <div class="progress-bar-bg">
            <div class="progress-bar-fill" style="width: <?= $completion_percentage ?>%;">
                <?= $completion_percentage ?>%
            </div>
        </div>
        <p><strong><?= $approved_count ?></strong> of <strong><?= $total_required_practicals ?></strong> required practicals approved.</p>
    </div>

    <!-- Submission Form -->
    <div class="card">
        <h3>Submit Practical Work</h3>
        <?php if ($upload_msg): ?><p><?= $upload_msg ?></p><?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <label>Student Full Name</label>
            <input type="text" name="student_name" required>

            <label>Class Selection</label>
            <select name="class_name" required>
                <option value="">-- Select Class --</option>
                <option value="Class 10-A">BCS TY</option>
                <option value="Class 10-B">BSC TY</option>

            </select>

            <label>Roll Number</label>
            <input type="text" name="roll_no" required>

            <label>Practical Name / Title</label>
            <input type="text" name="practical_name" required>

            <label>Upload File (PDF / Doc / Image)</label>
            <input type="file" name="practical_file" required>

            <button type="submit">Upload & Submit Work</button>
        </form>
    </div>

    <!-- Submission History Table -->
    <div class="card">
        <h3>Your Submission History</h3>
        <table>
            <thead>
                <tr>
                    <th>Practical Name</th>
                    <th>Class (Roll No)</th>
                    <th>Status</th>
                    <th>Submitted File</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($submissions) > 0): ?>
                    <?php foreach ($submissions as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['practical_name']) ?></td>
                        <td><?= htmlspecialchars($s['class_name']) ?> (<?= htmlspecialchars($s['roll_no']) ?>)</td>
                        <td><span class="status-<?= $s['status'] ?>"><?= $s['status'] ?></span></td>
                        <td><a href="<?= htmlspecialchars($s['file_path']) ?>" target="_blank">📁 View Submitted File</a></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">No practicals submitted yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>