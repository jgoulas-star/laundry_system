<?php
require_once("db.php");

// Fetch all buildings from machines table
$buildings_result = $conn->query("
    SELECT DISTINCT location 
    FROM machines 
    WHERE location IS NOT NULL AND location != '' 
    ORDER BY location
");

$buildings = [];
if ($buildings_result && $buildings_result->num_rows > 0) {
    while ($row = $buildings_result->fetch_assoc()) {
        $buildings[] = $row['location'];
    }
}
// Default fallback buildings if none in database
if (empty($buildings)) {
    $buildings = ['Townhouses', 'Aubuchon Hall', 'Russell Towers', 'Mara Village'];
}

// Handle form submission
$success = '';
$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $msg     = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($msg)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);
        if ($stmt->execute()) {
            $success = "Thank you! Your message has been sent.";
        } else {
            $error = "Something went wrong. Please try again later.";
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
    <title>Contact Us – Laundry System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar – identical to guest dashboard */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            margin-bottom: 30px;
            font-size: 22px;
            border-bottom: 1px solid #3d566e;
            padding-bottom: 15px;
        }
        .sidebar h3 {
            margin: 20px 0 15px;
            font-size: 16px;
            color: #bdc3c7;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar ul li {
            margin-bottom: 8px;
        }
        .sidebar ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-size: 15px;
            display: block;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.2s;
        }
        .sidebar ul li a:hover {
            background: #3498db;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .contact-box {
            background: white;
            padding: 35px;
            border-radius: 12px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .contact-box h2 {
            margin-bottom: 25px;
            color: #2c3e50;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #1abc9c;
        }
        .success { color: #27ae60; margin-top: 15px; }
        .error { color: #e74c3c; margin-top: 15px; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>🧺 Laundry Status</h2>
    <h3 style="margin-top: 30px;">Guest Access</h3>
    <ul>
        <li><a href="guest.php">Home</a></li>
        <li><a href="student_login.php">Student Login</a></li>
        <li><a href="admin_login.php">Admin</a></li>
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="contact-box">
        <h2>Contact Us</h2>

        <form method="POST" action="contact.php">
            <input type="text" name="name" placeholder="Your Name" required
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">

            <input type="email" name="email" placeholder="Your Email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">

            <textarea name="message" rows="5" placeholder="Your Message" required><?php
                echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '';
            ?></textarea>

            <button type="submit">Send Message</button>
        </form>

        <?php if ($success): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>