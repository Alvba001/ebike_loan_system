<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch all loan applications with borrower info
$sql = "
    SELECT l.*, u.name, u.email
    FROM loan_applications l
    JOIN users u ON l.user_id = u.user_id
    ORDER BY l.loan_id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Loan Applications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Google Fonts for premium feel -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f7f9fc;
            font-family: 'Inter', sans-serif;
            color: #334155;
        }
        .main-content {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
        }
        .page-title {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.5px;
        }
        .page-subtitle {
            font-size: 15px;
            color: #64748b;
            margin-top: 5px;
        }

        /* Card & Table Container */
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        th {
            padding: 20px 24px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        td {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 14px;
            transition: background-color 0.2s;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background-color: #f8fafc;
        }

        /* Status Pills */
        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background-color: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }
        .status-approved {
            background-color: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }
        .status-rejected {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }
        .status-completed {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }
        
        /* Typography Helpers */
        .text-main { color: #0f172a; font-weight: 600; }
        .text-sub { color: #64748b; font-size: 13px; }
        .amount-text { font-family: 'Inter', sans-serif; font-weight: 700; color: #334155; }
        
        /* Action Button */
        .btn-view {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            background-color: #ffffff;
            color: #004aad;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .btn-view:hover {
            background-color: #004aad;
            color: #ffffff;
            border-color: #004aad;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 74, 173, 0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="main-content">

    <div class="page-header">
        <div>
            <h1 class="page-title">Loan Applications</h1>
            <p class="page-subtitle">Overview of all borrower requests</p>
        </div>
        <!-- Placeholder for potential search bar or filters -->
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Attributes</th>
                    <th>Borrower Details</th>
                    <th>Loan Specs</th>
                    <th>Status</th>
                    <th>Submitted On</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php 
                            // Determine status pill class
                            $status_class = 'status-pending';
                            if ($row['status'] == 'approved') $status_class = 'status-approved';
                            if ($row['status'] == 'rejected') $status_class = 'status-rejected';
                            if ($row['status'] == 'completed') $status_class = 'status-completed';
                            
                            // Bike Image Logic
                            $imgMap = [
                                'EV1 Basic' => '../assets/img/ev1.png',
                                'EV2 Standard' => '../assets/img/ev2.png',
                                'EV3 Premium' => '../assets/img/ev3.png'
                            ];
                            $bikeImg = isset($imgMap[$row['bike_model']]) ? $imgMap[$row['bike_model']] : '../assets/img/ev1.png';
                            
                            $date_applied = date("M d, Y", strtotime($row['date_applied']));
                        ?>
                        <tr>
                            <!-- Bike visual -->
                            <td width="80">
                                <img src="<?php echo $bikeImg; ?>" style="width: 50px; height: auto; border-radius: 4px; border: 1px solid #f1f5f9; padding: 2px; background: #fff;">
                            </td>
                            
                            <td>
                                <div class="text-main"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="text-sub"><?php echo htmlspecialchars($row['email']); ?></div>
                            </td>
                            
                            <td>
                                <div class="text-main"><?php echo htmlspecialchars($row['bike_model']); ?></div>
                                <div class="text-sub">₦<?php echo number_format($row['amount']); ?> &bull; <?php echo $row['duration']; ?>m</div>
                            </td>
                            
                            <td>
                                <span class="status-pill <?php echo $status_class; ?>">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            
                            <td class="text-sub"><?php echo $date_applied; ?></td>
                            
                            <td style="text-align: right;">
                                <a href="loan_details.php?id=<?php echo $row['loan_id']; ?>" class="btn-view">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty-state">
                            No loan applications found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
