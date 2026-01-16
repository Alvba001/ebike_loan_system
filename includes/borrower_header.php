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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="assets/css/style.css"> 
    <style>
        /* Header Specific Overrides to ensure desired look */
        .navbar-custom {
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 10px 0;
            margin-bottom: 20px;
        }
        .brand-text {
            color: #004aad; /* Blue color */
            font-weight: 700;
            font-size: 1.25rem;
            margin-left: 10px;
            text-decoration: none;
        }
        .brand-logo {
            height: 40px;
            width: auto;
        }
        .nav-icon {
            font-size: 1.4rem;
            color: #555;
            margin-right: 15px;
            transition: color 0.3s;
        }
        .nav-icon:hover {
            color: #004aad;
        }
        .logout-link {
            color: #dc3545;
            font-weight: 600;
            text-decoration: none;
        }
        .logout-link:hover {
            text-decoration: underline;
        }
        /* Fix container conflict if any */
        .container-custom {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }
    </style>
</head>

<body>

<header class="navbar-custom">
    <div class="container-custom">

        <!-- Left: Logo + Text -->
        <div class="d-flex align-items-center">
            <img src="assets/img/logo.png.png" alt="Logo" class="brand-logo">
            <span class="brand-text">E-bike Loan System</span>
        </div>

        <!-- Right: Icons + Logout -->
        <div class="d-flex align-items-center">
            

            <!-- Profile Icon -->
            <a href="profile.php" class="nav-icon" title="Profile">
                <i class="bi bi-person-circle"></i>
            </a>
            
            <a href="logout.php" class="logout-link">Logout</a>
        </div>

    </div>
</header>

