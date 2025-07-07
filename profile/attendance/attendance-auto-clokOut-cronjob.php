<?php
session_start();
require_once '../../inc/config.php';

echo "Starting auto clock-out cron job at " . date('Y-m-d H:i:s') . "\n";

// Select Active Employees
$sql = "SELECT * FROM employees WHERE is_clocked_in = 1";
$result = mysqli_query($con, $sql);

if ($result) {
    $row_count = mysqli_num_rows($result);
    if ($row_count != 0) {
        echo "Found $row_count active employees to clock out\n";
        
        // Store all employees in an array first to avoid result conflicts
        $employees = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $employees[] = $row;
        }
        
        // Now process each employee
        foreach ($employees as $employee_data) {
            $employee_id = $employee_data['employ_id'];
            $employee_name = $employee_data['emp_name'];
            $action = 'Clock Out';

            echo "Processing employee: $employee_name (ID: $employee_id)\n";

            // Insert data into the attendance table
            $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
            insert_query($sql, "Attendance : Employee Name : $employee_name , Action : $action", "Attendance Auto Submitted by Cronjob");

            // Update the status of the employee in the employees table
            $sql = "UPDATE employees SET is_clocked_in = 0 WHERE employ_id = '$employee_id'";
            insert_query($sql, "Employee Name : $employee_name , Action : $action", "Attendance Auto Updated in Employee Profile by Cronjob");

            // Pay Day Salary When Clock Out
            // Get the employee's salary
            $sql = "SELECT day_salary FROM employees WHERE employ_id = '$employee_id'";
            $result_salary = mysqli_query($con, $sql);
            if (!$result_salary) {
                error_log('Error: Could not retrieve salary for employee ' . $employee_name);
                continue; // Skip to the next employee if an error occurs
            }

            $row_salary = mysqli_fetch_assoc($result_salary);
            $DaySalary = $row_salary['day_salary'];

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
                    error_log('Error: No Clock In Record Found for employee ' . $employee_name);
                    continue; // Skip to the next employee if no clock-in record is found
                }

                $row_clock_in = mysqli_fetch_assoc($result_clock_in);
                $last_clock_in_time = $row_clock_in['time'];

                // Get the employee's clock-out time
                $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock Out' ORDER BY id DESC LIMIT 1";
                $result_clock_out = mysqli_query($con, $sql);
                if (!$result_clock_out || mysqli_num_rows($result_clock_out) == 0) {
                    error_log('Error: No Clock Out Record Found for employee ' . $employee_name);
                    continue; // Skip to the next employee if no clock-out record is found
                }

                $row_clock_out = mysqli_fetch_assoc($result_clock_out);
                $last_clock_out_time = $row_clock_out['time'];

                // Calculate the working hours
                $clock_in = strtotime($last_clock_in_time);
                $clock_out = strtotime($last_clock_out_time);
                $emp_worked_hours = ($clock_out - $clock_in) / 3600;

                echo "Employee $employee_name worked $emp_worked_hours hours\n";

                // Working Time > $WorkingDayHours && Working Time <= $WorkingDayMaxHours 
                if ($emp_worked_hours >= $WorkingDayHours && $emp_worked_hours <= $WorkingDayMaxHours) {
                    $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today (Auto Added by Cronjob)')";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Auto Add Day Salary by Cronjob");

                    // Update the employee's salary in the employees table
                    $sql = "UPDATE employees SET salary = salary + '$DaySalary' WHERE employ_id = '$employee_id'";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Auto Update Employee Day Salary by Cronjob");
                    
                } elseif ($emp_worked_hours < $WorkingDayHours) {
                    $pay_amount = $emp_worked_hours * $HourSalary;
                    $emp_worked_hours_formatted = number_format($emp_worked_hours, 2);

                    $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$pay_amount', 'Day Salary - $today ($emp_worked_hours_formatted Hours) (Auto Added by Cronjob)')";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours_formatted", "Auto Add Day Salary by Cronjob");

                    // Update the employee's salary in the employees table
                    $sql = "UPDATE employees SET salary = salary + '$pay_amount' WHERE employ_id = '$employee_id'";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours_formatted", "Auto Update Employee Day Salary by Cronjob");
                    $DaySalary = $pay_amount;
                    
                } else {
                    // Overtime - still pay full salary but log it
                    $emp_worked_hours_formatted = number_format($emp_worked_hours, 2);
                    error_log("Warning: Employee $employee_name worked overtime ($emp_worked_hours_formatted hours)");
                    
                    $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today (Overtime: $emp_worked_hours_formatted Hours) (Auto Added by Cronjob)')";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today, Overtime Hours : $emp_worked_hours_formatted", "Auto Add Day Salary (Overtime) by Cronjob");
                    
                    $sql = "UPDATE employees SET salary = salary + '$DaySalary' WHERE employ_id = '$employee_id'";
                    insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today, Overtime Hours : $emp_worked_hours_formatted", "Auto Update Employee Day Salary (Overtime) by Cronjob");
                }

                // Deduct salary from Company Profit
                if ($DaySalary && $DaySalary > 0) {
                    $sql = "UPDATE accounts SET amount = amount - '{$DaySalary}' WHERE account_name = 'Company Profit'";
                    insert_query($sql, "Fall $employee_name's Day Salary from Company Profit : Rs. $DaySalary", "Fall Employee Day Salary from Company Profit Account");
                }
                
                echo "Processed salary for $employee_name: Rs. $DaySalary\n";
            } else {
                echo "Skipping salary calculation for $employee_name (day salary is 0)\n";
            }
        }
    } else {
        echo "No Active Employees Found for auto clock out\n";
    }
} else {
    error_log('Error in auto clock out query: ' . mysqli_error($con));
    echo "Database error occurred\n";
}

echo "Auto clock out cron job completed at " . date('Y-m-d H:i:s') . "\n";
?>
