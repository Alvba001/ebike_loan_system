<?php
include 'includes/db_connect.php';

$table = 'loan_applications';
$result = $conn->query("DESCRIBE $table");

if ($result) {
    echo "Columns in $table:\n";
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Error describing table: " . $conn->error;
}
?>
