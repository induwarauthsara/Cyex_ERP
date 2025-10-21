<?php
/**
 * Customer Invoices API for Admin Panel
 */

session_start();
require_once(__DIR__ . '/../config.php');

// Check admin access
if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Validate input
if (empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is required']);
    exit;
}

$customerId = (int)$_GET['id'];

// Check if customer exists
$checkQuery = "SELECT customer_name, customer_mobile, customer_type, customer_extra_fund FROM customers WHERE id = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 'i', $customerId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    mysqli_stmt_close($checkStmt);
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    exit;
}

$customerData = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

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
    WHERE customer_id = ?
    ORDER BY invoice_date DESC, time DESC
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $customerId);
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
        COALESCE(SUM(total), 0) as total_sales,
        COALESCE(SUM(advance), 0) as total_paid,
        COALESCE(SUM(balance), 0) as total_outstanding,
        COALESCE(SUM(profit), 0) as total_profit
    FROM invoice
    WHERE customer_id = ?
";

$totalsStmt = mysqli_prepare($con, $totalsQuery);
mysqli_stmt_bind_param($totalsStmt, 'i', $customerId);
mysqli_stmt_execute($totalsStmt);
$totalsResult = mysqli_stmt_get_result($totalsStmt);
$totals = mysqli_fetch_assoc($totalsResult);
mysqli_stmt_close($totalsStmt);

echo json_encode([
    'success' => true,
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
]);
