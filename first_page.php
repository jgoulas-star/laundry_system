<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry System</title>
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
            margin: 0 0 10px 0;
            font-size: 28px;
            color: #2c3e50;
        }

        .subtitle {
            color: #555;
            margin-bottom: 30px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
            font-weight: 500;
        }

        .btn-admin {
            background: #2c3e50;
        }
        .btn-admin:hover {
            background: #1a252f;
        }

        .btn-student {
            background: #3498db;
        }
        .btn-student:hover {
            background: #2980b9;
        }

        .btn-view {
            background: #27ae60;
        }
        .btn-view:hover {
            background: #1e8449;
        }

        .footer {
            margin-top: 20px;
            color: #7f8c8d;
            font-size: 14px;
        }

        .footer a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Laundry System</h1>
    <div class="subtitle">Choose an option</div>

    <button class="btn-admin" onclick="window.location.href='admin_login.php'">
     Admin Login
    </button>

    <button class="btn-student" onclick="window.location.href='student_login.php'">
     Student / Resident Login
    </button>

    <button class="btn-view" onclick="window.location.href='guest.php'">
     View Machine Status
    </button>
    
    <button class="btn-view" onclick="window.location.href='forgot_password.php'">
     Forgot Password
    </button>

    <div class="footer">
        Need an account?<br>
        <a href="create_account.php">Create Account</a>
    </div>
</div>

</body>
</html>