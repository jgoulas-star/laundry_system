<?php
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

/* TOP NAVBAR */
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

/* Sidebar (from second code) */
.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    padding: 20px;
    min-height: calc(100vh - 60px); /* prevents overlap with navbar */
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
    padding:20px;
}

/* Machine cards */
.machine-grid{
    display:grid;
    grid-template-columns: repeat(3, 1fr);
    gap:15px;
}

.machine-card{
    background:white;
    padding:15px;
    border-radius:8px;
    box-shadow:0px 2px 5px rgba(0,0,0,0.2);
    text-align:center;
}

.status{
    margin-top:10px;
    font-weight:bold;
}

/* Status colors */
.available{ color:green; }
.inuse{ color:orange; }
.out{ color:red; }

/* Status icons */
.status img{
    width:40px;
    height:40px;
    display:block;
    margin:10px auto;
}

/* Buttons */
button{
    margin-top:10px;
    padding:6px 12px;
    cursor:pointer;
    border:none;
    border-radius:5px;
}
</style>
</head>

<body>

<!-- Navbar -->
<div class="navbar">
    <div>
        <div><strong>Laundry System</strong></div>
    </div>

    <div>
        <a href="contact.php">Contact</a>
    </div>
</div>

<div class="wrapper">

    <!--sidebar-->
    <div class="sidebar">
        <h2>🧺Laundry Status</h2>

        <h3>Buildings</h3>
        <ul>
            <li><a href="Townhouses.php">Townhouses</a></li>
            <li><a href="Aubuchon_hall.php">Aubuchon Hall</a></li>
            <li><a href="Russell_tower.php">Russell Towers</a></li>
            <li><a href="Mara_village.php">Mara Village</a></li>
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

        <h2 style = "color: black">Mara Village</h2>

        <div class="machine-grid">

            <!-- Available Machine -->
            <div class="machine-card">
                <h3>Washer W1</h3>

                <img src="images/available.png" alt="Available">

                <p class="status available">Available</p>

               
            </div>

            <!-- In Use Machine -->
            <div class="machine-card">
                <h3>Dryer D1</h3>

                <img src="images/inuse.png" alt="In Use">

                <p class="status inuse">In Use</p>
            </div>

            <!-- Out of Order -->
            <div class="machine-card">
                <h3>Washer W2</h3>

                <img src="images/outoforder.png" alt="Out of Order">

                <p class="status out">Out of Order</p>
            </div>

        </div>

    </div>

</div>

</body>
</html>

