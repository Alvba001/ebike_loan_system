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
// Fix: user_id is the primary key column, not id
$user = $conn->query("SELECT * FROM users WHERE user_id='$user_id'")->fetch_assoc();
?>

<style>
    body {
        background-color: #f5f7fa; /* Light background for contrast */
    }
    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        max-width: 600px;
        margin: 40px auto;
        overflow: hidden;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        text-align: left; /* Reset alignment for content */
    }
    .profile-header {
        background: #004aad;
        color: #fff;
        padding: 40px 30px;
        text-align: center;
    }
    .profile-avatar {
        font-size: 60px;
        margin-bottom: 10px;
    }
    .profile-header h2 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
    }
    .profile-header p {
        margin: 5px 0 0;
        font-size: 16px;
        opacity: 0.9;
        color: #e0e0e0;
    }
    .profile-body {
        padding: 30px 40px;
    }
    .info-group {
        margin-bottom: 25px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 15px;
    }
    .info-group:last-child {
        border-bottom: none;
        margin-bottom: 10px;
    }
    .info-label {
        font-weight: 600;
        color: #888;
        display: block;
        margin-bottom: 5px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .info-value {
        font-size: 18px;
        color: #333;
        font-weight: 500;
    }
    .btn-back {
        display: inline-block;
        margin-top: 10px;
        color: #004aad;
        text-decoration: none;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        transition: background 0.3s;
    }
    .btn-back:hover {
        background-color: #e6f0ff;
    }
</style>

<div class="container">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">👤</div>
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <div class="profile-body">
            <div class="info-group">
                <span class="info-label">Full Name</span>
                <div class="info-value"><?php echo htmlspecialchars($user['name']); ?></div>
            </div>
            
            <div class="info-group">
                <span class="info-label">Email Address</span>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>

            <div class="info-group">
                <span class="info-label">Account Type</span>
                <div class="info-value"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></div>
            </div>
            
            <?php if(isset($user['created_at']) && $user['created_at']): ?>
            <div class="info-group">
                <span class="info-label">Member Since</span>
                <div class="info-value"><?php echo date("F j, Y", strtotime($user['created_at'])); ?></div>
            </div>
            <?php endif; ?>

            <div style="text-align: center; margin-top: 20px;">
                <a href="dashboard.php" class="btn-back"> <i class="bi bi-arrow-left"></i> Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
