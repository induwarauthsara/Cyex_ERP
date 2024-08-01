<?php
// 1. Check difference in amount and update relevant bank account
// 2. Update Bank Deposit Record
// 3. Fall Money in Cash-in-Hand Account

require_once '../../inc/config.php';

$bank_deposit_id = $_GET['bank_deposit_id'];
$bankAccountName = $_GET['bankAccountName'];
$amount = $_GET['amount'];
$deposit_date = $_GET['deposit_date'];
$deposit_time = $_GET['deposit_time'];
$fallMoneyInCashInHandCheckBox = $_GET['fallMoneyInCashInHand'];

// 1. Check difference in amount and update relevant bank account
$sql = "SELECT amount, bank_account FROM bank_deposits WHERE bank_deposit_id = '$bank_deposit_id';";
$bank_deposit = mysqli_fetch_assoc(mysqli_query($con, $sql));
$old_amount = $bank_deposit['amount'];
$old_bank_account = $bank_deposit['bank_account'];

if ($old_bank_account != $bankAccountName) {
    $sql = "UPDATE accounts SET amount = amount - $old_amount WHERE account_name = '$old_bank_account';";
    insert_query($sql, "Correct Previous Record : Fall Rs.$old_amount from $old_bank_account Bank Account", "Update Bank Deposit");
    $sql = "UPDATE accounts SET amount = amount + $amount WHERE account_name = '$bankAccountName';";
    insert_query($sql, "Add Rs.$amount to $bankAccountName Bank Account", "Update Bank Deposit");
}else{
    $sql = "UPDATE accounts SET amount = amount - $old_amount + $amount WHERE account_name = '$bankAccountName';";
    insert_query($sql, "Correct Previous Record : Fall Rs.$old_amount and Add Rs.$amount to $bankAccountName Bank Account", "Update Bank Deposit");
}

// 2. Update Bank Deposit Record
$sql = "UPDATE bank_deposits SET bank_account = '$bankAccountName', amount = '$amount', deposit_date = '$deposit_date', deposit_time = '$deposit_time' WHERE bank_deposit_id = '$bank_deposit_id';";
insert_query($sql, "$bankAccountName Bank Account - Rs.$amount at $deposit_date $deposit_time", "Update Bank Deposit");

// 3. Fall Money in Cash-in-Hand Account
if ($fallMoneyInCashInHandCheckBox == 'true') {
    if ($old_amount != $amount) {
        $sql = "UPDATE accounts SET amount = amount + $old_amount - $amount WHERE account_name = 'cash_in_hand';";
        $change = $old_amount - $amount;
        insert_query($sql, "Add/Fall Rs.$change in Cash in Hand Account", "Update Bank Deposit");
        if ($result) {
            // Add Transaction Log -> type, description, amount
            $transaction_type = 'Update Bank Deposit';
            $description = "Add/Fall Rs.$change in Cash in Hand Account for Update Bank Deposit";
            transaction_log($transaction_type, $description, $change);
        }
    }
}
