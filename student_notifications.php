<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle "Clear Notifications"
if (isset($_POST['clear_all'])) {
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: student_notifications.php");
    exit();
}

// Handle "Mark as read" (single notification)
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notif_id = (int)$_GET['mark_read'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: student_notifications.php");
    exit();
}

// Fetch notifications (using prepared statement for safety)
$stmt = $conn->prepare("
    SELECT notification_id, title, message, is_read, created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications</title>

<style>
body {
    margin:0;
    font-family:'Segoe UI', Arial;
    background:#f4f7fc;
    display:flex;
    min-height:100vh;
}

.sidebar {
    width:205px;
    background:#2c3e50;
    color:white;
    padding:20px;
}

.sidebar h2 {
    margin-bottom:30px;
    border-bottom:1px solid #3d566e;
    padding-bottom:15px;
}

.sidebar ul { list-style:none; padding:0; }

.sidebar ul li { margin-bottom:12px; }

.sidebar ul li a {
    color:#ecf0f1;
    text-decoration:none;
    display:block;
    padding:8px 12px;
    border-radius:5px;
}

.sidebar ul li a:hover {
    background:#3498db;
}

.main {
    flex:1;
    padding:30px;
}

.card {
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.unread {
    border-left: 4px solid #3498db;
    background: #f0f8ff;
}

.read {
    border-left: 4px solid transparent;
}

.card .actions {
    margin-top: 8px;
}

.actions a {
    color: #3498db;
    text-decoration: none;
    font-size: 14px;
}

.actions a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>Laundry</h2>
    <ul>
        <li><a href="student_dashboard.php">Dashboard</a></li>
        <li><a href="my_reservations.php">My Reservations</a></li>
        <li><a href="student_notifications.php">Notifications</a></li>
        <li><a href="student_settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <h1>Notifications</h1>

    <!-- Clear All Button (in‑page form, no separate file needed) -->
    <form method="POST" onsubmit="return confirm('Delete ALL your notifications?');">
        <button type="submit" name="clear_all"
            style="background:#e74c3c;color:white;padding:8px 12px;border:none;border-radius:5px;margin-bottom:15px;cursor:pointer;">
            Clear All Notifications
        </button>
    </form>

    <?php if ($notifications->num_rows == 0): ?>
        <p>No notifications yet.</p>
    <?php else: ?>
        <?php while ($n = $notifications->fetch_assoc()): ?>
            <div class="card <?php echo $n['is_read'] ? 'read' : 'unread'; ?>">
                <strong><?php echo htmlspecialchars($n['title']); ?></strong><br>
                <?php echo htmlspecialchars($n['message']); ?>
                <br><small><?php echo date('M j, Y g:i A', strtotime($n['created_at'])); ?></small>

                <?php if (!$n['is_read']): ?>
                    <div class="actions">
                        <a href="?mark_read=<?php echo $n['notification_id']; ?>">Mark as read</a>
                    </div>
                <?php else: ?>
                    <div class="actions" style="color:#95a5a6; font-size:14px;">Read</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</body>
</html>