<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    if (!isset($_GET['product_id'])) {
        throw new Exception('Product ID is required');
    }

    $product_id = (int)$_GET['product_id'];

    // Fetch product details
    $product_query = "
        SELECT 
            p.product_id,
            p.product_name,
            p.product_code,
            p.cost,
            p.selling_price
        FROM products p
        WHERE p.product_id = ?
    ";

    $stmt = mysqli_prepare($con, $product_query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    if (!$product) {
        throw new Exception('Product not found');
    }

    // Fetch active batches for this product
    $batch_query = "
        SELECT 
            pb.batch_id,
            pb.batch_name,
            pb.quantity,
            pb.cost,
            pb.selling_price,
            pb.expiry_date,
            pb.status,
            pb.restocked_at
        FROM product_batch pb
        WHERE pb.product_id = ? AND pb.status = 'active'
        ORDER BY pb.quantity DESC, pb.restocked_at DESC
    ";

    $stmt = mysqli_prepare($con, $batch_query);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $batches = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $batches[] = $row;
    }

    // Return product and batch details
    echo json_encode([
        'success' => true,
        'product' => $product,
        'batches' => $batches
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 