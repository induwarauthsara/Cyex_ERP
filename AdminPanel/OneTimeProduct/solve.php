<?php
// 1. Cost -=  Account (UPDATE accounts SET amount = amount - {$total_cost} WHERE account_name = {$account})
// 2. Update Invoice Table (UPDATE invoice SET cost = {$total_bill_cost}, profit = {$total_bill_profit} WHERE invoice_number = {$bill_no})
// 3. Update OneTimeProduct Table (UPDATE one_time_product SET status = 'Solved' WHERE id = {$id})

require_once '../../inc/config.php';


//Request : solve.php?id=${id}&supplier=${supplier}&cost=${total_cost}&profit=${profit}&tab=${activeTab}

$OneTimeProduct_id = $_GET['id'];
$supplier = $_GET['supplier'];
$cost = $_GET['cost'];
$profit = $_GET['profit'];
$tab = $_GET['tab'];
$account = $_GET['account'];
$invoiceID = $_GET['invoiceID'];

// 1. Cost -=  Account
$sql = "UPDATE accounts SET amount = amount - {$cost} WHERE account_name = '{$account}'";
$action = "Fall OneTimeProduct Cost from $account";
$msg = "Fall Rs.$cost in {$account} Account for One Time Product ID : {$OneTimeProduct_id}";
insert_query($sql, $msg, $action);
//Transaction Log
transaction_log($action, $msg, $cost);

// 2. Update Invoice Table
$sql = "UPDATE invoice SET cost = {$cost}, profit = {$profit} WHERE invoice_number = '{$invoiceID}'";
$action = "Update Invoice Cost and Profit";
$msg = "Update Invoice (ID:$invoiceID) for One Time Product ID : {$OneTimeProduct_id}";
insert_query($sql, $msg, $action);

// 3. Update OneTimeProduct Table
$sql = "UPDATE oneTimeProducts_sales SET cost='$cost', profit='$profit', status = 'cleared', supplier='$supplier', account_name='$account' WHERE oneTimeProduct_id = {$OneTimeProduct_id}";
$action = "Update OneTimeProduct Sale";
$msg = "Update OneTimeProduct (ID:$OneTimeProduct_id) for Invoice ID : {$invoiceID}, Cost : $cost, Profit : $profit, Supplier : $supplier";
insert_query($sql, $msg, $action);