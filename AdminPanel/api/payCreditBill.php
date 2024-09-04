<?php

// Pay Credit Bill 
//      Update Purchase Status to Paid
//      Update Supplier Balance
//      Add Transaction Log
//      Update Related Account
//      Fall Profit Account, 

require_once '../../inc/config.php';

$purchase_id = $_GET['purchase_id'];
$payAmount = $_GET['payAmount'];

// Check Credit Bill fully paid or not
$sql = "SELECT balance_payment FROM purchase WHERE purchase_id = $purchase_id;";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$balance_payment = $row['balance_payment'];
// If balance_payment is 0, then the bill is already paid
$new_credit_bill_balance = $balance_payment - $payAmount;
if ($balance_payment == 0) {
    $response = [
        'status' => 'error',
        'message' => 'This bill is already paid.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}else if($new_credit_bill_balance < 0){
    $response = [
        'status' => 'error',
        'message' => 'You are trying to pay more than the balance amount.'
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Update Purchase Status to Paid
if ($new_credit_bill_balance == 0) {
    // Bill Paid Fully
    // Update Purchase Status to Paid
    $sql = "UPDATE purchase SET balance_payment = 0, balance_payment_date = NULL, payment_status = 'paid' WHERE purchase_id = $purchase_id;";
}else if ($new_credit_bill_balance > 0){
    // Bill Partially Paid
    // Update Purchase Status to Partially Paid
    $sql = "UPDATE purchase SET balance_payment = $new_credit_bill_balance WHERE purchase_id = $purchase_id;";
}
// insert_query($query, $msg, $action)
insert_query($sql, "Pay Rs.$payAmount to PurchesID $purchase_id. Balance Payment : $balance_payment", "Pay Credit Bill - Update Purchase Table");


// Update Supplier Balance
$sql = "UPDATE suppliers SET credit_balance = credit_balance - $payAmount WHERE supplier_name = (SELECT supplier FROM purchase WHERE purchase_id = $purchase_id);";
insert_query($sql, "Pay Rs.$payAmount to PurchesID $purchase_id. Balance Payment : $balance_payment", "Pay Credit Bill - Update Supplier Balance");

// Add Transaction Log
transaction_log("Credit Bill Payment", "Pay Rs.$payAmount to PurchesID $purchase_id. Balance Payment : $balance_payment", $payAmount);

// Update Related Account
