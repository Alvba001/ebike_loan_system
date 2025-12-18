<?php
session_start();
include 'includes/db_connect.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // verify password (admin uses plain text, borrowers are hashed)
        if ($password === $row['password'] || password_verify($password, $row['password'])) {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('No account found with that email address.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="auth-body">

<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Login</h2>

        <form action="" method="POST" class="auth-form" onsubmit="return validateLogin()">

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" name="login" class="btn-primary">Login</button>
        </form>

        <p class="auth-text">
            Don’t have an account? 
            <a href="register.php" class="auth-link">Register here</a>
        </p>
    </div>
</div>

<script>
function validateLogin() {
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();

    if (email === "" || password === "") {
        alert("Please enter both email and password!");
        return false;
    }
    return true;
}
</script>

</body>
</html>
