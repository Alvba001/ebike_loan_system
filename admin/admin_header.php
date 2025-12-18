<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Restrict to admin users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<header class="navbar admin-navbar">
    <div class="nav-container">

        <!-- Left Navigation -->
        <ul class="nav-left">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="view_loans.php">Loan Applications</a></li>
            <li><a href="view_repayments.php">Repayments</a></li>
            <li><a href="reports.php">Reports</a></li>
        </ul>

        <!-- Right Navigation -->
        <ul class="nav-right">
            <li><a href="../logout.php" class="logout-btn">Logout</a></li>
        </ul>

    </div>
</header>

</body>
</html>
