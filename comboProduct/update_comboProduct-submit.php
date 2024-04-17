<?php require_once '../inc/config.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the JSON data from the request body
    $json_data = file_get_contents("php://input");

    // Decode the JSON data into PHP associative array
    $data = json_decode($json_data, true);

    // Extract data from the array
    $productId = $data['productId'];
    $productName = $data['productName'];
    $productRate = $data['productRate'];
    $showInLandingPage = $data['showInLandingPage'];
    $deleteProduct = $data['deleteProduct'];
    $finalCost = $data['finalCost'];
    $profit = $data['profit'];
    $rawData = $data['rawData'];

    // Update product data in the database
    $productUpdateQuery = "UPDATE products SET product_name = '$productName', rate = '$productRate', show_in_landing_page = '$showInLandingPage', cost = '$finalCost', profit = '$profit' WHERE product_id = '$productId'";
    echo $productUpdateQuery;
    $productUpdateQueryDb = mysqli_query($con, $productUpdateQuery);

    // Delete item data from the database where product name is $productName
    if ($productUpdateQueryDb) {
        $deleteQuery = "DELETE FROM makeProduct WHERE product_name = '$productName'";
        echo $deleteQuery;
        $deleteQueryDB = mysqli_query($con, $deleteQuery);

        // =============== Delete Product from products table ===============
        if ($deleteProduct) {
            $deleteProductQuery = "DELETE FROM products WHERE product_id = '$productId'";
            echo $deleteProductQuery;
            $deleteProductQueryDB = mysqli_query($con, $deleteProductQuery);
            exit();
        }
    } else {
        echo "Error: " . $productUpdateQuery . "<br>" . mysqli_error($con);
    }

    // Insert product data into the database
    if ($productUpdateQueryDb && $deleteQueryDB && !$deleteProduct) {
        // Insert raw item data into the database
        foreach ($rawData as $rawItem) {
            $itemName = $rawItem['itemName'];
            $itemQty = $rawItem['itemQty'];

            $rawItemQuery = "INSERT INTO makeProduct (item_name, qty, product_name) VALUES ('$itemName', '$itemQty', '$productName')";
            echo $rawItemQuery;
            mysqli_query($con, $rawItemQuery);
        }

        // Send a success response
        echo "Data submitted successfully.";
    } else {
        // Send an error response
        echo "Error: " . $productUpdateQuery . "<br>" . mysqli_error($con);
    }
    // Send a success response
    echo "Data submitted successfully.!";
} else {
    // If the request method is not POST, send an error response
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method.";
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
        $sql = "UPDATE products SET stock_qty = $min_qty WHERE product_name = '$product_name'";

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
?>
<!-- <pre>
    <?php
    // echo $productName . "<br>";
    // echo $productRate . "<br>";
    // echo $showInLandingPage . "<br>";
    // echo $finalCost . "<br>";
    // echo $profit . "<br>";
    // print_r($rawData);
    ?>
</pre> -->