<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all loan applications with borrower info
$sql = "
    SELECT l.*, u.name, u.email
    FROM loan_applications l
    JOIN users u ON l.user_id = u.user_id
    ORDER BY l.loan_id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Loan Applications</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #f0f0f0;
            text-align: left;
        }
        .status {
            font-weight: bold;
        }
        .pending { color: orange; }
        .approved { color: green; }
        .rejected { color: red; }
        .btn-small {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
        }
        .btn-view {
            background: #007bff;
            color: white;
        }
        .btn-view:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Loan Applications</h2>
    <p>Manage all borrower loan requests.</p>
    <hr>

    <table>
        <tr>
            <th>Borrower</th>
            <th>Email</th>
            <th>Bike Model</th>
            <th>Amount (₦)</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Date Applied</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                // Status color
                $status_class = "";
                if ($row['status'] == 'pending') $status_class = "pending";
                if ($row['status'] == 'approved') $status_class = "approved";
                if ($row['status'] == 'rejected') $status_class = "rejected";

                echo "<tr>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['bike_model']}</td>
                        <td>₦".number_format($row['amount'])."</td>
                        <td>{$row['duration']} months</td>
                        <td class='status $status_class'>".strtoupper($row['status'])."</td>
                        <td>{$row['date_applied']}</td>

                        <td>
                            <a href='loan_details.php?id={$row['loan_id']}' class='btn-small btn-view'>
                                View Details
                            </a>
                        </td>
                      </tr>";
            }

        } else {
            echo "<tr><td colspan='8'>No loan applications found.</td></tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
