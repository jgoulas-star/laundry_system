<?php
require_once("db.php");

// --- Machine stats (public) ---
$available = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'available'")->fetch_assoc()['total'];
$in_use = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'in_use'")->fetch_assoc()['total'];
$out_of_order = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'out_of_order'")->fetch_assoc()['total'];

// --- Buildings ---
$locations_result = $conn->query("SELECT DISTINCT location FROM machines WHERE location IS NOT NULL AND location != '' ORDER BY location");
$dorm_buildings = [];
while ($row = $locations_result->fetch_assoc()) {
    $dorm_buildings[] = $row['location'];
}
if (empty($dorm_buildings)) {
    $dorm_buildings = ['Russell Tower', 'TownHouses', 'Aubuchon Hall', 'Mara Village'];
}

// --- All machines ---
$machines_result = $conn->query("
    SELECT machine_ID, machine_number, machine_type, location, status
    FROM machines
    ORDER BY location, machine_number
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Machine Status – Guest View</title>
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
        .login-btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .login-btn:hover { background: #2980b9; }

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
        .card h3 { color: #7f8c8d; font-size: 16px; margin-bottom: 10px; }
        .card .number { font-size: 36px; font-weight: bold; color: #2c3e50; }

        .section-title { margin: 30px 0 20px; color: #2c3e50; }
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
        .dorm-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .dorm-card h4 { color: #2c3e50; margin-bottom: 15px; }
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
        .dorm-card a:hover { background: #2980b9; }

        .activity-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        .activity-table table { width: 100%; border-collapse: collapse; }
        .activity-table th {
            text-align: left;
            padding: 12px 5px;
            border-bottom: 2px solid #ecf0f1;
            color: #7f8c8d;
            font-weight: 500;
        }
        .activity-table td { padding: 12px 5px; border-bottom: 1px solid #ecf0f1; }
        .status { font-weight: bold; }
        .status.in_use { color: #e67e22; }
        .status.available { color: #27ae60; }
        .status.out_of_order { color: #e74c3c; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Laundry Status</h2>
    <ul>
        <li><a href="contact.php" class="active">Contact Us</a></li>
        <li><a href="create_account_guest.php" class="active">Create Account</a></li>
        <li style="margin-top:30px;"><a href="student_login.php">Student Login</a></li>
        <li><a href="admin_login.php">Admin Login</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <h1>Machine Status (Guest View)</h1>
        <a href="student_login.php" class="login-btn">Login to Reserve</a>
    </div>

    <div class="card-container">
        <div class="card">
            <h3>Available</h3>
            <div class="number"><?php echo $available; ?></div>
        </div>
        <div class="card">
            <h3>In Use</h3>
            <div class="number"><?php echo $in_use; ?></div>
        </div>
        <div class="card">
            <h3>Out of Order</h3>
            <div class="number" style="color:#e74c3c;"><?php echo $out_of_order; ?></div>
        </div>
    </div>

    <h2 class="section-title">All Machines</h2>
    <div class="activity-table">
        <table>
            <thead>
                <tr>
                    <th>Machine</th>
                    <th>Type</th>
                    <th>Building</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $machines_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['machine_number']); ?></td>
                        <td><?php echo ucfirst($row['machine_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['location']); ?></td>
                        <td class="status <?php echo $row['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>