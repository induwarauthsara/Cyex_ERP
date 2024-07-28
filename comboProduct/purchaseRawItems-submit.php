<?php require_once '../inc/config.php';

// Set Buy Item qty
$item_id = $_GET['itemId'];
$item_name = $_GET['itemName'];
$item_qty = $_GET['itemQty'];
$item_cost = $_GET['itemCost'];

// Buy Item
$sql = "UPDATE `items` SET `qty`= qty + {$item_qty}, `cost`={$item_cost} WHERE id = {$item_id}";
echo $sql;
insert_query($sql, "Item Name : $item_name, Item ID : $item_id, Item Qty : $item_qty, Item Cost : $item_cost", "Purchased Raw Item");

// Fall Cash In Hand Account when Item Buy
$buy_cost = $item_qty * $item_cost;
$account_name = "cash_in_hand";
$sql = "UPDATE `accounts` SET `amount`= amount - {$buy_cost} WHERE account_name = '{$account_name}'";
insert_query($sql, "Get Item Name : $item_name for RS. $buy_cost", "Fall Cash-In-Hand Account for Raw Item Purchase");

// Add Transaction Log -> type, description, amount
$transaction_type = 'Raw Item Purchase';
transaction_log($transaction_type, $item_name, -$buy_cost);
