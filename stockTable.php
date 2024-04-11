<?php

require_once 'inc/config.php';

// Fetch products and related items
// $sql = "SELECT * FROM `makeProduct`, products WHERE makeProduct.product_name = products.product_name ORDER BY products.`product_id` ASC;";
$sql = "SELECT * FROM `makeProduct`, products, items WHERE makeProduct.product_name = products.product_name AND items.item_name = makeProduct.item_name ORDER BY products.`product_id` ASC;";
$result = $con->query($sql);

// Initialize variables for storing current product name
$currentProduct = null;

// Start building HTML table
echo "<table border='1'>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Product Price</th>
            <th>Item Name</th>
            
            <th>Item COST</th>
        </tr>";
while ($row = $result->fetch_assoc()) {
    $product_id = $row["product_id"];
    $productName = $row["product_name"];
    $itemName = $row["item_name"];
    $comboQTY = $row["qty"];
    $price = $row["rate"];
    $itemPrice = $row["cost"];

    // If the product name changes, start a new row with the product name
    if ($productName != $currentProduct) {
        echo "<tr>
            <td>$product_id</td>
            <td>$productName</td>
            <td>$price</td>
            <td>$itemName</td>
            
            <td>$itemPrice</td>
        </tr>";
        $currentProduct = $productName;
    } else {
        // If the product name is the same, continue adding items to the same row
        echo "<tr>
                <td></td>
                <td></td>
                <td></td>
                <td>$itemName</td>
                
                <td>$itemPrice</td>
            </tr>";
    }
}
echo "</table>";

// Close conection
$con->close();
