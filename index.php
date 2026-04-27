<?php
include 'db_connect.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laundry System</title>
</head>

<body>

<h1>Smart Laundry System</h1>

<?php
$sql = "SELECT * FROM machines";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Machine: " . $row["machine_number"] . " - Status: " . $row["status"] . "<br>";
    }
} else {
    echo "No machines found.";
}
?>

</body>
</html>