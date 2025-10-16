<?php
/**
 * Customer Details API Endpoint
 * Get detailed information about a specific customer
 * 
 * @route GET /api/v1/customers/details.php?id=123
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

// Get customer ID
$customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($customerId <= 0) {
    ApiResponse::validationError(['id' => 'Valid customer ID is required']);
}

// Get customer details
$query = "SELECT 
    id,
    customer_name,
    customer_mobile,
    customer_type,
    customer_extra_fund
FROM customers
WHERE id = ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'i', $customerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    ApiResponse::notFound('Customer not found');
}

$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get customer's recent invoices
$invoiceQuery = "SELECT 
    invoice_number,
    total,
    advance,
    balance,
    invoice_date,
    paymentMethod,
    full_paid
FROM invoice
WHERE customer_id = ?
ORDER BY invoice_date DESC, time DESC
LIMIT 10";

$invoiceStmt = mysqli_prepare($con, $invoiceQuery);
mysqli_stmt_bind_param($invoiceStmt, 'i', $customerId);
mysqli_stmt_execute($invoiceStmt);
$invoiceResult = mysqli_stmt_get_result($invoiceStmt);

$recentInvoices = [];
$totalPurchases = 0;
$totalBalance = 0;

while ($invoice = mysqli_fetch_assoc($invoiceResult)) {
    $recentInvoices[] = [
        'invoice_number' => intval($invoice['invoice_number']),
        'total' => floatval($invoice['total']),
        'advance' => floatval($invoice['advance']),
        'balance' => floatval($invoice['balance']),
        'date' => $invoice['invoice_date'],
        'payment_method' => $invoice['paymentMethod'],
        'full_paid' => (bool)$invoice['full_paid']
    ];
    
    $totalPurchases += floatval($invoice['total']);
    $totalBalance += floatval($invoice['balance']);
}

mysqli_stmt_close($invoiceStmt);

// Build response
$customerData = [
    'id' => intval($customer['id']),
    'name' => $customer['customer_name'],
    'mobile' => $customer['customer_mobile'],
    'type' => $customer['customer_type'],
    'extra_fund' => floatval($customer['customer_extra_fund']),
    'statistics' => [
        'total_purchases' => $totalPurchases,
        'outstanding_balance' => $totalBalance,
        'recent_invoice_count' => count($recentInvoices)
    ],
    'recent_invoices' => $recentInvoices
];

ApiResponse::success($customerData, 'Customer details retrieved successfully');
