<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laundry_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $email;

            header("Location: view.php");
            exit();
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<style>
body{
    text-align:center;
    font-family: Arial;
    background: url('images/laundry.jpg') no-repeat center center fixed;
    background-size: cover;
}

form{
    display:inline-block;
    text-align:left;
    margin-top:30px;
    background:#f4f4f4;
    padding:20px;
    border-radius:10px;
}

input{
    width:200px;
    padding:5px;
}

button{
    margin-top:10px;
    padding:6px 12px;
}

.error{
    color:red;
    font-weight:bold;
}
</style>

</head>

<body>

<h1>Laundry System</h1>
<h2>Login</h2>

<?php
if ($error != "") {
    echo "<p class='error'>$error</p>";
}
?>

<form method="POST">

<label>Email</label><br>
<input type="email" name="email" required><br><br>

<label>Password</label><br>
<input type="password" name="password" required><br><br>

<button name="login">Login</button>

</form>

<p>Don't have an account? 
    <a href="register.php">Register here</a>
</p>

</body>
</html>