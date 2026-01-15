<?php
/**
 * Employee Commission History API Endpoint
 * Get commission history for the logged-in employee
 * 
 * @route GET /api/v1/employees/commission_history.php?page=1&per_page=20
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
$employeeId = $userData['id'];

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(1, intval($_GET['per_page']))) : 20;
$offset = ($page - 1) * $perPage;

// Count total records
$countQuery = "SELECT COUNT(*) as total FROM employee_commission_history WHERE employee_id = ?";
$stmt = mysqli_prepare($con, $countQuery);
if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'i', $employeeId);
mysqli_stmt_execute($stmt);
$countResult = mysqli_stmt_get_result($stmt);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
mysqli_stmt_close($stmt);

// Fetch commission records
$query = "SELECT 
    invoice_number,
    product_name,
    product_profit,
    commission_percentage,
    commission_amount,
    created_at
FROM employee_commission_history 
WHERE employee_id = ?
ORDER BY created_at DESC
LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($con, $query);
if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'iii', $employeeId, $perPage, $offset);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$history = [];
while ($row = mysqli_fetch_assoc($result)) {
    $history[] = [
        'invoice_number' => intval($row['invoice_number']),
        'product_name' => $row['product_name'],
        'product_profit' => floatval($row['product_profit']),
        'commission_percentage' => floatval($row['commission_percentage']),
        'commission_amount' => floatval($row['commission_amount']),
        'date' => $row['created_at']
    ];
}
mysqli_stmt_close($stmt);

// Get total earnings summary
$summaryQuery = "SELECT SUM(commission_amount) as total_earned FROM employee_commission_history WHERE employee_id = ?";
$stmt = mysqli_prepare($con, $summaryQuery);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $employeeId);
    mysqli_stmt_execute($stmt);
    $summaryResult = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($summaryResult);
    $totalEarned = $row['total_earned'] ? floatval($row['total_earned']) : 0.00;
    mysqli_stmt_close($stmt);
} else {
    $totalEarned = 0.00;
}

ApiResponse::paginated($history, intval($totalRecords), $page, $perPage, 'Commission history retrieved', [
    'total_earnings' => $totalEarned
]);
