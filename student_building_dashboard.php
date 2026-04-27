<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

require_once("db.php");

// Get building from URL, default to a safe value or show error
$building = isset($_GET['building']) ? trim($_GET['building']) : '';

if (empty($building)) {
    die("No building specified.");
}

// Fetch machines for this building
$stmt = $conn->prepare("SELECT * FROM machines WHERE location = ? ORDER BY machine_type, machine_number");
$stmt->bind_param("s", $building);
$stmt->execute();
$machines_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($building); ?> Dashboard - Laundry System</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; background: #f4f7fc; }
        .container { max-width: 1000px; margin: auto; }
        h1 { color: #2c3e50; }
        .back-link { margin-bottom: 20px; }
        .back-link a { text-decoration: none; color: #3498db; }
        table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background: #2c3e50; color: white; }
        .status { font-weight: bold; }
        .status.available { color: #27ae60; }
        .status.in_use { color: #e67e22; }
        .status.out_of_order { color: #e74c3c; }
    </style>
</head>
<body>
<div class="container">
    <div class="back-link">
        <a href="student_dashboard.php">&larr; Back to Dashboard</a>
    </div>
    <h1><?php echo htmlspecialchars($building); ?> - Washers & Dryers</h1>

    <table>
        <thead>
            <tr>
                <th>Machine #</th>
                <th>Type</th>
                <th>Status</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($machines_result->num_rows > 0): ?>
                <?php while ($machine = $machines_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($machine['machine_number']); ?></td>
                        <td><?php echo ucfirst($machine['machine_type']); ?></td>
                        <td class="status <?php echo $machine['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $machine['status'])); ?>
                        </td>
                        <td><?php echo htmlspecialchars($machine['added_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No machines found for this building.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>