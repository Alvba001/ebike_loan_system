<?php
session_start();
include 'config.php';
include 'includes/db_connect.php';

if (!isset($_GET['reference']) || !isset($_GET['loan_id'])) {
    die("Invalid access");
}

$reference = $_GET['reference'];
$loan_id = intval($_GET['loan_id']);
$user_id = $_SESSION['user_id'];

// Verify Transaction
$result = array();
$url = 'https://api.paystack.co/transaction/verify/' . $reference;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
    "Cache-Control: no-cache",
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    die("cURL Error #:" . $err);
} 

$tranx = json_decode($response, true);

if ($tranx['status'] && $tranx['data']['status'] === 'success') {
    // Payment Successful
    $amount_paid = $tranx['data']['amount'] / 100; // Convert kobo to naira
    
    // Check if reference already exists to prevent duplicate entries
    $check = $conn->query("SELECT * FROM repayments WHERE reference = '$reference'");
    if($check->num_rows == 0) {
        // ID generation or auto-increment? Assuming auto-increment
        // Need to check if 'repayments' table exists and has these columns.
        $stmt = $conn->prepare("INSERT INTO repayments (loan_id, amount_paid, reference, status, date_paid) VALUES (?, ?, ?, 'paid', NOW())");
        $stmt->bind_param("ids", $loan_id, $amount_paid, $reference);
        
        if($stmt->execute()) {
            // Check if fully paid
            // Recalculate Total Paid
            $payRow = $conn->query("SELECT SUM(amount_paid) AS total FROM repayments WHERE loan_id = '$loan_id'")->fetch_assoc();
            $total_paid = floatval($payRow['total']);
            
            // ================= FLEXIBLE SCHEDULE UPDATE LOGIC ================= //
            // Fetch all schedule items ordered by due date (earliest first)
            $schedules = $conn->query("SELECT * FROM repayment_schedule WHERE loan_id='$loan_id' ORDER BY due_date ASC");
            
            $cumulative_due = 0;
            
            while ($sch = $schedules->fetch_assoc()) {
                $cumulative_due += floatval($sch['amount_due']);
                
                // If the total user has paid covers this cumulative milestone, mark it as paid.
                if ($total_paid >= $cumulative_due) {
                     $conn->query("UPDATE repayment_schedule SET status='paid', date_paid=NOW() WHERE schedule_id='" . $sch['schedule_id'] . "'");
                } else {
                     // If total paid is less than cumulative due, it means this month (and subsequent ones) isn't fully paid yet.
                     // We leave it as pending. Note: We don't support 'partial' status yet per requirements, only pending/paid.
                     $conn->query("UPDATE repayment_schedule SET status='pending' WHERE schedule_id='" . $sch['schedule_id'] . "'");
                }
            }
            // ================================================================== //

            // Check if fully paid (Main Loan Status)
            $loan = $conn->query("SELECT amount FROM loan_applications WHERE loan_id='$loan_id'")->fetch_assoc();
            
            if ($total_paid >= $loan['amount']) {
                $conn->query("UPDATE loan_applications SET status='completed' WHERE loan_id='$loan_id'");
            }

            header("Location: dashboard.php?payment=success");
            exit();
        } else {
            echo "Database Error: " . $conn->error;
        }
    } else {
        // Already recorded
        header("Location: dashboard.php?payment=success");
        exit();
    }

} else {
    // Transaction failed
    echo "Transaction Verification Failed: " . $tranx['message'];
}
?>
