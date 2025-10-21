<?php
/**
 * Add Customer API for Admin Panel
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
if (empty($input['name'])) {
    echo json_encode(['success' => false, 'message' => 'Customer name is required']);
    exit;
}

if (empty($input['mobile']) || !preg_match('/^[0-9]{10}$/', $input['mobile'])) {
    echo json_encode(['success' => false, 'message' => 'Valid 10 digit mobile number is required']);
    exit;
}

$customerName = mysqli_real_escape_string($con, trim($input['name']));
$mobile = mysqli_real_escape_string($con, trim($input['mobile']));
$customerType = isset($input['type']) ? mysqli_real_escape_string($con, trim($input['type'])) : 'regular';

// Check if customer with same mobile already exists
$checkQuery = "SELECT id FROM customers WHERE customer_mobile = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 's', $mobile);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    mysqli_stmt_close($checkStmt);
    echo json_encode(['success' => false, 'message' => 'Customer with this mobile number already exists']);
    exit;
}

mysqli_stmt_close($checkStmt);

// Insert new customer
$insertQuery = "INSERT INTO customers (customer_name, customer_mobile, customer_type, customer_extra_fund) VALUES (?, ?, ?, 0.00)";
$insertStmt = mysqli_prepare($con, $insertQuery);

if (!$insertStmt) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

mysqli_stmt_bind_param($insertStmt, 'sss', $customerName, $mobile, $customerType);
$success = mysqli_stmt_execute($insertStmt);

if (!$success) {
    mysqli_stmt_close($insertStmt);
    echo json_encode(['success' => false, 'message' => 'Failed to create customer']);
    exit;
}

$customerId = mysqli_insert_id($con);
mysqli_stmt_close($insertStmt);

// Log action
$employeeId = $_SESSION['employ_id'];
$logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'Create Customer', 'Added new customer: $customerName (Mobile: $mobile)', CURRENT_DATE, CURRENT_TIME)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'i', $employeeId);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

echo json_encode([
    'success' => true,
    'message' => 'Customer created successfully',
    'customer' => [
        'id' => $customerId,
        'name' => $customerName,
        'mobile' => $mobile,
        'type' => $customerType
    ]
]);
