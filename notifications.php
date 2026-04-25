<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$notifications = $conn->query("
    SELECT * FROM notification
    WHERE user_id = $user_id
    ORDER BY created_at DESC
");
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
</style>
</head>

<body>

<div class="sidebar">
    <h2>Laundry</h2>
    <ul>
        <li><a href="student_page.php">Dashboard</a></li>
        <li><a href="my_laundry.php">My Laundry</a></li>
        <li><a href="notifications.php">Notifications</a></li>
    </ul>
</div>

<div class="main">
    <h1>Notifications</h1>

    <form method="POST" action="clear_notifications.php">
        <button type="submit"
            style="background:#2c3e50;color:white;padding:8px 12px;border:none;border-radius:5px;margin-bottom:15px;cursor:pointer;">
            Clear Notifications
        </button>
    </form>

    <?php while ($n = $notifications->fetch_assoc()): ?>
        <div class="card">
            <?php echo htmlspecialchars($n['message']); ?>
            <br>
            <small><?php echo $n['created_at']; ?></small>
        </div>
    <?php endwhile; ?>
</body>
</html>

