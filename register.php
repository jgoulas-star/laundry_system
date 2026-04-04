<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laundry_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    //Check school email
    if (!preg_match("/@fitchburgstate\.edu$/", $email)) {
        $message = "Please use your school email.";
    }
    //Check password length
    elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    }
    else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prevent duplicate emails
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $hashedPassword);

            if ($stmt->execute()) {
                //Login user
                $_SESSION['user'] = $email;

                //Redirect to view page
                header("Location: view.php");
                exit();
            } else {
                $message = "Error registering user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Register</h1>

<div class="form-box">
    <form method="post">
        <input type="email" name="email" placeholder="School Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button name="register">Register</button>
    </form>

   <?php if ($message != ""): ?>
        <p class="message <?php echo $messageType; ?>"><?php echo $message; ?></p>
    <?php endif; ?>
</div>

</body>
</html>