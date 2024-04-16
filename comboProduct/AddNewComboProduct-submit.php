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
    echo $productQuery;
    if (mysqli_query($con, $productQuery)) {
        // Get the last inserted product ID
        $productId = mysqli_insert_id($con);

        // Insert raw item data into raw item table
        foreach ($rawData as $rawItem) {
            $itemName = $rawItem['itemName'];
            $itemQty = $rawItem['itemQty'];

            $rawItemQuery = "INSERT INTO makeProduct (item_name, qty, product_name) VALUES ('$itemName', '$itemQty', '$productName')";
            mysqli_query($con, $rawItemQuery);
        }

        echo "Data submitted successfully.";
    } else {
        echo "Error: " . $productQuery . "<br>" . mysqli_error($con);
    }

    // Close the database connection
    mysqli_close($con);
} else {
    echo "Invalid request method.";
}
 