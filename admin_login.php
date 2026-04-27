<?php
session_start();
require_once(__DIR__ . "/db.php");

$error = "";

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_POST['login'])) {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT user_ID, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                if ($user['role'] !== 'admin') {
                    $error = "Access denied. This account is not an administrator.";
                } else {
                    $_SESSION['user_id']    = $user['user_ID'];
                    $_SESSION['user_name']  = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role']  = $user['role'];
                    header("Location: main_admin_page.php");
                    exit();
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Laundry System</title>
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
        .error { color: #e74c3c; margin-top: 10px; font-size: 14px; }
        .back-link {
            display: block;
            margin-top: 15px;
            color: #7f8c8d;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Laundry System</h1>
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required
               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <button type="submit" name="login">Login</button>
        <button class="btn-view" onclick="window.location.href='forgot_password.php'">
     Forgot Password
    </button>
    </form>
    <a href="first_page.php" class="back-link">&larr; Back to Home</a>
</div>
</body>
</html>