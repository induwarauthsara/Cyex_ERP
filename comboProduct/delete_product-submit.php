<?php require_once '../inc/config.php';


// Database blueprint
// items (`id`, `item_name`, `description`, `cost`, `qty`, `supplier`)
// products (`product_id`, `product_name`, `description`, `stock_qty`, `rate`, `cost`, `profit`, `has_stock`, `image`, `show_in_landing_page`)
// makeProduct (`id`, `item_name`, `product_name`, `qty`)

$product_id = $_GET['productId'];
$product_name = $_GET['productName'];

// Delete from makeProduct table
$sql = "DELETE FROM makeProduct WHERE product_name = '$product_name'";
insert_query($sql, "Delete from makeProduct table where product_name = $product_name", "Delete product from makeProduct table");
if ($result) {
    // echo "Record deleted successfully";
    $deleted = true;
} else {
    echo "Error deleting record in MakeProduct Table : " . mysqli_error($con) . "<br>";
}

// Delete From products Table
    $sql = "DELETE FROM products WHERE product_id = '$product_id'";
    insert_query($sql, "Delete product : $product_name", "Delete product");
if(!$result){
    echo "Error deleting record in Product Table: " . mysqli_error($con) . "<br>";
}