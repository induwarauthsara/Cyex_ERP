<?php
session_start();
require_once '../../inc/config.php';

if (
    isset($_SESSION['employee_name']) &&
    isset($_GET['employee_id']) &&
    isset($_GET['employee_name']) &&
    (isset($_GET['action']) || $_GET['action'] == 'Clock In' || $_GET['action'] == 'Clock Out')
) {
    $admin_name = $_SESSION['employee_name'];
    $employee_id = $_GET['employee_id'];
    $employee_name = $_GET['employee_name'];
    $action = $_GET['action'];

    // Insert data into the attendance table
    $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
    // echo $sql;
    insert_query($sql, "Attendance : Employee Name : $employee_name , Action : $action by ACP ($admin_name)", "Attendance Submitted ");

    // Update the status of the employee in the employees table
    if ($action == 'Clock In') {
        $sql = "UPDATE employees SET is_clocked_in = 1 WHERE employ_id = '$employee_id'";
    } else {
        $sql = "UPDATE employees SET is_clocked_in = 0 WHERE employ_id = '$employee_id'";
    }
    // echo $sql;
    insert_query($sql, "Employee Name : $employee_name , Action : $action by ACP ($admin_name)", "Attendance Updated in Employee Profile");

    // Pay Day Salary When Clock Out
    if ($action == 'Clock Out') {
        // Get the employee's salary
        $sql = "SELECT day_salary FROM employees WHERE employ_id = '$employee_id'";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);
        $DaySalary = $row['day_salary'];

        if ($DaySalary > 0) {

            // Employee Should be Working for 8 Hours
            $WorkingDayHours = 8;
            $WorkingDayMaxHours = 17;
            $HourSalary = $DaySalary / $WorkingDayHours;

            // Today
            $today = date('Y-m-d'); //for production
            // $today = '2024-08-06'; // for debugging

            // Get the employee's clock in time
            $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock In' ORDER BY id DESC LIMIT 1;";
            // echo $sql;
            $result = mysqli_query($con, $sql);
            if ($result) {
                $row_count = mysqli_num_rows($result);
                if ($row_count == 0) {
                    die('Error: No Clock In Record Found');
                }
                $row = mysqli_fetch_assoc($result);
                $last_clock_in_time = $row['time'];
                $last_clock_in_date = $row['date'];
                // echo "<br>Last Clock In Time: $last_clock_in_time <br>"; // for debugging
            } else {
                die('Error: ' . mysqli_error($con));
            }

            // Get the employee's clock out time
            $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock Out' ORDER BY id DESC LIMIT 1";
            // echo $sql ."<br>"; // for debugging
            $result = mysqli_query($con, $sql);
            if ($result) {
                $row_count = mysqli_num_rows($result);
                if ($row_count == 0) {
                    die('Error: No Clock Out Record Found');
                }
                $row = mysqli_fetch_assoc($result);
                $last_clock_out_time = $row['time'];
                $last_clock_out_date = $row['date'];
                // echo "<br>Last Clock Out Time: $last_clock_out_time <br>"; // for debugging
            } else {
                die('Error: ' . mysqli_error($con));
            }

            // Calculate the working hours
            $clock_in = strtotime($last_clock_in_time);
            $clock_out = strtotime($last_clock_out_time);
            $emp_worked_hours = ($clock_out - $clock_in) / 3600;
            // echo "<br>Working Hours: $emp_worked_hours <br>"; // for debugging

            // Working Time > $WorkingDayHours && Working Time < 15 Hours 
            if ($emp_worked_hours > $WorkingDayHours && $emp_worked_hours < $WorkingDayMaxHours) {
                $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today')";
                // echo $sql . "<br>"; // for debugging
                // echo "pay_amount Rs. $DaySalary"; // for debugging
                // echo $sql; // for debugging
                insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Add Day Salary"); // for production

                // Update the employee's salary in the employees table
                $sql = "UPDATE employees SET salary = salary + '$DaySalary' WHERE employ_id = '$employee_id'";
                // echo $sql; // for debugging
                insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Update Employee Day Salary"); // for production
            } elseif ($emp_worked_hours < $WorkingDayHours) {
                // echo 'Error: Working Hours Less Than 8 Hours'; // for debugging
                $pay_amount = $emp_worked_hours * $HourSalary;
                // $pay_amount = number_format($pay_amount, 2);

                // convert $emp_worked_hours to 2 decimal places
                $emp_worked_hours = number_format($emp_worked_hours, 2);

                $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$pay_amount', 'Day Salary - $today ($emp_worked_hours Hours)')";
                // echo $sql . "<br>"; // for debugging
                insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours", "Add Day Salary"); // for production
                // echo "Pay Amount: $pay_amount <br>"; // for debugging

                // Update the employee's salary in the employees table
                $sql = "UPDATE employees SET salary = salary + '$pay_amount' WHERE employ_id = '$employee_id'";
                // echo $sql; // for debugging
                insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours", "Update Employee Day Salary"); // for production
                $DaySalary = $pay_amount;
            } elseif ($emp_worked_hours > $WorkingDayMaxHours) {
                echo "Error: Working Hours More Than $WorkingDayMaxHours Hours";  // for debugging
            } else {
                echo 'Error: Unknown Error'; // for debugging
            }

            if ($DaySalary && $DaySalary > 0) {
                // Fall Day Salary From Company Profit
                $sql = "UPDATE accounts SET amount = amount - '{$DaySalary}' WHERE account_name = 'Company Profit'";
                insert_query($sql, "Fall $employee_name's Day Salary from Company Profit : Rs. $company_profit", "Fall Employee Day Salary from Company Profit Account");
            }
        }
    }
} else {
    echo 'Error2';
}


// employee_id: 12
// employee_name: ABCD
// action: Clock Out