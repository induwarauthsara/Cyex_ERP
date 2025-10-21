<?php
/**
 * Edit Employee API Endpoint
 * Update employee information
 * 
 * @route PUT /api/v1/employees/edit.php
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

// Require admin authentication
$userData = ApiAuth::requireAuth(['Admin']);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    ApiResponse::error('Invalid JSON input', 400);
}

// Validate required fields
$errors = [];

if (empty($input['id'])) {
    $errors['id'] = 'Employee ID is required';
}

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

$employeeId = intval($input['id']);

// Check if employee exists
$checkQuery = "SELECT employ_id FROM employees WHERE employ_id = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 'i', $employeeId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    ApiResponse::error('Employee not found', 404);
}
mysqli_stmt_close($checkStmt);

// Check if mobile number is used by another employee
$mobileCheckQuery = "SELECT employ_id FROM employees WHERE mobile = ? AND employ_id != ?";
$mobileCheckStmt = mysqli_prepare($con, $mobileCheckQuery);
mysqli_stmt_bind_param($mobileCheckStmt, 'si', $input['mobile'], $employeeId);
mysqli_stmt_execute($mobileCheckStmt);
$mobileCheckResult = mysqli_stmt_get_result($mobileCheckStmt);

if (mysqli_num_rows($mobileCheckResult) > 0) {
    ApiResponse::error('Mobile number is already used by another employee', 409);
}
mysqli_stmt_close($mobileCheckStmt);

// Check if NIC is used by another employee
$nicCheckQuery = "SELECT employ_id FROM employees WHERE nic = ? AND employ_id != ?";
$nicCheckStmt = mysqli_prepare($con, $nicCheckQuery);
mysqli_stmt_bind_param($nicCheckStmt, 'si', $input['nic'], $employeeId);
mysqli_stmt_execute($nicCheckStmt);
$nicCheckResult = mysqli_stmt_get_result($nicCheckStmt);

if (mysqli_num_rows($nicCheckResult) > 0) {
    ApiResponse::error('NIC is already used by another employee', 409);
}
mysqli_stmt_close($nicCheckStmt);

// Prepare data
$name = trim($input['name']);
$mobile = intval($input['mobile']);
$address = isset($input['address']) ? trim($input['address']) : null;
$bankAccount = isset($input['bank_account']) ? trim($input['bank_account']) : null;
$role = $input['role'];
$nic = trim($input['nic']);
$salary = floatval($input['salary']);
$daySalary = floatval($input['day_salary']);
$status = isset($input['status']) && $input['status'] === 'inactive' ? 0 : 1;

// Build update query
$updateFields = [
    'emp_name = ?',
    'mobile = ?',
    'address = ?',
    'bank_account = ?',
    'role = ?',
    'nic = ?',
    'salary = ?',
    'day_salary = ?',
    'status = ?'
];

// Add password update if provided
if (!empty($input['password'])) {
    if (strlen($input['password']) < 4) {
        ApiResponse::error('Password must be at least 4 characters', 400);
    }
    $updateFields[] = 'password = ?';
    $password = trim($input['password']);
}

$query = "UPDATE employees SET " . implode(', ', $updateFields) . " WHERE employ_id = ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

// Bind parameters
if (!empty($input['password'])) {
    mysqli_stmt_bind_param(
        $stmt,
        'sissssddisi',
        $name,
        $mobile,
        $address,
        $bankAccount,
        $role,
        $nic,
        $salary,
        $daySalary,
        $status,
        $password,
        $employeeId
    );
} else {
    mysqli_stmt_bind_param(
        $stmt,
        'sissssddi',
        $name,
        $mobile,
        $address,
        $bankAccount,
        $role,
        $nic,
        $salary,
        $daySalary,
        $status,
        $employeeId
    );
}

if (!mysqli_stmt_execute($stmt)) {
    ApiResponse::serverError('Failed to update employee', mysqli_error($con));
}

mysqli_stmt_close($stmt);

// Log action
$actionDesc = "Employee updated: $name (ID: $employeeId)";
$logQuery = "INSERT INTO action_log (employee_id, action, description) VALUES (?, 'Employee Updated', ?)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'is', $userData['employee_id'], $actionDesc);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

// Return updated employee
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

ApiResponse::success($response, 'Employee updated successfully');
