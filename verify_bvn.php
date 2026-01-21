<?php
include 'includes/db_connect.php';

$table = 'loan_applications';
$column = 'bvn';
$result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");

if ($result && $result->num_rows > 0) {
    echo "VERIFICATION PASSED: Column '$column' exists in table '$table'.";
} else {
    echo "VERIFICATION FAILED: Column '$column' does not exist in table '$table'.";
}
?>
