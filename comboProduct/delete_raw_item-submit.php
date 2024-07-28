<?php require_once '../inc/config.php';

$itemId = $_GET['itemId'];
$itemName = $_GET['itemName'];

// Delete from makeProduct table
$sql = "DELETE FROM makeProduct WHERE item_name = '$itemName'";
insert_query($sql, "Delete Raw Items from makeProduct Table (Item Name: $itemName)", "Delete Raw Item");

// Delete From Item Table
$sql = "DELETE FROM items WHERE id = '$itemId'";
insert_query($sql, "Delete Item from items Table (Item Name: $itemName)", "Delete Raw Item");

// echo "==================================================<pre>";

// Update the product cost and profit in the database
// Initialize array to store product total costs
$related_product_total_cost_and_profit = array();

// Query to get the total cost and profit of each product
$sql = "SELECT mp.product_name, SUM(mp.qty * i.cost) AS total_cost, products.rate
        FROM makeProduct mp
        INNER JOIN items i ON mp.item_name = i.item_name
        INNER JOIN products ON mp.product_name = products.product_name
        GROUP BY mp.product_name";

$result = $con->query($sql);

// Check if query was successful
if ($result) {
    // Fetch each row and store product total cost and profit in array
    while ($row = $result->fetch_assoc()) {
        $product_name = $row['product_name'];
        $total_cost = $row['total_cost'];
        $rate = $row['rate'];
        $profit = $rate - $total_cost;
        // Push the total cost and profit into the related_product_total_cost_and_profit array
        $related_product_total_cost_and_profit[$product_name] = array(
            'total_cost' => $total_cost,
            'profit' => $profit
        );
    }

    // Update the product cost and profit in the database
    // Iterate through the $related_product_total_cost_and_profit array
    foreach ($related_product_total_cost_and_profit as $product_name => $total_cost_profit) {
        // Prepare the SQL update query
        $total_cost = $total_cost_profit['total_cost'];
        $profit = $total_cost_profit['profit'];

        $sql = "UPDATE products SET cost = $total_cost, profit = $profit WHERE product_name = '$product_name'";

        // Execute the SQL update query
        if (
            $con->query($sql) === TRUE
        ) {
            // echo "Record updated successfully for product: $product_name <br>";
        } else {
            echo "Error updating record for product: $product_name <br>";
        }
    }
} else {
    echo "Error: " . $con->error;
}

// Output the result
// echo "<pre>";
// print_r($related_product_total_cost_and_profit);
// echo "</pre>";


// echo "==================================================<pre>";

// Update stock quantity in the products table
// Initialize array to store the minimum available quantity for each product
$min_available_qty = array();

// Query to find the minimum available quantity for each product
$sql = "SELECT mp.product_name, MIN(i.qty) AS min_qty
        FROM makeProduct mp
        INNER JOIN items i ON mp.item_name = i.item_name
        GROUP BY mp.product_name";

$result = $con->query($sql);

// Check if query was successful
if ($result) {
    // Fetch each row and store the minimum available quantity in array
    while ($row = $result->fetch_assoc()) {
        $product_name = $row['product_name'];
        $min_qty = $row['min_qty'];
        $min_available_qty[$product_name] = $min_qty;
    }

    // Update the stock_qty column in the products table with the minimum available quantity
    foreach ($min_available_qty as $product_name => $min_qty) {
        // Check Has Stock ?
        if ($min_qty > 0) {
            $has_stock = 1;
        }else{
            $has_stock = 0;
        }
        $sql = "UPDATE products SET stock_qty = $min_qty, has_stock = $has_stock WHERE product_name = '$product_name'";

        // Execute the SQL update query
        if ($con->query($sql) === TRUE) {
            // echo "Record updated successfully for product: $product_name <br>";
            // echo "Stock quantity updated successfully for product: $product_name <br>";
        } else {
            echo "Error updating record for product: $product_name <br>";
        }
    }
} else {
    echo "Error: " . $con->error;
}
