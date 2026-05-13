<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ------- Handle Mark as Read ----------
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $notif_id = (int)$_GET['mark_read'];
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $notif_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: notifications.php");
    exit();
}

// ------- Handle Clear All (same page) ----------
if (isset($_POST['clear_all'])) {
    $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: notifications.php");
    exit();
}

// ------- Fetch notifications ----------
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

// ------- Unread count for badge ----------
$count_stmt = $conn->prepare("SELECT COUNT(*) AS unread FROM notifications WHERE user_id = ? AND is_read = 0");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$unread_count = $count_stmt->get_result()->fetch_assoc()['unread'];
$count_stmt->close();
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
        width:250px;
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
    .sidebar ul li a:hover { background:#3498db; }
    .badge {
        background:#e74c3c;
        color:white;
        padding:2px 8px;
        border-radius:10px;
        font-size:12px;
        margin-left:5px;
    }
    .main {
        flex:1;
        padding:30px;
    }
    .top-bar {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:30px;
    }
    .top-bar h1 { color:#2c3e50; }
    .btn {
        padding:10px 20px;
        border:none;
        border-radius:5px;
        text-decoration:none;
        cursor:pointer;
        font-size:14px;
        font-weight:500;
        transition:background 0.2s;
        display:inline-block;
    }
    .btn-back { background:#95a5a6; color:white; }
    .btn-back:hover { background:#7f8c8d; }
    .btn-clear { background:#e74c3c; color:white; margin-left:10px; }
    .btn-clear:hover { background:#c0392b; }
    .card {
        background:white;
        padding:15px 20px;
        border-radius:10px;
        margin-bottom:10px;
        box-shadow:0 2px 10px rgba(0,0,0,0.05);
    }
    .unread {
        border-left:4px solid #3498db;
        background:#f0f8ff;
    }
    .read {
        border-left:4px solid transparent;
    }
    .notification-title { font-weight:600; color:#2c3e50; margin-bottom:4px; }
    .notification-message { color:#555; font-size:14px; }
    .notification-time { color:#95a5a6; font-size:12px; margin-top:5px; }
    .actions { margin-top:8px; }
    .actions a { color:#3498db; text-decoration:none; font-size:13px; }
    .actions a:hover { text-decoration:underline; }
    .empty-state { text-align:center; padding:40px; color:#7f8c8d; }
</style>
</head>
<body>

<div class="sidebar">
    <h2>Laundry</h2>
    <ul>
        <li><a href="student_dashboard.php">Dashboard</a></li>
        <li><a href="my_reservations.php">My Reservations</a></li>
        <li><a href="my_laundry.php">My Laundry</a></li>
        <li>
            <a href="notifications.php">
                Notifications
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li><a href="student_settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="top-bar">
        <h1>Notifications</h1>
        <div>
            <a href="student_dashboard.php" class="btn btn-back">Back to Dashboard</a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete ALL your notifications?');">
                <button type="submit" name="clear_all" class="btn btn-clear">Clear All</button>
            </form>
        </div>
    </div>

    <?php if ($notifications->num_rows == 0): ?>
        <div class="empty-state">
            <p>No notifications yet.</p>
        </div>
    <?php else: ?>
        <?php while ($n = $notifications->fetch_assoc()): ?>
            <div class="card <?php echo $n['is_read'] ? 'read' : 'unread'; ?>">
                <div class="notification-title">
                    <?php echo htmlspecialchars($n['title']); ?>
                </div>
                <div class="notification-message">
                    <?php echo htmlspecialchars($n['message']); ?>
                </div>
                <div class="notification-time">
                    <?php echo date('M j, Y g:i A', strtotime($n['created_at'])); ?>
                </div>
                <?php if (!$n['is_read']): ?>
                    <div class="actions">
                        <a href="?mark_read=<?php echo $n['notification_id']; ?>">Mark as read</a>
                    </div>
                <?php else: ?>
                    <div class="actions" style="color:#95a5a6; font-size:13px;">Read</div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

</body>
</html>