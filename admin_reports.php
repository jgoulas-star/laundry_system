<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

$reports = $conn->query("
    SELECT mr.*, m.machine_number, m.location, u.name
    FROM machine_reports mr
    JOIN machines m ON mr.machine_id = m.machine_ID
    JOIN users u ON mr.user_id = u.user_ID
    WHERE mr.status = 'open'
    ORDER BY mr.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Reports</title>

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

/* Sidebar (SAME AS DASHBOARD) */
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
}

.sidebar ul li a:hover,
.sidebar ul li a.active {
    background: #3498db;
}

/* Main content */
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

/* Table styling (MATCH DASHBOARD STYLE) */
.activity-table {
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
}

/* Buttons */
.resolve-btn {
    background: #27ae60;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 13px;
}

.resolve-btn:hover {
    background: #1e8449;
}

.badge {
    padding: 4px 8px;
    border-radius: 5px;
    font-size: 12px;
    color: white;
}

.badge.faulty { background: #e74c3c; }
.badge.turned_off { background: #f39c12; }
.badge.other { background: #3498db; }

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Laundry Admin</h2>
    <ul>
        <li><a href="main_admin_page.php">Dashboard</a></li>
        <li><a href="manage_machines.php">Manage Machines</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="admin_reports.php" class="active">Reports</a></li>
        <li><a href="settings.php">Settings</a></li>
    </ul>
</div>

<!-- MAIN -->
<div class="main-content">

    <div class="top-bar">
        <h1>Machine Reports</h1>
    </div>

    <div class="activity-table">
        <table>
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Location</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($reports && $reports->num_rows > 0): ?>
                <?php while ($r = $reports->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['machine_number']; ?></td>
                        <td><?php echo $r['location']; ?></td>
                        <td><?php echo $r['name']; ?></td>

                        <td>
                            <span class="badge <?php echo $r['report_type']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $r['report_type'])); ?>
                            </span>
                        </td>

                        <td><?php echo $r['message']; ?></td>

                        <td>
                            <a class="resolve-btn"
                               href="resolve_report.php?id=<?php echo $r['report_id']; ?>">
                               Resolve
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No open reports.</td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>

</body>
</html>