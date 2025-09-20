<?php
require '../../inc/config.php';

// Build the base query
$query = "SELECT product_id AS id, product_name AS name, product_type FROM products WHERE active_status = 1";

// Add search filter if provided
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($con, $_GET['search']);
    $query .= " AND product_name LIKE '%{$search}%'";
}

// Add product type filter if provided
if (isset($_GET['product_type']) && !empty($_GET['product_type'])) {
    $product_type = mysqli_real_escape_string($con, $_GET['product_type']);
    if ($product_type === 'standard') {
        // Include products with product_type = 'standard' or NULL/empty (as they are considered standard)
        $query .= " AND (product_type = 'standard' OR product_type IS NULL OR product_type = '')";
    } else {
        $query .= " AND product_type = '{$product_type}'";
    }
}

$query .= " ORDER BY product_name ASC";

$products = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($con);
