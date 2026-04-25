<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}


$building = isset($_GET['building']) ? $_GET['building'] : '';

if ($building == '') {
    die("No building selected.");
}


$machines = $conn->query("
    SELECT machine_ID, machine_number, machine_type, status, location
    FROM machines
    WHERE location = '$building'
    ORDER BY machine_number
");
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars($building); ?> Machines</title>

        <style>
            body {
                margin:0;
                font-family:'Segoe UI', Arial;
                background:#f4f7fc;
                display:flex;
                min-height:100vh;
            }

            .sidebar {
                width:250px;
                background:#2c3e50;
                color:white;
                padding:20px;
            }

            .sidebar h2 {
                margin-bottom:30px;
                border-bottom:1px solid #3d566e;
                padding-bottom:15px;
            }

            .sidebar ul {
                list-style:none;
            }

            .sidebar ul li {
                margin-bottom:12px;
            }

            .sidebar ul li a {
                color:#ecf0f1;
                text-decoration:none;
                display:block;
                padding:8px 12px;
                border-radius:5px;
            }

            .sidebar ul li a:hover {
                background:#3498db;
            }

            /* Main */
            .main-content {
                flex:1;
                padding:30px;
            }

            h1 {
                color:#2c3e50;
                margin-bottom:20px;
            }

            /* Grid */
            .grid {
                display:grid;
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap:20px;
            }

            /* Machine card */
            .card {
                background:white;
                padding:20px;
                border-radius:10px;
                box-shadow:0 2px 10px rgba(0,0,0,0.05);
                text-align:center;
            }

            .status {
                margin-top:10px;
                font-weight:bold;
            }

            .available {
                color:#27ae60;
            }
            .in_use {
                color:#e67e22;
            }
            .out_of_order {
                color:#e74c3c;
            }

            button {
                margin-top:10px;
                padding:8px 12px;
                border:none;
                border-radius:5px;
                cursor:pointer;
            }

            .reserve {
                background:#3498db;
                color:white;
            }

            .reserve:hover {
                background:#2980b9;
            }
        </style>
    </head>

    <body>

        <div class="sidebar">
            <h2>Laundry</h2>
            <ul>
                <li><a href="student_page.php">Dashboard</a></li>
                <li><a href="my_laundry.php">My Laundry</a></li>
                <li><a href="notifications.php">Notifications</a></li>
            </ul>
        </div>


        <div class="main-content">

            <h1><?php echo htmlspecialchars($building); ?></h1>

            <div class="grid">

                <?php while ($m = $machines->fetch_assoc()): ?>

                    <div class="card">
                        <h3><?php echo $m['machine_number']; ?></h3>
                        <p><?php echo ucfirst($m['machine_type']); ?></p>

                        <div class="status <?php echo $m['status']; ?>">
                            <?php echo ucfirst(str_replace('_', ' ', $m['status'])); ?>
                        </div>


                        <form method="POST" action="reserve_machine.php">
                            <input type="hidden" name="machine_id" value="<?php echo $m['machine_ID']; ?>">
                            <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>">

                            <?php if ($m['status'] == 'available'): ?>
                                <button type="submit" class="reserve">Reserve</button>
                            <?php else: ?>
                                <button type="button" disabled style="background:#ccc;color:white;">
                                    Unavailable
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>

                <?php endwhile; ?>

            </div>

        </div>

    </body>
</html>
