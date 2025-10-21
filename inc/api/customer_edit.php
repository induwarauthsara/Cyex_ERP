<?php
/**
 * Edit Customer API for Admin Panel
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

if (empty($input['name'])) {
    echo json_encode(['success' => false, 'message' => 'Customer name is required']);
    exit;
}

if (empty($input['mobile']) || !preg_match('/^[0-9]{10}$/', $input['mobile'])) {
    echo json_encode(['success' => false, 'message' => 'Valid 10 digit mobile number is required']);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Customer not found']);
    exit;
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
    echo json_encode(['success' => false, 'message' => 'Mobile number is already used by another customer']);
    exit;
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
    $employeeId = $_SESSION['employ_id'];
    $description = "Updated customer ID {$customerId}: {$customerName} (Mobile: {$mobile})";
    if ($updatePastInvoices && $invoicesUpdated > 0) {
        $description .= " - Updated {$invoicesUpdated} past invoice(s)";
    }
    
    $logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'Edit Customer', ?, CURRENT_DATE, CURRENT_TIME)";
    $logStmt = mysqli_prepare($con, $logQuery);
    mysqli_stmt_bind_param($logStmt, 'is', $employeeId, $description);
    mysqli_stmt_execute($logStmt);
    mysqli_stmt_close($logStmt);

    // Commit transaction
    mysqli_commit($con);

    echo json_encode([
        'success' => true,
        'message' => 'Customer updated successfully',
        'invoices_updated' => $invoicesUpdated
    ]);

} catch (Exception $e) {
    mysqli_rollback($con);
    echo json_encode(['success' => false, 'message' => 'Failed to update customer: ' . $e->getMessage()]);
}
