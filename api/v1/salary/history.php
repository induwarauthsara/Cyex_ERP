<?php
/**
 * Salary History API
 * 
 * Get salary payment history
 * GET /api/v1/salary/history.php?employee_id=5
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

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get query parameters
    $employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : null;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $per_page = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
    $offset = ($page - 1) * $per_page;
    
    // Build employee filter
    $employee_condition = '';
    $params = [];
    $types = '';
    
    if ($employee_id !== null) {
        $employee_condition = "WHERE s.emp_id = ?";
        $params[] = $employee_id;
        $types .= 'i';
    }
    
    // Count total records
    $count_query = "SELECT COUNT(*) as total FROM salary s $employee_condition";
    
    if ($employee_id !== null) {
        $stmt = mysqli_prepare($con, $count_query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $count_result = mysqli_stmt_get_result($stmt);
    } else {
        $count_result = mysqli_query($con, $count_query);
    }
    
    $total = mysqli_fetch_assoc($count_result)['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get salary records
    $query = "SELECT 
                s.salary_id,
                s.emp_id,
                s.date,
                s.amount,
                s.description,
                e.emp_name as employee_name
              FROM salary s
              LEFT JOIN employees e ON s.emp_id = e.employ_id
              $employee_condition
              ORDER BY s.date DESC, s.salary_id DESC
              LIMIT $per_page OFFSET $offset";
    
    if ($employee_id !== null) {
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($con, $query);
    }
    
    $salary_records = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $salary_records[] = [
            'id' => (int)$row['salary_id'],
            'employee' => [
                'id' => (int)$row['emp_id'],
                'name' => $row['employee_name']
            ],
            'date' => $row['date'],
            'amount' => (float)$row['amount'],
            'description' => $row['description'],
            'type' => $row['amount'] < 0 ? 'payment' : 'accrual'
        ];
    }
    
    // Return paginated response
    ApiResponse::paginated($salary_records, $total, $page, $per_page, 'Salary history retrieved successfully');
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to retrieve salary history', 500);
    }
}
