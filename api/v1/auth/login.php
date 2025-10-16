<?php
/**
 * Login API Endpoint
 * Authenticates user and returns JWT token
 * 
 * @route POST /api/v1/auth/login.php
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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$errors = [];

if (empty($input['username'])) {
    $errors['username'] = 'Username is required';
}

if (empty($input['password'])) {
    $errors['password'] = 'Password is required';
}

if (!empty($errors)) {
    ApiResponse::validationError($errors);
}

$username = mysqli_real_escape_string($con, trim($input['username']));
$password = mysqli_real_escape_string($con, trim($input['password']));

// Query to authenticate user
$query = "SELECT employ_id, emp_name, role, status FROM employees WHERE emp_name = ? AND password = ? AND status = '1'";
$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    // Log failed login attempt
    $logQuery = "INSERT INTO action_log (action, description, date, time) VALUES ('Failed Login', 'Username: $username', CURRENT_DATE, CURRENT_TIME)";
    mysqli_query($con, $logQuery);
    
    ApiResponse::error('Invalid username or password', 401);
}

$employee = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Generate JWT token
$token = ApiAuth::generateToken(
    $employee['employ_id'],
    $employee['emp_name'],
    $employee['role']
);

// Log successful login
$logQuery = "INSERT INTO action_log (employee_id, action, description, date, time) VALUES (?, 'API Login', 'Mobile POS Login', CURRENT_DATE, CURRENT_TIME)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'i', $employee['employ_id']);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

// Return response
ApiResponse::success([
    'token' => $token,
    'user' => [
        'id' => $employee['employ_id'],
        'name' => $employee['emp_name'],
        'role' => $employee['role']
    ],
    'expires_in' => JWT_EXPIRY
], 'Login successful');
