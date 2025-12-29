<?php
session_start();
include 'includes/db_connect.php';
include 'includes/borrower_header.php';

// Restrict access to borrowers
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'borrower') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Fetch latest loan for the borrower (if any) ---
$loan = $conn->query("
    SELECT * FROM loan_applications
    WHERE user_id = '$user_id'
    ORDER BY loan_id DESC
    LIMIT 1
")->fetch_assoc();

$loan_id = $loan ? intval($loan['loan_id']) : null;

// --- Calculate repayment summary if loan exists ---
$paid = 0;
$remaining = 0;
$percent = 0;
if ($loan_id) {
    $payRow = $conn->query("SELECT IFNULL(SUM(amount_paid),0) AS total FROM repayments WHERE loan_id = '$loan_id'")->fetch_assoc();
    $paid = floatval($payRow['total']);
    $remaining = floatval($loan['amount']) - $paid;
    $percent = $loan['amount'] > 0 ? ($paid / $loan['amount']) * 100 : 0;
    if ($percent < 0) $percent = 0;
    if ($percent > 100) $percent = 100;
}

// --- Fetch assigned bike if any ---
$assign = null;
if ($loan_id) {
    $assign = $conn->query("
        SELECT b.serial_number, b.model 
        FROM bike_assignments a
        JOIN bikes b ON a.bike_id = b.bike_id
        WHERE a.loan_id = '$loan_id'
        ORDER BY a.assigned_date DESC
        LIMIT 1
    ")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Borrower Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .header-row { display:flex; justify-content:space-between; align-items:center; gap:10px; }
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:18px; margin-top:18px; }
        .btn { display:inline-block; margin-top:10px; padding:8px 14px; background:#004aad; color:#fff; border-radius:8px; text-decoration:none; }
        .info-box { background:#f5f8ff; padding:14px; border-left:4px solid #004aad; border-radius:8px; margin-top:20px; }
        .timeline { display:flex; gap:8px; justify-content:space-between; margin-top:18px; }
        .step { flex:1; padding:8px; text-align:center; border-bottom:4px solid #f0d14bff; color:#777; font-weight:600; }
        .step.active { color:#004aad; border-color:#004aad; }
        .progress-box { background:#fbfdff; padding:14px; border-radius:10px; margin-top:14px; }
        .progress-bar { width:100%; background:#e9ecef; height:16px; border-radius:8px; overflow:hidden; margin:10px 0; }
        .progress-bar .fill { height:16px; background:#004aad; width:0%; transition:width .6s; }
    </style>
</head>
<body>

<?php
    // Determine if user has an active loan (not completed and not rejected)
    $hasActiveLoan = $loan && !in_array($loan['status'], ['completed', 'rejected']);
?>

<div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 20px;">
    <?php if (!$hasActiveLoan): ?>
        <!-- NEW SPLIT UI for No Active Loan -->
        <div class="split-container">
            <!-- Left Side: Image Carousel -->
            <div class="split-left">
                <div class="main-image-wrapper">
                    <button class="nav-btn prev-btn" onclick="prevBike()">&#10094;</button>
                    <img id="mainBikeImg" src="assets/img/ev1.png" alt="EV1 Bike" class="bike-img">
                    <button class="nav-btn next-btn" onclick="nextBike()">&#10095;</button>
                </div>
                
                <div class="thumbnails">
                    <div class="thumb active" onclick="setBike(0)">
                        <img src="assets/img/ev1.png" alt="EV1">
                        <span>EV1</span>
                    </div>
                    <div class="thumb" onclick="setBike(1)">
                        <img src="assets/img/ev2.png" alt="EV2">
                        <span>EV2</span>
                    </div>
                    <div class="thumb" onclick="setBike(2)">
                        <img src="assets/img/ev3.png" alt="EV3">
                        <span>EV3</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Info -->
            <div class="split-right">
                <div class="info-content">
                    <h1 id="bikeTitle">EV1 Bike</h1>
                    <p class="subtitle">LOAN AMOUNT</p>
                    <p class="price" id="bikePrice">₦900,000</p>
                    
                    <div class="description">
                        <p>Experience the future of commuting with our premium electric bikes. Efficient, eco-friendly, and stylish.</p>
                    </div>

                    <a href="apply_loan.php" class="btn-cta">Apply for Loan</a>
                </div>
            </div>
        </div>

        <style>
            /* Reset/Base for this section */
            .split-container {
                display: flex;
                min-height: 80vh;
                background: #fff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            }
            .split-left {
                flex: 1;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                padding: 40px;
                position: relative;
            }
            .split-right {
                flex: 1;
                padding: 60px;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            /* Left Side Styles */
            .main-image-wrapper {
                position: relative;
                width: 100%;
                max-width: 600px;
                height: 400px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .bike-img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                transition: transform 0.5s ease, opacity 0.5s ease;
                filter: drop-shadow(0 20px 30px rgba(0,0,0,0.2));
            }
            .nav-btn {
                background: rgba(255,255,255,0.8);
                border: none;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                font-size: 24px;
                color: #333;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
                position: absolute;
                z-index: 10;
            }
            .nav-btn:hover {
                background: #fff;
                transform: scale(1.1);
                box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            }
            .prev-btn { left: 0; }
            .next-btn { right: 0; }

            .thumbnails {
                display: flex;
                gap: 20px;
                margin-top: 40px;
            }
            .thumb {
                width: 80px;
                height: 80px;
                background: rgba(255,255,255,0.5);
                border-radius: 12px;
                padding: 10px;
                cursor: pointer;
                transition: all 0.3s ease;
                border: 2px solid transparent;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .thumb img {
                width: 100%;
                height: auto;
                object-fit: contain;
                margin-bottom: 5px;
            }
            .thumb span {
                font-size: 10px;
                font-weight: 700;
                color: #555;
            }
            .thumb.active {
                background: #fff;
                border-color: #004aad;
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0,74,173,0.2);
            }

            /* Right Side Styles */
            .info-content {
                max-width: 500px;
                animation: fadeIn 0.8s ease-out;
            }
            h1 {
                font-size: 4rem;
                font-weight: 800;
                color: #004aad;
                margin-bottom: 10px;
                line-height: 1.1;
                letter-spacing: -1px;
            }
            .subtitle {
                font-size: 1.2rem;
                color: #888;
                font-weight: 500;
                letter-spacing: 2px;
                margin-bottom: 5px;
                text-transform: uppercase;
            }
            .price {
                font-size: 3rem;
                font-weight: 700;
                color: #333;
                margin-bottom: 30px;
            }
            .description {
                font-size: 1.1rem;
                line-height: 1.6;
                color: #555;
                margin-bottom: 40px;
            }
            .btn-cta {
                display: inline-block;
                background: #004aad;
                color: #fff;
                font-size: 1.2rem;
                padding: 18px 40px;
                border-radius: 50px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 10px 20px rgba(0,74,173,0.3);
            }
            .btn-cta:hover {
                background: #003380;
                transform: translateY(-2px);
                box-shadow: 0 15px 30px rgba(0,74,173,0.4);
            }

            /* Responsive */
            @media (max-width: 900px) {
                .split-container {
                    flex-direction: column;
                }
                .split-left, .split-right {
                    padding: 30px;
                }
                h1 { font-size: 3rem; }
                .price { font-size: 2.5rem; }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>

        <script>
            const bikes = [
                { id: 1, name: "EV1 Bike", price: "₦900,000", image: "assets/img/ev1.png" },
                { id: 2, name: "EV2 Bike", price: "₦1,200,000", image: "assets/img/ev2.png" },
                { id: 3, name: "EV3 Bike", price: "₦1,500,000", image: "assets/img/ev3.png" }
            ];

            let currentIndex = 0;

            function updateDisplay() {
                const bike = bikes[currentIndex];
                const img = document.getElementById('mainBikeImg');
                const title = document.getElementById('bikeTitle');
                const price = document.getElementById('bikePrice');
                const thumbs = document.querySelectorAll('.thumb');

                // Animate info changes
                img.style.opacity = 0;
                
                setTimeout(() => {
                    img.src = bike.image;
                    img.style.opacity = 1;
                    
                    title.textContent = bike.name;
                    price.textContent = bike.price;
                }, 200);

                // Update Thumbs
                thumbs.forEach((thumb, index) => {
                    if (index === currentIndex) {
                        thumb.classList.add('active');
                    } else {
                        thumb.classList.remove('active');
                    }
                });
            }

            function nextBike() {
                currentIndex = (currentIndex + 1) % bikes.length;
                updateDisplay();
            }

            function prevBike() {
                currentIndex = (currentIndex - 1 + bikes.length) % bikes.length;
                updateDisplay();
            }

            function setBike(index) {
                currentIndex = index;
                updateDisplay();
            }
        </script>

    <?php else: ?>
        <!-- EXISTING DASHBOARD (Wrapped) -->
        <div class="header-row">
            <div>
                <h2>Borrower Dashboard</h2>
                <p>Welcome, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></p>
            </div>
        </div>
    
        <hr>
    
        <!-- Quick Actions -->
        <div class="grid">
            <div class="quick-card">
                <h3>Apply for a Loan</h3>
                <a href="apply_loan.php" class="btn">Start Application</a>
            </div>
    
            <div class="quick-card">
                <h3>Loan Status</h3>
                <a href="view_status.php" class="btn">Check Status</a>
            </div>
    
            <div class="quick-card">
                <h3>Make Repayment</h3>
                <a href="make_repayment.php" class="btn">Pay Now</a>
            </div>
    
            <div class="quick-card">
                <h3>Repayment History</h3>
                <a href="repayment_history.php" class="btn">View Records</a>
            </div>
    
            <div class="quick-card">
                <h3>Notifications</h3>
                <a href="notifications.php" class="btn">View Alerts</a>
            </div>
        </div>
    
        <!-- Assigned Bike -->
        <?php if ($assign) : ?>
            <div class="info-box">
                <p><strong>Assigned Bike:</strong> <?php echo htmlspecialchars($assign['model']); ?> (<?php echo htmlspecialchars($assign['serial_number']); ?>)</p>
            </div>
        <?php endif; ?>
    
        <!-- Loan Summary + Timeline -->
        <?php if ($loan) : ?>
            <div class="info-box" style="margin-top:18px;">
                <h3>Current Loan Summary</h3>
                <p><strong>Amount:</strong> ₦<?php echo number_format($loan['amount']); ?> &nbsp; | &nbsp; <strong>Status:</strong> <?php echo strtoupper(htmlspecialchars($loan['status'])); ?></p>
                <p><strong>Bike Model:</strong> <?php echo htmlspecialchars($loan['bike_model']); ?> &nbsp; | &nbsp; <strong>Duration:</strong> <?php echo intval($loan['duration']); ?> months</p>
            </div>
    
            <!-- Timeline -->
            <div class="timeline">
                <div class="step <?php echo ($loan ? 'active' : ''); ?>">Applied</div>
                <div class="step <?php echo ($loan['status'] === 'pending' ? 'active' : ''); ?>">Under Review</div>
                <div class="step <?php echo ($loan['status'] === 'approved' ? 'active' : ''); ?>">Approved</div>
                <div class="step <?php echo ($assign ? 'active' : ''); ?>">Bike Assigned</div>
                <div class="step <?php echo ($loan['status'] === 'approved' ? 'active' : ''); ?>">Repayment</div>
    
                <div class="step <?php echo ($loan && $remaining <= 0 ? 'active' : ''); ?>">Completed</div>
            </div>
    
            <!-- Progress -->
            <div class="progress-box">
                <p><strong>Total Loan:</strong> ₦<?php echo number_format($loan['amount']); ?></p>
                <p><strong>Total Paid:</strong> ₦<?php echo number_format($paid,2); ?></p>
                <p><strong>Remaining:</strong> ₦<?php echo number_format(max(0,$remaining),2); ?></p>
    
                <div class="progress-bar">
                    <div class="fill" style="width: <?php echo round($percent,2); ?>%"></div>
                </div>
    
                <p><strong><?php echo round($percent,1); ?>% Completed</strong></p>
            </div>
        <?php else: ?>
            <!-- Fallback if logic mismatch, but strictly technically reachable if loan exists but is completed, though we trapped that in top condition. 
                 If code reaches here, it means $hasActiveLoan is true, so $loan MUST be true. 
                 Wait, my logic:
                 $hasActiveLoan = $loan && !completed/rejected.
                 If $loan is null, $hasActiveLoan is false. -> New UI.
                 If $loan is completed, $hasActiveLoan is false. -> New UI.
                 If $loan is pending, $hasActiveLoan is true. -> Old UI.
                 So this inner else block (Line ~148 in original) "No active loan found" is actually unreachable/redundant now for the "no loan" case because they are in the top block.
                 But let's keep it safe. -->
            <div class="info-box" style="margin-top:18px;">
                 <p>Status: <?php echo htmlspecialchars($loan['status']); ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
