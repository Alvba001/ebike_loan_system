<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Restrict admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all repayment records
$query = $conn->prepare("
    SELECT 
        repayments.*, 
        loan_applications.amount AS loan_amount,
        loan_applications.bike_model,
        users.name AS borrower_name,
        users.email AS borrower_email
    FROM repayments
    JOIN loan_applications ON repayments.loan_id = loan_applications.loan_id
    JOIN users ON loan_applications.user_id = users.user_id
    ORDER BY repayments.payment_date DESC
");

$query->execute();
$result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repayment Records (Admin)</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
            border-radius: 8px;
            overflow: hidden;
        }
        thead {
            background: #004aad;
            color: white;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        tr:hover {
            background: #f5f9ff;
        }
        .paid {
            color: green;
            font-weight: 600;
        }
        .balance {
            color: #0056b3;
            font-weight: 600;
        }
        .name {
            font-weight: 600;
            color: #222;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Repayment Records</h2>
    <p>All repayment transactions made by borrowers are listed below.</p>
    <hr>

    <table>
        <thead>
            <tr>
                <th>Borrower</th>
                <th>Email</th>
                <th>Loan Amount</th>
                <th>Bike Model</th>
                <th>Amount Paid</th>
                <th>Remaining Balance</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td class='name'>{$row['borrower_name']}</td>";
                echo "<td>{$row['borrower_email']}</td>";
                echo "<td>₦".number_format($row['loan_amount'])."</td>";
                echo "<td>{$row['bike_model']}</td>";
                echo "<td class='paid'>₦".number_format($row['amount_paid'])."</td>";
                echo "<td class='balance'>₦".number_format($row['balance'])."</td>";
                echo "<td>{$row['payment_date']}</td>";
                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='7' class='no-data'>No repayment records found.</td></tr>";
        }
        ?>

        </tbody>
    </table>

    <br>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>

</div>

</body>
</html>
