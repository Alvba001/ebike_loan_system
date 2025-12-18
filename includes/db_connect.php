<?php
// Database connection file

$servername = "localhost";   // because we’re using XAMPP locally
$username = "root";          // default MySQL username in XAMPP
$password = "";              // leave empty by default
$dbname = "ebike_loan_db";   // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Database connected successfully!";
// You can comment out the above line after confirming the connection works.
?>
