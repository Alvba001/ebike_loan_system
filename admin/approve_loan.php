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

// Interest Logic
$interest_rate = 1.0; // Default
if ($duration == 3) {
    $interest_rate = 1.10; // 110%
} elseif ($duration == 6) {
    $interest_rate = 1.15; // 115%
} elseif ($duration == 12) {
    $interest_rate = 1.20; // 120%
}

$total_payable = $amount * $interest_rate;
$monthly_payment = $total_payable / $duration;

// Approve loan
$updateStmt = $conn->prepare("UPDATE loan_applications SET status='approved' WHERE loan_id=?");
$updateStmt->bind_param("i", $loan_id);
if (!$updateStmt->execute()) {
    echo "<script>alert('Error approving loan: " . $updateStmt->error . "'); window.location='loan_details.php?id=$loan_id';</script>";
    exit();
}

// Generate repayment schedule
$stmt = $conn->prepare("INSERT INTO repayment_schedule (loan_id, due_date, amount_due, status) VALUES (?, ?, ?, 'pending')");

for ($i = 1; $i <= $duration; $i++) {
    // Due date = 1 month from now, 2 months, 3 months... etc.
    $due_date = date("Y-m-d", strtotime("+$i month"));
    
    // Bind: loan_id (int), due_date (string), amount_due (double)
    $stmt->bind_param("isd", $loan_id, $due_date, $monthly_payment);
    
    if (!$stmt->execute()) {
        // Log error (or display for now)
        die("Error generating schedule for month $i: " . $stmt->error);
    }
}

echo "<script>
alert('Loan approved and repayment schedule generated!');
window.location='loan_details.php?id=$loan_id';
</script>";
?>
