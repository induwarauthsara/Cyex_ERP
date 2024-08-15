<?php
ob_start();
require_once '../inc/config.php';
header('Content-Type: application/json');


// 1. Generated Profit goto Biller & Worker
// 2. Update (+) Accounts Amount
// 3. Add Transaction Log
// 4. Add Record to `InvBalPayRecords` Table
// 5. Update Invoice Table

if (isset($_GET['status']) && isset($_GET['invoice_number']) && isset($_GET['account'])) {

    echo "<pre>";
    print_r($_GET);
    echo "</pre>";

    // From Get Request Parameters
    $status = $_GET['status'];
    $invoice_number = $_GET['invoice_number'];
    $account = $_GET['account'];

    // From Database
    $sql = "SELECT * FROM invoice WHERE invoice_number = $invoice_number";
    $result = mysqli_query($con, $sql);
    $invoice_details = mysqli_fetch_assoc($result);

    // Invoice details
    $invoice_total_amount =  $invoice_details['total'];
    $invoice_advance =  $invoice_details['advance'];
    $invoice_balance =  $invoice_details['balance'];
    $worker = $invoice_details['primary_worker'] ?? '';
    $biller = $invoice_details['biller'] ?? '';
    $invoice_discount = $invoice_details['discount'];
    $invoice_total_cost = $invoice_details['cost'];
    $invoice_total_profit = $invoice_details['profit'];
    $invoice_payment_method = $invoice_details['paymentMethod'];

    // get Employee ID of Biller and Default Worker
    $biller_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$biller'"))['employ_id'];
    $worker_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$worker'"))['employ_id'] ?? '';

    echo "total : " . $invoice_total_amount . "<br>";
    echo "advance : " . $invoice_advance . "<br>";
    echo "balance : " . $invoice_balance . "<br>";
    echo "worker : " . $worker . "<br>";
    echo "biller : " . $biller . "<br>";
    echo "discount : " . $invoice_discount . "<br>";
    echo "cost : " . $invoice_total_cost . "<br>";
    echo "profit : " . $invoice_total_profit . "<br>";
    echo "paymentMethod : " . $invoice_payment_method . "<br><br><br>";

    $invoice_total_profit = $invoice_total_profit < 0 ? 0 : $invoice_total_profit;

    if (isset($_GET['invoice_number']) && $status == 'addFund' && isset($_GET['fund_amount'])) {
        // ======================================== Add Fund ========================================
        $amount = $_GET['fund_amount'];

        $new_advance_amount = $invoice_advance + $amount; // Bill ekata hambuna Total Advance eka
        echo "new_advance_amount : " . $new_advance_amount . "<br>";
        $new_balance_amount = $invoice_total_amount - $new_advance_amount; // New Balance eka
        echo "new_balance_amount : " . $new_balance_amount . "<br>";
        $new_profit = $new_advance_amount - $invoice_total_cost; // New Profit eka
        echo "new_profit : " . $new_profit . "<br>";
        if ($new_profit > 0) {
            $generated_profit = $new_profit > 0 ? $new_profit - $invoice_total_profit : 0; // Wadi una Profit eka
        } else {
            $generated_profit = 0;
        }


        // check full paid
        if (
            $new_balance_amount <= 0.00
        ) {
            $full_paid = 1;
            $invoice_status = 'Invoice Full Paid';
        } else {
            $full_paid = 0;
            $invoice_status = 'Balance Available';
        }

        $no_any_erros = true;
    } else if (isset($_GET['invoice_number']) && $status == 'fullPayment') {
        // ======================================== Full Payment ========================================
        $amount = $invoice_balance;
        $full_paid = 1;
        $new_advance_amount = $invoice_balance;
        $new_balance_amount = 0.00;
        $new_profit = $new_advance_amount - $invoice_total_cost; // New Profit eka
        $generated_profit = $new_profit - $invoice_total_profit; // Wadi una Profit eka

        $invoice_status = 'Invoice Full Paid';

        $no_any_erros = true;
    } else {
        $no_any_erros = false;
        echo "Have a Error";
    }

    if ($no_any_erros) {
        $ERROR_Status = true;
        $ERR_msg = '';
        echo "Generated Profit : $generated_profit <br><br>";
        // ====================== 1. Generated Profit goto Biller & Worker
        if ($generated_profit > 0) {
            if ($biller == $worker) {
                $profit_for_biller = ($generated_profit / 100) * 15;
                $sql = "UPDATE employees SET salary = salary + {$profit_for_biller} WHERE emp_name = '{$biller}'";
                insert_query($sql, "send biller Profit : {$biller} Rs. {$profit_for_biller}", "Add Biller Profit to Employee Table");
                $ERROR_Status = ($result) ? $ERROR_Status : false;
                $ERR_msg .= 'Failed to update biller profit in Employee Profile. ';


                $description = "Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
                $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$biller_employee_id', '$profit_for_biller', '$description')";
                insert_query($sql, "Employee ID: $biller_employee_id, Rs. $profit_for_biller", "Employee Salary Paid - Update Salary Table");
                $ERROR_Status = ($result) ? $ERROR_Status : false;
                $ERR_msg .= (!$result) ? 'Failed to update biller profit in Salary Table. ' : '';
            } else { // if Biller is not Worker
                $for_biller = ($generated_profit / 100) * 5;
                $for_worker = ($generated_profit / 100) * 10;
                // for Biller
                $sql = "UPDATE employees SET salary = salary + {$for_biller} WHERE emp_name = '{$biller}'";
                insert_query($sql, "send biller Profit : {$biller} Rs. {$for_biller}", "Add Biller Profit to Employee Table");
                $ERROR_Status = ($result) ? $ERROR_Status : false;
                $ERR_msg .= (!$result) ? 'Failed to update biller profit in Employee Profile. ' : '';


                $description = "Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
                $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$biller_employee_id', '$for_biller', '$description')";
                insert_query($sql, "Employee ID: $biller_employee_id, Rs. $for_biller", "Employee Salary Paid - Update Salary Table");
                $ERROR_Status = ($result) ? $ERROR_Status : false;
                $ERR_msg .= (!$result) ? 'Failed to update biller profit in Salary Table. ' : '';


                // for Worker
                $sql = "UPDATE employees SET salary = salary + {$for_worker} WHERE emp_name = '{$worker}'";
                insert_query($sql, "send worker Profit : {$worker} Rs. {$for_worker}", "Add Worker Profit to Employee Table");
                $ERROR_Status = ($result) ? $ERROR_Status : false;
                $ERR_msg .= (!$result) ? 'Failed to update worker profit in Employee Profile. ' : '';


                $description = "Profit (Balance Pay) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
                $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$worker_employee_id', '$for_worker', '$description')";
                insert_query($sql, "Employee ID: $worker_employee_id, Rs. $for_worker", "Employee Salary Paid - Update Salary Table");
                $ERROR_Status = ($result) ? $ERROR_Status : false;
                $ERR_msg .= (!$result) ? 'Failed to update worker profit in Salary Table. ' : '';
            }
        }

        // ====================== 2. Update (+) Accounts Amount
        $sql = "UPDATE accounts SET amount = amount + {$amount} WHERE account_name = '{$account}'";
        echo $sql . "<br>";
        insert_query($sql, "Invoice Number : $invoice_number, Payment  : $new_advance_amount, New Invoice Balance : $new_balance_amount ", "Add Fund to 'Cash in Hand' Account");
        $ERROR_Status = ($result) ? $ERROR_Status : false;
        $ERR_msg .= (!$result) ? 'Failed to update account amount. ' : '';


        // ====================== 3. Add Transaction Log
        $description = "Add Fund to Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
        $sql = "INSERT INTO transaction_log (invoice_number, amount, description, account) VALUES ('$invoice_number', '$amount', '$description', '$account')";
        echo $sql . "<br>";
        insert_query($sql, "Invoice Number : $invoice_number, Payment  : $new_advance_amount, New Invoice Balance : $new_balance_amount ", "Add Fund to Invoice");
        $ERROR_Status = ($result) ? $ERROR_Status : false;
        $ERR_msg .= (!$result) ? 'Failed to add transaction log. ' : '';


        // ====================== 4. Add Record to `InvBalPayRecords` Table
        $sql = "INSERT INTO `InvoiceBalPayRecords` (`invoice_number`, `amount`, `account`, `invoice_status`) VALUES ('$invoice_number', '$amount', '$account', '$invoice_status')";
        echo $sql . "<br>";
        insert_query($sql, "Invoice Number : $invoice_number, Payment  : $new_advance_amount, New Invoice Balance : $new_balance_amount ", "Add Fund to Invoice");
        $ERROR_Status = ($result) ? $ERROR_Status : false;
        $ERR_msg .= (!$result) ? 'Failed to add record to InvoiceBalPayRecords Table. ' : '';


        // ====================== 5. Update Invoice Table
        $sql = "UPDATE `invoice` SET `advance` = '$new_advance_amount', `balance` = '$new_balance_amount', `full_paid` = $full_paid, `profit` = '$new_profit' WHERE `invoice`.`invoice_number` = $invoice_number;";
        echo $sql . "<br>";
        insert_query($sql, "Invoice Number : $invoice_number, Payment  : $new_advance_amount, New Invoice Balance : $new_balance_amount ", "Add Fund to Invoice");
        $ERROR_Status = ($result) ? $ERROR_Status : false;
        $ERR_msg .= (!$result) ? 'Failed to update invoice table. ' : '';
    } else {
        echo "ERROR";
        $ERROR_Status = false;
        $ERR_msg = (!$result) ? 'Invalid Request' : '';
    }
    echo "<br><br><br> $ERR_msg ";



    end_db_con();
}
$output = ob_get_clean();

// echo $output;

// Check if the output contains a success message or an error
if ($ERROR_Status) {
    $response = [
        'status' => 'success',
        'message' => 'Fund Add successfully.'
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Failed to add fund. ERROS : ' . $ERR_msg
    ];
}

// Set the content type to JSON
header('Content-Type: application/json');
echo json_encode($response);
