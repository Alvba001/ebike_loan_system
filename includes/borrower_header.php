<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict to borrowers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'borrower') {
    header("Location: login.php");
    exit();
}
?>

<!-- GLOBAL BORROWER NAVBAR -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>

<body>

<header class="navbar">
    <div class="nav-container">

        <!-- Left menu -->
        <ul class="nav-left">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="apply_loan.php">Apply for Loan</a></li>
        </ul>

        <!-- Right menu -->
        <ul class="nav-right">
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>

    </div>
</header>

</body>
</html>
