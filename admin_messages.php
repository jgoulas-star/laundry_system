<?php
session_start();
require_once("db.php");

// Only admins
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$message = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Message deleted.";
    } else {
        $error = "Failed to delete message.";
    }
    $stmt->close();
}

// Fetch all messages (newest first)
$messages_result = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar – standard admin sidebar */
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

        .message-box {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .message-box.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message-box.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            text-align: left;
            padding: 12px 5px;
            border-bottom: 2px solid #ecf0f1;
            color: #7f8c8d;
            font-weight: 500;
        }
        td {
            padding: 12px 5px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: top;
        }
        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-delete:hover { background: #c0392b; }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Laundry Admin</h2>
    <ul>
        <li><a href="main_admin_page.php">Dashboard</a></li>
        <li><a href="manage_machines.php">Manage Machines</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="admin_notifications.php">Send Notification</a></li>
        <li><a href="admin_reports.php">Reports</a></li>
        <li><a href="settings.php">Settings</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="top-bar">
        <h1>Contact Messages</h1>
        <a href="main_admin_page.php" class="btn-back">Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
        <div class="message-box success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message-box error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-container">
        <?php if ($messages_result && $messages_result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($msg = $messages_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M j, Y g:i A', strtotime($msg['submitted_at'])); ?></td>
                            <td><?php echo htmlspecialchars($msg['name']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($msg['message'])); ?></td>
                            <td>
                                <a href="admin_messages.php?delete=<?php echo $msg['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Delete this message?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>📭 No messages received yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>