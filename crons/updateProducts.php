<?php require_once '../inc/config.php'; ?>
<a href="javascript:history.back()"><button style="margin:15px;">Go Back</button></a>
<?php

// Item Cost walata anuwa Product Cost eka update Wenawa
// Product Rate eka update wenawa
// Product Stock Qty eka update wenawa
// Product Profit eka update wenawa
// Min Qty eka update wenawa

// Update the product cost and profit in the database
$sql1 = "UPDATE products p
         INNER JOIN (
             SELECT mp.product_name, SUM(mp.qty * i.cost) AS total_cost, p.rate
             FROM makeProduct mp
             INNER JOIN items i ON mp.item_name = i.item_name
             INNER JOIN products p ON mp.product_name = p.product_name
             GROUP BY mp.product_name
         ) AS temp ON p.product_name = temp.product_name
         SET p.cost = temp.total_cost, p.profit = temp.rate - temp.total_cost";

// Update stock quantity in the products table
$sql2 = "UPDATE products p
         INNER JOIN (
             SELECT mp.product_name, MIN(i.qty) AS min_qty
             FROM makeProduct mp
             INNER JOIN items i ON mp.item_name = i.item_name
             GROUP BY mp.product_name
         ) AS temp ON p.product_name = temp.product_name
         SET p.stock_qty = temp.min_qty, p.has_stock = CASE WHEN temp.min_qty > 0 THEN 1 ELSE 0 END";

// Execute the SQL queries
$result1 = $con->query($sql1);
$result2 = $con->query($sql2);

// Check for errors
if ($result1 && $result2) {
    echo "Records updated successfully.";
} else {
    echo "Error: " . $con->error;
}
?>
