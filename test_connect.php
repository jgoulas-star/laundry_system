<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Database Connection</h2>";

// Check if db.php exists
if (!file_exists("db.php")) {
    die("❌ db.php file not found in current directory.");
}

echo "✅ db.php file found.<br>";

include("db.php");

// Check if $conn is set and is a MySQLi object
if (isset($conn)) {
    echo "✅ Variable \$conn exists.<br>";
    if ($conn instanceof mysqli) {
        echo "✅ \$conn is a valid MySQLi object.<br>";
        echo "✅ Connection successful! Database '" . $conn->real_escape_string($dbname ?? '') . "' is accessible.<br>";
    } else {
        echo "❌ \$conn is not a MySQLi object. Type: " . gettype($conn) . "<br>";
    }
} else {
    echo "❌ Variable \$conn does NOT exist after including db.php.<br>";
}

// Show any PHP errors that occurred
echo "<h3>PHP Error Log:</h3>";
var_dump(error_get_last());
?>