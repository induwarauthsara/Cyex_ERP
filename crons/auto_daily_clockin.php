<?php
session_start();
require_once '../inc/config.php';

echo "Starting auto clock-in cron job at " . date('Y-m-d H:i:s') . "\n";

// Target employee ID
$employee_id = 1;
$action = 'Clock In';

// Get employee information
$sql = "SELECT * FROM employees WHERE employ_id = '$employee_id'";
$result = mysqli_query($con, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $employee_data = mysqli_fetch_assoc($result);
    $employee_name = $employee_data['emp_name'];
    $is_clocked_in = $employee_data['is_clocked_in'];
    
    echo "Processing employee: $employee_name (ID: $employee_id)\n";
    
    // Check if employee is already clocked in
    if ($is_clocked_in == 1) {
        echo "Employee $employee_name is already clocked in. Skipping...\n";
    } else {
        // Check if employee already has a clock-in record for today
        $today = date('Y-m-d');
        $sql_check = "SELECT * FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock In'";
        $result_check = mysqli_query($con, $sql_check);
        
        if ($result_check && mysqli_num_rows($result_check) > 0) {
            echo "Employee $employee_name already has a clock-in record for today. Skipping...\n";
        } else {
            // Insert data into the attendance table
            $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
            insert_query($sql, "Attendance : Employee Name : $employee_name , Action : $action", "Attendance Auto Submitted by Clock-in Cronjob");

            // Update the status of the employee in the employees table
            $sql = "UPDATE employees SET is_clocked_in = 1 WHERE employ_id = '$employee_id'";
            insert_query($sql, "Employee Name : $employee_name , Action : $action", "Attendance Auto Updated in Employee Profile by Clock-in Cronjob");
            
            echo "Successfully clocked in employee: $employee_name\n";
        }
    }
} else {
    error_log('Error: Employee with ID ' . $employee_id . ' not found');
    echo "Employee with ID $employee_id not found\n";
}

echo "Auto clock-in cron job completed at " . date('Y-m-d H:i:s') . "\n";
?>
