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

// Get details from URL or default
$bike_model = isset($_GET['model']) ? htmlspecialchars($_GET['model']) : 'EV1 Basic';
$loan_amount = isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : '200000';
$bike_image = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : 'assets/img/ev1.png';

// Validation Logic on Submit
if (isset($_POST['apply'])) {
    $amount     = $_POST['amount'];
    $bike_model = $_POST['bike_model'];
    $duration   = $_POST['duration'];
    $purpose    = $_POST['purpose'];
    $nin        = $_POST['nin'];
    $bvn        = $_POST['bvn'];

    $upload_dir = "uploads/";
    $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

    function uploadFile($fileKey, $upload_dir, $allowed_ext, $required = false) {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] != 0) {
            return $required ? false : "";
        }
        $file_name = $_FILES[$fileKey]['name'];
        $tmp_name  = $_FILES[$fileKey]['tmp_name'];
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            return false;
        }

        $new_name = $fileKey . '_' . uniqid() . "." . $ext;
        move_uploaded_file($tmp_name, $upload_dir . $new_name);
        return $upload_dir . $new_name; // Store relative path in DB
    }

    $id_card = uploadFile('id_card', $upload_dir, $allowed_ext, true);
    $utility_bill = uploadFile('utility_bill', $upload_dir, $allowed_ext, true);
    


    if ($id_card === false || $utility_bill === false) {
        $error = "Please upload valid files for ID Card and Utility Bill (JPG, PNG, PDF).";
    } else {
        // Insert
        $sql = "INSERT INTO loan_applications 
                (user_id, nin, bvn, amount, bike_model, duration, purpose, id_card, utility_bill, status, date_applied)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_guarantor', NOW())";
        
        $stmt = $conn->prepare($sql);
        // Fixed bind_param to match 9 placeholders (issssssss)
        $stmt->bind_param("issssssss", $user_id, $nin, $bvn, $amount, $bike_model, $duration, $purpose, $id_card, $utility_bill);

        if ($stmt->execute()) {
            $success = true;
            $new_loan_id = $stmt->insert_id; // Get the generated ID
        } else {
            $error = "Database Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<!-- Page Styles -->
<style>
    body { background: #f4f7f6; font-family: 'Inter', sans-serif; overflow-x: hidden; }
    .split-container { display: flex; min-height: calc(100vh - 80px); /* Adjust for header */ }
    
    /* Left Side */
    .left-panel {
        flex: 1;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        border-right: 1px solid #eee;
    }
    .bike-display img {
        max-width: 80%;
        height: auto;
        margin-bottom: 30px;
        filter: drop-shadow(0 15px 25px rgba(0,0,0,0.15));
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    .bike-info { text-align: center; }
    .bike-info h2 { font-size: 2.5rem; color: #004aad; margin-bottom: 5px; }
    .bike-info .price { font-size: 1.8rem; font-weight: 700; color: #555; }

    /* Right Side */
    .right-panel {
        flex: 1;
        background: #fdfdfd;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .form-header { margin-bottom: 30px; }
    .form-header h3 { font-size: 1.8rem; color: #333; margin-bottom: 10px; }
    .form-header p { color: #888; }

    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #444; }
    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 14px;
        border: 2px solid #eef0f5;
        border-radius: 10px;
        background: #fff;
        font-size: 1rem;
        transition: 0.3s;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        border-color: #004aad;
        outline: none;
        box-shadow: 0 0 0 4px rgba(0, 74, 173, 0.1);
    }
    .form-textarea { height: 120px; resize: none; }
    
    .upload-box {
        border: 2px dashed #ccc;
        padding: 20px;
        text-align: center;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        position: relative;
    }
    .upload-box:hover { border-color: #004aad; background: #f0f7ff; }
    .upload-box input {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0; left: 0;
        opacity: 0;
        cursor: pointer;
    }
    .upload-label { color: #666; font-size: 0.9rem; pointer-events: none; }

    .btn-submit {
        width: 100%;
        padding: 16px;
        background: #004aad;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
    }
    .btn-submit:hover { background: #003882; transform: translateY(-2px); }

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
    .checkmark {
        width: 80px; height: 80px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #4bb71b;
        stroke-miterlimit: 10;
        margin-bottom: 20px;
        box-shadow: inset 0px 0px 0px #4bb71b;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    }
    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #4bb71b;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    @keyframes stroke { 100% { stroke-dashoffset: 0; } }
    @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
    @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 30px #4bb71b; } }

    .success-text { font-size: 1.5rem; color: #333; font-weight: 700; opacity: 0; animation: fadeIn 0.5s 1.2s forwards; }
    @keyframes fadeIn { to { opacity: 1; } }
    
    @media(max-width: 900px) {
        .split-container { flex-direction: column; }
        .left-panel { padding: 30px; border-bottom: 1px solid #eee; border-right: none; }
        .right-panel { padding: 30px; }
    }
</style>

<!-- Layout -->
<div class="split-container">
    
    <!-- Left: Bike Info -->
    <div class="left-panel">
        <div class="bike-display">
            <img src="<?php echo $bike_image; ?>" alt="Selected Bike">
        </div>
        <div class="bike-info">
            <h2><?php echo $bike_model; ?></h2>
            <p class="price">Loan Amount: ₦<?php echo number_format($loan_amount); ?></p>
        </div>
    </div>

    <!-- Right: Application Form -->
    <div class="right-panel">
        <div class="form-header">
            <h3>Complete Your Application</h3>
            <p>Please enter your details below to proceed.</p>
        </div>

        <?php if(isset($error)): ?>
            <div style="background: #fee; color: #d00; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <!-- Hidden Fields -->
            <input type="hidden" name="bike_model" value="<?php echo $bike_model; ?>">
            <input type="hidden" name="amount" value="<?php echo $loan_amount; ?>">

            <div class="form-group">
                <label>Payment Duration</label>
                <select name="duration" class="form-select" required>
                    <option value="">Select Duration</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">12 Months</option>
                </select>
            </div>

            <div class="form-group">
                <label>NIN Number</label>
                <input type="text" name="nin" class="form-input" placeholder="Enter NIN Number" required maxlength="11" minlength="11">
            </div>

            <div class="form-group">
                <label>BVN Number</label>
                <input type="text" name="bvn" class="form-input" placeholder="Enter BVN Number" required maxlength="11" minlength="11">
            </div>

            <div class="form-group">
                <label>Upload ID Card</label>
                <div class="upload-box">
                    <input type="file" name="id_card" required accept=".jpg,.jpeg,.png,.pdf" onchange="updateFileName(this)">
                    <span class="upload-label">Click to upload ID Card (JPG, PNG, PDF)</span>
                </div>
            </div>

            <div class="form-group">
                <label>Upload Utility Bill</label>
                <div class="upload-box">
                    <input type="file" name="utility_bill" required accept=".jpg,.jpeg,.png,.pdf" onchange="updateFileName(this)">
                    <span class="upload-label">Click to upload Utility Bill</span>
                </div>
            </div>

            <div class="form-group">
                <label>Purpose of Loan</label>
                <textarea name="purpose" class="form-textarea" placeholder="Describe the purpose of this loan..." required></textarea>
            </div>

            <button type="submit" name="apply" class="btn-submit">Next: Guarantor Info</button>
        </form>
    </div>

</div>

<!-- Success Overlay -->
<div class="overlay" id="successOverlay">
    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
    </svg>
    <div class="success-text">Step 1 Complete! Redirecting...</div>
</div>

<script>
    function updateFileName(input) {
        if(input.files && input.files[0]) {
            input.nextElementSibling.textContent = "Selected: " + input.files[0].name;
            input.parentElement.style.borderColor = "#004aad";
            input.parentElement.style.background = "#f0f7ff";
        }
    }

    <?php if(isset($success) && $success): ?>
    document.addEventListener("DOMContentLoaded", function() {
        const overlay = document.getElementById('successOverlay');
        overlay.classList.add('active');
        
        // Wait 2000ms then redirect
        setTimeout(() => {
            window.location.href = 'guarantor_info.php?loan_id=<?php echo $new_loan_id; ?>';
        }, 2000);
    });
    <?php endif; ?>
</script>

</body>
</html>
