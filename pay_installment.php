<?php
session_start();
include 'config.php';
include 'includes/db_connect.php';

if (!isset($_GET['loan_id'])) {
    die("Invalid Loan ID");
}

$loan_id = intval($_GET['loan_id']);
$user_id = $_SESSION['user_id'];

// Fetch Loan Details
$loan = $conn->query("SELECT * FROM loan_applications WHERE loan_id='$loan_id' AND user_id='$user_id'")->fetch_assoc();

if (!$loan) {
    die("Loan not found");
}

// Fetch earliest pending installment from schedule
$schedule = $conn->query("SELECT * FROM repayment_schedule WHERE loan_id='$loan_id' AND status='pending' ORDER BY due_date ASC LIMIT 1")->fetch_assoc();

if (!$schedule) {
    die("Loan appears to be fully repaid or no pending schedule found.");
}

$monthly_amount = floatval($schedule['amount_due']);
$due_date = $schedule['due_date']; // Can be used for display if needed

// Ensure we don't overpay (safety check, though schedule should match total)
$payRow = $conn->query("SELECT IFNULL(SUM(amount_paid),0) AS total FROM repayments WHERE loan_id = '$loan_id'")->fetch_assoc();
$paid = floatval($payRow['total']);
$remaining = floatval($loan['amount']) - $paid;

if ($monthly_amount > $remaining) {
    $monthly_amount = $remaining;
}

// Paystack amount is in kobo
$amount_kobo = $monthly_amount * 100;
// ... previous code ...

// FIX 1: Handle the "Undefined array key" warning
// We check if the email is in the session; if not, we set it to empty so we can fetch it below.
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// FIX 2: Fetch email if missing, using the correct column 'user_id'
if (empty($email)) {
    // We use user_id here because you confirmed that is the column name
    $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $u = $resullbrot->fetch_assoc();
        $email = $u['email'];
    } else {
        die("Error: User email not found. Please log in again.");
    }
    $stmt->close();
}

// ... continue to Paystack initialization ...

$callback_url = "http://localhost/ebike_loan_system/verify_payment.php?loan_id=$loan_id";

// Initialize Paystack Transaction
$url = "https://api.paystack.co/transaction/initialize";

$fields = [
    'email' => $email,
    'amount' => $amount_kobo,
    'callback_url' => $callback_url,
    'metadata' => [
        'loan_id' => $loan_id,
        'user_id' => $user_id
    ]
];

$fields_string = http_build_query($fields);

// Open Connection
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, true);
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
  "Cache-Control: no-cache",
));
curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 

$result = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  $response = json_decode($result, true);
  if($response['status']) {
      // Redirect to authorization URL
      header("Location: " . $response['data']['authorization_url']);
      exit();
  } else {
      echo "Paystack Error: " . $response['message'];
  }
}
?>
