<?php
require_once '../inc/config.php';

// Write the SQL query to insert the data
$amount = $_POST['amount'];
$description = $_POST['description'];
$recordType = $_POST['type'];

if ($recordType == 'Payment') {
    $amount = -$amount;
}

$sql = "INSERT INTO godagamaPressCreditBill (amount, description) VALUES ('$amount', '$description');";
if ($con->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
$con->close();
