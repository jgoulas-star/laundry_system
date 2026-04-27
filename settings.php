<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

require_once("db.php");

$message = '';
$error = '';

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $user_id = $_SESSION['user_id'];

    if (empty($name) || empty($email)) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE user_ID=?");
        $stmt->bind_param("ssi", $name, $email, $user_id);
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $message = "Profile updated successfully.";
        } else {
            if ($conn->errno === 1062) {
                $error = "Email already in use.";
            } else {
                $error = "Error updating profile.";
            }
        }
        $stmt->close();
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['user_id'];

    // Verify current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_ID = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!password_verify($current_password, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_ID=?");
        $stmt->bind_param("si", $hashed, $user_id);
        if ($stmt->execute()) {
            $message = "Password changed successfully.";
        } else {
            $error = "Error changing password.";
        }
        $stmt->close();
    }
}

// Get system statistics
$total_machines = $conn->query("SELECT COUNT(*) AS total FROM machines")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_reports = 0;
if ($conn->query("SHOW TABLES LIKE 'machine_reports'")->num_rows > 0) {
    $total_reports = $conn->query("SELECT COUNT(*) AS total FROM machine_reports WHERE status = 'open'")->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Laundry System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
            display: flex;
            min-height: 100vh;
        }
        /* Sidebar */
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
        /* Main Content */
        .main-content { flex: 1; padding: 30px; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .top-bar h1 { color: #2c3e50; }
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .logout-btn:hover { background: #c0392b; }
        /* Messages */
        .message {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        /* Settings Grid */
        .settings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }
        .settings-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .settings-card h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border 0.2s;
        }
        .form-group input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-primary {
            background: #3498db;
            color: white;
        }
        .btn-primary:hover { background: #2980b9; }
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        .btn-warning:hover { background: #e67e22; }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover { background: #c0392b; }
        /* System Info */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #ecf0f1;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label {
            color: #7f8c8d;
            font-weight: 500;
        }
        .info-value {
            color: #2c3e50;
            font-weight: 600;
        }
        .maintenance-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }
        .warning-text {
            color: #e67e22;
            font-size: 13px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Laundry Admin</h2>
        <ul>
            <li><a href="main_admin_page.php" class="active">Dashboard</a></li>
            <li><a href="manage_machines.php">Manage Machines</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="admin_reports.php">Reports</a></li>
            <li><a href="admin_notifications.php">Send A Message</a></li>
            <li><a href="admin_messages.php">Messages</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Settings</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="settings-grid">
            <!-- Profile Settings -->
            <div class="settings-card">
                <h2>👤 Profile Information</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" 
                               value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="settings-card">
                <h2>🔐 Change Password</h2>
                <form method="POST">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" 
                               placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                </form>
            </div>

            <!-- System Information -->
            <div class="settings-card">
                <h2>📊 System Information</h2>
                <div class="info-row">
                    <span class="info-label">Total Machines</span>
                    <span class="info-value"><?php echo $total_machines; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Users</span>
                    <span class="info-value"><?php echo $total_users; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Open Reports</span>
                    <span class="info-value"><?php echo $total_reports; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">PHP Version</span>
                    <span class="info-value"><?php echo phpversion(); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Server Time</span>
                    <span class="info-value"><?php echo date('Y-m-d H:i:s'); ?></span>
                </div>
            </div>

            <!-- Maintenance -->
            <div class="settings-card">
                <h2>🛠️ Maintenance</h2>
                <p style="color: #7f8c8d; margin-bottom: 15px; font-size: 14px;">
                    Use these tools to manage system data.
                </p>
                <div class="maintenance-actions">
                    <button class="btn btn-primary" onclick="alert('This feature would reset all machine statuses to available.')">
                        Reset All Machines
                    </button>
                    <button class="btn btn-warning" onclick="alert('This would mark all machines as out of order.')">
                        Emergency Shutdown
                    </button>
                </div>
                <p class="warning-text">
                    ⚠️ Maintenance actions affect all machines. Use with caution.
                </p>
            </div>

            <!-- About -->
            <div class="settings-card">
                <h2>ℹ️ About</h2>
                <div class="info-row">
                    <span class="info-label">Application</span>
                    <span class="info-value">Laundry Management System</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Version</span>
                    <span class="info-value">1.0.0</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Database</span>
                    <span class="info-value">MySQL</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Updated</span>
                    <span class="info-value"><?php echo date('F j, Y'); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>