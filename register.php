<?php
include 'includes/db_connect.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // plain text as you requested

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('This email is already registered! Please log in instead.'); window.location='login.php';</script>";
    } else {
        $sql = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error while registering. Please try again.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="auth-body">

<div class="auth-container">
    <div class="auth-card">
        <h2 class="auth-title">Create an Account</h2>

        <form action="" method="POST" class="auth-form" onsubmit="return validateForm()">

            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" id="name" required>
            </div>

            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" id="email" required>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" name="register" class="btn-primary">Register</button>
        </form>

        <p class="auth-text">
            Already have an account?
            <a href="login.php" class="auth-link">Login here</a>
        </p>
    </div>
</div>

<script>
function validateForm() {
    let name = document.getElementById('name').value.trim();
    let email = document.getElementById('email').value.trim();
    let password = document.getElementById('password').value.trim();

    if (name === "" || email === "" || password === "") {
        alert("All fields are required!");
        return false;
    }
    return true;
}
</script>

</body>
</html>
