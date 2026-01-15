<?php
/**
 * Product Details API Endpoint
 * Get detailed information about a specific product
 * 
 * @route GET /api/v1/products/details.php?id=123
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed. Use GET', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get product ID
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId <= 0) {
    ApiResponse::validationError(['id' => 'Valid product ID is required']);
}

// Get product details
$query = "SELECT 
    p.product_id,
    p.product_name,
    p.sku,
    p.barcode,
    p.has_stock,
    p.active_status,
    p.image,
    p.category_id,
    p.brand_id,
    p.description,
    p.stock_alert_limit,
    p.created_at,
    p.updated_at,
    p.employee_commission_percentage,
    COALESCE(pb.selling_price, 0) as price,
    COALESCE(pb.cost, 0) as cost_price,
    COALESCE(pb.profit, 0) as profit_margin,
    COALESCE(SUM(pb.quantity), 0) as available_quantity,
    COALESCE(pb.alert_quantity, 0) as alert_quantity
FROM products p
LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
WHERE p.product_id = ?
GROUP BY p.product_id, p.product_name, p.sku, p.barcode, p.has_stock, p.active_status, p.image, 
         p.category_id, p.brand_id, p.description, p.stock_alert_limit, p.created_at, p.updated_at, p.employee_commission_percentage,
         pb.selling_price, pb.cost, pb.profit, pb.alert_quantity";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'i', $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    ApiResponse::notFound('Product not found');
}

$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get batch information
$batchQuery = "SELECT 
    batch_id,
    batch_number,
    quantity,
    cost,
    selling_price,
    profit,
    alert_quantity,
    status,
    expiry_date,
    created_at,
    updated_at
FROM product_batch
WHERE product_id = ? AND status = 'active'
ORDER BY created_at DESC";

$batchStmt = mysqli_prepare($con, $batchQuery);
mysqli_stmt_bind_param($batchStmt, 'i', $productId);
mysqli_stmt_execute($batchStmt);
$batchResult = mysqli_stmt_get_result($batchStmt);

$batches = [];
while ($batch = mysqli_fetch_assoc($batchResult)) {
    $batches[] = [
        'batch_id' => intval($batch['batch_id']),
        'batch_number' => $batch['batch_number'],
        'quantity' => floatval($batch['quantity']),
        'cost' => floatval($batch['cost']),
        'selling_price' => floatval($batch['selling_price']),
        'profit' => floatval($batch['profit']),
        'alert_quantity' => floatval($batch['alert_quantity']),
        'status' => $batch['status'],
        'expiry_date' => $batch['expiry_date'],
        'created_at' => $batch['created_at'],
        'updated_at' => $batch['updated_at']
    ];
}

mysqli_stmt_close($batchStmt);

// Build response
$productData = [
    'id' => intval($product['product_id']),
    'name' => $product['product_name'],
    'sku' => $product['sku'],
    'barcode' => $product['barcode'],
    'price' => floatval($product['price']),
    'cost_price' => floatval($product['cost_price']),
    'profit_margin' => floatval($product['profit_margin']),
    'category_id' => $product['category_id'] ? intval($product['category_id']) : null,
    'brand_id' => $product['brand_id'] ? intval($product['brand_id']) : null,
    'description' => $product['description'],
    'has_stock' => $product['has_stock'] == '1',
    'available_quantity' => floatval($product['available_quantity']),
    'stock_alert_limit' => intval($product['stock_alert_limit']),
    'alert_quantity' => floatval($product['alert_quantity']),
    'is_active' => (bool)$product['active_status'],
    'image' => $product['image'] ? '/products/create/uploads/products/' . $product['image'] : null,
    'created_at' => $product['created_at'],
    'updated_at' => $product['updated_at'],
    'batches' => $batches,
    'commission_percentage' => floatval($product['employee_commission_percentage']),
    'low_stock_alert' => floatval($product['available_quantity']) <= floatval($product['stock_alert_limit'])
];

ApiResponse::success($productData, 'Product details retrieved successfully');
