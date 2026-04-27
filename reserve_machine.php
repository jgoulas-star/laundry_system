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

// Handle reservation request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    $machine_id = (int)$_POST['machine_id'];
    $duration   = isset($_POST['duration']) ? (int)$_POST['duration'] : 60;

    if ($machine_id <= 0) {
        $error = "Invalid machine selection.";
    } elseif ($duration < 15 || $duration > 180) {
        $error = "Duration must be between 15 and 180 minutes.";
    } else {
        // Check if machine is available
        $check = $conn->prepare("SELECT status FROM machines WHERE machine_ID = ?");
        $check->bind_param("i", $machine_id);
        $check->execute();
        $machine = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$machine) {
            $error = "Machine not found.";
        } elseif ($machine['status'] !== 'available') {
            $error = "Machine is not available right now.";
        } else {
            // Create reservation
            $start = date("Y-m-d H:i:s");
            $end   = date("Y-m-d H:i:s", strtotime("+{$duration} minutes"));

            $conn->begin_transaction();
            try {
                // Insert reservation
                $stmt = $conn->prepare("INSERT INTO reservations (user_id, machine_id, reservation_start, reservation_end, status) VALUES (?, ?, ?, ?, 'active')");
                $stmt->bind_param("iiss", $user_id, $machine_id, $start, $end);
                $stmt->execute();
                $stmt->close();

                // Update machine status
                $update = $conn->prepare("UPDATE machines SET status = 'in_use' WHERE machine_ID = ?");
                $update->bind_param("i", $machine_id);
                $update->execute();
                $update->close();

                $conn->commit();
                $message = "Machine reserved successfully! Your session will end at " . date('g:i A', strtotime($end)) . ".";
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Reservation failed. Please try again.";
            }
        }
    }
}

// Fetch available machines for dropdown
$machines = $conn->query("SELECT machine_ID, machine_number, location, machine_type FROM machines WHERE status = 'available' ORDER BY location, machine_number");

// Fetch user’s active reservations
$reservations = $conn->query("
    SELECT r.reservation_id, r.reservation_start, r.reservation_end, m.machine_number, m.machine_type, m.location
    FROM reservations r
    JOIN machines m ON r.machine_id = m.machine_ID
    WHERE r.user_id = $user_id AND r.status = 'active'
    ORDER BY r.reservation_start DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reserve Machine</title>
    <style>
        body { font-family:'Segoe UI',Arial; background:#f4f7fc; display:flex; min-height:100vh; margin:0; }
        .sidebar { width:250px; background:#2c3e50; color:white; padding:20px; }
        .sidebar h2 { margin-bottom:30px; border-bottom:1px solid #3d566e; padding-bottom:15px; }
        .sidebar ul { list-style:none; padding:0; }
        .sidebar ul li { margin-bottom:12px; }
        .sidebar ul li a { color:#ecf0f1; text-decoration:none; display:block; padding:8px 12px; border-radius:5px; }
        .sidebar ul li a:hover { background:#3498db; }
        .main-content { flex:1; padding:30px; }
        .card { background:white; padding:25px; border-radius:10px; margin-bottom:25px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
        .btn { padding:10px 20px; border-radius:5px; text-decoration:none; cursor:pointer; margin-right:10px; }
        .btn-primary { background:#3498db; color:white; border:none; }
        .btn-danger { background:#e74c3c; color:white; border:none; }
        .btn-back { background:#95a5a6; color:white; }
        .message { padding:10px; border-radius:5px; margin-bottom:15px; }
        .success { background:#d4edda; color:#155724; }
        .error { background:#f8d7da; color:#721c24; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:10px 5px; border-bottom:1px solid #ecf0f1; text-align:left; }
    </style>
</head>
<body>
<div class="sidebar">
    <h2>Laundry</h2>
    <ul>
        <li><a href="student_dashboard.php">Dashboard</a></li>
        <li><a href="reserve_machine.php">Reserve Machine</a></li>
        <li><a href="my_reservations.php">My Reservations</a></li>
        <li><a href="student_notifications.php">Notifications</a></li>
        <li><a href="student_settings.php">Settings</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="main-content">
    <h1>Reserve a Machine</h1>
    <?php if ($message): ?><div class="message success"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>

    <div class="card">
        <form method="POST">
            <label>Select Machine</label>
            <select name="machine_id" required style="width:100%; padding:8px; margin:10px 0;">
                <option value="">-- Choose --</option>
                <?php while($m = $machines->fetch_assoc()): ?>
                    <option value="<?php echo $m['machine_ID']; ?>">
                        <?php echo $m['location'] . " - " . $m['machine_number'] . " (" . ucfirst($m['machine_type']) . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <label>Duration (minutes, 15-180)</label>
            <input type="number" name="duration" value="60" min="15" max="180" required style="width:100%; padding:8px; margin:10px 0;">
            <button type="submit" name="reserve" class="btn btn-primary">Reserve</button>
        </form>
    </div>

    <div class="card">
        <h2>Your Active Reservations</h2>
        <?php if ($reservations->num_rows > 0): ?>
            <table>
                <tr><th>Machine</th><th>Location</th><th>Started</th><th>Ends</th><th>Action</th></tr>
                <?php while($r = $reservations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $r['machine_number']; ?></td>
                        <td><?php echo $r['location']; ?></td>
                        <td><?php echo date('H:i', strtotime($r['reservation_start'])); ?></td>
                        <td><?php echo date('H:i', strtotime($r['reservation_end'])); ?></td>
                        <td><a href="my_reservations.php?cancel=<?php echo $r['reservation_id']; ?>" class="btn btn-danger" style="padding:4px 8px;">Cancel</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No active reservations.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>