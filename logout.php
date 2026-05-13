<?php
session_start();

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Unset all session variables
$_SESSION = array();

// Destroy the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// **Prevent the browser from caching this page and the previous ones**
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
h1 { color: #2c3e50; margin-bottom: 10px; }
p { color: #555; margin-bottom: 20px; }
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
.admin { background: #2c3e50; }
.admin:hover { background: #1f2a36; }
.customer { background: #3498db; }
.customer:hover { background: #2980b9; }
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

<!-- Optional: prevent back-button from showing cached admin pages -->
<script>
    // Replace the current history entry so that pressing "back" goes to login instead of a protected page
    if (window.history && window.history.pushState) {
        window.history.pushState(null, document.title, window.location.href);
        window.addEventListener('popstate', function () {
            window.location.href = 'admin_login.php';
        });
    }
</script>
</body>
</html>