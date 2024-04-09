<?php
require_once '../inc/config.php';

// Write the SQL query to insert the data
$amount = 3000;
$description = 'Day Salary';
$sql = "INSERT INTO lochana_wallet (amount, description) VALUES ('$amount', '$description');";
if ($con->query($sql) === TRUE) {
    echo "New record created successfully";
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
$con->close();
