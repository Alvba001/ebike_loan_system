<?php
session_start();
include '../includes/db_connect.php';

// Only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: view_loans.php");
    exit();
}

$loan_id = $_GET['id'];

// Fetch loan details
$loan = $conn->query("SELECT * FROM loan_applications WHERE loan_id='$loan_id'")->fetch_assoc();

if (!$loan) {
    echo "<script>alert('Loan not found.'); window.location='view_loans.php';</script>";
    exit();
}

$amount     = $loan['amount'];
$duration   = $loan['duration'];
$monthly_payment = $amount / $duration;

// Approve loan
$conn->query("UPDATE loan_applications SET status='approved' WHERE loan_id='$loan_id'");

// Generate repayment schedule
for ($i = 1; $i <= $duration; $i++) {

    // Due date = 1 month from now, 2 months, 3 months... etc.
    $due_date = date("Y-m-d", strtotime("+$i month"));

    $conn->query("
        INSERT INTO repayment_schedule (loan_id, due_date, amount_due)
        VALUES ('$loan_id', '$due_date', '$monthly_payment')
    ");
}

echo "<script>
alert('Loan approved and repayment schedule generated!');
window.location='loan_details.php?id=$loan_id';
</script>";
?>
