<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Validate ID
if (!isset($_GET['id'])) {
    header("Location: view_loans.php");
    exit();
}

$loan_id = $_GET['id'];

// Fetch loan + borrower details
$sql = "
    SELECT l.*, u.name, u.email
    FROM loan_applications l
    JOIN users u ON l.user_id = u.user_id
    WHERE l.loan_id = '$loan_id'
";

$result = $conn->query($sql);
$loan = $result->fetch_assoc();

if (!$loan) {
    echo "<script>alert('Loan record not found.'); window.location='view_loans.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan Details - #<?= $loan_id ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Inter', sans-serif;
            color: #334155;
        }

        .main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }

        /* Grid Layout */
        .details-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
        
        @media (max-width: 900px) {
            .details-grid { grid-template-columns: 1fr; }
        }

        /* Cards */
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .card-header {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-group { margin-bottom: 16px; }
        .info-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .info-value {
            font-size: 15px;
            color: #1e293b;
            font-weight: 500;
        }
        
        /* Status */
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 99px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
        }
        .status-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .status-approved { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        /* Documents */
        .doc-link {
            display: flex;
            align-items: center;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            color: #004aad;
            font-weight: 500;
            margin-bottom: 10px;
            transition: all 0.2s;
        }
        .doc-link:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        /* Buttons */
        .action-bar {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: transform 0.1s;
        }
        .btn:active { transform: scale(0.98); }
        
        .btn-approve { background: #16a34a; color: white; flex: 1; }
        .btn-reject { background: #dc2626; color: white; flex: 1; }
        .btn-back { background: #fff; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; }

    </style>
</head>
<body>

<div class="main-container">

    <div class="page-header">
        <h1 class="page-title">Loan Details <span style="font-weight: 400; color: #94a3b8;">#<?= $loan_id ?></span></h1>
        <a href="view_loans.php" class="btn-back">⬅ Back to List</a>
    </div>

    <div class="details-grid">
        
        <!-- LEFT COLUMN -->
        <div class="left-col">
            
            <!-- BORROWER INFO -->
            <div class="card">
                <div class="card-header">👤 Borrower Profile</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="info-group">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= htmlspecialchars($loan['name']) ?></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?= htmlspecialchars($loan['email']) ?></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">NIN</div>
                        <div class="info-value"><?= htmlspecialchars($loan['nin']) ?></div>
                    </div>
                    <div class="info-group">
                        <div class="info-label">BVN</div>
                        <div class="info-value"><?= isset($loan['bvn']) ? htmlspecialchars($loan['bvn']) : 'N/A' ?></div>
                    </div>
                </div>
            </div>

            <!-- GUARANTOR INFO -->
            <div class="card">
                <div class="card-header">🛡️ Guarantor Information</div>
                <?php
                $g_sql = "SELECT * FROM guarantor_information WHERE loan_application_id = '$loan_id'";
                $g_result = $conn->query($g_sql);
                $guarantor = $g_result->fetch_assoc();
                
                if ($guarantor): ?>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="info-group">
                            <div class="info-label">Guarantor Name</div>
                            <div class="info-value"><?= htmlspecialchars($guarantor['full_name']) ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Relationship</div>
                            <div class="info-value"><?= htmlspecialchars($guarantor['relationship']) ?> (<?= $guarantor['years_known'] ?> years)</div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Phone</div>
                            <div class="info-value"><?= htmlspecialchars($guarantor['phone']) ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">ID Type/Number</div>
                            <div class="info-value"><?= htmlspecialchars($guarantor['id_type']) ?> - <?= htmlspecialchars($guarantor['id_number']) ?></div>
                        </div>
                        <div class="info-group" style="grid-column: span 2;">
                            <div class="info-label">Address</div>
                            <div class="info-value"><?= htmlspecialchars($guarantor['address']) ?></div>
                        </div>
                         <div class="info-group" style="grid-column: span 2;">
                            <div class="info-label">Employment</div>
                            <div class="info-value"><?= htmlspecialchars($guarantor['occupation']) ?> at <?= htmlspecialchars($guarantor['employer']) ?></div>
                        </div>
                    </div>
                <?php else: ?>
                    <p style="color: #94a3b8; font-style: italic;">No guarantor information recorded.</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="right-col">
            
            <!-- LOAN SPECS -->
            <div class="card">
                <div class="card-header">🚲 Loan Specifications</div>
                
                <div class="info-group">
                    <div class="info-label">Bike Model</div>
                    <div class="info-value" style="font-size: 18px; color: #004aad;"><?= htmlspecialchars($loan['bike_model']) ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Amount Requested</div>
                    <div class="info-value">₦<?= number_format($loan['amount']) ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Duration</div>
                    <div class="info-value"><?= $loan['duration'] ?> Months</div>
                </div>
                
                <div class="info-group">
                    <div class="info-label">Purpose</div>
                    <div class="info-value"><?= htmlspecialchars($loan['purpose']) ?></div>
                </div>

                <div class="info-group">
                    <div class="info-label">Date Applied</div>
                    <div class="info-value"><?= date("F j, Y", strtotime($loan['date_applied'])) ?></div>
                </div>
                
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <div class="info-label">Status</div>
                    <?php 
                    $s = $loan['status'];
                    $cls = 'status-pending';
                    if($s=='approved') $cls='status-approved';
                    if($s=='rejected') $cls='status-rejected';
                    ?>
                    <span class="status-badge <?= $cls ?>"><?= strtoupper($s) ?></span>
                </div>

                <?php if ($loan['status'] === 'pending'): ?>
                    <div class="action-bar">
                        <a href="approve_loan.php?id=<?= $loan_id ?>" class="btn btn-approve" onclick="return confirm('Are you sure you want to approve this loan?')">Approve</a>
                        <a href="reject_loan.php?id=<?= $loan_id ?>" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this loan?')">Reject</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- DOCUMENTS -->
            <div class="card">
                <div class="card-header">📂 Documents</div>
                
                <?php if (!empty($loan['id_card'])): ?>
                    <a href="../<?= $loan['id_card'] ?>" target="_blank" class="doc-link">
                        <span>📄 View ID Card</span>
                    </a>
                <?php else: ?>
                    <div style="color: #94a3b8; padding: 10px;">ID Card missing</div>
                <?php endif; ?>

                <?php if (!empty($loan['utility_bill'])): ?>
                    <a href="../<?= $loan['utility_bill'] ?>" target="_blank" class="doc-link">
                        <span>📄 View Utility Bill</span>
                    </a>
                <?php else: ?>
                    <div style="color: #94a3b8; padding: 10px;">Utility Bill missing</div>
                <?php endif; ?>
            </div>

        </div>

    </div>

</div>

</body>
</html>
