<?php
include 'includes/db_connect.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Keeping plain text as requested

    // 1. Check if email already exists (Securely)
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('This email is already registered! Please log in instead.'); window.location='login.php';</script>";
    } else {
        $stmt->close(); // Close previous statement

        // 2. Insert new user (Securely)
        // This fixes your syntax error permanently because it separates code from data
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error while registering. Please try again.');</script>";
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>

<body class="auth-body">

<div class="auth-container">
    <div class="auth-card">
        <img src="assets/img/logo.png.png" alt="Logo" class="auth-logo">
        <div class="auth-system-name">E-Bike Loan System</div>
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
        <p class="auth-text" style="font-size: 0.9em; margin-top: 10px;">
            By registering, you agree to our <a href="terms.php" class="auth-link">Terms and Conditions</a>
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
