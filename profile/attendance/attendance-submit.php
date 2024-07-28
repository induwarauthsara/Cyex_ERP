<?php
session_start();
require_once '../../inc/config.php';

if (isset($_SESSION['employee_id']) && isset($_GET['action']) || $_GET['action'] == 'Clock In' || $_GET['action'] == 'Clock Out') {
    $employee_id = $_SESSION['employee_id'];
    $employee_name = $_SESSION['employee_name'];
    $action = $_GET['action'];

    // Insert data into the attendance table
    $sql = "INSERT INTO attendance (employee_id, action) VALUES ('$employee_id', '$action')";
    // echo $sql;
    insert_query($sql, "Attendance : Employee Name : $employee_name , Action : $action", "Attendance Submitted");
} else {
    echo 'Error2';
}
