<?php
/**
 * Product List API Endpoint
 * Get list of products with pagination and filtering
 * 
 * @route GET /api/v1/products/list.php?page=1&per_page=20&category=all
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

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
$offset = ($page - 1) * $perPage;

// Get filter parameters
$activeOnly = isset($_GET['active_only']) ? filter_var($_GET['active_only'], FILTER_VALIDATE_BOOLEAN) : true;
$inStockOnly = isset($_GET['in_stock_only']) ? filter_var($_GET['in_stock_only'], FILTER_VALIDATE_BOOLEAN) : false;

// Build query
$whereConditions = [];
$whereConditions[] = "p.active_status = " . ($activeOnly ? "1" : "1"); // Always active for POS

if ($inStockOnly) {
    $whereConditions[] = "pb.quantity > 0";
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

// Count total records
$countQuery = "SELECT COUNT(DISTINCT p.product_id) as total 
    FROM products p
    LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
    $whereClause";

$countResult = mysqli_query($con, $countQuery);

if (!$countResult) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

$totalRecords = mysqli_fetch_assoc($countResult)['total'];

// Get products
$query = "SELECT 
    p.product_id,
    p.product_name,
    p.sku,
    p.barcode,
    p.has_stock,
    p.active_status,
    p.image,
    p.category_id,
    COALESCE(pb.selling_price, 0) as price,
    COALESCE(pb.cost, 0) as cost_price,
    COALESCE(SUM(pb.quantity), 0) as available_quantity
FROM products p
LEFT JOIN product_batch pb ON p.product_id = pb.product_id AND pb.status = 'active'
$whereClause
GROUP BY p.product_id, p.product_name, p.sku, p.barcode, p.has_stock, p.active_status, p.image, p.category_id, pb.selling_price, pb.cost
ORDER BY p.product_name ASC
LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'ii', $perPage, $offset);
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
        'category_id' => $row['category_id'] ? intval($row['category_id']) : null,
        'has_stock' => $row['has_stock'] == '1',
        'available_quantity' => floatval($row['available_quantity']),
        'is_active' => (bool)$row['active_status'],
        'image' => $row['image'] ? '/products/create/uploads/products/' . $row['image'] : null
    ];
}

mysqli_stmt_close($stmt);

ApiResponse::paginated($products, intval($totalRecords), $page, $perPage, 'Products retrieved successfully');
