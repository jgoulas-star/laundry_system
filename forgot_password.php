<!DOCTYPE html>
<html>
<head>

<title>Forgot Password</title>

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

/* Card */
.card {
    background: white;
    padding: 40px;
    border-radius: 12px;
    width: 350px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    text-align: center;
}

/* Titles */
h1 {
    margin: 0;
    font-size: 28px;
    color: #2c3e50;
}

h2 {
    margin-top: 10px;
    font-size: 20px;
    color: #555;
}

/* Form */
form {
    margin-top: 25px;
    text-align: left;
}

/* Labels */
label {
    font-size: 14px;
    color: #333;
}

/* Inputs */
input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.2s;
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 5px rgba(52,152,219,0.5);
}

/* Buttons */
button {
    width: 100%;
    padding: 12px;
    background: #3498db;
    border: none;
    color: white;
    font-size: 16px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.2s;
    margin-top: 10px;
}

button:hover {
    background: #2980b9;
}

/* Secondary button */
.secondary {
    background: #95a5a6;
    margin-top: 10px;
}

.secondary:hover {
    background: #7f8c8d;
}

/* Error */
.error {
    color: red;
    text-align: center;
    font-size: 14px;
    margin-bottom: 10px;
}

</style>

</head>

<body>

<div class="card">

<h1>Laundry System</h1>
<h2>Forgot Password</h2>

<?php
if(isset($error)){
    echo "<p class='error'>$error</p>";
}
?>

<form method="POST">

<label>Email</label>
<input type="text" name="email" required>

<button type="submit">Send email</button>

<button type="button" class="secondary" onclick="window.location.href='admin_login.php'">
    Back to login
</button>


</form>

</div>

</body>
</html>