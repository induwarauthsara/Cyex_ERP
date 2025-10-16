<?php
/**
 * Product Search API Endpoint
 * Search products by name, SKU, or barcode for POS
 * 
 * @route GET /api/v1/products/search.php?q=search_term&limit=10
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

// Get search query
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;

if (empty($searchQuery)) {
    ApiResponse::validationError(['q' => 'Search query is required']);
}

// Limit maximum results
$limit = min($limit, 50);

// Search in products - looking for name, SKU, barcode
$searchTerm = mysqli_real_escape_string($con, $searchQuery);
$searchPattern = "%{$searchTerm}%";

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
    COALESCE(pb.selling_price, 0) as price,
    COALESCE(pb.cost, 0) as cost_price,
    COALESCE(SUM(pb.quantity), 0) as available_quantity
FROM products p
LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
WHERE (
    p.product_name LIKE ? 
    OR p.sku LIKE ? 
    OR p.barcode LIKE ?
)
AND p.active_status = 1
GROUP BY p.product_id, p.product_name, p.sku, p.barcode, p.has_stock, p.active_status, p.image, p.category_id, p.brand_id, pb.selling_price, pb.cost
ORDER BY p.product_name ASC
LIMIT ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'sssi', $searchPattern, $searchPattern, $searchPattern, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => intval($row['product_id']),
        'name' => $row['product_name'],
        'sku' => $row['sku'],
        'barcode' => $row['barcode'],
        'price' => floatval($row['price']),
        'cost_price' => floatval($row['cost_price']),
        'has_stock' => $row['has_stock'] == '1',
        'available_quantity' => floatval($row['available_quantity']),
        'is_active' => (bool)$row['active_status'],
        'image' => $row['image'] ? '/products/create/uploads/products/' . $row['image'] : null,
        'category_id' => $row['category_id'] ? intval($row['category_id']) : null,
        'brand_id' => $row['brand_id'] ? intval($row['brand_id']) : null
    ];
}

mysqli_stmt_close($stmt);

ApiResponse::success($products, count($products) . ' products found', 200, [
    'search_query' => $searchQuery,
    'result_count' => count($products)
]);
