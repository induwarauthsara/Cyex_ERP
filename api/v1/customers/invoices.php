<?php
/**
 * Customer Invoices API Endpoint
 * Get all invoices for a specific customer
 * 
 * @route GET /api/v1/customers/invoices.php
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

// Validate input
if (empty($_GET['id'])) {
    ApiResponse::validationError(['id' => 'Customer ID is required']);
}

$customerId = (int)$_GET['id'];
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(1, (int)$_GET['per_page'])) : 20;
$status = isset($_GET['status']) ? trim($_GET['status']) : 'all';

$offset = ($page - 1) * $perPage;

// Check if customer exists
$checkQuery = "SELECT customer_name, customer_mobile, customer_type, customer_extra_fund FROM customers WHERE id = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 'i', $customerId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    mysqli_stmt_close($checkStmt);
    ApiResponse::error('Customer not found', 404);
}

$customerData = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// Build query based on status filter
$whereClause = "WHERE customer_id = ?";
$params = [$customerId];
$types = 'i';

switch ($status) {
    case 'paid':
        $whereClause .= " AND full_paid = 1";
        break;
    case 'unpaid':
        $whereClause .= " AND full_paid = 0 AND balance > 0";
        break;
    case 'partial':
        $whereClause .= " AND full_paid = 0 AND advance > 0";
        break;
}

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM invoice {$whereClause}";
$countStmt = mysqli_prepare($con, $countQuery);
mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalCount = mysqli_fetch_assoc($countResult)['total'];
mysqli_stmt_close($countStmt);

// Get invoices
$query = "
    SELECT 
        invoice_number,
        invoice_description,
        customer_name,
        customer_mobile,
        invoice_date,
        time,
        biller,
        total,
        discount,
        advance,
        balance,
        cost,
        profit,
        full_paid,
        paymentMethod,
        credit_payment,
        amount_received,
        cash_change
    FROM invoice
    {$whereClause}
    ORDER BY invoice_date DESC, time DESC
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

$invoices = [];
while ($row = mysqli_fetch_assoc($result)) {
    $invoices[] = [
        'invoice_number' => (int)$row['invoice_number'],
        'description' => $row['invoice_description'],
        'customer_name' => $row['customer_name'],
        'customer_mobile' => $row['customer_mobile'],
        'date' => $row['invoice_date'],
        'time' => $row['time'],
        'biller' => $row['biller'],
        'total' => (float)$row['total'],
        'discount' => (float)$row['discount'],
        'advance' => (float)$row['advance'],
        'balance' => (float)$row['balance'],
        'cost' => (float)$row['cost'],
        'profit' => (float)$row['profit'],
        'full_paid' => (bool)$row['full_paid'],
        'payment_method' => $row['paymentMethod'],
        'credit_payment' => (bool)$row['credit_payment'],
        'amount_received' => (float)$row['amount_received'],
        'cash_change' => (float)$row['cash_change'],
        'status' => $row['full_paid'] ? 'Paid' : ($row['advance'] > 0 ? 'Partially Paid' : 'Unpaid')
    ];
}

mysqli_stmt_close($stmt);

// Calculate totals
$totalsQuery = "
    SELECT 
        SUM(total) as total_sales,
        SUM(advance) as total_paid,
        SUM(balance) as total_outstanding,
        SUM(profit) as total_profit
    FROM invoice
    WHERE customer_id = ?
";

$totalsStmt = mysqli_prepare($con, $totalsQuery);
mysqli_stmt_bind_param($totalsStmt, 'i', $customerId);
mysqli_stmt_execute($totalsStmt);
$totalsResult = mysqli_stmt_get_result($totalsStmt);
$totals = mysqli_fetch_assoc($totalsResult);
mysqli_stmt_close($totalsStmt);

// Calculate pagination
$totalPages = ceil($totalCount / $perPage);

ApiResponse::success([
    'customer' => [
        'id' => $customerId,
        'name' => $customerData['customer_name'],
        'mobile' => $customerData['customer_mobile'],
        'type' => $customerData['customer_type'],
        'extra_fund' => (float)$customerData['customer_extra_fund']
    ],
    'totals' => [
        'total_sales' => (float)$totals['total_sales'],
        'total_paid' => (float)$totals['total_paid'],
        'total_outstanding' => (float)$totals['total_outstanding'],
        'total_profit' => (float)$totals['total_profit']
    ],
    'invoices' => $invoices
], 'Customer invoices retrieved successfully', 200, [
    'pagination' => [
        'total' => (int)$totalCount,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'has_more' => $page < $totalPages
    ]
]);
