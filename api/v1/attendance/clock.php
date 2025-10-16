<?php
/**
 * Clock In/Out API Endpoint
 * Record employee attendance (clock in/out)
 * 
 * @route POST /api/v1/attendance/clock.php
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

// Require authentication
$userData = ApiAuth::requireAuth();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
$errors = [];

if (empty($input['action'])) {
    $errors['action'] = 'Action is required';
} elseif (!in_array($input['action'], ['Clock In', 'Clock Out'])) {
    $errors['action'] = 'Action must be either "Clock In" or "Clock Out"';
}

if (!empty($errors)) {
    ApiResponse::validationError($errors);
}

$employeeId = $userData['employee_id'];
$employeeName = $userData['employee_name'];
$action = $input['action'];

// Check current clock status
$statusQuery = "SELECT is_clocked_in FROM employees WHERE employ_id = ?";
$statusStmt = mysqli_prepare($con, $statusQuery);
mysqli_stmt_bind_param($statusStmt, 'i', $employeeId);
mysqli_stmt_execute($statusStmt);
$statusResult = mysqli_stmt_get_result($statusStmt);
$currentStatus = mysqli_fetch_assoc($statusResult);
mysqli_stmt_close($statusStmt);

if (!$currentStatus) {
    ApiResponse::error('Employee not found', 404);
}

$isClockedIn = (bool)$currentStatus['is_clocked_in'];

// Validate action against current status
if ($action === 'Clock In' && $isClockedIn) {
    ApiResponse::error('Already clocked in. Please clock out first.', 400);
}

if ($action === 'Clock Out' && !$isClockedIn) {
    ApiResponse::error('Not clocked in. Please clock in first.', 400);
}

// Insert attendance record
$insertQuery = "INSERT INTO attendance (employee_id, action, date, time) VALUES (?, ?, CURRENT_DATE, CURRENT_TIME)";
$insertStmt = mysqli_prepare($con, $insertQuery);

if (!$insertStmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($insertStmt, 'is', $employeeId, $action);
$success = mysqli_stmt_execute($insertStmt);

if (!$success) {
    mysqli_stmt_close($insertStmt);
    ApiResponse::serverError('Failed to record attendance', mysqli_error($con));
}

$attendanceId = mysqli_insert_id($con);
mysqli_stmt_close($insertStmt);

// Update employee clock status
$newStatus = ($action === 'Clock In') ? 1 : 0;
$updateQuery = "UPDATE employees SET is_clocked_in = ? WHERE employ_id = ?";
$updateStmt = mysqli_prepare($con, $updateQuery);
mysqli_stmt_bind_param($updateStmt, 'ii', $newStatus, $employeeId);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

// Log action
insert_query(
    "INSERT INTO action_log (employee_id, action, description, date, time) VALUES ($employeeId, 'Attendance', 'Employee: $employeeName, Action: $action', CURRENT_DATE, CURRENT_TIME)",
    "Attendance: $employeeName - $action",
    "Attendance"
);

// Handle salary calculation on Clock Out
$salaryInfo = null;
if ($action === 'Clock Out') {
    // Get employee's day salary
    $salaryQuery = "SELECT day_salary FROM employees WHERE employ_id = ?";
    $salaryStmt = mysqli_prepare($con, $salaryQuery);
    mysqli_stmt_bind_param($salaryStmt, 'i', $employeeId);
    mysqli_stmt_execute($salaryStmt);
    $salaryResult = mysqli_stmt_get_result($salaryStmt);
    $salaryRow = mysqli_fetch_assoc($salaryResult);
    mysqli_stmt_close($salaryStmt);
    
    $daySalary = floatval($salaryRow['day_salary'] ?? 0);
    
    if ($daySalary > 0) {
        $workingDayHours = 8;
        $workingDayMaxHours = 17;
        $today = date('Y-m-d');
        
        // Get clock in time
        $clockInQuery = "SELECT time FROM attendance WHERE employee_id = ? AND date = ? AND action = 'Clock In' ORDER BY id DESC LIMIT 1";
        $clockInStmt = mysqli_prepare($con, $clockInQuery);
        mysqli_stmt_bind_param($clockInStmt, 'is', $employeeId, $today);
        mysqli_stmt_execute($clockInStmt);
        $clockInResult = mysqli_stmt_get_result($clockInStmt);
        $clockInRow = mysqli_fetch_assoc($clockInResult);
        mysqli_stmt_close($clockInStmt);
        
        // Get clock out time
        $clockOutQuery = "SELECT time FROM attendance WHERE employee_id = ? AND date = ? AND action = 'Clock Out' ORDER BY id DESC LIMIT 1";
        $clockOutStmt = mysqli_prepare($con, $clockOutQuery);
        mysqli_stmt_bind_param($clockOutStmt, 'is', $employeeId, $today);
        mysqli_stmt_execute($clockOutStmt);
        $clockOutResult = mysqli_stmt_get_result($clockOutStmt);
        $clockOutRow = mysqli_fetch_assoc($clockOutResult);
        mysqli_stmt_close($clockOutStmt);
        
        if ($clockInRow && $clockOutRow) {
            $clockIn = strtotime($clockInRow['time']);
            $clockOut = strtotime($clockOutRow['time']);
            $hoursWorked = ($clockOut - $clockIn) / 3600;
            
            $salaryPaid = 0;
            $salaryReason = '';
            
            if ($hoursWorked >= $workingDayHours && $hoursWorked <= $workingDayMaxHours) {
                // Full day salary
                $salaryPaid = $daySalary;
                $salaryReason = "Full day salary";
                
                insert_query(
                    "INSERT INTO salary (emp_id, date, amount, description) VALUES ($employeeId, '$today', $daySalary, 'Day Salary - $today')",
                    "Employee: $employeeName, Salary: $daySalary",
                    "Add Day Salary"
                );
                
                mysqli_query($con, "UPDATE employees SET salary = salary + $daySalary WHERE employ_id = $employeeId");
                
            } elseif ($hoursWorked < $workingDayHours) {
                // Partial salary based on hours
                $hourSalary = $daySalary / $workingDayHours;
                $salaryPaid = $hourSalary * $hoursWorked;
                $salaryReason = "Partial salary (" . round($hoursWorked, 2) . " hours)";
                
                insert_query(
                    "INSERT INTO salary (emp_id, date, amount, description) VALUES ($employeeId, '$today', $salaryPaid, 'Partial Salary - $today - $hoursWorked hours')",
                    "Employee: $employeeName, Salary: $salaryPaid",
                    "Add Partial Salary"
                );
                
                mysqli_query($con, "UPDATE employees SET salary = salary + $salaryPaid WHERE employ_id = $employeeId");
            }
            
            $salaryInfo = [
                'hours_worked' => round($hoursWorked, 2),
                'salary_paid' => round($salaryPaid, 2),
                'reason' => $salaryReason
            ];
        }
    }
}

// Return response
ApiResponse::success([
    'attendance_id' => $attendanceId,
    'action' => $action,
    'employee' => [
        'id' => $employeeId,
        'name' => $employeeName
    ],
    'timestamp' => date('Y-m-d H:i:s'),
    'is_clocked_in' => ($action === 'Clock In'),
    'salary_info' => $salaryInfo
], $action . ' recorded successfully', 201);
