<?php
// 1. Add New Employee

require_once '../../inc/config.php';

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
$sql = "INSERT INTO `employees` (`emp_name`, `mobile`, `address`, `bank_account`, `role`, `nic`, `salary`, `day_salary`, `password`) 
        VALUES ('$emp_name', '$mobile', '$address', '$bank_account', '$role', '$nic', '$salary', '$day_salary', '$password')";
insert_query($sql, "Name: $emp_name", "New Employee Added");