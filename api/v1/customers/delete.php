<?php
/**
 * Delete Customer API Endpoint
 * Delete customer with validation
 * 
 * @route DELETE /api/v1/customers/delete.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow DELETE requests
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Method not allowed. Use DELETE', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($input['id'])) {
    ApiResponse::validationError(['id' => 'Customer ID is required']);
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
    ApiResponse::error('Customer not found', 404);
}

$customerData = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// Check if customer has invoices
$invoiceCheckQuery = "SELECT COUNT(*) as invoice_count FROM invoice WHERE customer_id = ?";
$invoiceCheckStmt = mysqli_prepare($con, $invoiceCheckQuery);
mysqli_stmt_bind_param($invoiceCheckStmt, 'i', $customerId);
mysqli_stmt_execute($invoiceCheckStmt);
$invoiceCheckResult = mysqli_stmt_get_result($invoiceCheckStmt);
$invoiceCount = mysqli_fetch_assoc($invoiceCheckResult)['invoice_count'];
mysqli_stmt_close($invoiceCheckStmt);

if ($invoiceCount > 0) {
    ApiResponse::error(
        "Cannot delete customer. This customer has {$invoiceCount} invoice(s) associated with their account. Please remove or reassign these invoices first.",
        409
    );
}

// Delete customer
$deleteQuery = "DELETE FROM customers WHERE id = ?";
$deleteStmt = mysqli_prepare($con, $deleteQuery);
mysqli_stmt_bind_param($deleteStmt, 'i', $customerId);
$success = mysqli_stmt_execute($deleteStmt);
mysqli_stmt_close($deleteStmt);

if (!$success) {
    ApiResponse::serverError('Failed to delete customer');
}

// Log action
$description = "Deleted customer ID {$customerId}: {$customerData['customer_name']} (Mobile: {$customerData['customer_mobile']})";
$logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'Delete Customer', ?, CURRENT_DATE, CURRENT_TIME)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'is', $userData['employee_id'], $description);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

ApiResponse::success(null, 'Customer deleted successfully');
