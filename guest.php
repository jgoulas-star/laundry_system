<?php
// no session needed (guest view)
?>
<!DOCTYPE html>
<html>
<head>
<title>Laundry System - Guest</title>

<style>

body{
    font-family: Arial;
    margin:0;
    background: url('images/laundry.jpg') no-repeat center center fixed;
    background-size: cover;
}

/* Top Navbar */
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

/* Main (empty for home) */
.main{
    flex:1;
    padding:20px;
    color:white;
}

</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div>
        Laundry System
    </div>

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
        <h1>Welcome</h1>
        <p>Select a building to view available machines.</p>
    </div>

</div>

</body>
</html>