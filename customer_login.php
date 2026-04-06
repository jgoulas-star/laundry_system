<?php
?>

<!DOCTYPE html>
<html>
<head>

<title>Customer Login</title>

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
}

input{
    width:200px;
    padding:5px;
}

button{
    margin-top:10px;
    padding:6px 12px;
}

</style>

</head>

<body>

<h1>Laundry System</h1>

<?php
if(isset($error)){
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="POST">

<label>Email</label><br>
<input type="text" name="email"><br><br>

<label>Password</label><br>
<input type="password" name="password"><br><br>

<a1 href="student_page.php">
<button type="submit">Login</button>
</a1>

<a href="create_account.php">
<button type="button">Forgot Password</button>
</a>

</form>

</body>
</html>


