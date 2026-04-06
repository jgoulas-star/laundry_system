<?php
?>
<!DOCTYPE html>
<html>
<head>
<title>Student Home Page</title>

<style>

body{
    font-family: Arial;
    background-color:#f4f4f4;
    margin:0;
}

/* Header */
.header{
    background-color:#2c3e50;
    color:white;
    padding:15px;
    text-align:center;
    font-size:28px;
}

/* Layout */
.container{
    display:flex;
}

/* Sidebar (Buildings) */
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

/* Main content */
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
}

.status{
    margin-top:10px;
    font-weight:bold;
}

.available{
    color:green;
}

.inuse{
    color:orange;
}

.out{
    color:red;
}

/* Buttons */
button{
    margin-top:10px;
    padding:5px 10px;
}

.logout-button {
    float: right;
    background-color: #e74c3c;
    color: white;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 16px;
}

.logout-button:hover {
    background-color: #c0392b;
}

</style>

</head>

<body>

<div class="header">
    Student Home Page
    <a href="customer_login.php" class="logout-button">Logout</a>
</div>

<div class="container">
    <!-- Sidebar (Buildings) -->
    <div class="sidebar">
        <h3>Buildings</h3>
        <a href="Townhouses_Customer.php">
            <button type="button">Townhouses</button>
        </a>
        <a href="Aubuchon_Customer.php">
            <button type="button">Aubuchon Hall</button>
        </a>
        <a href="Russell_Customer.php">
            <button type="button">Russell Towers</button>
        </a>
        <a href="Mara_Customer.php">
            <button type="button">Mara Village</button>
        </a>
    </div>

    <!-- Main Section -->
    <div class="main">

        <h2>DashBoard</h2>

        <div class="machine-grid">

            <div class="machine-card">
                <h3>Washer W1</h3>
                <p class="status available">Available</p>
                <button>Reserve</button>
                <button>Report</button>
            </div>

            <div class="machine-card">
                <h3>Dryer D1</h3>
                <p class="status inuse">In Use</p>
                <button>Report</button>             
            </div>

            <div class="machine-card">
                <h3>Washer W2</h3>
                <p class="status out">Out of Order</p>
            </div>

        </div>

    </div>

</div>

</body>
</html>

