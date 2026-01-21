<?php
include 'includes/db_connect.php';

// First check the current column type
$check = $conn->query("SHOW COLUMNS FROM loan_applications LIKE 'status'");
$row = $check->fetch_assoc();
echo "Current Type: " . $row['Type'] . "\n";

// Alter table to modify status column to VARCHAR(50) to accept any status, OR add to ENUM
// For flexibility, let's change it to VARCHAR(50) if it's not already, or update the ENUM
// Assuming it's ENUM('pending','approved','rejected')

$sql = "ALTER TABLE loan_applications MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'pending_guarantor') DEFAULT 'pending'";

if ($conn->query($sql) === TRUE) {
    echo "Table 'loan_applications' altered successfully. Added 'pending_guarantor'.\n";
} else {
    echo "Error altering table: " . $conn->error . "\n";
    
    // Fallback: Change to VARCHAR if ENUM fails or is annoying
    $sql_varchar = "ALTER TABLE loan_applications MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'";
    if ($conn->query($sql_varchar) === TRUE) {
        echo "Fallback: Changed status to VARCHAR(50).\n";
    } else {
        echo "Fallback Error: " . $conn->error . "\n";
    }
}
?>
