<?php 
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Restrict access to admin ONLY
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* ========================
   OVERVIEW STATISTICS
   ======================== */

// Borrowers count
$total_borrowers = $conn->query("
    SELECT COUNT(*) AS c FROM users WHERE role='borrower'
")->fetch_assoc()['c'];

// Loans counts
$total_loans = $conn->query("
    SELECT COUNT(*) AS c FROM loan_applications
")->fetch_assoc()['c'];

$approved_loans = $conn->query("
    SELECT COUNT(*) AS c FROM loan_applications WHERE status='approved'
")->fetch_assoc()['c'];

$pending_loans = $conn->query("
    SELECT COUNT(*) AS c FROM loan_applications WHERE status='pending'
")->fetch_assoc()['c'];

$rejected_loans = $conn->query("
    SELECT COUNT(*) AS c FROM loan_applications WHERE status='rejected'
")->fetch_assoc()['c'];

// Finance
$total_disbursed = $conn->query("
    SELECT IFNULL(SUM(amount),0) AS s 
    FROM loan_applications 
    WHERE status='approved'
")->fetch_assoc()['s'];

$total_repayments = $conn->query("
    SELECT IFNULL(SUM(amount_paid),0) AS s 
    FROM repayments
")->fetch_assoc()['s'];

$outstanding_balance = floatval($total_disbursed) - floatval($total_repayments);

// Bikes
$total_bikes = $conn->query("
    SELECT COUNT(*) AS c FROM bikes
")->fetch_assoc()['c'];

$assigned_bikes = $conn->query("
    SELECT COUNT(*) AS c FROM bikes WHERE status='assigned'
")->fetch_assoc()['c'];

$available_bikes = $conn->query("
    SELECT COUNT(*) AS c FROM bikes WHERE status='available'
")->fetch_assoc()['c'];

// Defaulters
$today = date('Y-m-d');
$defaulters = $conn->query("
    SELECT COUNT(DISTINCT la.loan_id) AS c
    FROM repayment_schedule rs
    JOIN loan_applications la ON rs.loan_id = la.loan_id
    WHERE rs.status='pending' AND rs.due_date < '$today'
")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">

<style>
.container { padding: 22px; }
.header { display:flex; justify-content:space-between; align-items:center; gap:10px; }
.grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; margin-top:18px; }
.card { background:#fff; padding:18px; border-radius:10px; box-shadow:0 6px 20px rgba(12,34,56,0.06); }
.card h4 { margin:0 0 6px; color:#333; font-size:14px; }
.stat { font-size:22px; font-weight:700; color:#004aad; }
.small { color:#666; font-size:13px; }
.actions { display:flex; gap:8px; flex-wrap:wrap; }
.btn { display:inline-block; padding:8px 12px; background:#004aad; color:white; border-radius:6px; text-decoration:none; }
</style>
</head>

<body>

<div class="container">

    <div class="header">
        <div>
            <h2>Admin Dashboard</h2>
            <p class="small">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
            </p>
        </div>

        <div class="actions">
            <a href="check_reminders.php" class="btn">Run Reminder Check</a>
            <a href="reports.php" class="btn" style="background:#2b8aef">Open Reports</a>
            <a href="add_bike.php" class="btn" style="background:#6c757d">Add Bike</a>
        </div>
    </div>

    <div class="grid">

        <div class="card">
            <h4>Total Borrowers</h4>
            <div class="stat"><?php echo number_format($total_borrowers); ?></div>
            <div class="small">Registered borrowers</div>
        </div>

        <div class="card">
            <h4>Total Loan Applications</h4>
            <div class="stat"><?php echo number_format($total_loans); ?></div>
            <div class="small">All time</div>
        </div>

        <div class="card">
            <h4>Approved Loans</h4>
            <div class="stat"><?php echo number_format($approved_loans); ?></div>
            <div class="small">Approved applications</div>
        </div>

        <div class="card">
            <h4>Pending Loans</h4>
            <div class="stat"><?php echo number_format($pending_loans); ?></div>
            <div class="small">Awaiting decision</div>
        </div>

        <div class="card">
            <h4>Rejected Loans</h4>
            <div class="stat"><?php echo number_format($rejected_loans); ?></div>
            <div class="small">Rejected applications</div>
        </div>

        <div class="card">
            <h4>Total Disbursed (₦)</h4>
            <div class="stat">₦<?php echo number_format($total_disbursed); ?></div>
            <div class="small">Approved loans</div>
        </div>

        <div class="card">
            <h4>Total Repaid (₦)</h4>
            <div class="stat">₦<?php echo number_format($total_repayments); ?></div>
            <div class="small">Payments received</div>
        </div>

        <div class="card">
            <h4>Outstanding Balance (₦)</h4>
            <div class="stat">₦<?php echo number_format($outstanding_balance); ?></div>
            <div class="small">Remaining balance</div>
        </div>

        <div class="card">
            <h4>Total Bikes</h4>
            <div class="stat"><?php echo number_format($total_bikes); ?></div>
            <div class="small">Inventory</div>
        </div>

        <div class="card">
            <h4>Assigned / Available</h4>
            <div class="stat">
                <?php echo number_format($assigned_bikes); ?> / <?php echo number_format($available_bikes); ?>
            </div>
            <div class="small">Bike status</div>
        </div>

        <div class="card">
            <h4>Defaulters</h4>
            <div class="stat"><?php echo number_format($defaulters); ?></div>
            <div class="small">Overdue loans</div>
        </div>

    </div>

</div>

</body>
</html>
