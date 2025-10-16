<?php
/**
 * Get Attendance Status API Endpoint
 * Get current clock in/out status of employee
 * 
 * @route GET /api/v1/attendance/status.php
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

$employeeId = $userData['employee_id'];

// Get current status
$statusQuery = "SELECT is_clocked_in FROM employees WHERE employ_id = ?";
$statusStmt = mysqli_prepare($con, $statusQuery);
mysqli_stmt_bind_param($statusStmt, 'i', $employeeId);
mysqli_stmt_execute($statusStmt);
$statusResult = mysqli_stmt_get_result($statusStmt);
$status = mysqli_fetch_assoc($statusResult);
mysqli_stmt_close($statusStmt);

if (!$status) {
    ApiResponse::notFound('Employee not found');
}

$isClockedIn = (bool)$status['is_clocked_in'];

// Get today's attendance records
$today = date('Y-m-d');
$attendanceQuery = "SELECT action, time FROM attendance WHERE employee_id = ? AND date = ? ORDER BY time ASC";
$attendanceStmt = mysqli_prepare($con, $attendanceQuery);
mysqli_stmt_bind_param($attendanceStmt, 'is', $employeeId, $today);
mysqli_stmt_execute($attendanceStmt);
$attendanceResult = mysqli_stmt_get_result($attendanceStmt);

$todayRecords = [];
$clockInTime = null;
$hoursWorked = 0;

while ($record = mysqli_fetch_assoc($attendanceResult)) {
    $todayRecords[] = [
        'action' => $record['action'],
        'time' => $record['time']
    ];
    
    if ($record['action'] === 'Clock In') {
        $clockInTime = $record['time'];
    }
}

mysqli_stmt_close($attendanceStmt);

// Calculate hours worked if clocked in
if ($isClockedIn && $clockInTime) {
    $clockIn = strtotime($today . ' ' . $clockInTime);
    $now = time();
    $hoursWorked = ($now - $clockIn) / 3600;
}

ApiResponse::success([
    'is_clocked_in' => $isClockedIn,
    'clock_in_time' => $clockInTime,
    'hours_worked' => round($hoursWorked, 2),
    'today_records' => $todayRecords,
    'next_action' => $isClockedIn ? 'Clock Out' : 'Clock In'
], 'Attendance status retrieved successfully');
