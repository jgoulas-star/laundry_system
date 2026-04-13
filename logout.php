<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Logged Out</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.card {
    background: white;
    padding: 40px;
    border-radius: 12px;
    width: 350px;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

h1 {
    color: #2c3e50;
    margin-bottom: 10px;
}

p {
    color: #555;
    margin-bottom: 20px;
}

button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 6px;
    margin-top: 10px;
    cursor: pointer;
    font-size: 15px;
    color: white;
}

.admin {
    background: #2c3e50;
}

.admin:hover {
    background: #1f2a36;
}

.customer {
    background: #3498db;
}

.customer:hover {
    background: #2980b9;
}
</style>
</head>

<body>

<div class="card">
    <h1>Logged Out</h1>
    <p>You have successfully logged out.</p>

    <button class="admin" onclick="window.location.href='admin_login.php'">
        Admin Login
    </button>

    <button class="customer" onclick="window.location.href='student_login.php'">
        Student Login
    </button>
</div>

</body>
