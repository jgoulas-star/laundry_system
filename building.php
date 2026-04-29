<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laundry_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$location = isset($_GET['location']) ? trim($_GET['location']) : "";

// Get all buildings for sidebar
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

// Get machines for selected building
$machines = [];

if (!empty($location)) {
    $stmt = $conn->prepare("
        SELECT machine_number, machine_type, status 
        FROM machines 
        WHERE location = ?
        ORDER BY machine_number
    ");

    $stmt->bind_param("s", $location);
    $stmt->execute();

    $machine_result = $stmt->get_result();

    while ($row = $machine_result->fetch_assoc()) {
        $machines[] = $row;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<title><?php echo htmlspecialchars($location); ?> Machines</title>

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

.sidebar ul li a.active {
    background: #1abc9c;
}

/* Main */
.main{
    flex:1;
    padding:40px;
    color:#2c3e50;
}

.main h1 {
    margin-bottom: 20px;
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    border-radius:10px;
    overflow:hidden;
}

th, td{
    padding:14px;
    text-align:left;
    border-bottom:1px solid #ddd;
}

th{
    background:#2c3e50;
    color:white;
}

tr:hover{
    background:#f1f1f1;
}

.status{
    padding:6px 10px;
    border-radius:20px;
    color:white;
    font-size:13px;
    font-weight:bold;
}

.available{
    background:#27ae60;
}

.in_use{
    background:#f39c12;
}

.out_of_order{
    background:#e74c3c;
}

.message{
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}
</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div><strong>Laundry System</strong></div>
    <div>
      
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
                    <a 
                        href="building.php?location=<?php echo urlencode($b); ?>"
                        class="<?php echo ($b == $location) ? 'active' : ''; ?>"
                    >
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
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>

    <!-- Main -->
    <div class="main">
        <?php if (empty($location)): ?>

            <div class="message">
                <h2>No building selected.</h2>
                <p>Please select a building from the sidebar.</p>
            </div>

        <?php else: ?>

            <h1><?php echo htmlspecialchars($location); ?> Machines</h1>

            <?php if (empty($machines)): ?>

                <div class="message">
                    <p>No machines found for this building.</p>
                </div>

            <?php else: ?>

                <table>
                    <tr>
                        <th>Machine Number</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>

                    <?php foreach ($machines as $machine): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($machine['machine_number']); ?></td>
                            <td><?php echo htmlspecialchars($machine['machine_type']); ?></td>
                            <td>
                                <span class="status <?php echo htmlspecialchars($machine['status']); ?>">
                                    <?php echo htmlspecialchars(str_replace("_", " ", $machine['status'])); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>

            <?php endif; ?>

        <?php endif; ?>
    </div>

</div>

</body>
</html>
