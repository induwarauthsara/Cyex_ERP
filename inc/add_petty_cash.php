<?php require_once 'config.php';
session_start(); ?>

<?php print_r($_GET);
$for = $_GET['for'];
$amount = $_GET['amount'];
$employee_name = $_SESSION['employee_name'];

// Add data to Petty Cash Tabel
$sql = "INSERT INTO pettycash (perrycash, amount, emp_name) VALUES ('{$for}','{$amount}', '{$employee_name}') ";
insert_query($sql, "Insert Petty Cash to Tabel");

// Petty Cash eka Company Profit eken adu karanawa
$sql = "UPDATE accounts SET amount = amount - {$amount} WHERE account_name = 'cash_in_hand'";
insert_query($sql, "Fall Petty Cash from Cash in Hand");

// Add Transaction Log
$transaction_type = 'Petty Cash';
transaction_log($transaction_type, $for, $amount);
?>

<?php end_db_con(); ?>