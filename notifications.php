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

// Fetch notifications
$sql = "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY date_sent DESC";
$result = $conn->query($sql);

// Mark all as read
$conn->query("UPDATE notifications SET status='read' WHERE user_id='$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .page-header {
            margin-bottom: 25px;
        }

        .card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .note-box {
            background: #f5f8ff;
            padding: 15px 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            border-left: 5px solid #004aad;
        }

        .note-message {
            font-size: 15px;
            margin: 0 0 5px 0;
            color: #333;
        }

        .note-time {
            font-size: 13px;
            color: #555;
            display: block;
        }

        .empty {
            padding: 15px;
            font-size: 15px;
            text-align: center;
            color: #777;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background: #555;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <h2>Notifications</h2>
        <p>Stay updated on your loan status and repayments.</p>
    </div>

    <div class="card">

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                echo "
                <div class='note-box'>
                    <p class='note-message'>{$row['message']}</p>
                    <span class='note-time'>📅 {$row['date_sent']}</span>
                </div>
                ";
            }

        } else {
            echo "<p class='empty'>No notifications available.</p>";
        }
        ?>

    </div>

    <br>
    <a href='dashboard.php' class='btn-secondary'>⬅ Back to Dashboard</a>

</div>

</body>
</html>
