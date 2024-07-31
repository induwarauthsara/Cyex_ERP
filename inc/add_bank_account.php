<?php
// 1. Add Bank Account to Accounts Table

require_once 'config.php';

$bankAccount = $_GET['bankAccount'];
$amount = $_GET['amount'];

// 1. Add Bank Account to Accounts Table
$sql = "INSERT INTO accounts (account_name, amount, account_type) VALUES ('$bankAccount', '$amount', 'bank');";
insert_query($sql, "Create New Bank Account : $bankAccount", "Create Bank Account");