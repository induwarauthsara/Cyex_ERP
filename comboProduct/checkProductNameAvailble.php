<?php
require_once '../inc/config.php';

if (isset($_POST['productName'])) {
    $productName = $_POST['productName'];

    // Query to check if the product exists
    $query = "SELECT * FROM products WHERE product_name = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'exists'; // Product exists
    } else {
        echo 'not_exists'; // Product doesn't exist
    }
}
