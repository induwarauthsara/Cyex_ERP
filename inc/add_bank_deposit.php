<?php
// 1. Fall Money in Cash-in-Hand Account
// 2. Add Record to Transaction Log
// 3. Add Money to Relevant Bank Account
// 4. Add Record to Bank Deposit Log

require_once 'config.php';

$bankAccount = $_GET['bankAccount'];
$amount = $_GET['amount'];

// 1. Fall Money in Cash-in-Hand Account
$fall_money_query = "UPDATE `accounts` SET `amount` = `amount` - $amount  WHERE `account_name` = 'cash_in_hand'";
insert_query($fall_money_query, "Fall Rs.$amount in Cash-in-Hand Account for $bankAccount Bank Deposit", "Bank Deposit");

// 2. Add Record to Transaction Log
// Add Transaction Log -> type, description, amount
$transaction_type = 'Bank Deposit';
$transaction_description = "Add Rs.$amount to $bankAccount Bank Account";
transaction_log($transaction_type, $transaction_description, $amount);

// 3. Add Money to Relevant Bank Account
$add_money_query = "UPDATE `accounts` SET `amount` = `amount` + $amount  WHERE `account_name` = '$bankAccount'";
insert_query($add_money_query, "Add Rs.$amount to $bankAccount Bank Account", "Bank Deposit");

// 4. Add Record to Bank Deposit Log
// Start Session if not started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$employee_id = $_SESSION['employee_id'] ?? 0;
$sql = "INSERT INTO bank_deposits (bank_account, amount, employee_id) VALUES ('$bankAccount', '$amount', '$employee_id');";
insert_query($sql, "Add Rs.$amount to $bankAccount Bank Account", "Bank Deposit");

