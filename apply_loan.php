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

// Handle form submission
if (isset($_POST['apply'])) {

    $amount     = $_POST['amount'];
    $bike_model = $_POST['bike_model'];
    $duration   = $_POST['duration'];
    $purpose    = $_POST['purpose'];
    $nin        = $_POST['nin'];

    // Allowed extensions
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

    // Upload folder
    $upload_dir = "uploads/";

    // FUNCTION TO HANDLE EACH FILE UPLOAD
    function uploadFile($fileKey, $upload_dir, $allowed_ext) {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] != 0) {
            return "";
        }

        $file_name = $_FILES[$fileKey]['name'];
        $tmp_name  = $_FILES[$fileKey]['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            echo "<script>alert('Invalid file: Only JPG, PNG or PDF allowed!');</script>";
            exit();
        }

        $new_name = $fileKey . '_' . uniqid() . "." . $ext;
        $save_path = $upload_dir . $new_name;
        move_uploaded_file($tmp_name, $save_path);

        return $save_path;
    }

    // Upload each file
    $id_card      = uploadFile('id_card', $upload_dir, $allowed_ext);
    $utility_bill = uploadFile('utility_bill', $upload_dir, $allowed_ext);
    $guarantor    = uploadFile('guarantor_doc', $upload_dir, $allowed_ext);
    $support_doc  = uploadFile('support_doc', $upload_dir, $allowed_ext);

    // Insert into database
    $sql = "INSERT INTO loan_applications 
            (user_id, nin, amount, bike_model, duration, purpose, id_card, utility_bill, guarantor_doc, support_doc)
            VALUES 
            ('$user_id', '$nin', '$amount', '$bike_model', '$duration', '$purpose', 
            '$id_card', '$utility_bill', '$guarantor', '$support_doc')";

    if ($conn->query($sql)) {
        echo "<script>alert('Loan application submitted successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error submitting loan application.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Loan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

    <div class="page-header">
        <h2>Apply for Electric Bike Loan</h2>
        <p>Fill in the required details to apply for a loan.</p>
    </div>

    <div class="form-card">

        <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateLoan()">

            <div class="form-group">
                <label>Select Bike Model</label>
                <select name="bike_model" id="bike_model" class="form-control" required onchange="updatePrice()">
                    <option value="">-- Select Bike Model --</option>
                    <option value="EV1 Basic" data-price="200000">EV1 Basic – ₦200,000</option>
                    <option value="EV2 Standard" data-price="300000">EV2 Standard – ₦300,000</option>
                    <option value="EV3 Premium" data-price="450000">EV3 Premium – ₦450,000</option>
                </select>
            </div>

            <div class="form-group">
                <label>Loan Amount</label>
                <input type="number" name="amount" id="amount" class="form-control" readonly required>
            </div>

            <div class="form-group">
                <label>Repayment Duration</label>
                <select name="duration" id="duration" class="form-control" required onchange="updateSummary()">
                    <option value="">-- Select Duration --</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">12 Months</option>
                </select>
            </div>

            <div class="form-group">
                <label>NIN Number</label>
                <input type="text" name="nin" id="nin" class="form-control" required minlength="11" maxlength="11" placeholder="Enter 11-digit NIN">
            </div>

            <!-- NEW DOCUMENT UPLOAD FIELDS -->
            <div class="form-group">
                <label>Upload ID Card</label>
                <input type="file" name="id_card" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Upload Utility Bill</label>
                <input type="file" name="utility_bill" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Upload Guarantor Document</label>
                <input type="file" name="guarantor_doc" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Supporting Document (Optional)</label>
                <input type="file" name="support_doc" class="form-control">
            </div>

            <div class="form-group">
                <label>Purpose of Loan</label>
                <textarea name="purpose" id="purpose" class="form-control" required></textarea>
            </div>

            <!-- Summary -->
            <div class="summary-box" id="summary" style="display:none;">
                <p id="summary-model"></p>
                <p id="summary-price"></p>
                <p id="summary-duration"></p>
                <p id="summary-monthly"></p>
            </div>

            <button type="submit" name="apply" class="btn btn-primary">Submit Application</button>
        </form>
    </div>

    <br>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>

</div>

<script>
function updatePrice() {
    let bike = document.getElementById("bike_model");
    let price = bike.options[bike.selectedIndex].getAttribute("data-price");
    document.getElementById("amount").value = price;
    updateSummary();
}

function updateSummary() {
    let bike = document.getElementById("bike_model");
    let model = bike.value;
    let price = document.getElementById("amount").value;
    let duration = document.getElementById("duration").value;

    if (!model || !price || !duration) {
        document.getElementById("summary").style.display = "none";
        return;
    }

    let monthly = (price / duration).toFixed(2);

    document.getElementById("summary-model").innerHTML = "Bike Model: " + model;
    document.getElementById("summary-price").innerHTML = "Bike Price: ₦" + Number(price).toLocaleString();
    document.getElementById("summary-duration").innerHTML = "Duration: " + duration + " months";
    document.getElementById("summary-monthly").innerHTML = "Estimated Monthly Payment: ₦" + Number(monthly).toLocaleString();

    document.getElementById("summary").style.display = "block";
}

function validateLoan() {
    let nin = document.getElementById("nin").value;
    if (nin.length !== 11) {
        alert("NIN must be exactly 11 digits!");
        return false;
    }
    return true;
}
</script>

</body>
</html>
