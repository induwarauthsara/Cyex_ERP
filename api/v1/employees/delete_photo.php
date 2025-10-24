<?php
/**
 * Delete Employee Photo API Endpoint
 * Remove employee profile photo
 * 
 * @route DELETE /api/v1/employees/delete_photo.php?id=5
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Allow DELETE and POST (for compatibility)
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'DELETE' && $method !== 'POST') {
    ApiResponse::error('Method not allowed. Use DELETE or POST', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get employee ID from query string or POST
$employeeId = 0;
if ($method === 'DELETE') {
    $employeeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
} else {
    $employeeId = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
}

// If no employee_id provided, use authenticated user's ID
if (!$employeeId) {
    $employeeId = intval($userData['id']);
}

// Validate employee ID
if (!$employeeId) {
    ApiResponse::error('Employee ID is required', 400);
}

// Check if user is authorized to delete this photo
// Admin can delete any photo, Employee can only delete their own
if ($userData['role'] !== 'Admin' && intval($userData['id']) !== $employeeId) {
    ApiResponse::error('You are not authorized to delete this employee photo', 403);
}

// Verify employee exists and has a photo
$checkQuery = "SELECT employ_id, emp_name, dp FROM employees WHERE employ_id = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);

if (!$checkStmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($checkStmt, 'i', $employeeId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    mysqli_stmt_close($checkStmt);
    ApiResponse::error('Employee not found', 404);
}

$employee = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// Check if employee has a photo
if (empty($employee['dp'])) {
    ApiResponse::error('Employee has no photo to delete', 404);
}

// Delete photo by setting dp to NULL
$deleteQuery = "UPDATE employees SET dp = NULL WHERE employ_id = ?";
$deleteStmt = mysqli_prepare($con, $deleteQuery);

if (!$deleteStmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($deleteStmt, 'i', $employeeId);

if (!mysqli_stmt_execute($deleteStmt)) {
    mysqli_stmt_close($deleteStmt);
    ApiResponse::serverError('Failed to delete photo', mysqli_error($con));
}

mysqli_stmt_close($deleteStmt);

// Prepare response
$response = [
    'employee_id' => $employeeId,
    'employee_name' => $employee['emp_name'],
    'message' => 'Photo deleted successfully'
];

ApiResponse::success($response, 'Employee photo deleted successfully');
