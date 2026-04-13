<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

require_once("db.php");

$user_id = $_SESSION['user_id'];

/* Find finished laundry cycles */
$finished = $conn->query("
    SELECT cycle_id, machine_Id
    FROM laundry_cycles
    WHERE user_id = $user_id
    AND cycle_status = 'running'
    AND end_time <= NOW()
");

while ($row = $finished->fetch_assoc()) {

    $cycle_id = $row['cycle_id'];
    $machine_id = $row['machine_Id'];

    // mark cycle as finished
    $conn->query("
        UPDATE laundry_cycles
        SET cycle_status = 'finished'
        WHERE cycle_id = $cycle_id
    ");

    // insert notification
    $conn->query("
        INSERT INTO notification (user_id, message)
        VALUES ($user_id, 'Your laundry is done!')
    ");

    // free machine
    $conn->query("
        UPDATE machines
        SET status = 'available'
        WHERE machine_ID = $machine_id
    ");
}
$notifications = $conn->query("
    SELECT * FROM notification
    WHERE user_id = $user_id
    ORDER BY created_at DESC
    LIMIT 5
");

// Machine stats
$available = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'available'")->fetch_assoc()['total'];
$in_use = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'in_use'")->fetch_assoc()['total'];
$out_of_order = $conn->query("SELECT COUNT(*) AS total FROM machines WHERE status = 'out_of_order'")->fetch_assoc()['total'];

// Buildings
$locations_result = $conn->query("SELECT DISTINCT location FROM machines WHERE location IS NOT NULL AND location != '' ORDER BY location");
$dorm_buildings = [];
while ($row = $locations_result->fetch_assoc()) {
    $dorm_buildings[] = $row['location'];
}
if (empty($dorm_buildings)) {
    $dorm_buildings = ['Russell Tower', 'TownHouses', 'Aubuchon Hall', 'Mara Village'];
}

// Machines
$machines = $conn->query("
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
        <title>Student Dashboard - Laundry System</title>

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

            /* Machine Table */
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
            .status.in_use {
                color: #e67e22;
            }
            .status.available {
                color: #27ae60;
            }
            .status.out_of_order {
                color: #e74c3c;
            }

        </style>
    </head>

    <body>

        <div class="sidebar">
            <h2>Laundry</h2>
            <ul>
                <li><a href="student_page.php" class="active">Dashboard</a></li>
                <li><a href="#">My Laundry</a></li>
                <li>
                    <a href="#">
                        Notifications
                        <?php if ($notifications->num_rows > 0): ?>
                            (<?php echo $notifications->num_rows; ?>)
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content">

            <div class="top-bar">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                <a href="logout.php" class="logout-btn">Logout</a>
                <?php while ($n = $notifications->fetch_assoc()): ?>
                    <div style="background:#27ae60;color:white;padding:10px;border-radius:5px;margin-bottom:10px;">
                        <?php echo htmlspecialchars($n['message']); ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="card-container">
                <div class="card">
                    <h3>Available Machines</h3>
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

            <h2 class="section-title">All Machines</h2>
            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>Machine</th>
                            <th>Type</th>
                            <th>Building</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $machines->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['machine_number']); ?></td>
                                <td><?php echo ucfirst($row['machine_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td class="status <?php echo $row['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'available'): ?>
                                        <a href="reserve_machine.php?id=<?php echo $row['machine_ID']; ?>" 
                                           style="background:#27ae60;color:white;padding:6px 10px;border-radius:5px;text-decoration:none;font-size:13px;">
                                            Reserve
                                        </a>
                                    <?php endif; ?>
                                    <button type="button"
                                            onclick="document.getElementById('report-<?php echo $row['machine_ID']; ?>').style.display = 'block'"
                                            style="background:#e74c3c;color:white;padding:6px 10px;border:none;border-radius:5px;cursor:pointer;">
                                        Report
                                    </button>
                                    <div id="report-<?php echo $row['machine_ID']; ?>"
                                         style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
                                         background:rgba(0,0,0,0.4); justify-content:center; align-items:center;">

                                        <div style="background:white; padding:20px; border-radius:10px; width:300px; margin:100px auto;">

                                            <form method="POST" action="report_machine.php">

                                                <input type="hidden" name="machine_id" value="<?php echo $row['machine_ID']; ?>">

                                                <h3>Report Machine</h3>

                                                <select name="report_type" required style="width:100%; padding:8px; margin-bottom:10px;">
                                                    <option value="faulty">Faulty</option>
                                                    <option value="turned_off">Turned Off</option>
                                                    <option value="other">Other</option>
                                                </select>

                                                <input type="text" name="message"
                                                       placeholder="Optional details"
                                                       style="width:100%; padding:8px; margin-bottom:10px;">

                                                <button type="submit"
                                                        style="background:#e74c3c;color:white;padding:8px;width:100%;border:none;border-radius:5px;">
                                                    Submit Report
                                                </button>

                                                <button type="button"
                                                        onclick="document.getElementById('report-<?php echo $row['machine_ID']; ?>').style.display = 'none'"
                                                        style="margin-top:10px;width:100%;padding:8px;">
                                                    Cancel
                                                </button>

                                            </form>

                                        </div>
                                    </div>
                                    <a href="view_machine.php?id=<?php echo $row['machine_ID']; ?>" 
                                       style="background:#3498db;color:white;padding:6px 10px;border-radius:5px;text-decoration:none;font-size:13px;">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </body>
</html>