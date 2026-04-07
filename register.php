<?php
session_start();

// Connect to DB
$servername = "localhost";
$username = "root";
$pasword = "";
$dbname = "laundry_system";

$conn = new mysqli($servername, $username, $pasword, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Check school email
    if (!preg_match("/@([a-z0-9]+\.)?fitchburgstate\.edu$/i", $email)) {
    $error = "Use your school email.";
}
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }
    else {
        $check = $conn->prepare("SELECT user_ID FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Account already exists.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $role = "customer";

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            if ($stmt->execute()) {

                $_SESSION['user'] = $email;

                header("Location: view.php");
                exit();
            } else {
                $error = "Error creating account: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Create Account</title>

<style>

body{
    text-align:center;
    font-family: Arial;
}

h1{
    font-size:48px;
}

h2{
    font-size:36px;
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

/* Error message */
.error{
    color:red;
    font-weight:bold;
}

</style>

</head>

<body>

<h1>Laundry System</h1>
<h2>Create Account</h2>

<?php
if(isset($error) && $error != ""){
    echo "<p class='error'>$error</p>";
}
?>

<form method="POST">

<label>Email</label><br>
<input type="email" name="email" required><br><br>

<label>Name</label><br>
<input type="text" name="name" required><br><br>

<label>Password</label><br>
<input type="password" name="password" required><br><br>

<label>Confirm Password</label><br>
<input type="password" name="confirm_password" required><br><br>

<button type="submit">Create Account</button>

</form>

</body>
</html>


