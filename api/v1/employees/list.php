<?php
/**
 * Employee List API Endpoint
 * Get list of employees with filtering and pagination
 * 
 * @route GET /api/v1/employees/list.php?page=1&per_page=20&status=all
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed. Use GET', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
$offset = ($page - 1) * $perPage;

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all'; // all, active, inactive
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$role = isset($_GET['role']) ? $_GET['role'] : null; // Employee, Admin

// Build WHERE clause
$whereConditions = ["1=1"];
$params = [];
$types = '';

// Status filter
if ($status === 'active') {
    $whereConditions[] = "status = 1";
} elseif ($status === 'inactive') {
    $whereConditions[] = "status = 0";
}

// Search filter
if ($search) {
    $whereConditions[] = "(emp_name LIKE ? OR mobile LIKE ? OR nic LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

// Role filter
if ($role) {
    $whereConditions[] = "role = ?";
    $params[] = $role;
    $types .= 's';
}

$whereClause = "WHERE " . implode(" AND ", $whereConditions);

// Count total records
$countQuery = "SELECT COUNT(*) as total 
    FROM employees
    $whereClause";

if (!empty($params)) {
    $countStmt = mysqli_prepare($con, $countQuery);
    if ($types) {
        mysqli_stmt_bind_param($countStmt, $types, ...$params);
    }
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
} else {
    $countResult = mysqli_query($con, $countQuery);
}

if (!$countResult) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

$totalRecords = mysqli_fetch_assoc($countResult)['total'];

// Get employees
$query = "SELECT 
    employ_id,
    emp_name,
    mobile,
    address,
    bank_account,
    role,
    nic,
    salary,
    day_salary,
    status,
    is_clocked_in,
    onboard_date
FROM employees
$whereClause
ORDER BY emp_name ASC
LIMIT ? OFFSET ?";

// Add pagination params
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

if ($types) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$employees = [];
while ($row = mysqli_fetch_assoc($result)) {
    $employees[] = [
        'id' => intval($row['employ_id']),
        'name' => $row['emp_name'],
        'mobile' => $row['mobile'],
        'address' => $row['address'],
        'bank_account' => $row['bank_account'],
        'role' => $row['role'],
        'nic' => $row['nic'],
        'salary' => floatval($row['salary']),
        'day_salary' => floatval($row['day_salary']),
        'status' => intval($row['status']) === 1 ? 'active' : 'inactive',
        'is_clocked_in' => (bool)$row['is_clocked_in'],
        'onboard_date' => $row['onboard_date']
    ];
}

mysqli_stmt_close($stmt);

ApiResponse::paginated($employees, intval($totalRecords), $page, $perPage, 'Employees retrieved successfully');
