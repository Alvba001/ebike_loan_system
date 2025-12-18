<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Validate ID
if (!isset($_GET['id'])) {
    header("Location: view_loans.php");
    exit();
}

$loan_id = $_GET['id'];

// Fetch loan + borrower details
$sql = "
    SELECT l.*, u.name, u.email
    FROM loan_applications l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.loan_id = '$loan_id'
";

$result = $conn->query($sql);
$loan = $result->fetch_assoc();

if (!$loan) {
    echo "<script>alert('Loan record not found.'); window.location='view_loans.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .details-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 1px 10px rgba(0,0,0,0.1);
        }
        .row { margin-bottom: 12px; }
        .label { font-weight: bold; color: #333; }
        .value { margin-left: 8px; }

        .doc-link {
            background: #0066cc;
            color: white;
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            margin-top: 5px;
            display: inline-block;
        }

        .btn-approve, .btn-reject {
            padding: 10px 18px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 10px;
            font-weight: bold;
        }
        .btn-approve { background: green; }
        .btn-reject { background: red; }

        .btn-back {
            background: #444;
            padding: 8px 14px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>

<body>

<div class="container">
    <h2>Loan Application Details</h2>
    <hr>

    <div class="details-box">

        <div class="row"><span class="label">Borrower:</span>
            <span class="value"><?= $loan['name'] ?></span>
        </div>

        <div class="row"><span class="label">Email:</span>
            <span class="value"><?= $loan['email'] ?></span>
        </div>

        <div class="row"><span class="label">NIN:</span>
            <span class="value"><?= $loan['nin'] ?></span>
        </div>

        <div class="row"><span class="label">Bike Model:</span>
            <span class="value"><?= $loan['bike_model'] ?></span>
        </div>

        <div class="row"><span class="label">Loan Amount:</span>
            <span class="value">₦<?= number_format($loan['amount']) ?></span>
        </div>

        <div class="row"><span class="label">Duration:</span>
            <span class="value"><?= $loan['duration'] ?> months</span>
        </div>

        <div class="row"><span class="label">Purpose:</span>
            <span class="value"><?= $loan['purpose'] ?></span>
        </div>

        <div class="row"><span class="label">Applied On:</span>
            <span class="value"><?= $loan['date_applied'] ?></span>
        </div>

        <hr>
        <h3>Uploaded Documents</h3><br>

        <div class="row">
            <span class="label">ID Card:</span>
            <span class="value">
                <?php if (!empty($loan['id_card'])) { ?>
                    <a class="doc-link" href="../<?= $loan['id_card'] ?>" target="_blank">View ID Card</a>
                <?php } else { echo "<span style='color:red'>Not uploaded</span>"; } ?>
            </span>
        </div>

        <div class="row">
            <span class="label">Utility Bill:</span>
            <span class="value">
                <?php if (!empty($loan['utility_bill'])) { ?>
                    <a class="doc-link" href="../<?= $loan['utility_bill'] ?>" target="_blank">View Utility Bill</a>
                <?php } else { echo "<span style='color:red'>Not uploaded</span>"; } ?>
            </span>
        </div>

        <div class="row">
            <span class="label">Guarantor Document:</span>
            <span class="value">
                <?php if (!empty($loan['guarantor_doc'])) { ?>
                    <a class="doc-link" href="../<?= $loan['guarantor_doc'] ?>" target="_blank">View Guarantor Doc</a>
                <?php } else { echo "<span style='color:red'>Not uploaded</span>"; } ?>
            </span>
        </div>

        <div class="row">
            <span class="label">Supporting Document:</span>
            <span class="value">
                <?php if (!empty($loan['support_doc'])) { ?>
                    <a class="doc-link" href="../<?= $loan['support_doc'] ?>" target="_blank">View Supporting Doc</a>
                <?php } else { echo "<span style='color:red'>No file provided</span>"; } ?>
            </span>
        </div>

        <hr>

        <div class="row">
            <span class="label">Status:</span>
            <span class="value"><b><?= strtoupper($loan['status']) ?></b></span>
        </div>

        <br>

        <?php if ($loan['status'] === 'pending') { ?>
            <a href="approve_loan.php?id=<?= $loan_id ?>" class="btn-approve">Approve Loan</a>
            <a href="assign_bike.php?loan_id=<?= $loan_id ?>" class="btn-assign">Assign Bike</a>
            <a href="reject_loan.php?id=<?= $loan_id ?>" class="btn-reject">Reject Loan</a>
        <?php } else { ?>
            <p style="color:green; font-weight:bold;">This loan has already been processed.</p>
        <?php } ?>

        <br><br>

        <a href="view_loans.php" class="btn-back">⬅ Back</a>

    </div>
</div>

</body>
</html>
