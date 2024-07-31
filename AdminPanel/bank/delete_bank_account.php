<?php
// 1. if want, Delete Previous Record
// 2. Delete Bank Account

require_once '../../inc/config.php';

$account_name = $_GET['account_name'];
$amount = $_GET['amount'];
$deletePreviousRecords = $_GET['deletePreviousRecords'];

// 1. if want, Delete Previous Record
if ($deletePreviousRecords == 'true') {
    $sql = "DELETE FROM bank_deposits WHERE bank_account = '$account_name';";
    insert_query($sql, "Delete Previous Records of $account_name Bank Account", "Delete Bank Account");
}

// 2. Delete Bank Account
// LIMIT 1 is used to delete only one record
$sql = "DELETE FROM accounts WHERE account_name = '$account_name' LIMIT 1;";
insert_query($sql, "Delete Bank Account: $account_name", "Delete Bank Account");

