<?php
/**
 * Customers List API for Admin Panel
 * Get paginated list of customers with search and filters
 */

session_start();
require_once(__DIR__ . '/../config.php');

// Check admin access
if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get query parameters
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
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
        COALESCE(SUM(i.total), 0) as total_purchases,
        COALESCE(SUM(i.balance), 0) as outstanding_balance
    FROM customers c
    LEFT JOIN invoice i ON c.id = i.customer_id
    {$whereClause}
    GROUP BY c.id
    ORDER BY c.id DESC
    LIMIT ? OFFSET ?
";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
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

echo json_encode([
    'success' => true,
    'customers' => $customers,
    'pagination' => [
        'total' => (int)$totalCount,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'has_more' => $page < $totalPages
    ]
]);
