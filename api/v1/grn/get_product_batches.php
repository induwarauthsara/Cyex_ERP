<?php
/**
 * Get Product Batches API
 * 
 * Get all batches for a specific product
 * GET /api/v1/grn/get_product_batches.php
 * 
 * Admin only
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate and require admin role
$user = ApiAuth::requireAuth();
if ($user['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Validate product_id
    if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
        ApiResponse::error('Product ID is required', 400);
    }

    $product_id = (int)$_GET['product_id'];

    // Get all batches for this product
    $sql = "
        SELECT 
            pb.batch_id,
            pb.batch_number,
            pb.cost,
            pb.selling_price,
            pb.quantity,
            pb.expiry_date,
            pb.alert_quantity,
            pb.status,
            pb.discount_price,
            pb.created_at
        FROM product_batch pb
        WHERE pb.product_id = ? AND pb.status = 'active'
        ORDER BY pb.created_at DESC
    ";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $product_id);

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to fetch batches: ' . mysqli_error($con));
    }

    $result = mysqli_stmt_get_result($stmt);
    $batches = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $batches[] = [
            'batch_id' => (int)$row['batch_id'],
            'batch_number' => $row['batch_number'],
            'cost' => (float)$row['cost'],
            'selling_price' => (float)$row['selling_price'],
            'quantity' => (float)$row['quantity'],
            'expiry_date' => $row['expiry_date'],
            'alert_quantity' => (int)$row['alert_quantity'],
            'status' => $row['status'],
            'discount_price' => $row['discount_price'] ? (float)$row['discount_price'] : null,
            'created_at' => $row['created_at']
        ];
    }

    ApiResponse::success([
        'batches' => $batches,
        'total' => count($batches)
    ]);

} catch (Exception $e) {
    ApiResponse::error($e->getMessage(), 500);
}
