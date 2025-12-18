<?php
session_start();
include 'includes/db_connect.php';
include 'includes/borrower_header.php';

// Restrict access to borrowers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'borrower') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Fetch latest loan for the borrower (if any) ---
$loan = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id = '$user_id'
    ORDER BY loan_id DESC
    LIMIT 1
")->fetch_assoc();

$loan_id = $loan ? intval($loan['loan_id']) : null;

// --- Calculate repayment summary if loan exists ---
$paid = 0;
$remaining = 0;
$percent = 0;
if ($loan_id) {
    $payRow = $conn->query("SELECT IFNULL(SUM(amount_paid),0) AS total FROM repayments WHERE loan_id = '$loan_id'")->fetch_assoc();
    $paid = floatval($payRow['total']);
    $remaining = floatval($loan['amount']) - $paid;
    $percent = $loan['amount'] > 0 ? ($paid / $loan['amount']) * 100 : 0;
    if ($percent < 0) $percent = 0;
    if ($percent > 100) $percent = 100;
}

// --- Fetch assigned bike if any ---
$assign = null;
if ($loan_id) {
    $assign = $conn->query("
        SELECT b.serial_number, b.model 
        FROM bike_assignments a
        JOIN bikes b ON a.bike_id = b.bike_id
        WHERE a.loan_id = '$loan_id'
        ORDER BY a.assigned_date DESC
        LIMIT 1
    ")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrower Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .header-row { display:flex; justify-content:space-between; align-items:center; gap:10px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:18px; margin-top:18px; }
        .btn { display:inline-block; margin-top:10px; padding:8px 14px; background:#004aad; color:#fff; border-radius:8px; text-decoration:none; }
        .info-box { background:#f5f8ff; padding:14px; border-left:4px solid #004aad; border-radius:8px; margin-top:20px; }
        .timeline { display:flex; gap:8px; justify-content:space-between; margin-top:18px; }
        .step { flex:1; padding:8px; text-align:center; border-bottom:4px solid #f0d14bff; color:#777; font-weight:600; }
        .step.active { color:#004aad; border-color:#004aad; }
        .progress-box { background:#fbfdff; padding:14px; border-radius:10px; margin-top:14px; }
        .progress-bar { width:100%; background:#e9ecef; height:16px; border-radius:8px; overflow:hidden; margin:10px 0; }
        .progress-bar .fill { height:16px; background:#004aad; width:0%; transition:width .6s; }
    </style>
</head>
<body>

<div class="container">
    <div class="header-row">
        <div>
            <h2>Borrower Dashboard</h2>
            <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></p>
        </div>
    </div>

    <hr>

    <!-- Quick Actions -->
    <div class="grid">
        <div class="quick-card">
            <h3>Apply for a Loan</h3>
            <a href="apply_loan.php" class="btn">Start Application</a>
        </div>

        <div class="quick-card">
            <h3>Loan Status</h3>
            <a href="view_status.php" class="btn">Check Status</a>
        </div>

        <div class="quick-card">
            <h3>Make Repayment</h3>
            <a href="make_repayment.php" class="btn">Pay Now</a>
        </div>

        <div class="quick-card">
            <h3>Repayment History</h3>
            <a href="repayment_history.php" class="btn">View Records</a>
        </div>

        <div class="quick-card">
            <h3>Notifications</h3>
            <a href="notifications.php" class="btn">View Alerts</a>
        </div>
    </div>

    <!-- Assigned Bike -->
    <?php if ($assign) : ?>
        <div class="info-box">
            <p><strong>Assigned Bike:</strong> <?php echo htmlspecialchars($assign['model']); ?> (<?php echo htmlspecialchars($assign['serial_number']); ?>)</p>
        </div>
    <?php endif; ?>

    <!-- Loan Summary + Timeline -->
    <?php if ($loan) : ?>
        <div class="info-box" style="margin-top:18px;">
            <h3>Current Loan Summary</h3>
            <p><strong>Amount:</strong> ₦<?php echo number_format($loan['amount']); ?> &nbsp; | &nbsp; <strong>Status:</strong> <?php echo strtoupper(htmlspecialchars($loan['status'])); ?></p>
            <p><strong>Bike Model:</strong> <?php echo htmlspecialchars($loan['bike_model']); ?> &nbsp; | &nbsp; <strong>Duration:</strong> <?php echo intval($loan['duration']); ?> months</p>
        </div>

        <!-- Timeline -->
        <div class="timeline">
            <div class="step <?php echo ($loan ? 'active' : ''); ?>">Applied</div>
            <div class="step <?php echo ($loan['status'] === 'pending' ? 'active' : ''); ?>">Under Review</div>
            <div class="step <?php echo ($loan['status'] === 'approved' ? 'active' : ''); ?>">Approved</div>
            <div class="step <?php echo ($assign ? 'active' : ''); ?>">Bike Assigned</div>
            <div class="step <?php echo ($loan['status'] === 'approved' ? 'active' : ''); ?>">Repayment</div>

            <div class="step <?php echo ($loan && $remaining <= 0 ? 'active' : ''); ?>">Completed</div>
        </div>

        <!-- Progress -->
        <div class="progress-box">
            <p><strong>Total Loan:</strong> ₦<?php echo number_format($loan['amount']); ?></p>
            <p><strong>Total Paid:</strong> ₦<?php echo number_format($paid,2); ?></p>
            <p><strong>Remaining:</strong> ₦<?php echo number_format(max(0,$remaining),2); ?></p>

            <div class="progress-bar">
                <div class="fill" style="width: <?php echo round($percent,2); ?>%"></div>
            </div>

            <p><strong><?php echo round($percent,1); ?>% Completed</strong></p>
        </div>
    <?php else: ?>
        <div class="info-box" style="margin-top:18px;">
            <p>No active loan found. Apply now to get an electric bike.</p>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
~