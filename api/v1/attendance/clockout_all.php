<?php
/**
 * Clock Out All Employees API Endpoint
 * Admin-only endpoint to clock out all currently clocked-in employees
 * Uses the same logic as the cron job but accessible via API
 * 
 * @route POST /api/v1/attendance/clockout_all.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';
require_once __DIR__ . '/../expenses/sync_salary_expense.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Require authentication and admin role
$userData = ApiAuth::requireAuth();
if ($userData['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Include main config for database operations
require_once __DIR__ . '/../../../inc/config.php';

try {
    // Select Active Employees (those who are clocked in)
    $sql = "SELECT * FROM employees WHERE is_clocked_in = 1";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($con));
    }

    $row_count = mysqli_num_rows($result);
    
    if ($row_count == 0) {
        ApiResponse::success([], [
            'message' => 'No active employees found to clock out',
            'employees_clocked_out' => 0
        ]);
    }

    // Store all employees in an array first to avoid result conflicts
    $employees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $employees[] = $row;
    }

    $processed_employees = [];
    $errors = [];
    $total_processed = 0;

    // Process each employee
    foreach ($employees as $employee_data) {
        $employee_id = $employee_data['employ_id'];
        $employee_name = $employee_data['emp_name'];
        $action = 'Clock Out';

        try {
            // Insert data into the attendance table
            $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
            insert_query($sql, "Attendance : Employee Name : $employee_name , Action : $action (Admin API)", "Attendance Auto Submitted by Admin via API");

            // Update the status of the employee in the employees table
            $sql = "UPDATE employees SET is_clocked_in = 0 WHERE employ_id = '$employee_id'";
            insert_query($sql, "Employee Name : $employee_name , Action : $action (Admin API)", "Attendance Auto Updated in Employee Profile by Admin via API");

            // Pay Day Salary When Clock Out
            // Get the employee's salary
            $sql = "SELECT day_salary FROM employees WHERE employ_id = '$employee_id'";
            $result_salary = mysqli_query($con, $sql);
            if (!$result_salary) {
                throw new Exception('Could not retrieve salary for employee ' . $employee_name);
            }

            $row_salary = mysqli_fetch_assoc($result_salary);
            $DaySalary = $row_salary['day_salary'];

            $salary_paid = 0;
            $worked_hours = 0;

            if ($DaySalary > 0) {
                $WorkingDayHours = 8;
                $WorkingDayMaxHours = 17;
                $HourSalary = $DaySalary / $WorkingDayHours;

                // Today
                $today = date('Y-m-d');

                // Get the employee's clock in time
                $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock In' ORDER BY id DESC LIMIT 1";
                $result_clock_in = mysqli_query($con, $sql);
                if (!$result_clock_in || mysqli_num_rows($result_clock_in) == 0) {
                    throw new Exception('No Clock In Record Found for employee ' . $employee_name);
                }

                $row_clock_in = mysqli_fetch_assoc($result_clock_in);
                $last_clock_in_time = $row_clock_in['time'];

                // Get the employee's clock-out time
                $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock Out' ORDER BY id DESC LIMIT 1";
                $result_clock_out = mysqli_query($con, $sql);
                if (!$result_clock_out || mysqli_num_rows($result_clock_out) == 0) {
                    throw new Exception('No Clock Out Record Found for employee ' . $employee_name);
                }

                $row_clock_out = mysqli_fetch_assoc($result_clock_out);
                $last_clock_out_time = $row_clock_out['time'];

                // Calculate the working hours
                $clock_in = strtotime($last_clock_in_time);
                $clock_out = strtotime($last_clock_out_time);
                $emp_worked_hours = ($clock_out - $clock_in) / 3600;
                $worked_hours = round($emp_worked_hours, 2);

                // Working Time >= $WorkingDayHours && Working Time <= $WorkingDayMaxHours 
                if ($emp_worked_hours >= $WorkingDayHours && $emp_worked_hours <= $WorkingDayMaxHours) {
                    $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today (Admin API)')";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Add Day Salary by Admin via API");

                    // Update the employee's salary in the employees table
                    $sql = "UPDATE employees SET salary = salary + '$DaySalary' WHERE employ_id = '$employee_id'";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Update Employee Day Salary by Admin via API");
                    
                    $salary_paid = $DaySalary;
                    
                    // Sync with expenses
                    syncSalaryExpense($con, $employee_id, $employee_name, date('Y-m'));
                    
                } elseif ($emp_worked_hours < $WorkingDayHours) {
                    $pay_amount = $emp_worked_hours * $HourSalary;
                    $emp_worked_hours_formatted = number_format($emp_worked_hours, 2);

                    $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$pay_amount', 'Day Salary - $today ($emp_worked_hours_formatted Hours) (Admin API)')";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours_formatted", "Add Day Salary by Admin via API");

                    // Update the employee's salary in the employees table
                    $sql = "UPDATE employees SET salary = salary + '$pay_amount' WHERE employ_id = '$employee_id'";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours_formatted", "Update Employee Day Salary by Admin via API");
                    
                    $salary_paid = $pay_amount;
                    
                    // Sync with expenses
                    syncSalaryExpense($con, $employee_id, $employee_name, date('Y-m'));
                    
                } else {
                    // Overtime - still pay full salary but log it
                    $emp_worked_hours_formatted = number_format($emp_worked_hours, 2);
                    
                    $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today (Overtime: $emp_worked_hours_formatted Hours) (Admin API)')";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today, Overtime Hours : $emp_worked_hours_formatted", "Add Day Salary (Overtime) by Admin via API");
                    
                    $sql = "UPDATE employees SET salary = salary + '$DaySalary' WHERE employ_id = '$employee_id'";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today, Overtime Hours : $emp_worked_hours_formatted", "Update Employee Day Salary (Overtime) by Admin via API");
                    
                    $salary_paid = $DaySalary;
                    
                    // Sync with expenses
                    syncSalaryExpense($con, $employee_id, $employee_name, date('Y-m'));
                }

                // Deduct salary from Company Profit
                if ($salary_paid > 0) {
                    $sql = "UPDATE accounts SET amount = amount - '{$salary_paid}' WHERE account_name = 'Company Profit'";
                    insert_query($sql, "Fall $employee_name's Day Salary from Company Profit : Rs. $salary_paid", "Fall Employee Day Salary from Company Profit Account");
                }
            }

            $processed_employees[] = [
                'employee_id' => $employee_id,
                'employee_name' => $employee_name,
                'worked_hours' => $worked_hours,
                'salary_paid' => $salary_paid,
                'status' => 'success'
            ];
            
            $total_processed++;

        } catch (Exception $e) {
            $errors[] = [
                'employee_id' => $employee_id,
                'employee_name' => $employee_name,
                'error' => $e->getMessage()
            ];
        }
    }

    // Prepare response
    $response_data = [
        'employees_clocked_out' => $total_processed,
        'total_found' => $row_count,
        'processed_employees' => $processed_employees
    ];

    if (!empty($errors)) {
        $response_data['errors'] = $errors;
    }

    ApiResponse::success($response_data, [
        'message' => "Successfully clocked out $total_processed out of $row_count employees",
        'timestamp' => date('Y-m-d H:i:s')
    ], 200);

} catch (Exception $e) {
    ApiResponse::error('Failed to process clock out: ' . $e->getMessage(), 500);
}
