<?php
// 1. Cost -=  Account (UPDATE accounts SET amount = amount - {$total_cost} WHERE account_name = {$account})
// 2. Update Invoice Table (UPDATE invoice SET cost = {$total_bill_cost}, profit = {$total_bill_profit} WHERE invoice_number = {$bill_no})
// 3. Update OneTimeProduct Table (UPDATE one_time_product SET status = 'Solved' WHERE id = {$id})
// 4. If Edit, Balance Previous Cost and Profit with New Cost and Profit
require_once '../../inc/config.php';


//Request : solve.php?id=${id}&worker=${worker}&cost=${total_cost}&profit=${profit}&invoiceID=${invoice_id}&edit=${OTPEdit}

$OneTimeProduct_id = $_GET['id'];
$worker = $_GET['worker'];
$cost = $_GET['cost'];
$profit = $_GET['profit'];
$account = 'Company Profit';
$invoiceID = $_GET['invoiceID'];
$edit = $_GET['edit'];
$product_name = $_GET['product_name'];

echo "EDIT REQUEST : $edit (for debugging)<br>";

// 1. Cost -= Profit Account
if ($edit == 'true') {
    // Revert Previous Cost to Account
    $sql = "SELECT cost FROM oneTimeProducts_sales WHERE oneTimeProduct_id = {$OneTimeProduct_id}";
    $previous = mysqli_fetch_assoc(mysqli_query($con, $sql));
    $previous_cost = $previous['cost'];
    $previous_account = 'Company Profit';
    $sql = "UPDATE accounts SET amount = amount + {$previous_cost} WHERE account_name = '{$previous_account}'";
    $action = "Revert Previous Service Cost to $previous_account";
    $msg = "Revert Rs.$previous_cost in {$previous_account} Account for Repair/Service ID : {$OneTimeProduct_id}";
    insert_query($sql, $msg, $action);
    transaction_log($action, $msg, $previous_cost);

    // Revert Previous Worker Salary
    $sql = "UPDATE employees SET salary = salary - $previous_cost WHERE emp_name = '$worker'";
    $action = "Revert Previous Worker Salary";
    $msg = "Revert Salary of $worker by Rs.$previous_cost";
    insert_query($sql, $msg, $action);
    transaction_log($action, $msg, -$previous_cost);

    // Add New Salary record for Fix Previous Salary Issue
    // Calculate New Cost
    $NewCost = $cost - $previous_cost;
    // get EMP ID
    $description = "Commission Changed for $product_name in Invoice Number : <a href=\'/invoice/print.php?id=$invoiceID\'> $invoiceID </a>";
    $sql = "INSERT INTO salary (emp_id, amount, description)
            SELECT employ_id, $NewCost, '$description'
            FROM employees
            WHERE emp_name = '$worker'
            LIMIT 1; ";
    insert_query($sql, $description, "Add New Commission Record");

} else {
    // Add New Salary record
    $description = "Commission Added for $product_name in Invoice Number : <a href=\'/invoice/print.php?id=$invoiceID\'> $invoiceID </a>";
    $sql = "INSERT INTO salary (emp_id, amount, description)
            SELECT employ_id, $cost, '$description'
            FROM employees
            WHERE emp_name = '$worker'
            LIMIT 1; ";
    insert_query($sql, $description, "Add New Commission Record");
}
$sql = "UPDATE accounts SET amount = amount - {$cost} WHERE account_name = '{$account}'";
$action = "Fall OneTimeProduct Cost from $account";
$msg = "Fall Rs.$cost in {$account} Account for Repair/Service ID : {$OneTimeProduct_id}";
insert_query($sql, $msg, $action);
//Transaction Log
transaction_log($action, $msg, -$cost);

// 2. Update Invoice Table
$sql = "UPDATE invoice SET cost = {$cost}, profit = {$profit} WHERE invoice_number = '{$invoiceID}'";
$action = "Update Invoice Cost and Profit";
$msg = "Update Invoice (ID:$invoiceID) for Repair/Service ID : {$OneTimeProduct_id}";
insert_query($sql, $msg, $action);

// 3. Update OneTimeProduct Table
$sql = "UPDATE oneTimeProducts_sales SET cost='$cost', profit='$profit', status = 'cleared', worker='$worker'WHERE oneTimeProduct_id = {$OneTimeProduct_id}";
$action = "Update OneTimeProduct Sale";
$msg = "Update OneTimeProduct (ID:$OneTimeProduct_id) for Invoice ID : {$invoiceID}, Cost : $cost, Profit : $profit, worker : $worker";
insert_query($sql, $msg, $action);

// 4. Increase Worker Salary
$sql = "UPDATE employees SET salary = salary + $cost WHERE emp_name = '$worker'";
$action = "Add Salary Commission";
$msg = "Increase Salary of $worker for $product_name by Rs.$cost";
insert_query($sql, $msg, $action);
transaction_log($action, $msg, $cost);
