<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$machine_id = $_GET['id'] ?? null;

if (!$machine_id) {
    header("Location: student_page.php");
    exit();
}

/* 1. Check if machine is available */
$check = $conn->query("
    SELECT status 
    FROM machines 
    WHERE machine_ID = $machine_id
");

$machine = $check->fetch_assoc();

if (!$machine || $machine['status'] !== 'available') {
    header("Location: student_page.php");
    exit();
}

/* 2. Set times */
$start = date("Y-m-d H:i:s");
$end = date("Y-m-d H:i:s", strtotime("+1 minute"));

/* 3. Create reservation */
$conn->query("
    INSERT INTO reservations (user_id, machine_Id, reservation_start, reservation_end, status)
    VALUES ($user_id, $machine_id, '$start', '$end', 'active')
");

/* 4. Update machine status */
$conn->query("
    UPDATE machines 
    SET status = 'in_use'
    WHERE machine_ID = $machine_id
");

/* 5. Create laundry cycle */
$conn->query("
    INSERT INTO laundry_cycles (user_id, machine_Id, start_time, end_time, cycle_status)
    VALUES ($user_id, $machine_id, '$start', '$end', 'running')
");

header("Location: student_page.php");
exit();
?>
