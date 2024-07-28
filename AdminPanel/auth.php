<?php
$employee_name = $_SESSION['employee_name'];
$employee_id = $_SESSION['employee_id'];
$employee_role = $_SESSION['employee_role'];

$employee = "Employee";
$admin = "Admin";
if ($employee_role !== $admin) {
    header("Location: /index.php");
}
