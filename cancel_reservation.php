<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$cycle_id = $_POST['cycle_id'] ?? $_GET['cycle_id'] ?? null;

if (!$cycle_id) {
    header("Location: my_laundry.php");
    exit();
}

$cycle_id = (int)$cycle_id;

/* Get machine linked to cycle */
$cycle = $conn->query("
    SELECT machine_Id 
    FROM laundry_cycles 
    WHERE cycle_id = $cycle_id 
    AND user_id = $user_id
")->fetch_assoc();

if (!$cycle) {
    header("Location: my_laundry.php");
    exit();
}

$machine_id = $cycle['machine_Id'];

/* Cancel cycle */
$conn->query("
    UPDATE laundry_cycles
    SET cycle_status = 'finished'
    WHERE cycle_id = $cycle_id
");

/* Free machine */
$conn->query("
    UPDATE machines
    SET status = 'available'
    WHERE machine_ID = $machine_id
");

/* Remove reservation */
$conn->query("
    DELETE FROM reservations
    WHERE machine_Id = $machine_id
    AND user_id = $user_id
");

header("Location: my_laundry.php");
exit();
?>