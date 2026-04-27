<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once("db.php");

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ADD NEW MACHINE
    if (isset($_POST['add_machine'])) {
        $machine_number = trim($_POST['machine_number']);
        $machine_type   = $_POST['machine_type'];
        $status         = $_POST['status'];
        $location       = trim($_POST['location']);

        if (empty($machine_number) || empty($location)) {
            $error = "Machine number and location are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO machines (machine_number, machine_type, status, location, added_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("ssss", $machine_number, $machine_type, $status, $location);
            if ($stmt->execute()) {
                $message = "Machine added successfully.";
            } else {
                $error = "Error adding machine: " . $conn->error;
            }
            $stmt->close();
        }
    }

    // UPDATE MACHINE
    if (isset($_POST['update_machine'])) {
        $machine_id     = $_POST['machine_id'];
        $machine_number = trim($_POST['machine_number']);
        $machine_type   = $_POST['machine_type'];
        $status         = $_POST['status'];
        $location       = trim($_POST['location']);

        $stmt = $conn->prepare("UPDATE machines SET machine_number=?, machine_type=?, status=?, location=? WHERE machine_ID=?");
        $stmt->bind_param("ssssi", $machine_number, $machine_type, $status, $location, $machine_id);
        if ($stmt->execute()) {
            $message = "Machine updated successfully.";
        } else {
            $error = "Error updating machine: " . $conn->error;
        }
        $stmt->close();
    }

    // DELETE MACHINE
    if (isset($_POST['delete_machine'])) {
        $machine_id = $_POST['machine_id'];
        $stmt = $conn->prepare("DELETE FROM machines WHERE machine_ID=?");
        $stmt->bind_param("i", $machine_id);
        if ($stmt->execute()) {
            $message = "Machine deleted successfully.";
        } else {
            $error = "Error deleting machine: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch all machines for display
$machines_result = $conn->query("SELECT * FROM machines ORDER BY location, machine_type, machine_number");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Machines - Laundry System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li {
            margin-bottom: 12px;
        }

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
        .sidebar ul li a.active {
            background: #3498db;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .top-bar h1 {
            color: #2c3e50;
        }

        .back-btn {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .back-btn:hover {
            background: #7f8c8d;
        }

        /* Messages */
        .message {
            padding: 12px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Add Machine Form */
        .add-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        .add-form h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .form-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .form-group {
            flex: 1 1 180px;
        }

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

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-warning {
            background: #f39c12;
            color: white;
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        .btn-danger {
            background: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        /* Machines Table */
        .machines-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        .machines-table h2 {
            margin-bottom: 20px;
            color: #2c3e50;
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
        }

        .status {
            font-weight: bold;
        }
        .status.available { color: #27ae60; }
        .status.in_use { color: #e67e22; }
        .status.out_of_order { color: #e74c3c; }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons form {
            display: inline;
        }

        .inline-edit-form {
            display: none;
            background: #f9f9f9;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Laundry Admin</h2>
        <ul>
            <li><a href="main_admin_page.php" class="active">Dashboard</a></li>
            <li><a href="manage_users.php">Manage Users</a></li>
            <li><a href="admin_reports.php">Reports</a></li>
            <li><a href="admin_notifications.php">Send A Message</a></li>
            <li><a href="admin_messages.php">Messages</a></li>
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Manage Machines</h1>
            <a href="main_admin_page.php" class="back-btn">&larr; Back to Dashboard</a>
        </div>

        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Add New Machine Form -->
        <div class="add-form">
            <h2>Add New Machine</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="machine_number">Machine #</label>
                        <input type="text" name="machine_number" id="machine_number" placeholder="e.g., W01" required>
                    </div>
                    <div class="form-group">
                        <label for="machine_type">Type</label>
                        <select name="machine_type" id="machine_type" required>
                            <option value="washer">Washer</option>
                            <option value="dryer">Dryer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" required>
                            <option value="available">Available</option>
                            <option value="in_use">In Use</option>
                            <option value="out_of_order">Out of Order</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location">Location (Building)</label>
                        <input type="text" name="location" id="location" placeholder="e.g., Adams Hall" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_machine" class="btn btn-primary">Add Machine</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Machines List -->
        <div class="machines-table">
            <h2>All Machines</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Machine #</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($machines_result && $machines_result->num_rows > 0): ?>
                        <?php while ($machine = $machines_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $machine['machine_ID']; ?></td>
                                <td><?php echo htmlspecialchars($machine['machine_number']); ?></td>
                                <td><?php echo ucfirst($machine['machine_type']); ?></td>
                                <td class="status <?php echo $machine['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $machine['status'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($machine['location']); ?></td>
                                <td><?php echo htmlspecialchars($machine['added_at']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- Edit Button (opens inline form via JavaScript) -->
                                        <button type="button" class="btn btn-warning" onclick="toggleEditForm(<?php echo $machine['machine_ID']; ?>)">Edit</button>
                                        
                                        <!-- Delete Form -->
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this machine?');">
                                            <input type="hidden" name="machine_id" value="<?php echo $machine['machine_ID']; ?>">
                                            <button type="submit" name="delete_machine" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <!-- Inline Edit Row (hidden by default) -->
                            <tr id="edit-form-<?php echo $machine['machine_ID']; ?>" style="display: none;">
                                <td colspan="7">
                                    <form method="POST" style="display: flex; gap: 10px; align-items: center; padding: 10px; background: #f9f9f9;">
                                        <input type="hidden" name="machine_id" value="<?php echo $machine['machine_ID']; ?>">
                                        <input type="text" name="machine_number" value="<?php echo htmlspecialchars($machine['machine_number']); ?>" placeholder="Machine #" required style="padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                        <select name="machine_type" required style="padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                            <option value="washer" <?php echo $machine['machine_type'] == 'washer' ? 'selected' : ''; ?>>Washer</option>
                                            <option value="dryer" <?php echo $machine['machine_type'] == 'dryer' ? 'selected' : ''; ?>>Dryer</option>
                                        </select>
                                        <select name="status" required style="padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                            <option value="available" <?php echo $machine['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                            <option value="in_use" <?php echo $machine['status'] == 'in_use' ? 'selected' : ''; ?>>In Use</option>
                                            <option value="out_of_order" <?php echo $machine['status'] == 'out_of_order' ? 'selected' : ''; ?>>Out of Order</option>
                                        </select>
                                        <input type="text" name="location" value="<?php echo htmlspecialchars($machine['location']); ?>" placeholder="Location" required style="padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
                                        <button type="submit" name="update_machine" class="btn btn-primary">Save</button>
                                        <button type="button" class="btn" style="background:#95a5a6; color:white;" onclick="toggleEditForm(<?php echo $machine['machine_ID']; ?>)">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">No machines found. Add your first machine above.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleEditForm(machineId) {
            const row = document.getElementById('edit-form-' + machineId);
            if (row.style.display === 'none') {
                // Hide any other open edit forms first
                document.querySelectorAll('[id^="edit-form-"]').forEach(el => el.style.display = 'none');
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        }
    </script>
</body>
</html>