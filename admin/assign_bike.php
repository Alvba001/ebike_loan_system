<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$loan_id = $_GET['loan_id'];

// Fetch available bikes
$bikes = $conn->query("SELECT * FROM bikes WHERE status='available'");

// Fetch loan info
$loan = $conn->query("
    SELECT l.loan_id, l.bike_model, u.name 
    FROM loan_applications l 
    JOIN users u ON l.user_id=u.user_id 
    WHERE l.loan_id='$loan_id'
")->fetch_assoc();

if (isset($_POST['assign'])) {
    $bike_id = $_POST['bike_id'];

    // Save assignment
    $conn->query("INSERT INTO bike_assignments (loan_id, bike_id) VALUES ('$loan_id', '$bike_id')");

    // Update bike status
    $conn->query("UPDATE bikes SET status='assigned' WHERE bike_id='$bike_id'");

    echo "<script>alert('Bike assigned successfully!'); window.location='view_loans.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Bike</title>
</head>
<body>

<div class="container">
    <h2>Assign Bike to Borrower</h2>

    <p><strong>Borrower:</strong> <?= $loan['name'] ?></p>
    <p><strong>Bike Model:</strong> <?= $loan['bike_model'] ?></p>

    <form method="POST">
        <label>Select Available Bike</label>
        <select name="bike_id" required>
            <option value="">-- Select Bike --</option>
            <?php while ($b = $bikes->fetch_assoc()) { ?>
                <option value="<?= $b['bike_id'] ?>">
                    <?= $b['serial_number'] ?> (<?= $b['model'] ?>)
                </option>
            <?php } ?>
        </select>

        <button type="submit" name="assign">Assign Bike</button>
    </form>
</div>

</body>
</html>
