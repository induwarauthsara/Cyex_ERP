<?php
require '../../inc/config.php';

$query = "SELECT product_id AS id, product_name AS name FROM products WHERE active_status = 1";
$products = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($con);
