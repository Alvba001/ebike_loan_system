<?php
include 'includes/db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS guarantor_information (
    id INT AUTO_INCREMENT PRIMARY KEY,
    loan_application_id INT NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    gender VARCHAR(50),
    dob DATE,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    address TEXT NOT NULL,
    relationship VARCHAR(100) NOT NULL,
    years_known VARCHAR(50),
    id_type VARCHAR(100) NOT NULL,
    id_number VARCHAR(100) NOT NULL,
    occupation VARCHAR(100) NOT NULL,
    employer VARCHAR(255),
    work_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (loan_application_id) REFERENCES loan_applications(loan_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'guarantor_information' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
