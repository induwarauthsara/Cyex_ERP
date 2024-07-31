<?php
// 1. If want, Change Previous Records Bank Account Names
// 2. Update Bank Account Record
require_once '../../inc/config.php';

$change_previous_records = $_GET['change_previous_records'];
$account_name = $_GET['account_name'];
$amount = $_GET['amount'];
$previous_account_name = $_GET['previous_account_name'];

// 1. If want, Change Previous Records Bank Account Names
if ($change_previous_records == 'true') {
    $sql = "UPDATE bank_deposits SET bank_account = '$account_name' WHERE bank_account = '$previous_account_name';";
    insert_query($sql, "Change Previous Records Bank Account Names to $account_name", "Update Bank Account");
}

// 2. Update Bank Account Record
$sql = "UPDATE accounts SET account_name = '$account_name', amount = '$amount' WHERE account_name = '$previous_account_name';";
insert_query($sql, "Update $previous_account_name Bank Account Name to  Amount to $account_name and Rs.$amount", "Update Bank Account");