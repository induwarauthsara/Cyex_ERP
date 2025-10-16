<?php
/**
 * Pay Salary API
 * 
 * Process employee salary payment
 * POST /api/v1/salary/pay.php
 * 
 * Admin only
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate and require admin role
$user = ApiAuth::requireAuth();
if ($user['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Only allow POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    $required = ['employee_id', 'amount', 'account'];
    $errors = [];
    
    foreach ($required as $field) {
        if (!isset($input[$field]) || trim($input[$field]) === '') {
            $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    if (!empty($errors)) {
        ApiResponse::validationError('Validation failed', $errors);
    }
    
    $employee_id = intval($input['employee_id']);
    $amount = floatval($input['amount']);
    $account = mysqli_real_escape_string($con, $input['account']);
    $description = isset($input['description']) ? mysqli_real_escape_string($con, $input['description']) : 'Salary Paid';
    
    // Validate amount
    if ($amount <= 0) {
        ApiResponse::error('Amount must be greater than zero', 422);
    }
    
    // Start transaction
    mysqli_begin_transaction($con);
    
    try {
        // 1. Add Record to Salary Table
        $sql = "INSERT INTO salary (emp_id, amount, description) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        $negative_amount = -$amount;
        mysqli_stmt_bind_param($stmt, 'ids', $employee_id, $negative_amount, $description);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to record salary payment");
        }
        
        // 2. Update Employee Salary (deduct from balance)
        $sql = "UPDATE employees SET salary = salary - ? WHERE employ_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'di', $amount, $employee_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to update employee salary balance");
        }
        
        // 3. Deduct from Account
        $sql = "UPDATE accounts SET amount = amount - ? WHERE account_name = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ds', $amount, $account);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to deduct from account");
        }
        
        // 4. Add Transaction Log
        $action = "Salary Payment";
        $msg = "Salary paid to Employee ID: $employee_id, Rs. $amount from $account account";
        $sql = "INSERT INTO transaction_log (transaction_type, description, amount, transaction_date, employee_id) 
                VALUES (?, ?, ?, CURDATE(), ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 'ssdi', $action, $msg, $negative_amount, $user['id']);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Failed to log transaction");
        }
        
        // Commit transaction
        mysqli_commit($con);
        
        // Get employee name
        $emp_query = "SELECT name FROM employees WHERE employ_id = ?";
        $stmt = mysqli_prepare($con, $emp_query);
        mysqli_stmt_bind_param($stmt, 'i', $employee_id);
        mysqli_stmt_execute($stmt);
        $emp_result = mysqli_stmt_get_result($stmt);
        $emp_data = mysqli_fetch_assoc($emp_result);
        
        // Return success
        ApiResponse::success('Salary paid successfully', [
            'employee' => [
                'id' => $employee_id,
                'name' => $emp_data['name'] ?? 'Unknown'
            ],
            'amount' => $amount,
            'account' => $account,
            'description' => $description,
            'paid_by' => [
                'id' => $user['id'],
                'name' => $user['name']
            ],
            'paid_at' => date('Y-m-d H:i:s')
        ], 201);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to process salary payment', 500);
    }
}
