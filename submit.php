<?php
//Set Error Array
$error_array = array();
require_once 'inc/config.php'; ?>

<?php
//isset
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Data</title>
</head>

<body>

    <!-- Send Data to Invoice Tabel -->
    <?php
    // Check Submit Button Cliecked
    if ((isset($_POST['submit'])) || isset($_POST['submit_and_print']) || isset($_POST['submit_and_print_fullPayment']) || isset($_POST['submit_and_fullPayment'])) {
        // if (isset($_POST['submit'])) {
        // Set Invoice values
        // if customer name and mobile empty, assign default values as Cash and 0
        if (empty($_POST['name']) && empty($_POST['tele'])) {
            $customer_name = "Cash";
            $customer_mobile = 0;
        } else {
            $customer_name = $_POST['name'];
            $customer_mobile = $_POST['tele'] ?? 0;
        }
        // $bill_no = $_POST['bill-no'];
        $date = $_POST['today'];
        $bill_total = floatval($_POST['total']) ?? 0;
        $bill_discount = floatval($_POST['discount']) ?? 0;
        $bill_advance = floatval($_POST['advance']) ?? 0;
        $bill_balance = $_POST['balance'] ?? 0;
        $biller = $_POST['biller'];
        $default_worker = $_POST['default_worker'];
        $bill_rows = $_POST['no'];
        $InvoiceDescription = $_POST['InvoiceDescription'];
        $paymentMethod = $_POST['PaymentMethod'];

        // get Employee ID of Biller and Default Worker
        $biller_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$biller'"))['employ_id'];
        $worker_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$default_worker'"))['employ_id'];

        echo "Customer Name : " . $customer_name . "<br>";
        echo "Customer Mobile : " . $customer_mobile . "<br>";
        echo "Date : " . $date . "<br>";
        echo "Total : " . $bill_total . "<br>";
        echo "Discount : " . $bill_discount . "<br>";
        echo "Advance : " . $bill_advance . "<br>";
        echo "Balance : " . $bill_balance . "<br>";
        echo "Biller : " . $biller . "<br>";
        echo "Default Worker : " . $default_worker . "<br>";
        echo "Bill Rows : " . $bill_rows . "<br>";
        echo "Invoice Description : " . $InvoiceDescription . "<br>";
        echo "Payment Method : " . $paymentMethod . "<br>";


        // ========== Full Payment ==========
        if (isset($_POST['submit_and_print_fullPayment']) || isset($_POST['submit_and_fullPayment'])) {
            $bill_advance = $bill_total;
            $full_paid = 1;
            $bill_balance = 0;
        }

        // check full paid
        if ($bill_balance <= 0.00) {
            $full_paid = 1;
        } else {
            $full_paid = 0;
        }

        // Check Discount
        if ($bill_discount != 0) {
            $bill_advance = $bill_total - $bill_discount;
        }

        // Check Payment Method
        // Cash -  Cash In Hand
        // BankTransfer -  (-30) - BOC 
        // CardPayment - (-3%) - DFCC
        // Cheque -  (-30) - BOC
        // QRPayment - (-0.5%) - BOC
        if ($paymentMethod == "Cash") {
            $paymentMethod = "Cash";
        } elseif ($paymentMethod == "BankTransfer") {
            $paymentMethod = "BankTransfer";
            $bill_advance = $bill_advance - 30;
        } elseif ($paymentMethod == "CardPayment") {
            $paymentMethod = "CardPayment";
            $bill_advance = $bill_advance - ($bill_advance * 0.03);
        } elseif ($paymentMethod == "Cheque") {
            $paymentMethod = "Cheque";
            $bill_advance = $bill_advance - 30;
        } elseif ($paymentMethod == "QRPayment") {
            $paymentMethod = "QRPayment";
            $bill_advance = $bill_advance - ($bill_advance * 0.005);
        }
        echo "Payment Method after Advance : " . $bill_advance . "<br>";
        // Check Customer is in Database?
        $has_customer_name_in_db = "";
        $sql = "SELECT COUNT(customer_name) FROM customers WHERE customer_name = '$customer_name';";
        $result = mysqli_query($con, $sql);
        $rowcount = mysqli_num_rows($result);

        $selected_customer = mysqli_fetch_array($result);
        if ($selected_customer['COUNT(customer_name)'] == 0) {
            $sql = "INSERT INTO customers (customer_name, customer_mobile) VALUES ('$customer_name', '$customer_mobile')";
            insert_query($sql, "$customer_name", "Add New Customer");
        }


        // Send Invoice Data to Database
        $sql = "INSERT INTO invoice (invoice_type, invoice_description, customer_name, invoice_date, customer_mobile, biller, primary_worker, total, discount, advance, balance, full_paid, paymentMethod)
         VALUES ('product', '$InvoiceDescription', '{$customer_name}', '{$date}', '{$customer_mobile}', '{$biller}', '{$default_worker}', '{$bill_total}', '{$bill_discount}', '{$bill_advance}', '{$bill_balance}', {$full_paid}, '{$paymentMethod}')";
        if (mysqli_query($con, $sql)) {
            // Retrieve the auto-generated InvoiceID
            $bill_no = mysqli_insert_id($con); // Get the last inserted ID
            echo "Invoice Number : " . $bill_no . "<br>";

            // Save in Action Log Table
            $action = "Add New Invoice";
            $msg = "Invoice Number : $bill_no, Customer Name : $customer_name, Date : $date, Total : $bill_total, Discount : $bill_discount, Advance : $bill_advance, Balance : $bill_balance, Full Paid : $full_paid, Payment Method : $paymentMethod";
            if (isset($_SESSION['employee_id'])) {
                $biller_employee_id = $_SESSION['employee_id'];
                // CREATE TABLE action_log ( id INT AUTO_INCREMENT PRIMARY KEY, employee_id  INT NOT NULL, action VARCHAR(20) NOT NULL, description TEXT, date DATE DEFAULT CURRENT_DATE, time TIME DEFAULT CURRENT_TIME );
                $sql = "INSERT INTO action_log (employee_id, action, description) VALUES ('$biller_employee_id', '$action', '$msg');";
                insert_query($sql, "$action", "Add New Action Log");
            }
        } else {
            echo "Error While generation Invoice ID : " . $sql . "<br>" . mysqli_error($con);
        }


        // Check todo
        if (isset($_POST['add_to_todo'])) {
            $todoName = $_POST['todoName'];
            $todoTime = $_POST['todoTime'];
            $sql = "INSERT INTO `todo`(`invoice_number`, `title`, `submision_time`) VALUES ('$bill_no','$todoName','$todoTime')";
            insert_query($sql, "$todoName", "Add Todo Item");
        }

        // Total Bill Product Cost
        $total_bill_cost = 0;
        $emoloyee_commission = 0;

        $one_time_product_advance = 0; // Declare a variable to track the advance from One-Time-Product

        if ($bill_rows > -1) {
            for ($no = 0; $no <= $bill_rows; $no++) {
                if (isset($_POST["product_{$no}"])) { // Check row is removed
                    if (!function_exists('sales_arry')) { // voice function re-call in for loop

                        function sales_arry($product, $qty, $rate, $amount, $worker)
                        {
                            global $no;
                            global $con;
                            global $biller;
                            global $bill_advance;
                            global $bill_no;
                            global $total_bill_cost;
                            global $default_worker;
                            global $one_time_product_advance;
                            global $emoloyee_commission;
                            global $worker_employee_id;

                            $product = $_POST["{$product}_{$no}"];
                            // $description = $_POST["{$description}_{$no}"];
                            $qty = $_POST["{$qty}_{$no}"];
                            $rate = $_POST["{$rate}_{$no}"];
                            $amount = $_POST["{$amount}_{$no}"];

                            echo $product . "<br>";
                            echo $qty . "<br>";
                            echo $rate . "<br>";
                            echo $amount . "<br>";
                            echo $default_worker . "<br>";
                            echo "<br>";

                            // Check is this One-Time-Product
                            // Check Product is in Database?
                            $sql = "SELECT COUNT(product_name) FROM products WHERE product_name = '$product';";
                            $result = mysqli_query($con, $sql);
                            $selected_product = mysqli_fetch_array($result);
                            if ($selected_product['COUNT(product_name)'] == 0) { // Check is this One-Time-Product
                                // This is One-Time-Product
                                $sql = "INSERT INTO `oneTimeProducts_sales`(`invoice_number`, `product`, `qty`, `rate`, `amount`, `Worker`) VALUES 
                            ('$bill_no','$product','$qty','$rate','$amount', '$default_worker')";
                                insert_query($sql, "$product", "Sale One-Time-Product");
                                echo $sql;

                                // Accumulate the amount from One-Time-Product sales
                                $one_time_product_advance += $amount;
                            } else {
                                // This is Regular (Database Saved) Product (Not One-Time-Product) 

                                // Product Cost eka Stock Account eken Adu wenawa (Kalin Thibbe + wenna.)
                                $cost_sql = "SELECT cost, worker_commision FROM products WHERE product_name = '{$product}'";
                                $cost_result = mysqli_query($con, $cost_sql);
                                $cost_row = mysqli_fetch_assoc($cost_result);
                                $cost = $cost_row['cost'] * $qty;
                                $worker_commission = $cost_row['worker_commision'] * $qty;
                                $emoloyee_commission += $worker_commission;
                                $total_cost = $cost + $worker_commission;
                                $profit = $amount - $total_cost;

                                $sql = "UPDATE accounts SET amount = amount - {$cost} WHERE account_name = 'Stock Account'";
                                insert_query($sql, "Rs. $cost", "Add Product Cost to Stock Account");

                                // Total Bill Product Cost and Profit
                                $total_bill_cost += $total_cost;

                                // send Employee Commission to Employee Account Salary
                                $sql = "UPDATE employees SET salary = salary + {$worker_commission} WHERE employ_id = {$worker_employee_id}";
                                insert_query($sql, "Employee Commission : Rs. $worker_commission", "Add Employee Commission to Employee Account");

                                // Send Employee Commission to Salary Table
                                $description = "Commission from Invoice : <a href=\'/invoice/print.php?id=$bill_no\'> $bill_no </a> for $product";
                                $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$worker_employee_id', '$worker_commission', '$description')";
                                insert_query($sql, "Add Commison to $default_worker Rs. $worker_commission for $product", "Add Commision - Update Salary Table");

                                // Send Sales Data to DB
                                $sql = "INSERT INTO sales (invoice_number, product, qty, rate, amount, worker, cost, profit)
                                    VALUES ('{$bill_no}', '{$product}', '{$qty}', '{$rate}', '{$amount}', '{$default_worker}', '{$total_bill_cost}', '{$profit}')";
                                    echo $sql;
                                insert_query($sql, "Add New Sale : $product", "Add New Sale");

                                //Wikunapu Product eka Stock eken adu wenawa. (meka Automated kala yata)
                                $sql = "UPDATE products SET stock_qty = stock_qty - {$qty} WHERE product_name = '{$product}'";
                                insert_query($sql, "Fall {$qty} of {$product} in Stock", "Fall Sell Product Qty from Stock");

                                // Update Has_Stock state
                                $new_stock_qty = mysqli_fetch_assoc(mysqli_query($con, "SELECT stock_qty FROM products WHERE product_name = '{$product}'"))['stock_qty'];
                                if ($new_stock_qty > 0) {
                                    $sql = "UPDATE products SET has_stock = 1 WHERE product_name = '{$product}'";
                                    insert_query($sql, "$product is In Stock", "Update Product Has_Stock State");
                                } else {
                                    $sql = "UPDATE products SET has_stock = 0 WHERE product_name = '{$product}'";
                                    insert_query($sql, "$product is Out of Stock", "Update Product Has_Stock State");
                                }
                            }
                        }
                    }
                } else {
                    continue;
                }
                echo $bill_no . "<br>";
                sales_arry('product', 'qty', 'rate', 'amount', 'worker');
                echo "<br>";
            }
        } else {
            echo "<br>Bill Rows are Empty. No Any Products Added to Invoice<br>"; // for debugging
        }

        // Profit
        $total_bill_profit = $bill_advance - $total_bill_cost;

        // Send Profit to Company Profit Accounts
        $company_profit = $total_bill_profit;
        $sql = "UPDATE accounts SET amount = amount + {$company_profit} WHERE account_name = 'Company Profit'";
        insert_query($sql, "Company Profit : Rs. $company_profit", "Add Company Profit to Company Profit Account");
        // Add Transaction Log -> type, description, amount
        $transaction_type = 'Invoice - Company Profit';
        $transaction_description = "Profit to Company. Inv: $bill_no - $customer_name, Profit : Rs. $company_profit";
        transaction_log($transaction_type, $transaction_description, $company_profit);


        // Update Invoice Total Profit And Cost
        $sql = "UPDATE invoice SET cost = {$total_bill_cost}, profit = {$total_bill_profit} WHERE invoice_number = {$bill_no}";
        insert_query($sql, "Invoice Number : $bill_no, Total Bill Cost : $total_bill_cost, Total Bill Profit : $total_bill_profit", "Update Invoice Total Profit And Cost");

        // Advance Hambuna Salli Account ekata add wenawa
        // Cash -  Cash In Hand
        // CardPayment - (-3%) - DFCC
        // BankTransfer -  (-30) - BOC 
        // Cheque -  (-30) - BOC
        // QRPayment - (-0.5%) - BOC
        if ($paymentMethod == "Cash") {
            $sql = "UPDATE accounts SET amount = amount + {$bill_advance} WHERE account_name = 'cash_in_hand'";
            insert_query($sql, "Invoice Number : $bill_no, Rs. {$bill_advance}", "Add Invoice Advance Money to Cash in Hand");
        } else if ($paymentMethod == "CardPayment") {
            $sql = "UPDATE accounts SET amount = amount + {$bill_advance} WHERE account_name = 'DFCC'";
            insert_query($sql, "Invoice Number : $bill_no, Rs. {$bill_advance}, Payment Mothod : $paymentMethod", "Add Invoice Advance Money to DFCC Account");
        } else if ($paymentMethod == "BankTransfer" || $paymentMethod == "Cheque" || $paymentMethod == "QRPayment") {
            $sql = "UPDATE accounts SET amount = amount + {$bill_advance} WHERE account_name = 'BOC'";
            insert_query($sql, "Invoice Number : $bill_no, Rs. {$bill_advance}, Payment Mothod : $paymentMethod", "Add Invoice Advance Money to BOC Account");
        }

        // Add Transaction Log -> type, description, amount
        $transaction_type = 'Invoice - Cash In';
        $transaction_description = "$bill_no - $customer_name, Payment Method : $paymentMethod, Advance : Rs. $bill_advance";
        transaction_log($transaction_type, $transaction_description, $bill_advance);

        // ========== Print ==========
        if (isset($_POST['submit_and_print_fullPayment']) || isset($_POST['submit_and_print'])) {
            $print_path = "/invoice/print.php?id=" . $bill_no;
            header("Location: " . $print_path);
            die();

            //         $this_url = "/index.php";
            //         /*header("refresh:2; url={$this_url}");*/
            //         echo "<script>
            // setTimeout(`location.href = '$this_url';`, 100);
            // </script> ";
        }
    }
    echo "My Name is {$customer_name} is customer...!";
    ?>

    <?php
    $sql = "SELECT * FROM invoice";
    $result = mysqli_query($con, $sql);
    $resultCheck = mysqli_num_rows($result);

    echo "    <pre><br><br>";
    print_r($_POST);

    ?>
    </pre>
</body>

</html>

<?php

if (empty($error_array)) {

    // Refresh Page
    $this_url = "/index.php";
    /*header("refresh:2; url={$this_url}");*/
    echo "<script>
    setTimeout(`location.href = '$this_url';`, 100);
    </script> ";
} else {
    echo "<h1>Error List</h1>";
    echo "<pre>";
    print_r($error_array);
    echo "</pre>";
}



?>

<?php end_db_con(); ?>