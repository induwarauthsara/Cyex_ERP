<?php require_once '../inc/config.php';

// Set Buy Item qty
$item_id = $_GET['itemId'];
$item_name = $_GET['itemName'];
$item_qty = $_GET['itemQty'];
$item_cost = $_GET['itemCost'];

// Buy Item
$sql = "UPDATE `items` SET `qty`= qty + {$item_qty}, `cost`={$item_cost} WHERE id = {$item_id}";
echo $sql;
insert_query($sql, "Successfully Purchased {$item_name} Item!");

// Fall Cash In Hand Account when Item Buy
$buy_cost = $item_qty * $item_cost;
$account_name = "cash_in_hand";
$sql = "UPDATE `accounts` SET `amount`= amount - {$buy_cost} WHERE account_name = '{$account_name}'";
insert_query($sql, "");
