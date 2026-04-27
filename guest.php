<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laundry_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Get buildings directly from DB */
$buildings = [];

$result = $conn->query("
    SELECT DISTINCT location 
    FROM machines 
    WHERE location IS NOT NULL AND location != ''
    ORDER BY location
");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $buildings[] = $row['location'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Laundry System - Guest</title>

<style>
* { margin:0; padding:0; box-sizing:border-box; }

body{
    font-family: Arial;
    background:#f4f7fc;
}

/* Navbar */
.navbar{
    background:#2c3e50;
    color:white;
    padding:15px 25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.navbar a{
    color:white;
    margin-left:15px;
    text-decoration:none;
    font-weight:bold;
}
.navbar a:hover{
    color:#1abc9c;
}

/* Layout */
.wrapper{
    display:flex;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    padding: 20px;
    min-height: calc(100vh - 60px);
}

.sidebar h2 {
    margin-bottom: 25px;
    font-size: 22px;
    border-bottom: 1px solid #3d566e;
    padding-bottom: 10px;
}

.sidebar h3 {
    margin: 20px 0 10px;
    font-size: 15px;
    color: #bdc3c7;
}

.sidebar ul {
    list-style: none;
}

.sidebar ul li {
    margin-bottom: 8px;
}

.sidebar ul li a {
    color: #ecf0f1;
    text-decoration: none;
    font-size: 14px;
    display: block;
    padding: 10px;
    border-radius: 6px;
    transition: 0.2s;
}

.sidebar ul li a:hover {
    background: #3498db;
}

/* Main */
.main{
    flex:1;
    padding:40px;
    color:#2c3e50;
}
</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div><strong>Laundry System</strong></div>
    <div>
        <a href="contact.php">Contact</a>
    </div>
</div>

<div class="wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>🧺 Laundry Status</h2>

        <h3>Buildings</h3>
        <ul>
            <?php foreach ($buildings as $b): ?>
                <li>
                    <a href="building.php?location=<?php echo urlencode($b); ?>">
                        <?php echo htmlspecialchars($b); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Account</h3>
        <ul>
            <li><a href="guest.php">Home</a></li>
            <li><a href="register.php">Sign Up</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <h1>Welcome</h1>
        <h2>Select a building to view machines</h2>
    </div>

</div>

</body>
</html>