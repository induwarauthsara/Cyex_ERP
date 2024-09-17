<?php
require_once '../inc/config.php';

// Fetch product details from the GET request
$product_id = $_GET['productId'];
$productName = $_GET['productName'];
$productRate = $_GET['productRate'];
$productCost = $_GET['cost']; // Capturing cost from the request
$productProfit = $_GET['productProfit']; // Capturing the calculated profit from the request
$workerCommission = $_GET['commission']; // Capturing worker commission from the request
$stock_qty = $_GET['stockAvailable']; // Stock Available Count from the request
$lowStockAlertLimit = $_GET['lowStockAlertLimit']; // Low Stock Alert Limit from the request

// Determine if the product is in stock
$has_stock = $stock_qty > 0 ? 1 : 0;

// Create the SQL query to update the product details in the database
$sql = "UPDATE products 
        SET 
            product_name = '$productName', 
            rate = $productRate, 
            cost = $productCost, 
            profit = $productProfit, 
            stock_qty = $stock_qty, 
            has_stock = $has_stock, 
            stock_alert_limit = $lowStockAlertLimit, 
            worker_commision = $workerCommission 
        WHERE product_id = $product_id";

insert_query($sql, "Edit Updated : $productName", "Edit Product");

// Close the database connection
$con->close();
