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

// Fetch latest loan
$loan = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id = '$user_id'
    ORDER BY loan_id DESC
    LIMIT 1
")->fetch_assoc();

if (!$loan) {
    echo "<div style='padding:50px; text-align:center;'>No active application found. <a href='dashboard.php'>Go Home</a></div>";
    exit();
}

// Status Logic and Mapping
$bike_images = [
    'EV1 Basic' => 'assets/img/ev1.png',
    'EV2 Standard' => 'assets/img/ev2.png',
    'EV3 Premium' => 'assets/img/ev3.png'
];
$image_path = isset($bike_images[$loan['bike_model']]) ? $bike_images[$loan['bike_model']] : 'assets/img/ev1.png';

// Check Balance for Completion
$loan_id = $loan['loan_id'];
$payRow = $conn->query("SELECT IFNULL(SUM(amount_due),0) AS total FROM repayment_schedule WHERE loan_id = '$loan_id' AND status='paid'")->fetch_assoc();
$paid = floatval($payRow['total']);
$remaining = floatval($loan['amount']) - $paid;

$isCompleted = ($loan['status'] === 'completed') || ($remaining <= 0);

// Status Logic
$status = $loan['status']; 
$steps = [
    ['label' => 'Applied', 'active' => true],
    ['label' => 'Under Review', 'active' => false],
    ['label' => 'Approved', 'active' => false],
    ['label' => 'Repayment', 'active' => false]
];

// Determine active steps based on status
// Determine active steps based on status
if ($status === 'pending') {
    $steps[0]['active'] = true; 
    $steps[1]['active'] = true; 
} elseif ($status === 'approved' && !$isCompleted) {
    $steps[0]['active'] = true;
    $steps[1]['active'] = true;
    $steps[2]['active'] = true;
    // Repayment step stays inactive until completed
} elseif ($isCompleted) {
    foreach($steps as &$step) $step['active'] = true;
}
?>

<!-- Page Specific Styles -->
<style>
    body { background: #f8f9fa; font-family: 'Inter', sans-serif; }
    .status-container {
        display: flex;
        height: calc(100vh - 120px); /* Adjusted for header */
        background: #fff;
        max-width: 1400px;
        margin: 20px auto;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    /* LEFT SIDE (1 part) - 25% */
    .status-left {
        flex: 1;
        background: linear-gradient(135deg, #eef2f3 0%, #8e9eab 100%);
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
        position: relative;
    }
    .bike-img {
        width: 100%;
        max-width: 300px;
        height: auto;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.2));
        animation: float 5s ease-in-out infinite;
    }

    /* RIGHT SIDE (3 parts) - 75% */
    .status-right {
        flex: 3;
        padding: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .header-text h2 { font-size: 2.5rem; color: #333; margin-bottom: 10px; }
    .header-text p { color: #666; font-size: 1.1rem; }

    /* Progress Tracker */
    .tracker-container {
        margin-top: 60px;
        position: relative;
        padding: 20px 0;
    }
    
    .progress-line {
        position: absolute;
        top: 45px;
        left: 5%;
        width: 90%;
        height: 2px;
        background: #e0e0e0;
        z-index: 1;
    }

    .steps-wrapper {
        display: flex;
        justify-content: space-between;
        position: relative;
        z-index: 2;
    }
    
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 120px;
        text-align: center;
    }

    .circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #ccc;
        margin-bottom: 10px;
        transition: all 0.4s ease;
    }
    
    .step-item.active .circle {
        background: #4bb71b; /* Green for check */
        border-color: #4bb71b;
        color: #fff;
        box-shadow: 0 5px 15px rgba(75, 183, 27, 0.3);
    }

    .step-label {
        font-size: 0.9rem;
        color: #999;
        font-weight: 600;
    }
    .step-item.active .step-label { color: #333; }


        /* Status Message Box - Redesigned */
        .msg-box {
            margin-top: 50px;
            background: #fff;
            border: 1px solid #eef0f5;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            text-align: center;
        }
        .msg-box h4 { 
            color: #004aad; 
            margin-bottom: 12px; 
            font-size: 1.4rem; 
            font-weight: 700;
        }
        .msg-box p { 
            color: #666; 
            line-height: 1.6;
            font-size: 1.05rem;
            max-width: 80%;
            margin: 0 auto;
        }

        @media (max-width: 900px) {
            .status-container { flex-direction: column; height: auto; }
            .steps-wrapper { flex-wrap: wrap; gap: 20px; justify-content: center; }
            .progress-line { display: none; }
        }
    </style>
</head>
<body>

<div class="status-container">
    
    <!-- Left Side -->
    <div class="status-left">
        <img src="<?php echo $image_path; ?>" alt="Bike" class="bike-img">
    </div>

    <!-- Right Side -->
    <div class="status-right">
        <div class="header-text">
            <h2>Application Status</h2>
            <p>Track the progress of your loan application below.</p>
        </div>

        <div class="tracker-container">
            <div class="progress-line"></div>
            <div class="steps-wrapper">
                <?php foreach($steps as $step): ?>
                <div class="step-item <?php echo $step['active'] ? 'active' : ''; ?>">
                    <div class="circle">
                        <?php echo $step['active'] ? '&#10003;' : ''; ?>
                    </div>
                    <span class="step-label"><?php echo $step['label']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if($status === 'pending'): ?>
        <div class="msg-box">
            <h4>Application Under Review</h4>
            <p>Your application has been received and is currently being reviewed by our admin team. Please check back in 24 to 48 hours for an update.</p>
        </div>
        <?php elseif($isCompleted): ?>
            <div class="msg-box" style="border-color: #d1fae5; background: #ecfdf5;">
                <h4 style="color: #059669;">Congratulations! Loan Repaid</h4>
                <p>You have successfully repaid your loan. We hope you are enjoying your e-bike! Would you like to apply for another one?</p>
                <a href="dashboard.php?new_loan=true" style="display:inline-block; margin-top:15px; background:#059669; color:#fff; padding:10px 20px; text-decoration:none; border-radius:8px; font-weight:600;">Start New Loan</a>
            </div>
        <?php elseif($status === 'approved'): ?>
            <div class="msg-box" style="border-color: #d1fae5; background: #ecfdf5;">
                <h4 style="color: #059669;">Application Approved!</h4>
                <p>Congratulations! Your loan has been approved. Please follow the instructions to pick up your bike.</p>
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>
