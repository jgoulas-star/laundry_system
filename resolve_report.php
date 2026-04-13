<?php
require_once("db.php");

$report_id = $_GET['id'];

/* Get machine linked to report */
$res = $conn->query("
    SELECT machine_id FROM machine_reports
    WHERE report_id = $report_id
");

$row = $res->fetch_assoc();
$machine_id = $row['machine_id'];

/* Delete report */
$conn->query("DELETE FROM machine_reports WHERE report_id = $report_id");

/* Reset machine */
$conn->query("
    UPDATE machines
    SET status = 'available'
    WHERE machine_ID = $machine_id
");

header("Location: admin_reports.php");
exit();
?>