<?php
require_once '../../inc/config.php';

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

$productId = (int)$_GET['product_id'];

try {
    // Get product details
    $sql = "SELECT p.*, b.brand_name as brand_name, c.category_name as category_name 
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.brand_id 
            LEFT JOIN categories c ON p.category_id = c.category_id 
            WHERE p.product_id = ?";
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Product not found");
    }
    
    $product = $result->fetch_assoc();
    
    // Get product batches
    $sql = "SELECT * FROM product_batch WHERE product_id = ? ORDER BY created_at DESC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $batches = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $product['batches'] = $batches;
    
    // Get combo products if it's a combo product
    if ($product['product_type'] === 'combo') {
        $sql = "SELECT cp.*, p.product_name, pb.batch_number, pb.cost, pb.selling_price 
                FROM combo_products cp 
                JOIN products p ON cp.component_product_id = p.product_id 
                JOIN product_batch pb ON cp.batch_number = pb.batch_number 
                WHERE cp.combo_product_id = ?";
        
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $product['combo_components'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $product
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

if (isset($con)) {
    $con->close();
} 