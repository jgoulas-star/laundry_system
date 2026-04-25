<?php
session_start();
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: student_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn->query("
    DELETE FROM notification
    WHERE user_id = $user_id
");

header("Location: notifications.php");
exit();
?>

