<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// date filter
$from = isset($_GET['from']) && $_GET['from'] ? $_GET['from'] : date('Y-m-01'); // default first day this month
$to   = isset($_GET['to']) && $_GET['to'] ? $_GET['to'] : date('Y-m-d');

// sanitize
$from_safe = $conn->real_escape_string($from);
$to_safe = $conn->real_escape_string($to);

// summary (within date range)
$total_loans_range = $conn->query("
    SELECT COUNT(*) AS c, IFNULL(SUM(amount),0) AS s
    FROM loan_applications
    WHERE DATE(date_applied) BETWEEN '$from_safe' AND '$to_safe'
")->fetch_assoc();

$total_repayments_range = $conn->query("
    SELECT IFNULL(SUM(amount_paid),0) AS s
    FROM repayments
    WHERE DATE(payment_date) BETWEEN '$from_safe' AND '$to_safe'
")->fetch_assoc();

// monthly breakdown (grouped months in range)
$monthly_sql = "
    SELECT DATE_FORMAT(date_applied, '%M %Y') AS month, COUNT(*) AS total, IFNULL(SUM(amount),0) AS sum_amount
    FROM loan_applications
    WHERE DATE(date_applied) BETWEEN '$from_safe' AND '$to_safe'
    GROUP BY YEAR(date_applied), MONTH(date_applied)
    ORDER BY date_applied ASC
";
$monthly_res = $conn->query($monthly_sql);

// repayments breakdown
$repayment_sql = "
    SELECT r.*, l.user_id, u.name, l.bike_model
    FROM repayments r
    JOIN loan_applications l ON r.loan_id = l.loan_id
    JOIN users u ON l.user_id = u.user_id
    WHERE DATE(r.payment_date) BETWEEN '$from_safe' AND '$to_safe'
    ORDER BY r.payment_date DESC
";
$repayment_res = $conn->query($repayment_sql);

// defaulters in range (loans with pending due before today)
$today = date('Y-m-d');
$defaulters_q = "
    SELECT DISTINCT la.loan_id, u.name, la.amount, MIN(rs.due_date) AS next_due
    FROM repayment_schedule rs
    JOIN loan_applications la ON rs.loan_id = la.loan_id
    JOIN users u ON la.user_id = u.user_id
    WHERE rs.status='pending' AND rs.due_date < '$today'
    GROUP BY la.loan_id
    ORDER BY next_due ASC
";
$defaulters_res = $conn->query($defaulters_q);

// CSV export logic
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=loan_report_'.$from_safe.'_'.$to_safe.'.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Type','Label','Value']);

    // add summary
    fputcsv($output, ['Summary','Total Loans', $total_loans_range['c']]);
    fputcsv($output, ['Summary','Total Disbursed', number_format($total_loans_range['s'],2)]);
    fputcsv($output, ['Summary','Total Repayments', number_format($total_repayments_range['s'],2)]);

    // monthly
    fputcsv($output, []);
    fputcsv($output, ['Monthly Breakdown']);
    fputcsv($output, ['Month','Applications','Amount']);
    $monthly_out = $conn->query($monthly_sql);
    while ($m = $monthly_out->fetch_assoc()) {
        fputcsv($output, [$m['month'], $m['total'], number_format($m['sum_amount'],2)]);
    }

    fclose($output);
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Reports</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.container { padding:20px; }
.controls { display:flex; gap:10px; align-items:center; margin-bottom:12px; flex-wrap:wrap; }
.btn { padding:8px 12px; background:#004aad; color:white; border-radius:6px; text-decoration:none; }
.summary { display:grid; grid-template-columns: repeat(auto-fit,minmax(200px,1fr)); gap:12px; margin-bottom:18px; }
.summary .box { background:#fff; padding:14px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.05); }
.table { width:100%; border-collapse:collapse; margin-top:10px; }
.table th, .table td { padding:10px; border-bottom:1px solid #eee; text-align:left; }
.section-title { margin-top:18px; color:#004aad; font-weight:700; }
</style>
</head>
<body>

<div class="container">
    <h2>Reports</h2>
    <p class="small">Filtered reports & exports</p>

    <div class="controls">
        <form method="GET" style="display:flex; gap:8px; align-items:center;">
            <label>From: <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>"></label>
            <label>To: <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>"></label>
            <button class="btn" type="submit">Apply</button>
        </form>

        <div style="margin-left:auto;">
            <a class="btn" href="?export=csv&from=<?php echo urlencode($from_safe); ?>&to=<?php echo urlencode($to_safe); ?>">Export CSV</a>
            <a class="btn" href="dashboard.php" style="background:#6c757d">Back</a>
        </div>
    </div>

    <div class="summary">
        <div class="box"><h4>Total Loans</h4><p><?php echo number_format($total_loans_range['c']); ?></p></div>
        <div class="box"><h4>Total Disbursed (₦)</h4><p>₦<?php echo number_format($total_loans_range['s'],2); ?></p></div>
        <div class="box"><h4>Total Repayments (₦)</h4><p>₦<?php echo number_format($total_repayments_range['s'],2); ?></p></div>
        <div class="box"><h4>Defaulters (overdue)</h4><p><?php echo number_format($defaulters_res->num_rows); ?></p></div>
    </div>

    <div class="section-title">Monthly Breakdown</div>
    <table class="table">
        <thead><tr><th>Month</th><th>Applications</th><th>Amount (₦)</th></tr></thead>
        <tbody>
        <?php
        if ($monthly_res->num_rows > 0) {
            while ($m = $monthly_res->fetch_assoc()) {
                echo "<tr><td>{$m['month']}</td><td>{$m['total']}</td><td>₦".number_format($m['sum_amount'],2)."</td></tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No data in this range.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="section-title">Repayments (<?php echo htmlspecialchars($from).' → '.htmlspecialchars($to); ?>)</div>
    <table class="table">
        <thead><tr><th>Borrower</th><th>Loan ID</th><th>Bike Model</th><th>Amount</th><th>Date</th></tr></thead>
        <tbody>
        <?php
        if ($repayment_res->num_rows > 0) {
            while ($r = $repayment_res->fetch_assoc()) {
                echo "<tr>
                        <td>".htmlspecialchars($r['name'])."</td>
                        <td>{$r['loan_id']}</td>
                        <td>".htmlspecialchars($r['bike_model'])."</td>
                        <td>₦".number_format($r['amount_paid'],2)."</td>
                        <td>{$r['payment_date']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No repayments in this range.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="section-title">Defaulters (Overdue)</div>
    <table class="table">
        <thead><tr><th>Loan ID</th><th>Borrower</th><th>Loan Amount</th><th>Next Due</th></tr></thead>
        <tbody>
        <?php
        if ($defaulters_res->num_rows > 0) {
            while ($d = $defaulters_res->fetch_assoc()) {
                echo "<tr>
                        <td>{$d['loan_id']}</td>
                        <td>".htmlspecialchars($d['name'])."</td>
                        <td>₦".number_format($d['amount'],2)."</td>
                        <td>{$d['next_due']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No defaulters found.</td></tr>";
        }
        ?>
        </tbody>
    </table>

</div>

</body>
</html>
