<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once("db.php");

// Fetch real counts from database
$total_users_result = $conn->query("SELECT COUNT(*) AS total FROM users");
$total_users = $total_users_result->fetch_assoc()['total'];

$active_machines_result = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'in_use'");
$active_machines = $active_machines_result->fetch_assoc()['total'];

$out_of_order_result = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'out_of_order'");
$pending_alerts = $out_of_order_result->fetch_assoc()['total'];

// Fetch distinct building locations from machines table
$locations_result = $conn->query("SELECT DISTINCT location FROM machines WHERE location IS NOT NULL AND location != '' ORDER BY location");
$dorm_buildings = [];
while ($row = $locations_result->fetch_assoc()) {
    $dorm_buildings[] = $row['location'];
}

// If no buildings exist yet, provide fallback
if (empty($dorm_buildings)) {
    $dorm_buildings = ['Russell Tower', 'TownHouses', 'Aubuchon Hall', 'Mara Village'];
}

// Fetch recent activity (using added_at column)
$recent_activity = $conn->query("
    SELECT machine_number, machine_type, location, status, added_at 
    FROM machines 
    ORDER BY added_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Laundry System</title>
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

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Cards */
        .card-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            flex: 1 1 180px;
            text-align: center;
        }

        .card h3 {
            color: #7f8c8d;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .card .number {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
        }

        /* Dorm Grid */
        .section-title {
            margin: 30px 0 20px;
            color: #2c3e50;
        }

        .dorm-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .dorm-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
            transition: transform 0.2s;
        }

        .dorm-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .dorm-card h4 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .dorm-card a {
            display: inline-block;
            background: #3498db;
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.2s;
        }

        .dorm-card a:hover {
            background: #2980b9;
        }

        /* Recent Activity Table */
        .activity-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        .activity-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .activity-table th {
            text-align: left;
            padding: 12px 5px;
            border-bottom: 2px solid #ecf0f1;
            color: #7f8c8d;
            font-weight: 500;
        }

        .activity-table td {
            padding: 12px 5px;
            border-bottom: 1px solid #ecf0f1;
        }

        .status {
            font-weight: bold;
        }
        .status.in_use { color: #e67e22; }
        .status.available { color: #27ae60; }
        .status.out_of_order { color: #e74c3c; }
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
            <li><a href="settings.php">Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>

        <!-- Summary Cards -->
        <div class="card-container">
            <div class="card">
                <h3>Total Users</h3>
                <div class="number"><?php echo $total_users; ?></div>
            </div>
            <div class="card">
                <h3>Active Machines</h3>
                <div class="number"><?php echo $active_machines; ?></div>
            </div>
            <div class="card">
                <h3>Out of Order</h3>
                <div class="number" style="color: #e74c3c;"><?php echo $pending_alerts; ?></div>
            </div>
        </div>

        <!-- Dorm Building Quick Links -->
        <h2 class="section-title">Dorm Buildings</h2>
        <div class="dorm-grid">
            <?php foreach ($dorm_buildings as $dorm): ?>
                <div class="dorm-card">
                    <h4><?php echo htmlspecialchars($dorm); ?></h4>
                    <a href="Building_dashboard.php?building=<?php echo urlencode($dorm); ?>">
                        View Machines
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Recent Activity -->
        <h2 class="section-title">Recent Machine Updates</h2>
        <div class="activity-table">
            <table>
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Type</th>
                        <th>Building</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent_activity && $recent_activity->num_rows > 0): ?>
                        <?php while ($row = $recent_activity->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['machine_number']); ?></td>
                                <td><?php echo ucfirst($row['machine_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td class="status <?php echo $row['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['added_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No recent activity.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>