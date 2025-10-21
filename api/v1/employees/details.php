<?php
/**
 * Employee Details API Endpoint
 * Get detailed information about a specific employee
 * 
 * @route GET /api/v1/employees/details.php?id=5
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

// Get employee ID
$employeeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$employeeId) {
    ApiResponse::error('Employee ID is required', 400);
}

// Get employee details
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
WHERE employ_id = ?";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'i', $employeeId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    ApiResponse::error('Employee not found', 404);
}

$employee = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Get attendance statistics
$statsQuery = "SELECT 
    COUNT(*) as total_attendance_records,
    SUM(CASE WHEN action = 'Clock In' THEN 1 ELSE 0 END) as total_clock_ins,
    SUM(CASE WHEN action = 'Clock Out' THEN 1 ELSE 0 END) as total_clock_outs
FROM attendance
WHERE employee_id = ?";

$statsStmt = mysqli_prepare($con, $statsQuery);
mysqli_stmt_bind_param($statsStmt, 'i', $employeeId);
mysqli_stmt_execute($statsStmt);
$statsResult = mysqli_stmt_get_result($statsStmt);
$stats = mysqli_fetch_assoc($statsResult);
mysqli_stmt_close($statsStmt);

// Get recent attendance records (last 10)
$attendanceQuery = "SELECT 
    id,
    action,
    date,
    time
FROM attendance
WHERE employee_id = ?
ORDER BY date DESC, time DESC
LIMIT 10";

$attendanceStmt = mysqli_prepare($con, $attendanceQuery);
mysqli_stmt_bind_param($attendanceStmt, 'i', $employeeId);
mysqli_stmt_execute($attendanceStmt);
$attendanceResult = mysqli_stmt_get_result($attendanceStmt);

$recentAttendance = [];
while ($row = mysqli_fetch_assoc($attendanceResult)) {
    $recentAttendance[] = [
        'id' => intval($row['id']),
        'action' => $row['action'],
        'date' => $row['date'],
        'time' => $row['time']
    ];
}
mysqli_stmt_close($attendanceStmt);

// Get salary payment statistics
$salaryQuery = "SELECT 
    COUNT(*) as total_payments,
    SUM(ABS(amount)) as total_paid
FROM trans
WHERE employee_id = ? AND description LIKE '%Salary%'";

$salaryStmt = mysqli_prepare($con, $salaryQuery);
mysqli_stmt_bind_param($salaryStmt, 'i', $employeeId);
mysqli_stmt_execute($salaryStmt);
$salaryResult = mysqli_stmt_get_result($salaryStmt);
$salaryStats = mysqli_fetch_assoc($salaryResult);
mysqli_stmt_close($salaryStmt);

// Prepare response
$response = [
    'id' => intval($employee['employ_id']),
    'name' => $employee['emp_name'],
    'mobile' => $employee['mobile'],
    'address' => $employee['address'],
    'bank_account' => $employee['bank_account'],
    'role' => $employee['role'],
    'nic' => $employee['nic'],
    'salary' => floatval($employee['salary']),
    'day_salary' => floatval($employee['day_salary']),
    'status' => intval($employee['status']) === 1 ? 'active' : 'inactive',
    'is_clocked_in' => (bool)$employee['is_clocked_in'],
    'onboard_date' => $employee['onboard_date'],
    'statistics' => [
        'attendance' => [
            'total_records' => intval($stats['total_attendance_records']),
            'total_clock_ins' => intval($stats['total_clock_ins']),
            'total_clock_outs' => intval($stats['total_clock_outs'])
        ],
        'salary' => [
            'total_payments' => intval($salaryStats['total_payments']),
            'total_paid' => floatval($salaryStats['total_paid'] ?? 0)
        ]
    ],
    'recent_attendance' => $recentAttendance
];

ApiResponse::success($response, 'Employee details retrieved successfully');
