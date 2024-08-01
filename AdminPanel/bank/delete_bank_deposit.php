<?php
// 1. Adjust the amount in the bank account
// 2. Delete Bank Deposit Record
// 3. If want, Fall Money from Cash-in-Hand Account

require_once '../../inc/config.php';
$bank_deposit_id = $_GET['bank_deposit_id'];
$bankAccountName = $_GET['bankAccountName'];
$amount = $_GET['amount'];
$fallMoneyInCashInHand = $_GET['fallMoneyInCashInHand'];


// 1. Adjust the amount in the bank account
$sql = "UPDATE accounts SET amount = amount - $amount WHERE account_name = '$bankAccountName';";
insert_query($sql, "Fall Rs.$amount from $bankAccountName Bank Account", "Delete Bank Deposit");

// 2. Delete Bank Deposit Record
$sql = "DELETE FROM bank_deposits WHERE bank_deposit_id = '$bank_deposit_id';";
insert_query($sql, "Bank Deposit Record Deleted. id: $bank_deposit_id, $bankAccountName Account, Rs.$amount", "Delete Bank Deposit");

// 3. If want, Fall Money from Cash-in-Hand Account
if ($fallMoneyInCashInHand == 'true') {
    $sql = "UPDATE accounts SET amount = amount - $amount WHERE account_name = 'cash_in_hand';";
    insert_query($sql, "Fall Rs.$amount from Cash-in-Hand Account for $account_name Bank Account", "Delete Bank Account");

    // Add Transaction Log -> type, description, amount
    $transaction_type = 'Delete Bank Deposit';
    $description = "Fall Rs.$amount in Cash in Hand Account for Delete Bank Deposit";
    transaction_log($transaction_type, $description, $change);
}
