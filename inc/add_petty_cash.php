<?php require_once 'config.php'; ?>

<?php print_r($_GET);
$for = $_GET['for'];
$amount = $_GET['amount'];

// Add data to Petty Cash Tabel
$sql = "INSERT INTO pettycash (perrycash, amount) VALUES ('{$for}','{$amount}') ";
insert_query($sql, "Insert Petty Cash to Tabel");

// Petty Cash eka Company Profit eken adu karanawa
$sql = "UPDATE accounts SET amount = amount - {$amount} WHERE account_name = 'Company Profit'";
insert_query($sql, "Fall Petty Cash from Company Profit");
?>

<?php end_db_con(); ?>