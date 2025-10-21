<?php
/**
 * Delete Employee API Endpoint
 * Delete/deactivate an employee
 * 
 * @route DELETE /api/v1/employees/delete.php
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

// Require admin authentication
$userData = ApiAuth::requireAuth(['Admin']);

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    ApiResponse::error('Invalid JSON input', 400);
}

// Validate employee ID
if (empty($input['id'])) {
    ApiResponse::error('Employee ID is required', 400);
}

$employeeId = intval($input['id']);

// Check if employee exists
$checkQuery = "SELECT employ_id, emp_name, status FROM employees WHERE employ_id = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 'i', $employeeId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    ApiResponse::error('Employee not found', 404);
}

$employee = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// Prevent deleting self
if ($employeeId === intval($userData['employee_id'])) {
    ApiResponse::error('Cannot delete your own account', 400);
}

// Check delete mode - soft delete (deactivate) or hard delete
$hardDelete = isset($input['hard_delete']) && $input['hard_delete'] === true;

if ($hardDelete) {
    // Hard delete - actually remove from database
    // Note: This might fail if there are foreign key constraints
    $deleteQuery = "DELETE FROM employees WHERE employ_id = ?";
    $deleteStmt = mysqli_prepare($con, $deleteQuery);
    mysqli_stmt_bind_param($deleteStmt, 'i', $employeeId);
    
    if (!mysqli_stmt_execute($deleteStmt)) {
        $error = mysqli_error($con);
        mysqli_stmt_close($deleteStmt);
        
        // Check if error is due to foreign key constraint
        if (strpos($error, 'foreign key constraint') !== false || strpos($error, 'Cannot delete') !== false) {
            ApiResponse::error('Cannot delete employee with existing records. Consider deactivating instead.', 409);
        }
        
        ApiResponse::serverError('Failed to delete employee', $error);
    }
    
    mysqli_stmt_close($deleteStmt);
    $actionType = 'Employee Deleted (Hard)';
    $message = 'Employee permanently deleted successfully';
} else {
    // Soft delete - deactivate employee
    $deactivateQuery = "UPDATE employees SET status = 0 WHERE employ_id = ?";
    $deactivateStmt = mysqli_prepare($con, $deactivateQuery);
    mysqli_stmt_bind_param($deactivateStmt, 'i', $employeeId);
    
    if (!mysqli_stmt_execute($deactivateStmt)) {
        ApiResponse::serverError('Failed to deactivate employee', mysqli_error($con));
    }
    
    mysqli_stmt_close($deactivateStmt);
    $actionType = 'Employee Deactivated';
    $message = 'Employee deactivated successfully';
}

// Log action
$actionDesc = "Employee {$employee['emp_name']} (ID: $employeeId) " . ($hardDelete ? 'permanently deleted' : 'deactivated');
$logQuery = "INSERT INTO action_log (employee_id, action, description) VALUES (?, ?, ?)";
$logStmt = mysqli_prepare($con, $logQuery);
mysqli_stmt_bind_param($logStmt, 'iss', $userData['employee_id'], $actionType, $actionDesc);
mysqli_stmt_execute($logStmt);
mysqli_stmt_close($logStmt);

$response = [
    'id' => $employeeId,
    'name' => $employee['emp_name'],
    'action' => $hardDelete ? 'deleted' : 'deactivated'
];

ApiResponse::success($response, $message);
