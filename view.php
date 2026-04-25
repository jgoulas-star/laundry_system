<?php
session_start();

/*Redirect if not logged in.*/
if (!isset($_SESSION['user'])) {
    header("Location: register.php");
    exit();
}

/*Connect DB*/
$conn = new mysqli("localhost", "root", "", "laundry_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/*Get user*/
$email = $_SESSION['user'];

$stmt = $conn->prepare("SELECT user_ID, name, email, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$userId = $user['user_ID'];

$page = $_GET['page'] ?? "home";

$messageSent = "";

/*Check if reserved*/
function isReserved($conn, $machineId) {

    $stmt = $conn->prepare("
        SELECT 1 
        FROM reservations 
        WHERE machine_Id = ? 
        AND status = 'active'
        LIMIT 1
    ");

    $stmt->bind_param("i", $machineId);
    $stmt->execute();
    $res = $stmt->get_result();

    return $res->num_rows > 0;
}


/*Reservation*/
if (isset($_POST['reserve'])) {

    $machineId = $_POST['machine_id'];

    // checks if the user must exist
    if (!isset($userId)) {
        die("ERROR: User ID not found (session/login issue)");
    }

    //Check if already reserved
    $check = $conn->prepare("
        SELECT * 
        FROM reservations 
        WHERE machine_Id = ? 
        AND status = 'active'
    ");

    if (!$check) {
        die("Prepare failed (check query): " . $conn->error);
    }

    $check->bind_param("i", $machineId);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {

        $messageSent = "Machine already reserved.";

    } else {

        /*Time setup*/
        $start = date("Y-m-d H:i:s");
        $end = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $insert = $conn->prepare("
            INSERT INTO reservations 
            (Reservation_ID, user_id, machine_Id, reservation_start, reservation_end, status)
            VALUES (NULL, ?, ?, ?, ?, 'active')
        ");

        if (!$insert) {
            die("Prepare failed (insert query): " . $conn->error);
        }

        $insert->bind_param("iiss", $userId, $machineId, $start, $end);

        /*Error check*/
        if ($insert->execute()) {
            $messageSent = "Machine reserved successfully!";
        } else {
            die("Insert failed: " . $insert->error);
        }
    }
}


/*Contact*/
if (isset($_POST['send'])) {
    $messageSent = "Your message has been sent!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>

<style>
body{
    font-family: Arial;
    text-align:center;
    background: url('images/laundry.jpg') no-repeat center center fixed;
    background-size: cover;
    color:white;
}

.navbar{
    background:#2c3e50;
    padding:15px;
}

.navbar a{
    color:white;
    margin:0 15px;
    text-decoration:none;
    font-weight:bold;
}

.card{
    background: rgba(0,0,0,0.75);
    padding:20px;
    margin:20px auto;
    width:90%;
    border-radius:12px;
}

.machine-grid{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:15px;
}

.machine-card{
    background: rgba(255,255,255,0.95);
    color:black;
    padding:10px;
    border-radius:10px;
}

.machine-img{
    width:70px;
    height:70px;
}

.available{ color:green; font-weight:bold; }
.inuse{ color:orange; font-weight:bold; }
.out{ color:red; font-weight:bold; }

button{
    background:#1abc9c;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:6px;
    cursor:pointer;
}
button:hover{
    background:#16a085;
}

textarea{
    width:80%;
    padding:10px;
    border-radius:8px;
}
</style>

</head>

<body>

<div class="navbar">
    <a href="view.php?page=home">Home</a>
    <a href="view.php?page=contact">Contact</a>
    <a href="view.php?page=account">My Account</a>
</div>

<div class="main">

<?php
/*Home page*/
if ($page == "home") {

    echo "<div class='card'>";
    echo "<h2>Welcome, " . $user['name'] . "</h2>";
    echo "<p>$messageSent</p>";
    echo "</div>";

     function machineCard($conn, $id, $name, $img, $statusText, $class)
    {
        echo "<div class='machine-card'>
            <h3>$name</h3>
            <img src='images/$img' class='machine-img'>
            <p class='$class'>$statusText</p>";

        // check reservation
        if (isReserved($conn, $id)) {
            echo "<p style='color:red;font-weight:bold;'>RESERVED</p>";
        } else {
            if ($class == "available") {
                echo "
                <form method='post'>
                    <input type='hidden' name='machine_id' value='$id'>
                    <button type='submit' name='reserve'>Reserve</button>
                </form>
                ";
            }
        }

        echo "</div>";
    }

    /*Buildings*/
    echo "<div class='card'><h2>Townhouses</h2><div class='machine-grid'>";
    machineCard($conn, 1, "Washer W1", "available.png", "Available", "available");
    machineCard($conn, 2, "Dryer D1", "inuse.png", "In Use", "inuse");
    echo "</div></div>";

    echo "<div class='card'><h2>Aubuchon Hall</h2><div class='machine-grid'>";
    machineCard($conn, 3, "Washer W2", "available.png", "Available", "available");
    machineCard($conn, 4, "Dryer D2", "outoforder.png", "Out of Order", "out");
    echo "</div></div>";

    echo "<div class='card'><h2>Russell Towers</h2><div class='machine-grid'>";
    machineCard($conn, 5, "Washer W3", "inuse.png", "In Use", "inuse");
    machineCard($conn, 6, "Dryer D3", "available.png", "Available", "available");
    echo "</div></div>";

    echo "<div class='card'><h2>Mara Village</h2><div class='machine-grid'>";
    machineCard($conn, 7, "Washer W4", "outoforder.png", "Out of Order", "out");
    machineCard($conn, 8, "Dryer D4", "available.png", "Available", "available");
    echo "</div></div>";
}

/*Contact Page*/
elseif ($page == "contact") {
?>
    <div class="card">
        <h2>Contact</h2>

        <form method="post">
            <textarea name="message" placeholder="Write something.." rows="5" required></textarea><br><br>
            <button name="send">Send</button>
        </form>

        <p><?php echo $messageSent; ?></p>
    </div>
<?php
}

/*Account Page*/
elseif ($page == "account") {
?>
    <div class="card">
        <h2>My Account</h2>

        <p>Name: <?php echo $user['name']; ?></p>
        <p>Email: <?php echo $user['email']; ?></p>
        <p>Role: <?php echo $user['role']; ?></p>
    </div>
<?php
}
?>

</div>

</body>
</html>