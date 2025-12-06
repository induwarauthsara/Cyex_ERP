<?php
require_once '../../inc/config.php';
require_once '../../api/v1/expenses/sync_salary_expense.php';

if (isset($_POST['employee_id']) && isset($_POST['amount'])) {

    $employee_id = $_POST['employee_id'];
    $amount = $_POST['amount'];
    
    // Fix: Undefined variable $account
    $account = 'Cash'; 

    // Fetch Employee Name
    $emp_query = mysqli_query($con, "SELECT emp_name FROM employees WHERE employ_id = '$employee_id'");
    $emp_data = mysqli_fetch_assoc($emp_query);
    $emp_name = $emp_data['emp_name'] ?? 'Unknown';
    
    // Get current month for expense sync
    $current_month = date('Y-m');

    // 1. Add Record to Salary Table
    $description = "Salary Paid";
    $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$employee_id', '-$amount', '$description')";
    insert_query($sql, "Employee ID: $employee_id, Rs. $amount", "Employee Salary Paid - Update Salary Table");

    // 2. Sync with Expenses - Create/Update expense and add payment
    $expense_id = syncSalaryExpense($employee_id, $emp_name, $current_month);
    if ($expense_id) {
        $payment_id = addSalaryPayment($expense_id, $amount, 'Cash', 'Salary Payment', 1);
    }

    // 3. Update Employee Salary Balance
    $sql = "UPDATE employees SET salary = salary - '$amount' WHERE employ_id = '$employee_id'";
    insert_query($sql, "Employee ID: $employee_id, Rs. $amount", "Salary Paid - Update Employee Table");

    // 4. Fall Money in Cash in Hand
    $sql = "UPDATE accounts SET amount = amount - '$amount' WHERE account_name = '$account'";
    insert_query($sql, "Employee ID: $employee_id, Rs. $amount from $account table", "Salary Paid from $account table");

    // 5. Add Transaction Log
    $action = "Salary Payment";
    $msg = "Salary Pay to Employee ID: $employee_id, Rs. $amount from $account table";
    transaction_log($action, $msg, -$amount);
}
