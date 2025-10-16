<?php
/**
 * Invoice List API Endpoint
 * Get list of invoices with filtering and pagination
 * 
 * @route GET /api/v1/invoices/list.php?page=1&per_page=20&status=all&date_from=2025-01-01
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
$status = isset($_GET['status']) ? $_GET['status'] : 'all'; // all, paid, pending, partially_paid, overdue
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : null;
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : null;
$customerId = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : null;
$invoiceNumber = isset($_GET['invoice_number']) ? $_GET['invoice_number'] : null;

// Build WHERE clause
$whereConditions = ["1=1"];
$params = [];
$types = '';

// Status filter
if ($status === 'paid') {
    $whereConditions[] = "i.full_paid = 1";
} elseif ($status === 'pending') {
    $whereConditions[] = "i.full_paid = 0 AND i.advance = 0";
} elseif ($status === 'partially_paid') {
    $whereConditions[] = "i.full_paid = 0 AND i.advance > 0";
} elseif ($status === 'overdue') {
    $whereConditions[] = "i.full_paid = 0 AND DATEDIFF(CURRENT_DATE, i.invoice_date) > 30";
}

// Date range filter
if ($dateFrom) {
    $whereConditions[] = "i.invoice_date >= ?";
    $params[] = $dateFrom;
    $types .= 's';
}

if ($dateTo) {
    $whereConditions[] = "i.invoice_date <= ?";
    $params[] = $dateTo;
    $types .= 's';
}

// Customer filter
if ($customerId) {
    $whereConditions[] = "i.customer_id = ?";
    $params[] = $customerId;
    $types .= 'i';
}

// Invoice number filter
if ($invoiceNumber) {
    $whereConditions[] = "i.invoice_number LIKE ?";
    $params[] = "%$invoiceNumber%";
    $types .= 's';
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

// Count total records
$countQuery = "SELECT COUNT(*) as total 
    FROM invoice i
    $whereClause";

if (!empty($params)) {
    $countStmt = mysqli_prepare($con, $countQuery);
    if ($types) {
        mysqli_stmt_bind_param($countStmt, $types, ...$params);
    }
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
} else {
    $countResult = mysqli_query($con, $countQuery);
}

if (!$countResult) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

$totalRecords = mysqli_fetch_assoc($countResult)['total'];

// Get invoices
$query = "SELECT 
    i.invoice_number,
    i.customer_id,
    i.customer_name,
    i.customer_mobile,
    i.total,
    i.discount,
    i.advance,
    i.balance,
    i.invoice_date,
    i.time,
    i.paymentMethod,
    i.full_paid,
    i.biller,
    DATEDIFF(CURRENT_DATE, i.invoice_date) as days_old
FROM invoice i
LEFT JOIN employees e ON i.biller = e.employ_id
$whereClause
ORDER BY i.invoice_date DESC, i.time DESC
LIMIT ? OFFSET ?";

// Add pagination params
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$invoices = [];
while ($row = mysqli_fetch_assoc($result)) {
    $isPending = !$row['full_paid'];
    $isOverdue = $isPending && intval($row['days_old']) > 30;
    
    $statusText = 'Paid';
    if ($isPending) {
        if (floatval($row['advance']) == 0) {
            $statusText = 'Pending';
        } else {
            $statusText = 'Partially Paid';
        }
    }
    if ($isOverdue) {
        $statusText = 'Overdue';
    }
    
    $invoices[] = [
        'invoice_number' => intval($row['invoice_number']),
        'customer' => [
            'id' => $row['customer_id'] ? intval($row['customer_id']) : 0,
            'name' => $row['customer_name'],
            'mobile' => $row['customer_mobile']
        ],
        'total' => floatval($row['total']),
        'discount' => floatval($row['discount']),
        'advance' => floatval($row['advance']),
        'balance' => floatval($row['balance']),
        'date' => $row['invoice_date'],
        'time' => $row['time'],
        'payment_method' => $row['paymentMethod'],
        'biller_name' => $row['biller'],
        'status' => $statusText,
        'is_paid' => (bool)$row['full_paid'],
        'is_overdue' => $isOverdue,
        'days_old' => intval($row['days_old'])
    ];
}

mysqli_stmt_close($stmt);

ApiResponse::paginated($invoices, intval($totalRecords), $page, $perPage, 'Invoices retrieved successfully');
