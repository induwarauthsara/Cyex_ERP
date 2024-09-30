<?php
require_once '../inc/config.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the JSON data sent from the client
    $jsonData = file_get_contents("php://input");

    // Decode the JSON data into an associative array
    $data = json_decode($jsonData, true);

    // Extract data from the array
    $productName = mysqli_real_escape_string($con, $data['productName']);
    $productRate = mysqli_real_escape_string($con, $data['productRate']);
    $category = mysqli_real_escape_string($con, $data['category']); // Get category
    $commission = mysqli_real_escape_string($con, $data['commission']); // Get commission
    $finalCost = mysqli_real_escape_string($con, $data['finalCost']);
    $profit = mysqli_real_escape_string($con, $data['profit']);
    $rawData = $data['rawData']; // Array containing raw item data

    // Insert product data into the product table
    $productQuery = "INSERT INTO repair_items (repair_name, selling_price, category_id, commission, cost, profit) 
                     VALUES ('$productName', '$productRate', '$category', '$commission', '$finalCost', '$profit')";
    $result = mysqli_query($con, $productQuery);

    if ($result) {
        // Get the last inserted product ID
        $productId = mysqli_insert_id($con);

        // Insert raw item data into raw item table
        foreach ($rawData as $rawItem) {
            $itemName = mysqli_real_escape_string($con, $rawItem['itemName']);
            $itemQty = mysqli_real_escape_string($con, $rawItem['itemQty']);

            $rawItemQuery = "INSERT INTO repair_stock_map (item_name, qty, repair_name) VALUES ('$itemName', '$itemQty', '$productName')";
            mysqli_query($con, $rawItemQuery); // Assuming you want to log or handle errors separately
        }

        echo "Data submitted successfully.";
    } else {
        echo "Error: " . mysqli_error($con);
    }

    // Close the database connection
    mysqli_close($con);
} else {
    echo "Invalid request method.";
}
