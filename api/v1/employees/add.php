<?php
/**
 * Add Employee API Endpoint
 * Create a new employee
 * 
 * @route POST /api/v1/employees/add.php
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

// Require admin authentication
$userData = ApiAuth::requireAuth(['Admin']);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    ApiResponse::error('Invalid JSON input', 400);
}

// Validate required fields
$errors = [];

if (empty($input['name'])) {
    $errors['name'] = 'Employee name is required';
}

if (empty($input['mobile'])) {
    $errors['mobile'] = 'Mobile number is required';
} elseif (!preg_match('/^\d{9,10}$/', $input['mobile'])) {
    $errors['mobile'] = 'Mobile number must be 9 or 10 digits';
}

if (empty($input['nic'])) {
    $errors['nic'] = 'NIC is required';
}

if (empty($input['password'])) {
    $errors['password'] = 'Password is required';
} elseif (strlen($input['password']) < 4) {
    $errors['password'] = 'Password must be at least 4 characters';
}

if (empty($input['role'])) {
    $errors['role'] = 'Role is required';
} elseif (!in_array($input['role'], ['Employee', 'Admin'])) {
    $errors['role'] = 'Role must be either Employee or Admin';
}

if (!isset($input['salary']) || $input['salary'] < 0) {
    $errors['salary'] = 'Valid salary is required';
}

if (!isset($input['day_salary']) || $input['day_salary'] < 0) {
    $errors['day_salary'] = 'Valid day salary is required';
}

if (!empty($errors)) {
    ApiResponse::validationError($errors);
}

// Check if mobile number already exists
$checkQuery = "SELECT employ_id FROM employees WHERE mobile = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 's', $input['mobile']);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) > 0) {
    ApiResponse::error('Mobile number is already registered', 409);
}
mysqli_stmt_close($checkStmt);

// Check if NIC already exists
$checkNicQuery = "SELECT employ_id FROM employees WHERE nic = ?";
$checkNicStmt = mysqli_prepare($con, $checkNicQuery);
mysqli_stmt_bind_param($checkNicStmt, 's', $input['nic']);
mysqli_stmt_execute($checkNicStmt);
$checkNicResult = mysqli_stmt_get_result($checkNicStmt);

if (mysqli_num_rows($checkNicResult) > 0) {
    ApiResponse::error('NIC is already registered', 409);
}
mysqli_stmt_close($checkNicStmt);

// Prepare data
$name = trim($input['name']);
$mobile = intval($input['mobile']);
$address = isset($input['address']) ? trim($input['address']) : null;
$bankAccount = isset($input['bank_account']) ? trim($input['bank_account']) : null;
$role = $input['role'];
$nic = trim($input['nic']);
$salary = floatval($input['salary']);
$daySalary = floatval($input['day_salary']);
$password = trim($input['password']);
$status = isset($input['status']) && $input['status'] === 'inactive' ? 0 : 1;

// Insert employee
$query = "INSERT INTO employees 
    (emp_name, mobile, address, bank_account, role, nic, salary, day_salary, password, status, onboard_date) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_DATE)";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param(
    $stmt,
    'sissssddsi',
    $name,
    $mobile,
    $address,
    $bankAccount,
    $role,
    $nic,
    $salary,
    $daySalary,
    $password,
    $status
);

if (!mysqli_stmt_execute($stmt)) {
    ApiResponse::serverError('Failed to create employee', mysqli_error($con));
}

$employeeId = mysqli_insert_id($con);
mysqli_stmt_close($stmt);

// Log action
$actionDesc = "New employee created: $name (ID: $employeeId)";
$logQuery = "INSERT INTO action_log (employee_id, action, description) VALUES (?, 'Employee Added', ?)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'is', $userData['employee_id'], $actionDesc);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

// Return created employee
$response = [
    'id' => $employeeId,
    'name' => $name,
    'mobile' => $mobile,
    'address' => $address,
    'bank_account' => $bankAccount,
    'role' => $role,
    'nic' => $nic,
    'salary' => $salary,
    'day_salary' => $daySalary,
    'status' => $status === 1 ? 'active' : 'inactive'
];

ApiResponse::success($response, 'Employee created successfully', 201);
