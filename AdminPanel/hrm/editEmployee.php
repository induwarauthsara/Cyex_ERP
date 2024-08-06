<?php
// 1. Update Employee

require_once '../../inc/config.php';

$employ_id = $_POST['emp_id'];
$emp_name = $_POST['emp_name'];
$mobile = $_POST['mobile'];
$address = $_POST['address'];
$bank_account = $_POST['bank_account'];
$role = $_POST['role'];
$nic = $_POST['nic'];
$salary = $_POST['salary'];
$day_salary = $_POST['day_salary'];
$password = $_POST['password'];


// `employees`(`employ_id`, `emp_name`, `mobile`, `address`, `bank_account`, `role`, `nic`, `salary`, `day_salary`, `password`)
$sql = "UPDATE `employees` SET `emp_name` = '$emp_name', `mobile` = '$mobile', `address` = '$address', `bank_account` = '$bank_account', `role` = '$role', `nic` = '$nic', `salary` = '$salary', `day_salary` = '$day_salary', `password` = '$password' WHERE `employees`.`employ_id` = {$employ_id}";
insert_query($sql, "Name: $emp_name", "Update Employee Details");