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
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

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
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    text-align: center;
}

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

form {
    margin-top: 25px;
    text-align: left;
}

label {
    font-size: 14px;
    color: #333;
}

input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.2s;
    box-sizing: border-box;
}

input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 5px rgba(52,152,219,0.5);
}

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

button.secondary {
    background: #95a5a6;
}

button.secondary:hover {
    background: #7f8c8d;
}

.error {
    color: #e74c3c;
    margin: 10px 0 0 0;
    text-align: center;
    font-size: 14px;
}
</style>

</head>

<body>

<div class="card">
    <h1>Laundry System</h1>
    <h2>Login</h2>

    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Email</label>
        <input type="email" name="email" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="login">Login</button>

        <button type="button" class="secondary" onclick="window.location.href='register.php'">
            Create Account
        </button>

    </form>
</div>

</body>
</html>