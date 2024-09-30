<?php require_once '../inc/config.php';

$itemId = $_GET['itemId'];
$itemName = $_GET['itemName'];

// Delete from repair_stock_map table
$sql = "DELETE FROM repair_stock_map WHERE item_name = '$itemName'";
insert_query($sql, "Delete Repair Stock Items from repair_stock_map Table (Item Name: $itemName)", "Delete Raw Item");

// Delete From repair_stock Table
$sql = "DELETE FROM repair_stock WHERE id = '$itemId'";
insert_query($sql, "Delete Item from repair_stock Table (Item Name: $itemName)", "Delete Raw Item");

// echo "==================================================<pre>";

// Update the product cost and profit in the database
// Initialize array to store product total costs
$related_product_total_cost_and_profit = array();

// Query to get the total cost and profit of each product
$sql = "SELECT mp.repair_name, SUM(mp.qty * i.stock_cost) AS total_cost, repair.selling_price
        FROM repair_stock_map mp
        INNER JOIN repair_stock i ON mp.item_name = i.stock_item_name
        INNER JOIN repair_items repair ON mp.repair_name = repair.repair_name
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
