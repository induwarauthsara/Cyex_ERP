<link rel="stylesheet" href="style.css">
<?php
include 'nav.php';

// Use the main system's database connection
// Include the main config file that has the database connection
include '../inc/config.php';

// statistics Bank Deposit Balance
$sql = "SELECT * FROM printer_counter_statistics where name='to_deposit_balance'";
$result = $con->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $to_deposit_balance = $row['amount'];
    // make it as currency
    $to_deposit_balance = number_format($to_deposit_balance, 2);
} else {
    $to_deposit_balance = "ERROR";
}

include 'bankDeposit-header.php';