<?php
session_start();
include '../includes/db_connect.php';

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Validate loan ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: view_loans.php");
    exit();
}

$loan_id = intval($_GET['id']);

// ------------------------------------
// 1. Reject Loan
//-------------------------------------
$update = $conn->prepare("
    UPDATE loan_applications
    SET status = 'rejected', approval_date = NOW()
    WHERE loan_id = ?
");
$update->bind_param("i", $loan_id);
$update->execute();

// ------------------------------------
// 2. Retrieve user_id for notification
//-------------------------------------
$getUser = $conn->prepare("
    SELECT user_id FROM loan_applications WHERE loan_id = ?
");
$getUser->bind_param("i", $loan_id);
$getUser->execute();
$result = $getUser->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Loan not found!'); window.location='view_loans.php';</script>";
    exit();
}

$user = $result->fetch_assoc();
$user_id = $user['user_id'];

// ------------------------------------
// 3. Insert Rejection Notification
//-------------------------------------
$message = "Your loan application #$loan_id has been REJECTED.";

$notify = $conn->prepare("
    INSERT INTO notifications (user_id, message)
    VALUES (?, ?)
");
$notify->bind_param("is", $user_id, $message);
$notify->execute();

// ------------------------------------
// Redirect back
//-------------------------------------
echo "<script>
        alert('Loan rejected successfully!');
        window.location='view_loans.php';
      </script>";
exit();
?>
