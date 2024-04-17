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
$sql = "UPDATE accounts SET amount = amount - $amount WHERE account_name = 'cash_in_hand'";
if ($con->query($sql) === TRUE) {
    echo "Account updated successfully";

    // Add Transaction Log
    $transaction_type = 'Wallet';
    $description = 'Lochana Wallet';
    transaction_log($transaction_type, $description, -$amount);
} else {
    echo "Error: " . $sql . "<br>" . $con->error;
}
$con->close();
