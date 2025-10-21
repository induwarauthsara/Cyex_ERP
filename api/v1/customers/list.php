<?php
/**
 * List Customers API Endpoint
 * Get paginated list of customers with search and filters
 * 
 * @route GET /api/v1/customers/list.php
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

// Get query parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(1, (int)$_GET['per_page'])) : 20;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : 'all';

$offset = ($page - 1) * $perPage;

// Build query
$whereConditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $whereConditions[] = "(customer_name LIKE ? OR customer_mobile LIKE ?)";
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'ss';
}

if ($type !== 'all') {
    $whereConditions[] = "customer_type = ?";
    $params[] = $type;
    $types .= 's';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM customers {$whereClause}";
$countStmt = mysqli_prepare($con, $countQuery);

if (!empty($params)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
}

mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalCount = mysqli_fetch_assoc($countResult)['total'];
mysqli_stmt_close($countStmt);

// Get customers
$query = "
    SELECT 
        c.id,
        c.customer_name,
        c.customer_mobile,
        c.customer_type,
        c.customer_extra_fund,
        COUNT(DISTINCT i.invoice_number) as invoice_count,
        SUM(i.total) as total_purchases,
        SUM(i.balance) as outstanding_balance
    FROM customers c
    LEFT JOIN invoice i ON c.id = i.customer_id
    {$whereClause}
    GROUP BY c.id
    ORDER BY c.id DESC
    LIMIT ? OFFSET ?
";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$customers = [];
while ($row = mysqli_fetch_assoc($result)) {
    $customers[] = [
        'id' => (int)$row['id'],
        'name' => $row['customer_name'],
        'mobile' => $row['customer_mobile'],
        'type' => $row['customer_type'],
        'extra_fund' => (float)$row['customer_extra_fund'],
        'statistics' => [
            'invoice_count' => (int)$row['invoice_count'],
            'total_purchases' => (float)$row['total_purchases'],
            'outstanding_balance' => (float)$row['outstanding_balance']
        ]
    ];
}

mysqli_stmt_close($stmt);

// Calculate pagination
$totalPages = ceil($totalCount / $perPage);

ApiResponse::success($customers, 'Customers retrieved successfully', 200, [
    'pagination' => [
        'total' => (int)$totalCount,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'has_more' => $page < $totalPages
    ]
]);
