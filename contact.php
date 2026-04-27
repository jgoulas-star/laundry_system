<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laundry_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Contact Us</title>

<style>

body{
    font-family: Arial;
    margin:0;
    background-color:#f4f4f4;
    
}

/* Navbar */
.navbar{
    background-color:#2c3e50;
    color:white;
    padding:15px;
    display:flex;
    justify-content:space-between;
}

.navbar a{
    color:white;
    margin:0 10px;
    text-decoration:none;
    font-weight:bold;
}

.navbar a:hover{
    color:#1abc9c;
}

/* Layout */
.container{
    display:flex;
}

/* Sidebar */
.sidebar{
    width:220px;
    background:#34495e;
    color:white;
    height:100vh;
    padding-top:20px;
}

.sidebar h3{
    text-align:center;
}

.sidebar a{
    display:block;
    color:white;
    padding:12px;
    margin:5px;
    text-decoration:none;
    background:#2c3e50;
    text-align:center;
    border-radius:5px;
}

.sidebar a:hover{
    background:#1abc9c;
}

/* Main */
.main{
    flex:1;
    padding:30px;
    display:flex;
    justify-content:center;   /* horizontal center */
    align-items:center;       /* vertical center */
    
}

/* Form */
.contact-box{
    background:white;
    padding:25px;
    border-radius:8px;
    max-width:500px;
    box-shadow:0px 2px 5px rgba(0,0,0,0.2);
    
    
}

.contact-box h2{
    margin-bottom:15px;
    display: flex;
    justify-content: center;
    align-items: center;
    
}

input, textarea{
    width:100%;
    padding:10px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:5px;
}

button{
    background:#2c3e50;
    color:white;
    padding:10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
    width:100%;
}

button:hover{
    background:#1abc9c;
}

</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div>Laundry System</div>

    <div>
        <a href="guest.php">Home</a>
        <a href="contact.php">Contact</a>
        <a href="register.php">Sign Up</a>
        <a href="login.php">Login</a>
    </div>
</div>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Buildings</h3>

        <a href="Townhouses.php">Townhouses</a>
        <a href="Aubuchon_hall.php">Aubuchon Hall</a>
        <a href="Russell_tower.php">Russell Towers</a>
        <a href="Mara_village.php">Mara Village</a>
    </div>

    <!-- Main -->
    <div class="main">

        <div class="contact-box">
            <h2>Contact Us</h2>

            <form action="contact.php" method="POST">
                <input type="text" name="name" placeholder="Your Name" required>

                <input type="email" name="email" placeholder="Your Email" required>

                <textarea name="message" rows="5" placeholder="Your Message" required></textarea>

                <button type="submit">Send Message</button>
            </form>

            <?php
            if($_SERVER["REQUEST_METHOD"] == "POST"){
                $name = $_POST['name'];
                $email = $_POST['email'];
                $message = $_POST['message'];

                echo "<p style='color:green; margin-top:10px;'>Message sent!</p>";
            }
            ?>

        </div>

    </div>

</div>

</body>
</html>

