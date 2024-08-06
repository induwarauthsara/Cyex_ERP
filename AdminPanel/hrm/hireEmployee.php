<?php
session_start();
require_once '../../inc/config.php';

$admin_name = $_SESSION['employee_name'];
$employee_id = $_GET['employee_id'];

// Hire the employee
$sql = "UPDATE employees SET status = 1 WHERE employ_id = '$employee_id'";
insert_query($sql, "Employee ID: $employee_id by ACP ($admin_name)", "Employee Hired Again");
