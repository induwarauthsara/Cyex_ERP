<?php
require '../../inc/config.php';

// Get products with their cost information for combo calculations
$query = "SELECT 
    p.product_id AS id, 
    p.product_name AS name, 
    p.active_status,
    COALESCE(AVG(b.cost), 0) AS avg_cost
FROM products p
LEFT JOIN product_batch b ON p.product_id = b.product_id AND b.status = 'active'
WHERE p.product_type != 'combo'
GROUP BY p.product_id, p.product_name, p.active_status
ORDER BY p.product_name ASC";

$products = fetch_data($query);

header('Content-Type: application/json');
echo json_encode($products);

mysqli_close($con);
?>