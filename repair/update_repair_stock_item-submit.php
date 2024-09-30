<?php
require_once '../inc/config.php';

// Update Raw Item details in the database
$raw_item_id = $_GET['itemId'];
$raw_item_name = $_GET['itemName'];
$raw_item_cost = $_GET['itemCost'];
$raw_item_availble_qty = $_GET['itemQty'];

$sql = "UPDATE repair_stock SET stock_item_name = '$raw_item_name', stock_cost = $raw_item_cost, stock_qty = $raw_item_availble_qty WHERE id = $raw_item_id";

if ($con->query($sql) === TRUE) {
    echo "Repair Stock Item Details updated successfully";
} else {
    echo "Error updating record: " . $con->error;
}

// Update the product rate in the database
// Find the $raw_item_name in the makeProduct table and get them all in an array

$related_repair_names = array();
$sql = "SELECT repair_name FROM repair_stock_map WHERE item_name = '$raw_item_name'";
$result = $con->query($sql);
while ($row = $result->fetch_assoc()) {
    $related_repair_names[] = $row['repair_name'];
}
// echo "<pre>";
// print_r($related_repair_names);
// echo "</pre>";

// echo "==================================================<pre>";

// Update the product cost and profit in the database
// Initialize array to store product total costs
$related_product_total_cost_and_profit = array();

// Query to get the total cost and profit of each product
$sql = "SELECT mp.repair_name, SUM(mp.qty * i.stock_cost) AS total_cost, repair.selling_price
        FROM repair_stock_map mp
        INNER JOIN repair_stock i ON mp.item_name = i.stock_item_name
        INNER JOIN repair_items repair ON mp.repair_name = repair.repair_name
        WHERE mp.repair_name IN ('" . implode("','", $related_repair_names) . "')
        GROUP BY mp.repair_name";

$result = $con->query($sql);

// Check if query was successful
if ($result) {
    // Fetch each row and store product total cost and profit in array
    while ($row = $result->fetch_assoc()) {
        $repair_name = $row['repair_name'];
        $total_cost = $row['total_cost'];
        $rate = $row['selling_price'];
        $profit = $rate - $total_cost;
        // Push the total cost and profit into the related_product_total_cost_and_profit array
        $related_product_total_cost_and_profit[$repair_name] = array(
            'total_cost' => $total_cost,
            'profit' => $profit
        );
    }

    // Update the product cost and profit in the database
    // Iterate through the $related_product_total_cost_and_profit array
    foreach ($related_product_total_cost_and_profit as $repair_name => $total_cost_profit) {
        // Prepare the SQL update query
        $total_cost = $total_cost_profit['total_cost'];
        $profit = $total_cost_profit['profit'];

        $sql = "UPDATE repair_items SET cost = $total_cost, profit = $profit WHERE repair_name = '$repair_name'";

        // Execute the SQL update query
        if (
            $con->query($sql) === TRUE
        ) {
            // echo "Record updated successfully for product: $repair_name <br>";
        } else {
            echo "Error updating record for product: $repair_name <br>";
        }
    }
} else {
    echo "Error: " . $con->error;
}

// Output the result
// echo "<pre>";
// print_r($related_product_total_cost_and_profit);
// echo "</pre>";
