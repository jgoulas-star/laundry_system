<?php
require_once("db.php");

// Fetch buildings for sidebar
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

// Handle form submission
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $msg   = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($msg)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);

        if ($stmt->execute()) {
            $success = "Message sent successfully!";
        } else {
            $error = "Something went wrong.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Contact Us</title>

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
    display: block;
    padding: 10px;
    border-radius: 6px;
}

.sidebar ul li a:hover {
    background: #3498db;
}

/* Main */
.main-content{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px;
}

/* Contact box */
.contact-box{
    background:white;
    padding:30px;
    border-radius:10px;
    width:100%;
    max-width:500px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.contact-box h2{
    text-align:center;
    margin-bottom:20px;
}

input, textarea{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:6px;
}

button{
    width:100%;
    padding:12px;
    background:#2c3e50;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
button:hover{
    background:#1abc9c;
}

.success{ color:#27ae60; text-align:center; }
.error{ color:#e74c3c; text-align:center; }
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
    <div class="main-content">
        <div class="contact-box">
            <h2>Contact Us</h2>

            <form method="POST">
                <input type="text" name="name" placeholder="Your Name" required
                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">

                <input type="email" name="email" placeholder="Your Email" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

                <textarea name="message" rows="5" placeholder="Your Message" required><?php
                    echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';
                ?></textarea>

                <button type="submit">Send Message</button>
            </form>

            <?php if ($success): ?>
                <p class="success"><?php echo $success; ?></p>
            <?php elseif ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </div>

</div>

</body>
</html>

