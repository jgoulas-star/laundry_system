<?php
session_start();
require_once("db.php");

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $reservation_id = (int)$_GET['cancel'];

    // Verify that this reservation belongs to the user and is active
    $check_stmt = $conn->prepare("SELECT machine_id FROM reservations WHERE reservation_id = ? AND user_id = ? AND status = 'active'");
    $check_stmt->bind_param("ii", $reservation_id, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 1) {
        $res = $check_result->fetch_assoc();
        $machine_id = $res['machine_id'];

        // Begin transaction
        $conn->begin_transaction();
        try {
            // Cancel reservation
            $cancel_stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
            $cancel_stmt->bind_param("i", $reservation_id);
            $cancel_stmt->execute();
            $cancel_stmt->close();

            // Free up the machine
            $update_machine = $conn->prepare("UPDATE machines SET status = 'available' WHERE machine_ID = ?");
            $update_machine->bind_param("i", $machine_id);
            $update_machine->execute();
            $update_machine->close();

            $conn->commit();
            $message = "Reservation cancelled successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Failed to cancel reservation. Please try again.";
        }
    } else {
        $error = "Invalid reservation or already cancelled.";
    }
    $check_stmt->close();
}

// Fetch active reservations
$active_stmt = $conn->prepare("
    SELECT r.reservation_id, r.reservation_start, r.reservation_end, r.status,
           m.machine_number, m.machine_type, m.location
    FROM reservations r
    JOIN machines m ON r.machine_id = m.machine_ID
    WHERE r.user_id = ? AND r.status = 'active'
    ORDER BY r.reservation_start DESC
");
$active_stmt->bind_param("i", $user_id);
$active_stmt->execute();
$active_reservations = $active_stmt->get_result();
$active_stmt->close();

// Fetch past (completed/cancelled) reservations
$past_stmt = $conn->prepare("
    SELECT r.reservation_id, r.reservation_start, r.reservation_end, r.status,
           m.machine_number, m.machine_type, m.location
    FROM reservations r
    JOIN machines m ON r.machine_id = m.machine_ID
    WHERE r.user_id = ? AND r.status IN ('completed','cancelled')
    ORDER BY r.reservation_start DESC
    LIMIT 20
");
$past_stmt->bind_param("i", $user_id);
$past_stmt->execute();
$past_reservations = $past_stmt->get_result();
$past_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Laundry System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar (same as student dashboard) */
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
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background 0.2s;
            display: inline-block;
        }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-back { background: #95a5a6; color: white; }
        .btn-back:hover { background: #7f8c8d; }

        /* Messages */
        .message {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* Table */
        .reservations-table {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow-x: auto;
        }
        .reservations-table h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
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
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge.active { background: #fdebd0; color: #e67e22; }
        .badge.completed { background: #d4edda; color: #155724; }
        .badge.cancelled { background: #fadbd8; color: #e74c3c; }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Laundry</h2>
    <ul>
        <li><a href="student_dashboard.php">Dashboard</a></li>
        <li><a href="my_reservations.php" class="active">My Reservations</a></li>
        <li><a href="student_notifications.php">Notifications</a></li>
        <li><a href="student_settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="top-bar">
        <h1>My Reservations</h1>
        <div>
            <a href="reserve_machine.php" class="btn btn-primary">New Reservation</a>
            <a href="student_dashboard.php" class="btn btn-back">Back to Dashboard</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Active Reservations -->
    <div class="reservations-table">
        <h2>Active Reservations</h2>
        <?php if ($active_reservations->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Started</th>
                        <th>Ends</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $active_reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['machine_number']); ?></td>
                            <td><?php echo ucfirst($row['machine_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo date('M j, g:i A', strtotime($row['reservation_start'])); ?></td>
                            <td><?php echo date('M j, g:i A', strtotime($row['reservation_end'])); ?></td>
                            <td><span class="badge active">Active</span></td>
                            <td>
                                <a href="my_reservations.php?cancel=<?php echo $row['reservation_id']; ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Cancel this reservation?')">
                                    Cancel
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>You have no active reservations.</p>
                <a href="reserve_machine.php" class="btn btn-primary" style="margin-top:15px;">Reserve a Machine</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Past Reservations -->
    <div class="reservations-table">
        <h2>Past Reservations</h2>
        <?php if ($past_reservations->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Machine</th>
                        <th>Type</th>
                        <th>Location</th>
                        <th>Started</th>
                        <th>Ends</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $past_reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['machine_number']); ?></td>
                            <td><?php echo ucfirst($row['machine_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo date('M j, g:i A', strtotime($row['reservation_start'])); ?></td>
                            <td><?php echo date('M j, g:i A', strtotime($row['reservation_end'])); ?></td>
                            <td>
                                <span class="badge <?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <p>No past reservations found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>