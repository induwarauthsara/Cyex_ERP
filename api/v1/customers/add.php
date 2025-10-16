<?php
/**
 * Add Customer API Endpoint
 * Create a new customer
 * 
 * @route POST /api/v1/customers/add.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$errors = [];

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
    ApiResponse::error('Customer with this mobile number already exists', 409);
}

mysqli_stmt_close($checkStmt);

// Insert new customer
$insertQuery = "INSERT INTO customers (customer_name, customer_mobile, customer_type, customer_extra_fund) VALUES (?, ?, ?, 0.00)";
$insertStmt = mysqli_prepare($con, $insertQuery);

if (!$insertStmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($insertStmt, 'sss', $customerName, $mobile, $customerType);
$success = mysqli_stmt_execute($insertStmt);

if (!$success) {
    mysqli_stmt_close($insertStmt);
    ApiResponse::serverError('Failed to create customer', mysqli_error($con));
}

$customerId = mysqli_insert_id($con);
mysqli_stmt_close($insertStmt);

// Log action
$logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'Create Customer', 'Added new customer: $customerName (Mobile: $mobile)', CURRENT_DATE, CURRENT_TIME)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'i', $userData['employee_id']);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

// Return created customer
ApiResponse::success([
    'id' => $customerId,
    'name' => $customerName,
    'mobile' => $mobile,
    'type' => $customerType,
    'extra_fund' => 0.00
], 'Customer created successfully', 201);
