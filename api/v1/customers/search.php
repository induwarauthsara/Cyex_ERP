<?php
/**
 * Customer Search API Endpoint
 * Search customers by name or mobile number
 * 
 * @route GET /api/v1/customers/search.php?q=search_term&limit=10
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

// Search in customers
$searchTerm = mysqli_real_escape_string($con, $searchQuery);
$searchPattern = "%{$searchTerm}%";

$query = "SELECT 
    id,
    customer_name,
    customer_mobile,
    customer_type,
    customer_extra_fund
FROM customers
WHERE (
    customer_name LIKE ? 
    OR customer_mobile LIKE ?
)
ORDER BY customer_name ASC
LIMIT ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'ssi', $searchPattern, $searchPattern, $limit);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = [
        'id' => intval($row['id']),
        'name' => $row['customer_name'],
        'mobile' => $row['customer_mobile'],
        'type' => $row['customer_type'],
        'extra_fund' => floatval($row['customer_extra_fund'])
    ];
}

mysqli_stmt_close($stmt);

ApiResponse::success($customers, count($customers) . ' customers found', 200, [
    'search_query' => $searchQuery,
    'result_count' => count($customers)
]);
