<?php
include '../includes/db_connect.php';

// Fetch all pending repayment schedules
$sql = "
    SELECT rs.*, u.user_id, u.name, la.bike_model
    FROM repayment_schedule rs
    JOIN loan_applications la ON rs.loan_id = la.loan_id
    JOIN users u ON la.user_id = u.user_id
    WHERE rs.status='pending'
";

$result = $conn->query($sql);
$today = date('Y-m-d');

while ($row = $result->fetch_assoc()) {

    $due_date = $row['due_date'];
    $user_id = $row['user_id'];
    $amount = number_format($row['amount_due']);
    $model = $row['bike_model'];

    $days_left = (strtotime($due_date) - strtotime($today)) / 86400;

    // Reminder 3 days before
    if ($days_left == 3) {
        // Notification logic removed as per user request
    }

    // Reminder on due date
    if ($days_left == 0) {
        // Notification logic removed as per user request
    }

    // Overdue notification
    if ($days_left < 0) {
        // Notification logic removed as per user request
    }
}

echo "Reminder check complete.";
?>
