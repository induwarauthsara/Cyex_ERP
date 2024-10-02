<?php require_once '../inc/config.php';


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Extract data from the array
    $productName = $_POST['productName'];
    $productRate = $_POST['productRate'];
    $stock_qty = $_POST['stockAvailable'];
    $productCost = $_POST['cost'];
    $lowStockAlertLimit = $_POST['lowStockAlertLimit'];
    $workerCommission = $_POST['Commission'];
    
    $finalCost = $productCost + $workerCommission;

    // Calculate profit
    $profit = $productRate - $finalCost;

    // has_stock = 1 means the product is in stock
    if ($stock_qty > 0) {
        $has_stock = 1;
    } else {
        $has_stock = 0;
    }

    echo "$productName <br>";
    echo "$productRate <br>";
    // echo "$image <br>";
    echo "$finalCost <br>";
    echo "$profit <br>";


    echo "-------------------------------------";


    // Insert product data into product table
    $productQuery = "INSERT IGNORE INTO products (product_name, rate, cost, profit, stock_qty, has_stock, stock_alert_limit, worker_commision) 
                     VALUES ('$productName', '$productRate', '$productCost', '$profit','$stock_qty', '$has_stock', '$lowStockAlertLimit', '$workerCommission')";
    // echo $productQuery;
    insert_query($productQuery, "Add New Combo Product : $productName", "Add Combo Product");

    // Close the database connection
    mysqli_close($con);

    // Redirect to the /comboProduct/productsList.php page
    header("Location: productsList.php");
} else {
    echo "Invalid request method.";
}
 