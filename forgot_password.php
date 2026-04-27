<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laundry_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$step = 1; // step control

if (isset($_POST['check_email'])) {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT user_ID FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['reset_email'] = $email;
        $step = 2;
    } else {
        $message = "Email not found.";
    }
}

if (isset($_POST['reset_password'])) {

    if (!isset($_SESSION['reset_email'])) {
        die("Session expired. Try again.");
    }

    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
        $step = 2;
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $step = 2;
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed, $_SESSION['reset_email']);

        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $message = "Error updating password.";
            $step = 2;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: linear-gradient(135deg, #2c3e50, #3498db);
    height: 100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

.card {
    background:white;
    padding:30px;
    border-radius:10px;
    width:320px;
    text-align:center;
}

input {
    width:100%;
    padding:10px;
    margin:10px 0;
}

button {
    width:100%;
    padding:10px;
    background:#3498db;
    color:white;
    border:none;
    cursor:pointer;
}

.error {
    color:red;
}
</style>

</head>

<body>

<div class="card">

<h2>Forgot Password</h2>

<p class="error"><?php echo $message; ?></p>

<?php if ($step == 1): ?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button name="check_email">Next</button>
</form>

<?php elseif ($step == 2): ?>

<form method="POST">
    <input type="password" name="password" placeholder="New password" required>
    <input type="password" name="confirm" placeholder="Confirm password" required>
    <button name="reset_password">Reset Password</button>
</form>

<?php endif; ?>

</div>

</body>
</html>
