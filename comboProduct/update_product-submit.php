<?php
require_once '../inc/config.php';

// Fetch product details
$product_id = $_GET['productId'];
$productRate = $_GET['productRate'];
$productProfit = $_GET['productProfit'];
$productName = $_GET['productName'];

// echo $product_id;
// echo "<br>";
// echo $productRate;
// echo "<br>";
// echo $productProfit;
// echo "<br>";
// echo $productName;
// echo "<br>";

// Update the product rate and Prodcut Name in the database
$sql = "UPDATE products SET rate = $productRate, profit = $productProfit, product_name = '$productName' WHERE product_id = $product_id";
if ($con->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $con->error;
}

// Update the product rate in the database
// Add your database update logic here
