<?php
session_start();
require_once("db.php");

// Only admins can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$message = '';
$error = '';

// Handle form submission
if (isset($_POST['send_notification'])) {
    $target_user = (int)$_POST['user_id'];
    $title       = trim($_POST['title']);
    $body        = trim($_POST['message']);

    if (empty($title) || empty($body)) {
        $error = "Please provide both a title and a message.";
    } else {
        // Verify the user exists
        $check = $conn->prepare("SELECT user_ID FROM users WHERE user_ID = ?");
        $check->bind_param("i", $target_user);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $target_user, $title, $body);
            if ($stmt->execute()) {
                $message = "Notification sent successfully!";
            } else {
                $error = "Failed to send notification.";
            }
            $stmt->close();
        } else {
            $error = "Selected user does not exist.";
        }
        $check->close();
    }
}

// Fetch users for the dropdown
$users_result = $conn->query("SELECT user_ID, name, email, role FROM users ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Notification - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 22px;
            border-bottom: 1px solid #3d566e;
            padding-bottom: 15px;
        }
        .sidebar ul { list-style: none; }
        .sidebar ul li { margin-bottom: 12px; }
        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 16px;
            display: block;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .sidebar ul li a:hover,
        .sidebar ul li a.active { background: #3498db; }
        .main-content { flex: 1; padding: 30px; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .top-bar h1 { color: #2c3e50; }
        .btn-back {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-back:hover { background: #7f8c8d; }
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 600px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-weight: 500;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        textarea { resize: vertical; min-height: 100px; }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Laundry Admin</h2>
    <ul>
            <li><a href="main_admin_page.php" class="active">Dashboard</a></li>
            <li><a href="manage_machines.php">Manage Machines</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="admin_reports.php">Reports</a></li>
            <li><a href="admin_messages.php">Messages</a></li>
            <li><a href="settings.php">Settings</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <h1>Send Notification</h1>
        <a href="main_admin_page.php" class="btn-back">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label for="user_id">Select User</label>
                <select name="user_id" id="user_id" required>
                    <option value="">-- Choose a user --</option>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?php echo $user['user_ID']; ?>">
                            <?php echo htmlspecialchars($user['name'] . ' (' . $user['email'] . ') - ' . ucfirst($user['role'])); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" required placeholder="e.g., Machine ready">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea name="message" id="message" required placeholder="Write the notification message..."></textarea>
            </div>
            <button type="submit" name="send_notification" class="btn btn-primary">Send Notification</button>
        </form>
    </div>
</div>

</body>
</html>