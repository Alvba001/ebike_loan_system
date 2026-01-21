<?php
session_start();
include 'includes/db_connect.php';
include 'includes/borrower_header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['loan_id'])) {
    echo "<script>alert('Invalid Access'); window.location='apply_loan.php';</script>";
    exit();
}

$loan_id = $_GET['loan_id'];

// Check if loan exists and belongs to user
$checkSql = "SELECT user_id, status FROM loan_applications WHERE loan_id = ?";
$stmt = $conn->prepare($checkSql);
$stmt->bind_param("i", $loan_id);
$stmt->execute();
$result = $stmt->get_result();
$loan = $result->fetch_assoc();

if (!$loan || $loan['user_id'] != $_SESSION['user_id']) {
    echo "<script>alert('Unauthorized Access'); window.location='dashboard.php';</script>";
    exit();
}

if ($loan['status'] !== 'pending_guarantor') {
    echo "<script>alert('Loan application is not in the correct stage.'); window.location='application_status.php';</script>";
    exit();
}

if (isset($_POST['submit_guarantor'])) {
    $full_name = $_POST['full_name'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $relationship = $_POST['relationship'];
    $years_known = $_POST['years_known'];
    $id_type = $_POST['id_type'];
    $id_number = $_POST['id_number'];
    $occupation = $_POST['occupation'];
    $employer = $_POST['employer'];
    $work_address = $_POST['work_address'];

    // Insert Guarantor Info
    $sql = "INSERT INTO guarantor_information (loan_application_id, full_name, gender, dob, phone, email, address, relationship, years_known, id_type, id_number, occupation, employer, work_address)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssssssssss", $loan_id, $full_name, $gender, $dob, $phone, $email, $address, $relationship, $years_known, $id_type, $id_number, $occupation, $employer, $work_address);

    if ($stmt->execute()) {
        // Update Loan Status to 'pending'
        $updateSql = "UPDATE loan_applications SET status = 'pending' WHERE loan_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $loan_id);
        $updateStmt->execute();

        $success = true;
    } else {
        $error = "Database Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guarantor Information</title>
    <style>
        body { background: #f4f7f6; font-family: 'Inter', sans-serif; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h2 { color: #004aad; margin-bottom: 20px; text-align: center; }
        .section-title { font-size: 1.1rem; color: #555; border-bottom: 2px solid #eee; padding-bottom: 5px; margin-bottom: 15px; margin-top: 30px; font-weight: 600; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; color: #444; }
        .form-input, .form-select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 1rem; }
        .full-width { grid-column: span 2; }
        
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #004aad;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 20px;
        }
        .btn-submit:hover { background: #003882; }

        /* Success Overlay */
        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.95);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            opacity: 0; pointer-events: none;
            transition: opacity 0.5s;
        }
        .overlay.active { opacity: 1; pointer-events: all; }
        .success-text { font-size: 1.5rem; color: #333; font-weight: 700; opacity: 0; animation: fadeIn 0.5s 1.2s forwards; }
        @keyframes fadeIn { to { opacity: 1; } }
    </style>
</head>
<body>

<div class="container">
    <h2>Step 2: Guarantor Information</h2>
    <p style="text-align:center; color:#666;">Please provide details of a guarantor to support your application.</p>

    <?php if(isset($error)): ?>
        <p style="color:red; text-align:center;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        
        <div class="section-title">1. Basic Identity</div>
        <div class="form-grid">
            <div class="form-group full-width">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-select">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="form-input" required>
            </div>
        </div>

        <div class="section-title">2. Contact Information</div>
        <div class="form-grid">
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-input">
            </div>
            <div class="form-group full-width">
                <label>Residential Address</label>
                <input type="text" name="address" class="form-input" required>
            </div>
        </div>

        <div class="section-title">3. Relationship to Borrower</div>
        <div class="form-grid">
            <div class="form-group">
                <label>Relationship Type</label>
                <select name="relationship" class="form-select" required>
                    <option value="Parent">Parent</option>
                    <option value="Sibling">Sibling</option>
                    <option value="Spouse">Spouse</option>
                    <option value="Friend">Friend</option>
                    <option value="Employer">Employer</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Years Known</label>
                <input type="text" name="years_known" class="form-input" placeholder="e.g. 5 years">
            </div>
        </div>

        <div class="section-title">4. Identification Details</div>
        <div class="form-grid">
            <div class="form-group">
                <label>ID Type</label>
                <select name="id_type" class="form-select" required>
                    <option value="NIN">National ID (NIN)</option>
                    <option value="Voters Card">Voter's Card</option>
                    <option value="Drivers License">Driver's License</option>
                    <option value="Passport">International Passport</option>
                </select>
            </div>
            <div class="form-group">
                <label>ID Number</label>
                <input type="text" name="id_number" class="form-input" required>
            </div>
        </div>

        <div class="section-title">5. Employment / Financial</div>
        <div class="form-grid">
            <div class="form-group">
                <label>Occupation</label>
                <input type="text" name="occupation" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Employer Name</label>
                <input type="text" name="employer" class="form-input">
            </div>
            <div class="form-group full-width">
                <label>Work Address</label>
                <input type="text" name="work_address" class="form-input">
            </div>
        </div>

        <button type="submit" name="submit_guarantor" class="btn-submit">Finalize Application</button>
    </form>
</div>

<!-- Success Overlay -->
<div class="overlay" id="successOverlay">
    <div style="text-align:center;">
        <h2 style="font-size:3rem; margin:0;">🎉</h2>
        <div class="success-text" style="opacity:1; animation:none;">Application Submitted!</div>
        <p>Redirecting to status page...</p>
    </div>
</div>

<script>
    <?php if(isset($success) && $success): ?>
    document.addEventListener("DOMContentLoaded", function() {
        const overlay = document.getElementById('successOverlay');
        overlay.classList.add('active');
        
        setTimeout(() => {
            window.location.href = 'application_status.php';
        }, 2000);
    });
    <?php endif; ?>
</script>

</body>
</html>
