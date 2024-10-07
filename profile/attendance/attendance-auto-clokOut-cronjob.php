<?php
session_start();
require_once '../../inc/config.php';

// Select Active Employees
$sql = "SELECT * FROM employees WHERE is_clocked_in = 1";
$result = mysqli_query($con, $sql);
if ($result) {
    $row_count = mysqli_num_rows($result);
    if ($row_count != 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $employee_id = $row['employ_id'];
            $employee_name = $row['emp_name'];
            $action = 'Clock Out';

            // Insert data into the attendance table
            $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
            // echo $sql;
            insert_query($sql, "Attendance : Employee Name : $employee_name , Action : $action", "Attendance Auto Submitted by Cronjob");

            // Update the status of the employee in the employees table
            $sql = "UPDATE employees SET is_clocked_in = 0 WHERE employ_id = '$employee_id'";
            // echo $sql;
            insert_query($sql, "Employee Name : $employee_name , Action : $action", "Attendance Auto Updated in Employee Profile by Cronjob");

            // Pay Day Salary When Clock Out
            if ($action == 'Clock Out') {
                // Get the employee's salary
                $sql = "SELECT day_salary FROM employees WHERE employ_id = '$employee_id'";
                $result_salary = mysqli_query($con, $sql);
                if (!$result_salary) {
                    error_log('Error: Could not retrieve salary for employee ' . $employee_name);
                    continue; // Skip to the next employee if an error occurs
                }

                $row = mysqli_fetch_assoc($result_salary);
                $DaySalary = $row['day_salary'];

                if ($DaySalary > 0) {
                    $WorkingDayHours = 8;
                    $WorkingDayMaxHours = 17;
                    $HourSalary = $DaySalary / $WorkingDayHours;

                    // Today
                    $today = date('Y-m-d'); //for production
                    // $today = '2024-08-06'; // for debugging

                    // Get the employee's clock in time
                    $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock In' ORDER BY id DESC LIMIT 1;";
                    // echo $sql;
                    $result_clock_in = mysqli_query($con, $sql);
                    if (!$result_clock_in || mysqli_num_rows($result_clock_in) == 0) {
                        error_log('Error: No Clock In Record Found for employee ' . $employee_name);
                        continue; // Skip to the next employee if no clock-in record is found
                    }

                    $row = mysqli_fetch_assoc($result_clock_in);
                    $last_clock_in_time = $row['time'];
                    $last_clock_in_date = $row['date'];


                    // Get the employee's clock-out time
                    $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock Out' ORDER BY id DESC LIMIT 1";
                    $result_clock_out = mysqli_query($con, $sql);
                    if (!$result_clock_out || mysqli_num_rows($result_clock_out) == 0) {
                        error_log('Error: No Clock Out Record Found for employee ' . $employee_name);
                        continue; // Skip to the next employee if no clock-out record is found
                    }

                    $row = mysqli_fetch_assoc($result_clock_out);
                    $last_clock_out_time = $row['time'];
                    $last_clock_out_date = $row['date'];

                    // Calculate the working hours
                    $clock_in = strtotime($last_clock_in_time);
                    $clock_out = strtotime($last_clock_out_time);
                    $emp_worked_hours = ($clock_out - $clock_in) / 3600;

                    // Pay according to worked hours
                    if ($emp_worked_hours > $WorkingDayHours && $emp_worked_hours < $WorkingDayMaxHours) {
                        $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today (Auto Added by Cronjob)')";
                        insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Auto Add Day Salary by Cronjob");

                        $sql = "UPDATE employees SET salary = salary + '$DaySalary' WHERE employ_id = '$employee_id'";
                        insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Auto Update Employee Day Salary by Cronjob");
                    } elseif ($emp_worked_hours < $WorkingDayHours) {
                        $pay_amount = $emp_worked_hours * $HourSalary;
                        $emp_worked_hours = number_format($emp_worked_hours, 2);

                        $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$pay_amount', 'Day Salary - $today ($emp_worked_hours Hours) (Auto Added by Cronjob)')";
                        insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours", "Auto Add Day Salary by Cronjob");

                        $sql = "UPDATE employees SET salary = salary + '$pay_amount' WHERE employ_id = '$employee_id'";
                        insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours", "Auto Update Employee Day Salary by Cronjob");
                        $DaySalary = $pay_amount;
                    } elseif ($emp_worked_hours > $WorkingDayMaxHours) {
                        error_log("Error: Working Hours More Than $WorkingDayMaxHours Hours for employee " . $employee_name);
                    } else {
                        error_log('Error: Unknown Error for employee ' . $employee_name);
                    }

                    // Deduct salary from Company Profit
                    if ($DaySalary > 0) {
                        $sql = "UPDATE accounts SET amount = amount - '{$DaySalary}' WHERE account_name = 'Company Profit'";
                        insert_query($sql, "Fall $employee_name's Day Salary from Company Profit : Rs. $DaySalary", "Fall Employee Day Salary from Company Profit Account");
                    }
                }
            } else {
                error_log('Error: Invalid action for employee ' . $employee_name);
            }
        }
    } else {
        echo 'Error: No Active Employees Found';
        return; // Stop the execution if no active employees are found
    }
}
