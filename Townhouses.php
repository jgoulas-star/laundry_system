<?php
?>
<!DOCTYPE html>
<html>
<head>
<title>Laundry System - Guest</title>

<style>

body{
    font-family: Arial;
    background-color:#f4f4f4;
    margin:0;
    background: url('images/laundry.jpg') no-repeat center center fixed;
    background-size: cover;
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
    width:200px;
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

        <h2 style = "color: white">Townhouses</h2>

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

