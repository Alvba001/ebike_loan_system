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

// Fetch Loan
$loan = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id = '$user_id'
    ORDER BY loan_id DESC
    LIMIT 1
")->fetch_assoc();

// Redirect if not approved
if (!$loan || $loan['status'] !== 'approved') {
    header("Location: dashboard.php");
    exit();
}

$loan_id = intval($loan['loan_id']);

// Calculate repayment summary
$payRow = $conn->query("SELECT IFNULL(SUM(amount_paid),0) AS total FROM repayments WHERE loan_id = '$loan_id'")->fetch_assoc();
$paid = floatval($payRow['total']);
$remaining = floatval($loan['amount']) - $paid;

// Bike assignment query removed
?>

<!-- Page Styles -->
<style>
    body { background: #fdfdfd; }
    .split-container {
        display: flex;
        min-height: calc(100vh - 80px); /* Adjust for header */
        background: #fff;
        overflow: hidden;
    }
    .split-left {
        flex: 1; /* 25% - 1 part */
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        position: relative;
        border-right: 1px solid #eee;
    }
    .split-right {
        flex: 3; /* 75% - 3 parts */
        padding: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .bike-img {
        max-width: 90%;
        height: auto;
        object-fit: contain;
        filter: drop-shadow(0 20px 30px rgba(0,0,0,0.15));
        transition: transform 0.5s ease;
    }
    .bike-img:hover { transform: scale(1.05); }

    .info-content { max-width: 700px; }
    h1 {
        font-size: 3.5rem;
        font-weight: 800;
        color: #004aad;
        margin-bottom: 5px;
        letter-spacing: -1px;
    }
    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        background: #d1fae5;
        color: #065f46;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .interest-badge {
        display: inline-block;
        padding: 6px 14px;
        background: #f3f4f6;
        color: #4b5563;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-left: 10px;
    }
    .subtitle {
        font-size: 1.1rem;
        color: #888;
        font-weight: 600;
        letter-spacing: 2px;
        margin-bottom: 5px;
        text-transform: uppercase;
        margin-top: 30px;
    }
    .price {
        font-size: 3.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
    }
    .description {
        font-size: 1.2rem;
        line-height: 1.6;
        color: #555;
        margin-bottom: 40px;
    }
    
    .btn-cta {
        display: inline-block;
        background: #004aad;
        color: #fff;
        font-size: 1.2rem;
        padding: 18px 45px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(0,74,173,0.3);
        border: none;
        text-align: center;
    }
    .btn-cta:hover {
        background: #003380;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,74,173,0.4);
    }
    
    .btn-secondary-gray {
        display: inline-block;
        background: #f1f5f9;
        color: #475569;
        font-size: 1.1rem;
        padding: 18px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-left: 15px;
    }
    .btn-secondary-gray:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    @media (max-width: 900px) {
        .split-container { flex-direction: column; }
        .split-left, .split-right { padding: 40px; border: none; }
        .split-left { min-height: 300px; }
    }
</style>

<div class="split-container">
    <!-- Left Side: Bike Image -->
    <div class="split-left">
        <?php 
            $imgMap = [
                'EV1 Basic' => 'assets/img/ev1.png',
                'EV2 Standard' => 'assets/img/ev2.png',
                'EV3 Premium' => 'assets/img/ev3.png'
            ];
            $bikeImg = isset($imgMap[$loan['bike_model']]) ? $imgMap[$loan['bike_model']] : 'assets/img/ev1.png';
        ?>
        <img src="<?php echo $bikeImg; ?>" alt="My Bike" class="bike-img">
        
        <div style="margin-top: 30px; text-align: center;">
            <h3 style="color: #004aad; font-size: 1.5rem;"><?php echo htmlspecialchars($loan['bike_model']); ?></h3>

        </div>
    </div>

    <!-- Right Side: Info & Actions -->
    <div class="split-right">
        <div class="info-content">
            <span class="status-badge">Loan Active</span>
            
            <?php
            $duration = intval($loan['duration']);
            $interest_text = "10% Interest";
            if ($duration == 6) $interest_text = "15% Interest";
            if ($duration == 12) $interest_text = "20% Interest";
            ?>
            <span class="interest-badge"><?php echo $interest_text; ?></span>

            <h1>Approved & Active</h1>
            
            <?php if (isset($_GET['payment']) && $_GET['payment'] == 'success'): ?>
                <div style="background: #ecfdf5; color: #047857; padding: 15px 20px; border-radius: 10px; margin: 20px 0; border: 1px solid #a7f3d0;">
                    <strong>Payment Successful!</strong> Your account has been credited. Next installment due in 30 days.
                </div>
            <?php endif; ?>

            <p class="subtitle">Outstanding Balance</p>
            <p class="price">₦<?php echo number_format($remaining); ?></p>
            
            <div class="description">
                <p>Congratulation! Your loan application has been approved. You are now required to make monthly repayment installments to clear your balance.</p>
            </div>
             

            <div class="actions">
                <?php if ($remaining > 0): ?>
                    <a href="pay_installment.php?loan_id=<?php echo $loan_id; ?>" class="btn-cta">Pay Installment</a>
                <?php else: ?>
                    <div class="btn-cta" style="background: #10b981; cursor: default;">Loan Fully Repaid</div>
                <?php endif; ?>

                <a href="repayment_history.php" class="btn-secondary-gray">View History</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<!-- Helper for footer overlap if any -->
<script>
    // Ensure footer doesn't break split layout if it's fixed, but standard footer needs no script usually.
</script>
</body>
</html>
