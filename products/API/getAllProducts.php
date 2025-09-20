<?php
require '../../inc/config.php';

$query = "SELECT product_id AS id, product_name AS name, active_status FROM products ORDER BY product_name ASC";
$products = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($con);
?>