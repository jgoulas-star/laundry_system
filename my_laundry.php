<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$laundry = $conn->query("
    SELECT 
        c.cycle_id,
        c.start_time,
        c.end_time,
        c.cycle_status,
        m.machine_number,
        m.location,
        m.machine_type
    FROM laundry_cycles c
    JOIN machines m ON c.machine_Id = m.machine_ID
    WHERE c.user_id = $user_id
    AND c.cycle_status = 'running'
    ORDER BY c.start_time DESC
");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>My Laundry</title>

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

            .main-content {
                flex: 1;
                padding: 30px;
            }

            .top-bar {
                margin-bottom: 30px;
            }

            .top-bar h1 {
                color: #2c3e50;
            }

            .activity-table {
                background: white;
                border-radius: 10px;
                padding: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
            }

            td {
                padding: 12px 5px;
                border-bottom: 1px solid #ecf0f1;
            }
        </style>
    </head>

    <body>

        <div class="sidebar">
            <h2>Laundry</h2>
            <ul>
                <li><a href="student_page.php">Dashboard</a></li>
                <li><a href="my_laundry.php" class="active">My Laundry</a></li>
                <li><a href="notifications.php">Notifications</a></li>
            </ul>
        </div>

        <div class="main-content">

            <div class="top-bar">
                <h1>My Laundry</h1>
            </div>

            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>Machine</th>
                            <th>Type</th>
                            <th>Building</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($laundry && $laundry->num_rows > 0): ?>
                            <?php while ($row = $laundry->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['machine_number']; ?></td>
                                    <td><?php echo ucfirst($row['machine_type']); ?></td>
                                    <td><?php echo $row['location']; ?></td>
                                    <td><?php echo $row['start_time']; ?></td>
                                    <td><?php echo $row['end_time']; ?></td>
                                    <td>
                                        <?php if ($row['cycle_status'] == 'running'): ?>
                                            <form method="POST" action="cancel_reservation.php" style="display:inline;">
                                                <input type="hidden" name="cycle_id" value="<?php echo $row['cycle_id']; ?>">

                                                <button type="submit"
                                                        style="background:#e74c3c;color:white;padding:6px 10px;border-radius:5px;border:none;cursor:pointer;">
                                                    Cancel
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No active laundry.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

        </div>

    </body>
</html>