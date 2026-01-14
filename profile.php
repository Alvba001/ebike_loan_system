<?php
session_start();
include 'includes/db_connect.php';
include 'includes/borrower_header.php';

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'borrower') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id='$user_id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <!-- CSS handling by header -->
</head>
<body>

<div class="container">
    <div class="page-header">
        <h2>My Profile</h2>
        <p>Manage your account details.</p>
    </div>

    <div class="info-box" style="margin-top: 20px; background: #fff; padding: 30px;">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Joined:</strong> <?php echo isset($user['created_at']) ? $user['created_at'] : 'N/A'; ?></p>
    </div>
</div>

</body>
</html>
