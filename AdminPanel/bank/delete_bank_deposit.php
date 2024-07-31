<?php
// 1. Adjust the amount in the bank account
// 2. Delete Bank Deposit Record

require_once '../../inc/config.php';
$bank_deposit_id = $_GET['bank_deposit_id'];
$bankAccountName = $_GET['bankAccountName'];
$amount = $_GET['amount'];

// 1. Adjust the amount in the bank account
$sql = "UPDATE accounts SET amount = amount - $amount WHERE account_name = '$bankAccountName';";
insert_query($sql, "Fall Rs.$amount from $bankAccountName Bank Account", "Delete Bank Deposit");

// 2. Delete Bank Deposit Record
$sql = "DELETE FROM bank_deposits WHERE bank_deposit_id = '$bank_deposit_id';";
insert_query($sql, "Bank Deposit Record Deleted. id: $bank_deposit_id, $bankAccountName Account, Rs.$amount", "Delete Bank Deposit");