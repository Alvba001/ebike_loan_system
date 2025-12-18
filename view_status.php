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

// Fetch all loans
$loans = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id='$user_id'
    ORDER BY date_applied DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Status</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .loan-box {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: #fff;
        }

        .pending { background: orange; }
        .approved { background: green; }
        .rejected { background: red; }

        .progress-bar {
            width: 100%;
            height: 10px;
            background: #eee;
            border-radius: 8px;
            margin-top: 8px;
        }

        .progress {
            height: 10px;
            background: #004aad;
            border-radius: 8px;
        }

        .info-box {
            background: #f5f8ff;
            padding: 12px;
            border-left: 4px solid #004aad;
            border-radius: 8px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <h2>Loan Status</h2>
        <p>Track all your loan applications and repayments.</p>
    </div>

    <?php
    if ($loans->num_rows == 0) {
        echo "<p>No loan applications found.</p>";
    }

    while ($loan = $loans->fetch_assoc()) {

        $loan_id = $loan['loan_id'];

        // Assigned bike
        $assign = $conn->query("
            SELECT b.model, b.serial_number
            FROM bike_assignments a
            JOIN bikes b ON a.bike_id = b.bike_id
            WHERE a.loan_id='$loan_id'
        ")->fetch_assoc();

        // Repayment progress
        $paid = $conn->query("
            SELECT COUNT(*) AS total
            FROM repayment_schedule
            WHERE loan_id='$loan_id' AND status='paid'
        ")->fetch_assoc()['total'];

        $total = (int)$loan['duration'];
        $percent = ($total > 0) ? round(($paid / $total) * 100) : 0;

        // Next payment
        $next = $conn->query("
            SELECT amount_due, due_date
            FROM repayment_schedule
            WHERE loan_id='$loan_id' AND status='pending'
            ORDER BY due_date ASC LIMIT 1
        ")->fetch_assoc();

        echo "
        <div class='loan-box'>
            <h3>₦" . number_format($loan['amount']) . " — {$loan['bike_model']}</h3>
            <p><strong>Status:</strong> 
                <span class='badge {$loan['status']}'>{$loan['status']}</span>
            </p>
            <p><strong>Duration:</strong> {$loan['duration']} months</p>
            <p><strong>Date Applied:</strong> {$loan['date_applied']}</p>
        ";

        if ($assign) {
            echo "
            <div class='info-box'>
                <p><strong>Assigned Bike:</strong> {$assign['model']} ({$assign['serial_number']})</p>
            </div>
            ";
        }

        if ($loan['status'] === 'approved') {
            echo "
            <p><strong>Repayment Progress:</strong> {$percent}%</p>
            <div class='progress-bar'>
                <div class='progress' style='width: {$percent}%'></div>
            </div>
            ";
        }

        if ($next) {
            echo "
            <div class='info-box'>
                <p><strong>Next Payment:</strong> ₦" . number_format($next['amount_due']) . "</p>
                <p><strong>Due Date:</strong> {$next['due_date']}</p>
            </div>
            ";
        }

        if (!$next && $loan['status'] === 'approved') {
            echo "
            <div class='info-box' style='border-left-color: green'>
                <p><strong>Loan fully repaid ✔</strong></p>
            </div>
            ";
        }

        echo "</div>";
    }
    ?>

</div>

</body>
</html>
