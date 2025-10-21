<?php
/**
 * Edit Customer API Endpoint
 * Update customer information with option to update past invoices
 * 
 * @route PUT /api/v1/customers/edit.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow PUT requests
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    ApiResponse::error('Method not allowed. Use PUT', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$errors = [];

if (empty($input['id'])) {
    $errors['id'] = 'Customer ID is required';
}

if (empty($input['name'])) {
    $errors['name'] = 'Customer name is required';
}

if (empty($input['mobile'])) {
    $errors['mobile'] = 'Mobile number is required';
} elseif (!preg_match('/^[0-9]{10}$/', $input['mobile'])) {
    $errors['mobile'] = 'Mobile number must be 10 digits';
}

if (!empty($errors)) {
    ApiResponse::validationError($errors);
}

$customerId = (int)$input['id'];
$customerName = mysqli_real_escape_string($con, trim($input['name']));
$mobile = mysqli_real_escape_string($con, trim($input['mobile']));
$customerType = isset($input['type']) ? mysqli_real_escape_string($con, trim($input['type'])) : 'regular';
$updatePastInvoices = isset($input['update_past_invoices']) ? (bool)$input['update_past_invoices'] : false;

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

$oldCustomerData = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// Check if mobile number is already used by another customer
$mobileCheckQuery = "SELECT id FROM customers WHERE customer_mobile = ? AND id != ?";
$mobileCheckStmt = mysqli_prepare($con, $mobileCheckQuery);
mysqli_stmt_bind_param($mobileCheckStmt, 'si', $mobile, $customerId);
mysqli_stmt_execute($mobileCheckStmt);
$mobileCheckResult = mysqli_stmt_get_result($mobileCheckStmt);

if (mysqli_num_rows($mobileCheckResult) > 0) {
    mysqli_stmt_close($mobileCheckStmt);
    ApiResponse::error('Mobile number is already used by another customer', 409);
}

mysqli_stmt_close($mobileCheckStmt);

// Start transaction
mysqli_begin_transaction($con);

try {
    // Update customer
    $updateQuery = "UPDATE customers SET customer_name = ?, customer_mobile = ?, customer_type = ? WHERE id = ?";
    $updateStmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, 'sssi', $customerName, $mobile, $customerType, $customerId);
    $success = mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);

    if (!$success) {
        throw new Exception('Failed to update customer');
    }

    $invoicesUpdated = 0;
    
    // Update past invoices if requested and name or mobile changed
    if ($updatePastInvoices && 
        ($oldCustomerData['customer_name'] !== $customerName || $oldCustomerData['customer_mobile'] !== $mobile)) {
        
        $updateInvoicesQuery = "UPDATE invoice SET customer_name = ?, customer_mobile = ? WHERE customer_id = ?";
        $updateInvoicesStmt = mysqli_prepare($con, $updateInvoicesQuery);
        mysqli_stmt_bind_param($updateInvoicesStmt, 'ssi', $customerName, $mobile, $customerId);
        mysqli_stmt_execute($updateInvoicesStmt);
        $invoicesUpdated = mysqli_stmt_affected_rows($updateInvoicesStmt);
        mysqli_stmt_close($updateInvoicesStmt);
    }

    // Log action
    $description = "Updated customer ID {$customerId}: {$customerName} (Mobile: {$mobile})";
    if ($updatePastInvoices && $invoicesUpdated > 0) {
        $description .= " - Updated {$invoicesUpdated} past invoice(s)";
    }
    
    $logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'Edit Customer', ?, CURRENT_DATE, CURRENT_TIME)";
    $logStmt = mysqli_prepare($con, $logQuery);
    mysqli_stmt_bind_param($logStmt, 'is', $userData['employee_id'], $description);
    mysqli_stmt_execute($logStmt);
    mysqli_stmt_close($logStmt);

    // Commit transaction
    mysqli_commit($con);

    // Get updated customer with statistics
    $getQuery = "
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
    
    $getStmt = mysqli_prepare($con, $getQuery);
    mysqli_stmt_bind_param($getStmt, 'i', $customerId);
    mysqli_stmt_execute($getStmt);
    $result = mysqli_stmt_get_result($getStmt);
    $customer = mysqli_fetch_assoc($result);
    mysqli_stmt_close($getStmt);

    ApiResponse::success([
        'id' => (int)$customer['id'],
        'name' => $customer['customer_name'],
        'mobile' => $customer['customer_mobile'],
        'type' => $customer['customer_type'],
        'extra_fund' => (float)$customer['customer_extra_fund'],
        'invoices_updated' => $invoicesUpdated,
        'invoice_count' => (int)$customer['invoice_count']
    ], 'Customer updated successfully');

} catch (Exception $e) {
    mysqli_rollback($con);
    ApiResponse::serverError('Failed to update customer: ' . $e->getMessage());
}
