<?php

$host = "localhost";
$user = "root";        // default for XAMPP
$pass = "";            // default is empty
$db   = "laundry_system";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// TEMPORARY DEBUG LINE – REMOVE AFTER TESTING
echo "<!-- DEBUG: db.php included successfully. Connection ID: " . $conn->thread_id . " -->";
?>

