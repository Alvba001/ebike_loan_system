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

/* ================= FETCH LATEST APPROVED LOAN ================= */
$loan = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id='$user_id' AND status='approved'
    ORDER BY loan_id DESC
    LIMIT 1
")->fetch_assoc();

if (!$loan) {
    echo "<script>alert('No approved loan found.'); window.location='dashboard.php';</script>";
    exit();
}

$loan_id     = $loan['loan_id'];
$loan_amount = floatval($loan['amount']);

/* ================= FETCH REPAYMENTS ================= */
$result = $conn->query("
    SELECT *
    FROM repayments
    WHERE loan_id='$loan_id'
    ORDER BY payment_date ASC
");

/* ================= CALCULATE TOTAL PAID ================= */
$sumRow = $conn->query("
    SELECT IFNULL(SUM(amount_paid),0) AS total_paid
    FROM repayments
    WHERE loan_id='$loan_id'
")->fetch_assoc();

$total_paid = floatval($sumRow['total_paid']);
$remaining  = max(0, $loan_amount - $total_paid);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repayment History</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .summary-box {
            background: #f5f8ff;
            padding: 18px;
            border-left: 5px solid #004aad;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .table-card {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: #f0f4ff;
            padding: 12px;
            text-align: left;
            color: #003c8a;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f8faff;
        }

        .amount {
            font-weight: 600;
            color: #004aad;
        }

        .empty {
            text-align: center;
            padding: 16px;
            color: #777;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <h2>Repayment History</h2>
        <p>Detailed record of your loan repayments.</p>
    </div>

    <!-- SUMMARY -->
    <div class="summary-box">
        <p><strong>Total Loan Amount:</strong> ₦<?php echo number_format($loan_amount,2); ?></p>
        <p><strong>Total Paid:</strong> ₦<?php echo number_format($total_paid,2); ?></p>
        <p><strong>Remaining Balance:</strong> ₦<?php echo number_format($remaining,2); ?></p>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Amount Paid (₦)</th>
                    <th>Payment Date</th>
                    <th>Balance After Payment (₦)</th>
                </tr>
            </thead>

            <tbody>
            <?php
            if ($result->num_rows > 0) {

                $balance = $loan_amount;
                $count = 1;

                while ($row = $result->fetch_assoc()) {
                    $balance -= $row['amount_paid'];

                    echo "
                        <tr>
                            <td>{$count}</td>
                            <td class='amount'>₦" . number_format($row['amount_paid'],2) . "</td>
                            <td>{$row['payment_date']}</td>
                            <td>₦" . number_format(max(0,$balance),2) . "</td>
                        </tr>
                    ";
                    $count++;
                }

            } else {
                echo "<tr><td colspan='4' class='empty'>No repayment records found.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="dashboard.php" class="btn-secondary">⬅ Back to Dashboard</a>

</div>

</body>
</html>
