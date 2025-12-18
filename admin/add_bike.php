<?php
session_start();
include '../includes/db_connect.php';
include 'admin_header.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['add'])) {
    $serial = $_POST['serial'];
    $model  = $_POST['model'];

    $sql = "INSERT INTO bikes (serial_number, model) VALUES ('$serial', '$model')";
    $conn->query($sql);

    echo "<script>alert('Bike added successfully!'); window.location='add_bike.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Bike</title>
</head>
<body>

<div class="container">
    <h2>Add Electric Bike</h2>

    <form method="POST">
        <label>Bike Serial Number</label>
        <input type="text" name="serial" required>

        <label>Bike Model</label>
        <select name="model" required>
            <option value="EV1 Basic">EV1 Basic</option>
            <option value="EV2 Standard">EV2 Standard</option>
            <option value="EV3 Premium">EV3 Premium</option>
        </select>

        <button type="submit" name="add">Add Bike</button>
    </form>
</div>

</body>
</html>
