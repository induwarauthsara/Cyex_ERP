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
} else {
    echo 'Error2';
}


// employee_id: 12
// employee_name: ABCD
// action: Clock Out