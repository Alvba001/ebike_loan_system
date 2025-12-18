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

/* ================= FETCH APPROVED LOAN ================= */
$loan = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id='$user_id' AND status='approved'
    ORDER BY loan_id DESC
    LIMIT 1
")->fetch_assoc();

if (!$loan) {
    echo "<script>alert('You have no approved loan for repayment.'); window.location='dashboard.php';</script>";
    exit();
}

$loan_id = $loan['loan_id'];

/* ================= FETCH NEXT DUE SCHEDULE ================= */
$next = $conn->query("
    SELECT * FROM repayment_schedule
    WHERE loan_id='$loan_id' AND status='pending'
    ORDER BY due_date ASC
    LIMIT 1
")->fetch_assoc();

if (!$next) {
    echo "<script>alert('You have completed all repayments!'); window.location='dashboard.php';</script>";
    exit();
}

$amount_due = floatval($next['amount_due']);
$due_date   = $next['due_date'];

/* ================= HANDLE PAYMENT ================= */
if (isset($_POST['pay'])) {

    $amount_paid = floatval($_POST['amount_paid']);

    if ($amount_paid <= 0) {
        echo "<script>alert('Invalid payment amount.');</script>";
    }
    elseif ($amount_paid != $amount_due) {
        echo "<script>alert('You must pay the exact amount: ₦" . number_format($amount_due, 2) . "');</script>";
    }
    else {

        $schedule_id = $next['schedule_id'];

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert repayment
            $conn->query("
                INSERT INTO repayments (loan_id, amount_paid, payment_date)
                VALUES ('$loan_id', '$amount_paid', CURDATE())
            ");

            // Mark schedule as PAID
            $conn->query("
                UPDATE repayment_schedule
                SET status='paid', date_paid=CURDATE()
                WHERE schedule_id='$schedule_id' AND status='pending'
            ");

            $conn->commit();

            echo "<script>alert('Repayment successful!'); window.location='repayment_history.php';</script>";
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Payment failed. Please try again.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Repayment</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<div class="container">

    <div class="page-header">
        <h2>Make Repayment</h2>
        <p>Submit a repayment for your approved electric bike loan.</p>
    </div>

    <div class="card">

        <div class="info-box">
            <p><strong>Loan Amount:</strong> ₦<?php echo number_format($loan['amount']); ?></p>
            <p><strong>Bike Model:</strong> <?php echo htmlspecialchars($loan['bike_model']); ?></p>
            <p><strong>Duration:</strong> <?php echo intval($loan['duration']); ?> Months</p>
            <p><strong>Next Due Amount:</strong> ₦<?php echo number_format($amount_due,2); ?></p>
            <p><strong>Due Date:</strong> <?php echo $due_date; ?></p>
        </div>

        <form action="" method="POST">

            <label>Amount Due (₦)</label>
            <input type="number" class="form-control" value="<?php echo $amount_due; ?>" readonly>

            <label>Pay Amount (₦)</label>
            <input type="number"
                   name="amount_paid"
                   class="form-control"
                   value="<?php echo $amount_due; ?>"
                   readonly
                   required>

            <button type="submit" name="pay" class="btn-primary">
                Confirm Payment
            </button>

        </form>

    </div>

    <a href="dashboard.php" class="btn-secondary">⬅ Back to Dashboard</a>

</div>

</body>
</html>
