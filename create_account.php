<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db.php");

$error = $success = "";

if (isset($_POST['register'])) {

    $email    = trim($_POST['email']);
    $name     = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'admin'; // Default role for self-registration

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

            if ($stmt->execute()) {
                $success = "Account created successfully! You can now <a href='index.php'>login</a>.";
            } else {
                if ($conn->errno === 1062) {
                    $error = "This email is already registered.";
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
            $stmt->close();
        } else {
            $error = "Database error: unable to prepare statement.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
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
        h1 { margin: 0; font-size: 28px; color: #2c3e50; }
        h2 { margin-top: 10px; font-size: 20px; color: #555; }
        form { margin-top: 25px; text-align: left; }
        label { font-size: 14px; color: #333; }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
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
            margin-top: 10px;
        }
        button:hover { background: #2980b9; }
        button.secondary { background: #95a5a6; }
        button.secondary:hover { background: #7f8c8d; }
        .error { color: #e74c3c; margin: 10px 0 0 0; font-size: 14px; }
        .success { color: #27ae60; margin: 10px 0 0 0; font-size: 14px; }
        .success a { color: #27ae60; text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <h1>Laundry System</h1>
    <h2>Create Account</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required
               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit" name="register">Create Account</button>
        <button type="button" class="secondary" onclick="window.location.href='first_page.php'">
            Back to Home
        </button>
    </form>
</div>
</body>
</html>