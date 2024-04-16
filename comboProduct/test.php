<?php
require_once '../inc/config.php';
require_once '../inc/header.php';
include '../inc/DataTable_cdn.php';
?>

<?php
require_once '../inc/config.php';

// Update Raw Item details in the database
// $raw_item_id = $_GET['itemId'];
// $raw_item_name = $_GET['itemName'];
// $raw_item_cost = $_GET['itemCost'];
// $raw_item_availble_qty = $_GET['itemQty'];


// Update the product rate in the database
// Find the $raw_item_name in the makeProduct table and get them all in an array

$related_product_names = array();
$sql = "SELECT product_name FROM makeProduct ";
$result = $con->query($sql);
while ($row = $result->fetch_assoc()) {
    $related_product_names[] = $row['product_name'];
}
// echo "<pre>";
// print_r($related_product_names);
// echo "</pre>";

// echo "==================================================<pre>";

// Update the product rate in the database
// Initialize array to store product total costs
$related_product_total_cost = array();

// Query to get the total cost and rate of each product
$sql = "SELECT mp.product_name, SUM(mp.qty * i.cost) AS total_cost, products.rate
        FROM makeProduct mp
        INNER JOIN items i ON mp.item_name = i.item_name
        INNER JOIN products ON mp.product_name = products.product_name
        -- WHERE mp.product_name IN ('" . implode("','", $related_product_names) . "')
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
        // Push the total cost and profit into the related_product_total_cost array
        $related_product_total_cost[$product_name] = array(
            'total_cost' => $total_cost,
            'profit' => $profit
        );
    }

    // Update the product cost and profit in the database
    // Iterate through the $related_product_total_cost array
    foreach ($related_product_total_cost as $product_name => $total_cost_profit) {
        // Prepare the SQL update query
        $total_cost = $total_cost_profit['total_cost'];
        $profit = $total_cost_profit['profit'];

        $sql = "UPDATE products SET cost = $total_cost, profit = $profit WHERE product_name = '$product_name'";
        echo "$sql ; <br>";

        // Execute the SQL update query
        // if (
        //     $con->query($sql) === TRUE
        // ) {
        //     // echo "Record updated successfully for product: $product_name <br>";
        // } else {
        //     echo "Error updating record for product: $product_name <br>";
        // }
    }
} else {
    echo "Error: " . $con->error;
}

echo "<pre>";
print_r($related_product_total_cost);
echo "</pre>";
