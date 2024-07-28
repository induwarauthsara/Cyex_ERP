<?php
// Include your database connection file or function here
require_once 'config.php';
session_start();

// Check if the required parameters are received and validate them
if (isset($_GET['itemName']) && isset($_GET['itemPrice']) && isset($_GET['itemQty'])) {
    $itemName = $_GET['itemName'];

    // Validate cost (double)
    $itemPrice = $_GET['itemPrice'];
    if (!is_numeric($itemPrice) || !filter_var($itemPrice, FILTER_VALIDATE_FLOAT) || !preg_match('/^\d{0,10}(\.\d{1,2})?$/', $itemPrice)) {
        echo "Invalid cost format.";
        exit;
    }

    // Validate qty (int)
    $itemQty = $_GET['itemQty'];
    if (!is_numeric($itemQty) || !filter_var($itemQty, FILTER_VALIDATE_INT) || $itemQty < 0 || $itemQty > 99999) {
        echo "Invalid quantity format.";
        exit;
    }

    // Perform any additional validation or processing if needed

    // Insert the new raw item into the database
    $sql = "INSERT INTO items (item_name, cost, qty) VALUES ('$itemName', $itemPrice, $itemQty)";
    insert_query($sql, "Name : $itemName, Price : $itemPrice, Qty : $itemQty", "Add Raw Item");

    // fall cash in hand account when item buy
    $buy_cost = $itemQty * $itemPrice;
    $account_name = "cash_in_hand";
    $sql = "UPDATE `accounts` SET `amount`= amount - {$buy_cost} WHERE account_name = '{$account_name}'";
    insert_query($sql,"Name : $itemName, Price : $itemPrice, Qty : $itemQty", "Fall Cash-In-Hand Account for Raw Item Purchase");

    // Add Transaction Log -> type, description, amount
    $transaction_type = 'Raw Item Purchase';
    $description = "$itemName";
    transaction_log($transaction_type, $description, -$buy_cost);
} else {
    // Return an error message if required parameters are missing
    echo "Missing required parameters.";
}


end_db_con();
