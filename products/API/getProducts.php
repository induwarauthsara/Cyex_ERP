<?php
require '../../inc/config.php';

$query = "SELECT product_id AS id, product_name AS name FROM products";
$products = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($con);
