<?php require_once 'config.php';
session_start(); ?>

<?php print_r($_GET);
$for = $_GET['for'];
$amount = $_GET['amount'];
$employee_name = $_SESSION['employee_name'];
$account = $_GET['account'];

// Add data to Petty Cash Tabel
$sql = "INSERT INTO pettycash (perrycash, amount, emp_name) VALUES ('{$for}','{$amount}', '{$employee_name}') ";
insert_query($sql, "for : $for , Rs. $amount, By : $employee_name", "Add Petty Cash");

// Petty Cash eka Company Profit & Casg Account eken adu karanawa
$sql = "UPDATE accounts SET amount = amount - {$amount} WHERE account_name = IN ('Company Profit', '$account');";
insert_query($sql,"for : $for , Rs. $amount, By : $employee_name, Account : $account and profit", "Fall Petty Cash from $account Account and profit");

// Add Transaction Log
$transaction_type = 'Petty Cash';
transaction_log($transaction_type, $for, -$amount);
?>

<?php end_db_con(); ?>