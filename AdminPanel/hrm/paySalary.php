<?php
require_once '../../inc/config.php';
if (isset($_POST['employee_id']) && isset($_POST['amount'])) {

    $employee_id = $_POST['employee_id'];
    $amount = $_POST['amount'];

    // 1. Add Record to Salary Table
    // 2. Update Employee Salary
    // 3. Fall Money in Cash in Hand

    // 1. Add Record to Salary Table
    $description = "Salary Paid";
    $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$employee_id', '$amount', '$description')";
    insert_query($sql, "Employee ID: $employee_id, Rs. $amount", "Employee Salary Paid - Update Salary Table");

    // 2. Update Employee Salary
    $sql = "UPDATE employees SET salary = salary - '$amount' WHERE employ_id = '$employee_id'";
    insert_query($sql, "Employee ID: $employee_id, Rs. $amount", "Salary Paid - Update Employee Table");

    // 3. Fall Money in Cash in Hand
    $sql = "UPDATE accounts SET amount = amount - '$amount' WHERE account_name = 'cash_in_hand'";
    insert_query($sql, "Employee ID: $employee_id, Rs. $amount", "Salary Paid - Update Cash in Hand");
}
