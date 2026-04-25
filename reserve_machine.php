<?php
session_start();
date_default_timezone_set("America/New_York");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$machine_id = $_POST['machine_id'] ?? $_GET['id'] ?? null;

if (!$machine_id) {
    header("Location: student_page.php");
    exit();
}

$machine_id = (int)$machine_id;

$check = $conn->query("
    SELECT status FROM machines WHERE machine_ID = $machine_id
");

$machine = $check->fetch_assoc();

if (!$machine || $machine['status'] !== 'available') {
    header("Location: student_page.php");
    exit();
}

$start = date("Y-m-d H:i:s");
$end = date("Y-m-d H:i:s", strtotime("+1 minute"));

$conn->query("
    INSERT INTO reservations (user_id, machine_Id, reservation_start, reservation_end, status)
    VALUES ($user_id, $machine_id, '$start', '$end', 'active')
");

$conn->query("
    INSERT INTO laundry_cycles (user_id, machine_Id, start_time, end_time, cycle_status)
    VALUES ($user_id, $machine_id, '$start', '$end', 'running')
");

$conn->query("
    UPDATE machines
    SET status = 'in_use'
    WHERE machine_ID = $machine_id
");

header("Location: student_page.php");
exit();
?>