<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

require_once("db.php");

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ADD NEW USER
    if (isset($_POST['add_user'])) {
        $name     = trim($_POST['name']);
        $email    = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm  = $_POST['confirm_password'];
        $role     = $_POST['role'];

        if (empty($name) || empty($email) || empty($password)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        } elseif ($password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed, $role);
            if ($stmt->execute()) {
                $message = "User added successfully.";
            } else {
                if ($conn->errno === 1062) {
                    $error = "Email already exists.";
                } else {
                    $error = "Error adding user: " . $conn->error;
                }
            }
            $stmt->close();
        }
    }

    // UPDATE USER (name, email, role)
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $name    = trim($_POST['name']);
        $email   = trim($_POST['email']);
        $role    = $_POST['role'];

        if (empty($name) || empty($email)) {
            $error = "Name and email are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=? WHERE user_ID=?");
            $stmt->bind_param("sssi", $name, $email, $role, $user_id);
            if ($stmt->execute()) {
                $message = "User updated successfully.";
            } else {
                if ($conn->errno === 1062) {
                    $error = "Email already in use by another account.";
                } else {
                    $error = "Error updating user: " . $conn->error;
                }
            }
            $stmt->close();
        }
    }

    // UPDATE PASSWORD
    if (isset($_POST['update_password'])) {
        $user_id      = $_POST['user_id'];
        $new_password = $_POST['new_password'];
        $confirm      = $_POST['confirm_new_password'];

        if (strlen($new_password) < 6) {
            $error = "Password must be at least 6 characters.";
        } elseif ($new_password !== $confirm) {
            $error = "Passwords do not match.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE user_ID=?");
            $stmt->bind_param("si", $hashed, $user_id);
            if ($stmt->execute()) {
                $message = "Password updated successfully.";
            } else {
                $error = "Error updating password: " . $conn->error;
            }
            $stmt->close();
        }
    }

    // DELETE USER
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        if ($user_id == $_SESSION['user_id']) {
            $error = "You cannot delete your own account while logged in.";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_ID=?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $message = "User deleted successfully.";
            } else {
                $error = "Error deleting user: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

// Fetch all users for display
$users_result = $conn->query("SELECT user_ID, name, email, role, created_at FROM users ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Laundry System</title>
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
        .back-btn {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .back-btn:hover { background: #7f8c8d; }
        /* Messages */
        .message { padding: 12px 20px; border-radius: 5px; margin-bottom: 20px; }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        /* Add User Form */
        .add-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .add-form h2 { margin-bottom: 20px; color: #2c3e50; }
        .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .form-group { flex: 1 1 180px; }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #e67e22; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        /* Users Table */
        .users-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        .users-table h2 { margin-bottom: 20px; color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 12px 5px;
            border-bottom: 2px solid #ecf0f1;
            color: #7f8c8d;
            font-weight: 500;
        }
        td { padding: 12px 5px; border-bottom: 1px solid #ecf0f1; }
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .role-admin { background: #2c3e50; color: white; }
        .role-student { background: #3498db; color: white; }
        .action-buttons { display: flex; gap: 5px; flex-wrap: wrap; }
        .action-buttons form { display: inline; }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Laundry Admin</h2>
        <ul>
            <li><a href="main_admin_page.php" class="active">Dashboard</a></li>
            <li><a href="manage_machines.php">Manage Machines</a></li>
            <li><a href="admin_reports.php">Reports</a></li>
            <li><a href="admin_notifications.php">Send A Message</a></li>
            <li><a href="admin_messages.php">Messages</a></li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Manage Users</h1>
            <a href="main_admin_page.php" class="back-btn">&larr; Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Add New User Form -->
        <div class="add-form">
            <h2>Add New User</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="user@example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Min. 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select name="role" id="role" required>
                            <option value="student">Student</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Users List -->
        <div class="users-table">
            <h2>All Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['user_ID']; ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="role-badge role-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-warning" onclick="toggleEditForm(<?php echo $user['user_ID']; ?>)">Edit</button>
                                        <?php if ($user['user_ID'] != $_SESSION['user_id']): ?>
                                            <form method="POST" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_ID']; ?>">
                                                <button type="submit" name="delete_user" class="btn btn-danger">Delete</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color:#95a5a6; font-size:12px;">(You)</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <!-- Inline Edit Row -->
                            <tr id="edit-form-<?php echo $user['user_ID']; ?>" style="display: none;">
                                <td colspan="6">
                                    <div style="padding: 15px; background: #f9f9f9; border-radius: 5px;">
                                        <!-- Edit Name, Email, Role -->
                                        <form method="POST" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 15px;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_ID']; ?>">
                                            <div style="flex:1;">
                                                <label>Name</label>
                                                <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                                            </div>
                                            <div style="flex:2;">
                                                <label>Email</label>
                                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                                            </div>
                                            <div style="flex:1;">
                                                <label>Role</label>
                                                <select name="role" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                                                    <option value="student" <?php echo $user['role'] == 'student' ? 'selected' : ''; ?>>Student</option>
                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </div>
                                            <button type="submit" name="update_user" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn" style="background:#95a5a6; color:white;" onclick="toggleEditForm(<?php echo $user['user_ID']; ?>)">Cancel</button>
                                        </form>

                                        <!-- Separate Password Change Form -->
                                        <form method="POST" style="padding: 10px; background: #f1f1f1; border-radius: 5px; display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['user_ID']; ?>">
                                            <div style="flex:1;">
                                                <label>New Password (min. 6 chars)</label>
                                                <input type="password" name="new_password" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                                            </div>
                                            <div style="flex:1;">
                                                <label>Confirm New Password</label>
                                                <input type="password" name="confirm_new_password" required style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
                                            </div>
                                            <button type="submit" name="update_password" class="btn btn-warning">Change Password</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleEditForm(userId) {
            const row = document.getElementById('edit-form-' + userId);
            if (row.style.display === 'none') {
                document.querySelectorAll('[id^="edit-form-"]').forEach(el => el.style.display = 'none');
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        }
    </script>
</body>
</html>