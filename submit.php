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
        $bill_total = $_POST['total'];
        $bill_discount = $_POST['discount'];
        $bill_advance = $_POST['advance'];
        $bill_balance = $_POST['balance'];
        $biller = $_POST['biller'];
        $default_worker = $_POST['default_worker'];
        $bill_rows = $_POST['no'];


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
        $sql = "INSERT INTO invoice (customer_name, invoice_date, customer_mobile, biller, total, discount, advance, balance, full_paid)
        VALUES ('{$customer_name}', '{$date}', '{$customer_mobile}', '{$biller}', '{$bill_total}', '{$bill_discount}', '{$bill_advance}', '{$bill_balance}', {$full_paid})";
        if (mysqli_query($con, $sql)) {
            // Retrieve the auto-generated InvoiceID
            $bill_no = mysqli_insert_id($con); // Get the last inserted ID
            echo "Invoice Number : " . $bill_no . "<br>";

            // Save in Action Log Table
            $action = "Add New Invoice";
            $msg = "Invoice Number : $bill_no, Customer Name : $customer_name, Date : $date, Total : $bill_total, Discount : $bill_discount, Advance : $bill_advance, Balance : $bill_balance, Full Paid : $full_paid";
            if (isset($_SESSION['employee_id'])) {
                $employee_id = $_SESSION['employee_id'];
                // CREATE TABLE action_log ( id INT AUTO_INCREMENT PRIMARY KEY, employee_id  INT NOT NULL, action VARCHAR(20) NOT NULL, description TEXT, date DATE DEFAULT CURRENT_DATE, time TIME DEFAULT CURRENT_TIME );
                $sql = "INSERT INTO action_log (employee_id, action, description) VALUES ('$employee_id', '$action', '$msg');";
            } else {
                $sql = "INSERT INTO action_log (action, description) VALUES ('$action', '$msg');";
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
                        global $employee_id;

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
                            $sql = "INSERT INTO `oneTimeProducts_sales`(`invoice_number`, `product`, `qty`, `rate`, `amount`) VALUES 
                            ('$bill_no','$product','$qty','$rate','$amount')";
                            insert_query($sql, "$product", "Sale One-Time-Product");
                        } else {
                            // This is Regular (Database Saved) Product (Not One-Time-Product) 

                            // Product Cost eka Stock Account eken Adu wenawa (Kalin Thibbe + wenna.)
                            $cost_sql = "SELECT cost, profit FROM products WHERE product_name = '{$product}'";
                            $cost_result = mysqli_query($con, $cost_sql);
                            $cost_row = mysqli_fetch_assoc($cost_result);
                            $cost = $cost_row['cost'] * $qty;
                            $profit = $cost_row['profit'] * $qty;
                            $sql = "UPDATE accounts SET amount = amount - {$cost} WHERE account_name = 'Stock Account'";
                            insert_query($sql, "Rs. $cost", "Add Product Cost to Stock Account");

                            // Total Bill Product Cost and Profit
                            $total_bill_cost += $cost;

                            // Send Sales Data to DB
                            $sql = "INSERT INTO sales (invoice_number, product, qty, rate, amount, worker, cost, profit)
                                    VALUES ('{$bill_no}', '{$product}', '{$qty}', '{$rate}', '{$amount}', '{$default_worker}', '{$cost}', '{$profit}')";
                            insert_query($sql, "Add New Sale : $product", "Add New Sale");

                            /*  //Wikunapu Product eka Stock eken adu wenawa. (meka Automated kala yata)
                            $sql = "UPDATE products SET stock_qty = stock_qty - {$qty} WHERE product_name = '{$product}'";
                            insert_query($sql, "Fall Sell Product Qty from Stock");
                             */


                            // ========== Wikunapu Product eka Stock eken adu wenawa. ==========
                            // -------- Get Product makeProduct to Array --------
                            $sql = "SELECT item_name FROM makeProduct WHERE product_name='{$product}'";
                            $result = mysqli_query($con, $sql);
                            $ingridians_list = array();
                            if (mysqli_num_rows($result) > 0) {
                                while ($recoard = mysqli_fetch_assoc($result)) {
                                    array_push($ingridians_list, $recoard["item_name"]);
                                }
                                $thisProductHasRawMaterials = true;
                            }

                            // --------  Fall Product Items from Item List -------
                            if ($thisProductHasRawMaterials) {
                                // Select Item Qty of Product
                                for ($i = 0; $i < count($ingridians_list); $i++) {
                                    $selected_item = $ingridians_list[$i];
                                    $ingridians_product_item_qty = "SELECT qty FROM makeProduct WHERE item_name = '{$selected_item}' AND product_name = '{$product}';";
                                    $result = mysqli_query($con, $ingridians_product_item_qty);
                                    $product_item_qty = mysqli_fetch_array($result)['qty'] * $qty;

                                    // Fall Item qty
                                    $fall_item = "UPDATE `items` SET qty= qty - {$product_item_qty} WHERE item_name = '{$selected_item}'";
                                    insert_query($fall_item, "$selected_item, Qty : $qty items", "fall item from stock");
                                }


                                /// ========== Stock eke Product wala QTY eka hadanawa ==========

                                // -------- Get QTY of ingridians_list to Array --------
                                $ingridians_requement_qty_array = array();
                                $item_qty_array = array();
                                $makeable_product_qty_array = array();

                                for ($i = 0; $i < count($ingridians_list); $i++) {
                                    $selected_item = $ingridians_list[$i];

                                    $selected_item_req_qty_sql = "SELECT qty FROM makeProduct WHERE item_name = '{$selected_item}' AND product_name = '{$product}';";
                                    $result = mysqli_query($con, $selected_item_req_qty_sql);
                                    $recoard = mysqli_fetch_assoc($result);
                                    array_push($ingridians_requement_qty_array, $recoard["qty"]);

                                    $selected_item_qty_sql = "SELECT qty FROM items WHERE item_name = '{$selected_item}';";
                                    $result = mysqli_query($con, $selected_item_qty_sql);
                                    $recoard = mysqli_fetch_assoc($result);
                                    array_push($item_qty_array, $recoard["qty"]);

                                    $selected_item_ingridians_requement = $ingridians_requement_qty_array[$i];
                                    $selected_item_qty = $item_qty_array[$i];
                                    $makeable_product_qty = $selected_item_qty / $selected_item_ingridians_requement;
                                    array_push($makeable_product_qty_array, $makeable_product_qty);
                                }
                                // -------- Select Min QTY of ingridians_qty --------
                                $min_ingridians_qty = min($makeable_product_qty_array);
                                // -------- Set Product QTY = $min_ingridians_qty --------
                                $sql = "UPDATE products SET stock_qty = {$min_ingridians_qty} WHERE product_name = '{$product}'";
                                insert_query($sql, "$product Available Qty : $min_ingridians_qty", "Update Product available Qty");

                                /// ========== update Has_Stock state ==========
                                if ($min_ingridians_qty > 0) {
                                    $sql = "UPDATE products SET has_stock = 1 WHERE product_name = '{$product}'";
                                    insert_query($sql, "$product is In Stock", "Update Product Has_Stock State");
                                } else {
                                    $sql = "UPDATE products SET has_stock = 0 WHERE product_name = '{$product}'";
                                    insert_query($sql, "$product is Out of Stock", "Update Product Has_Stock State");
                                }
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

        // Profit
        $total_bill_profit = $bill_advance - $total_bill_cost;

        // ==================== Profit Distribution ====================
        // Send Profit to Accounts
        $profit_for_biller = ($total_bill_profit / 100) * 15;
        $sql = "UPDATE employees SET salary = salary + {$profit_for_biller} WHERE emp_name = '{$biller}'";
        insert_query($sql, "send biller Profit : {$biller} Rs. {$profit_for_biller}", "Add Biller Profit to Employee Table");

        $description = "Profit from Invoice Number : $bill_no";
        $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$employee_id', '$profit_for_biller', '$description')";
        insert_query($sql, "Employee ID: $employee_id, Rs. $profit_for_biller", "Employee Salary Paid - Update Salary Table");

        // Update Invoice Total Profit And Cost
        $sql = "UPDATE invoice SET cost = {$total_bill_cost}, profit = {$total_bill_profit} WHERE invoice_number = {$bill_no}";
        insert_query($sql, "Invoice Number : $bill_no, Total Bill Cost : $total_bill_cost, Total Bill Profit : $total_bill_profit", "Update Invoice Total Profit And Cost");

        // Advance Hambuna Salli Cash in Hand Ekata ekathu wenawa
        $sql = "UPDATE accounts SET amount = amount + {$bill_advance} WHERE account_name = 'cash_in_hand'";
        insert_query($sql, "Invoice Number : $bill_no, Rs. {$bill_advance}", "Add Advance Money to Cash in Hand");

        // Add Transaction Log -> type, description, amount
        $transaction_type = 'Invoice - Cash In';
        $transaction_description = "$bill_no - $customer_name";
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