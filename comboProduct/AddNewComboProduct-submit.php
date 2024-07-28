<?php require_once '../inc/config.php';


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the JSON data sent from the client
    $jsonData = file_get_contents("php://input");

    // Decode the JSON data into an associative array
    $data = json_decode($jsonData, true);

    // Extract data from the array
    $productName = $data['productName'];
    $productRate = $data['productRate'];
    // $image = $data['image'];
    $showInLandingPage = $data['showInLandingPage'];
    $finalCost = $data['finalCost'];
    $profit = $data['profit'];
    $rawData = $data['rawData']; // Array containing raw item data

    echo "$productName <br>";
    echo "$productRate <br>";
    // echo "$image <br>";
    echo "$showInLandingPage <br>";
    echo "$finalCost <br>";
    echo "$profit <br>";
    print_r($rawData);


    echo "-------------------------------------";


    // Insert product data into product table
    $productQuery = "INSERT INTO products (product_name, rate, cost, profit, show_in_landing_page) 
                     VALUES ('$productName', '$productRate', '$finalCost', '$profit','$showInLandingPage')";
    // echo $productQuery;
    insert_query($productQuery, "Add New Combo Product : $productName", "Add Combo Product");

    if ($result) {
        // Get the last inserted product ID
        $productId = mysqli_insert_id($con);

        // Insert raw item data into raw item table
        foreach ($rawData as $rawItem) {
            $itemName = $rawItem['itemName'];
            $itemQty = $rawItem['itemQty'];

            $rawItemQuery = "INSERT INTO makeProduct (item_name, qty, product_name) VALUES ('$itemName', '$itemQty', '$productName')";
            insert_query($rawItemQuery, "Add Combo Product items : '$itemName' to  '$productName' Product", "Add Combo Product Items");
        }

        echo "Data submitted successfully.";
    } else {
        echo "Error: " . $productQuery . "<br>" . mysqli_error($con);
    }

    // echo "==================================================<pre>";

    // Update stock quantity in the products table
    // Initialize array to store the minimum available quantity for each product
    $min_available_qty = array();

    // Query to find the minimum available quantity for each product
    $sql = "SELECT mp.product_name, MIN(i.qty) AS min_qty
        FROM makeProduct mp
        INNER JOIN items i ON mp.item_name = i.item_name
        WHERE mp.product_name = '$productName'
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
            if ($min_qty > 0
            ) {
                $has_stock = 1;
            } else {
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

    // Close the database connection
    mysqli_close($con);
} else {
    echo "Invalid request method.";
}
 