<?php
/**
 * Get Customer API for Admin Panel
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

// Get customer with invoice count
$query = "
    SELECT 
        c.id,
        c.customer_name,
        c.customer_mobile,
        c.customer_type,
        c.customer_extra_fund,
        COUNT(DISTINCT i.invoice_number) as invoice_count
    FROM customers c
    LEFT JOIN invoice i ON c.id = i.customer_id
    WHERE c.id = ?
    GROUP BY c.id
";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $customerId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    exit;
}

$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'customer' => [
        'id' => (int)$customer['id'],
        'name' => $customer['customer_name'],
        'mobile' => $customer['customer_mobile'],
        'type' => $customer['customer_type'],
        'extra_fund' => (float)$customer['customer_extra_fund'],
        'invoice_count' => (int)$customer['invoice_count']
    ]
]);
