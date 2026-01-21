<?php
include 'includes/db_connect.php';

$sql = "ALTER TABLE loan_applications ADD COLUMN bvn VARCHAR(11) NOT NULL AFTER nin";

if ($conn->query($sql) === TRUE) {
    echo "Column bvn added successfully";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>
