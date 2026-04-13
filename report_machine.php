<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$machine_id = $_POST['machine_id'];
$report_type = $_POST['report_type'];
$message = $_POST['message'] ?? null;

/* Auto message handling */
if ($report_type == "faulty") {
    $message = "Machine reported as faulty";
} elseif ($report_type == "turned_off") {
    $message = "Machine was turned off";
}


$conn->query("
    INSERT INTO machine_reports (user_id, machine_id, report_type, message)
    VALUES ($user_id, $machine_id, '$report_type', '$message')
");

$conn->query("
    UPDATE machines
    SET status = 'out_of_order'
    WHERE machine_ID = $machine_id
");

header("Location: student_page.php");
exit();
?>

