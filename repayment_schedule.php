<?php
session_start();
include 'includes/db_connect.php';
include 'includes/borrower_header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'borrower') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user loan
$loan = $conn->query("SELECT * FROM loan_applications WHERE user_id='$user_id' AND status='approved'")->fetch_assoc();

if (!$loan) {
    echo "<script>alert('You have no approved loan.'); window.location='dashboard.php';</script>";
    exit();
}

$loan_id = $loan['loan_id'];

$schedule = $conn->query("SELECT * FROM repayment_schedule WHERE loan_id='$loan_id' ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Repayment Schedule</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

<h2>Repayment Schedule</h2>
<p>Your monthly repayment breakdown.</p>

<div class="card table-card">

<table class="table">
    <thead>
        <tr>
            <th>Due Date</th>
            <th>Amount (₦)</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
    <?php while ($row = $schedule->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['due_date'] ?></td>
            <td>₦<?= number_format($row['amount_due'], 2) ?></td>
            <td>
                <?php if ($row['status'] == 'pending') { ?>
                    <span style="color: orange; font-weight: bold;">Pending</span>
                <?php } else { ?>
                    <span style="color: green; font-weight: bold;">Paid</span>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>

</div>

<br>
<a href="dashboard.php" class="btn btn-secondary">⬅ Back</a>

</div>
</body>
</html>
