<?php
/**
 * Products API for AdminPanel
 * Simple endpoint to provide product list for dropdowns
 */

require_once(__DIR__ . '/../../inc/config.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use GET'
    ]);
    exit;
}

try {
    // Get products from the database
    $query = "SELECT p.product_id, p.product_name, p.del_rate as selling_price, 
              pb.quantity, pb.cost 
              FROM products p 
              LEFT JOIN product_batch pb ON p.product_id = pb.product_id 
              WHERE p.product_name != '' AND p.product_name IS NOT NULL 
              AND p.active_status = 1
              ORDER BY p.product_name ASC";
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($con));
    }
    
    $products = [];
    $productIds = []; // To track unique products
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Only add each product once (in case of multiple batches)
        if (!in_array($row['product_id'], $productIds)) {
            $products[] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'price' => floatval($row['selling_price'] ?? 0),
                'selling_price' => floatval($row['selling_price'] ?? 0),
                'cost_price' => floatval($row['cost'] ?? 0),
                'quantity' => floatval($row['quantity'] ?? 0)
            ];
            $productIds[] = $row['product_id'];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $products,
        'count' => count($products)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
