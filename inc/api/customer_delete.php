<?php
/**
 * Delete Customer API for Admin Panel
 */

session_start();
require_once(__DIR__ . '/../config.php');

// Check admin access
if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Customer ID is required']);
    exit;
}

$customerId = (int)$input['id'];

// Check if customer exists
$checkQuery = "SELECT customer_name, customer_mobile FROM customers WHERE id = ?";
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

// Delete customer (invoices are not affected as they have hardcoded customer data)
$deleteQuery = "DELETE FROM customers WHERE id = ?";
$deleteStmt = mysqli_prepare($con, $deleteQuery);
mysqli_stmt_bind_param($deleteStmt, 'i', $customerId);
$success = mysqli_stmt_execute($deleteStmt);
mysqli_stmt_close($deleteStmt);

if (!$success) {
    echo json_encode(['success' => false, 'message' => 'Failed to delete customer']);
    exit;
}

// Log action
$employeeId = $_SESSION['employee_id'];
$description = "Deleted customer ID {$customerId}: {$customerData['customer_name']} (Mobile: {$customerData['customer_mobile']})";
$logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'Delete Customer', ?, CURRENT_DATE, CURRENT_TIME)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'is', $employeeId, $description);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

echo json_encode([
    'success' => true,
    'message' => 'Customer deleted successfully'
]);
