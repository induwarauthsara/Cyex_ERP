<?php require_once '../inc/config.php';

// 1. Buy Raw Item
// 2. Fall Cash In Related Account when Item Buy
// 3. Add Transaction Log
// 4. Add to Purchase Table

echo "<pre>";
print_r($_GET);
echo "</pre>";

// Set Buy Item qty
$item_id = $_GET['itemId'] ?? '';
$item_name = $_GET['itemName'] ?? '';
$item_qty = $_GET['itemQty'] ?? '';
$singleUnitCost = $_GET['singleUnitCost'] ?? '';
$paymentAccount = $_GET['paymentAccount'] ?? '';
$supplier = $_GET['supplier'] ?? '';
$BillTotal = $_GET['BillTotal'] ?? '';

// packet purchase
$packetPurchaseCheckBox = $_GET['packetPurchaseCheckBox'] ?? ''; // checkbox
$purchasedPacketQty = $_GET['purchasedPacketQty'] ?? '';
$quantityPerPacket = $_GET['quantityPerPacket'] ?? '';
$packetPrice = $_GET['packetPrice'];

// Credit Payment
$creditPaymentCheckBox = $_GET['creditPaymentCheckBox'] ?? ''; // checkbox
$FirstPayment = $_GET['FirstPayment'] ?? '';
$BalancePaymentDate = $_GET['BalancePaymentDate'] ?? '';
$BalancePayment = $_GET['BalancePayment'] ?? '';

// Remove commas to make the numbers plain
$BillTotal = str_replace(',', '', $BillTotal);
$packetPrice = str_replace(',', '', $packetPrice);
$FirstPayment = str_replace(',', '', $FirstPayment);
$BalancePayment = str_replace(',', '', $BalancePayment);


// Buy Item
$sql = "UPDATE `items` SET `qty`= qty + {$item_qty}, `cost`={$singleUnitCost}, `supplier`='{$supplier}' WHERE id = {$item_id}";
echo $sql;
insert_query($sql, "Item Name : $item_name, Item ID : $item_id, Item Qty : $item_qty, Item Cost : $singleUnitCost", "Purchased Raw Item");

// Fall Cash In Hand Account when Item Buy
$buy_cost = $item_qty * $singleUnitCost;
$sql = "UPDATE `accounts` SET `amount`= amount - {$buy_cost} WHERE account_name = '{$paymentAccount}'";
insert_query($sql, "Get Item Name : $item_name for RS. $buy_cost", "Fall Cash-In-Hand Account for Raw Item Purchase");

// Add Transaction Log -> type, description, amount
$transaction_type = 'Raw Item Purchase';
transaction_log($transaction_type, $item_name, -$buy_cost);

// if this supplier is not exist in suppliers table, then add this supplier
$sql = "SELECT * FROM `suppliers` WHERE supplier_name = '{$supplier}'";
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) == 0) {
    $sql = "INSERT INTO `suppliers`(`supplier_name`) VALUES ('{$supplier}')";
    insert_query($sql, "Supplier Name : $supplier", "Add New Supplier");
}

// Add to Purchase Table
$bill_payment_type = $creditPaymentCheckBox == 'true' ? "credit" : "full";
// if this is full payment, then make $FirstPayment = $BillTotal
$FirstPayment = $creditPaymentCheckBox == 'false' ? $BillTotal : $FirstPayment;
$BalancePaymentDate = $creditPaymentCheckBox == 'false' ? date('Y-m-d') : $BalancePaymentDate;
// if this is full payment, then make payment_status = paid. Otherwise, due
$payment_status = $creditPaymentCheckBox == 'false' ? "paid" : "due";
$sql = "INSERT INTO `purchase`(`item_id`, `item_name`, `single_unit_cost`, `item_qty`, `payment_account`, `supplier`, `purchased_packet_qty`, `quantity_per_packet`, `packet_price`, `first_payment`, 
            `balance_payment_date`, `balance_payment`, `bill_total`, `bill_payment_type`, `packet_purchase`, `payment_status`) 
        VALUES ({$item_id}, '{$item_name}', '{$singleUnitCost}', '{$item_qty}', '{$paymentAccount}', '{$supplier}', '{$purchasedPacketQty}', '{$quantityPerPacket}', '{$packetPrice}', '{$FirstPayment}',
            '{$BalancePaymentDate}', '{$BalancePayment}', '{$BillTotal}', '{$bill_payment_type}', {$packetPurchaseCheckBox}, '{$payment_status}')";
echo $sql;
insert_query($sql, "Item Name : $item_name, Item ID : $item_id, Item Qty : $item_qty, Item Cost : $singleUnitCost", "Purchased Raw Item add to Purchase Table");

// If this is credit payment, Increase Supplier Credit Balance
if ($creditPaymentCheckBox == 'true') {
    $sql = "UPDATE `suppliers` SET `credit_balance`= credit_balance + '{$BalancePayment}' WHERE supplier_name = '{$supplier}'";
    echo "<br>" . $sql . "<br>";
    insert_query($sql, "Supplier : $supplier, Credit Balance : $BillTotal for $item_name", "Increase Supplier Credit Balance");
}