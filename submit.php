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
        $customer_name = $_POST['name'];
        $customer_mobile = $_POST['tele'];
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
            $sql = "INSERT INTO customers (customer_name, customer_mobile) VALUES ('$customer_name', $customer_mobile)";
            insert_query($sql, "Add New Customer");
        }


        // Send Invoice Data to Database
        $sql = "INSERT INTO invoice (customer_name, invoice_date, customer_mobile, biller, total, discount, advance, balance, full_paid)
        VALUES ('{$customer_name}', '{$date}', '{$customer_mobile}', '{$biller}', '{$bill_total}', '{$bill_discount}', '{$bill_advance}', '{$bill_balance}', {$full_paid})";
        insert_query($sql, "Send Invoice Data to Database");

        // Retrieve the auto-generated InvoiceID
        $bill_no = mysqli_insert_id($con);

        // Check todo
        if (isset($_POST['add_to_todo'])) {
            $todoName = $_POST['todoName'];
            $todoTime = $_POST['todoTime'];
            $sql = "INSERT INTO `todo`(`invoice_number`, `title`, `submision_time`) VALUES ('$bill_no','$todoName','$todoTime')";
            insert_query($sql, "Add Todo Item Data to Database");
        }

        // Total Bill Product Cost
        $total_bill_cost = 0;

        for ($no = 0; $no <= $bill_rows; $no++) {
            if (isset($_POST["product_{$no}"])) { // Check row is removed
                if (!function_exists('sales_arry')) { // voice function re-call in for loop

                    function sales_arry($product, $description, $qty, $rate, $amount, $worker)
                    {
                        global $no;
                        global $con;
                        global $biller;
                        global $bill_advance;
                        global $bill_no;
                        global $total_bill_cost;

                        $product = $_POST["{$product}_{$no}"];
                        $description = $_POST["{$description}_{$no}"];
                        $qty = $_POST["{$qty}_{$no}"];
                        $rate = $_POST["{$rate}_{$no}"];
                        $amount = $_POST["{$amount}_{$no}"];
                        $worker = $_POST["{$worker}_{$no}"];

                        echo $product . "<br>";
                        echo $qty . "<br>";
                        echo $rate . "<br>";
                        echo $amount . "<br>";
                        echo $worker . "<br>";
                        echo "<br>";
                        // Product Cost eka Stock Account eken Adu wenawa (Kalin Thibbe + wenna.)
                        $cost_sql = "SELECT cost, profit FROM products WHERE product_name = '{$product}'";
                        $cost_result = mysqli_query($con, $cost_sql);
                        $cost = mysqli_fetch_assoc($cost_result);
                        $cost = $cost['cost'] * $qty;
                        $profit = $cost['profit'] * $qty;
                        $sql = "UPDATE accounts SET amount = amount - {$cost} WHERE account_name = 'Stock Account'";
                        insert_query($sql, "Add Product Cost to Stock Account");

                        // Total Bill Product Cost and Profit
                        $total_bill_cost += $cost;

                        // Send Sales Data to DB
                        $sql = "INSERT INTO sales (invoice_number, product, `description`, qty, rate, amount, worker, cost, profit)
                        VALUES ('{$bill_no}', '{$product}', '{$description}', '{$qty}', '{$rate}', '{$amount}', '{$worker}', '{$cost}', '{$profit}')";
                        insert_query($sql, "Send Invoice data to DB");

                        /*  //Wikunapu Product eka Stock eken adu wenawa. (meka Automated kala yata)
                        $sql = "UPDATE products SET stock_qty = stock_qty - {$qty} WHERE product_name = '{$product}'";
                        insert_query($sql, "Fall Sell Product Qty from Stock");
                        */

                        // Send Profit to Accounts
                        $profit_for_worker = ($profit / 100) * 15;
                        $sql = "UPDATE employees SET salary = salary + {$profit_for_worker} WHERE emp_name = '{$biller}'";
                        insert_query($sql, "send worker Profit");

                        // $profit_for_utility_bills_account = ($profit / 100) * 20;
                        // $sql = "UPDATE accounts SET amount = amount + {$profit_for_utility_bills_account} WHERE account_name = 'Utility Bills'";
                        // insert_query($sql, "send Utility Bills Profit");
                        // 
                        // $profit_for_machines_account = ($profit / 100) * 20;
                        // $sql = "UPDATE accounts SET amount = amount + {$profit_for_machines_account} WHERE account_name = 'Machines Account'";
                        // insert_query($sql, "send Machines Account Profit");
                        // 
                        // $profit_for_stock_account = ($profit / 100) * 8.5;
                        // $sql = "UPDATE accounts SET amount = amount + {$profit_for_stock_account} WHERE account_name = 'Stock Account'";
                        // insert_query($sql, "send Stock Account Profit");
                        // 
                        // $profit_for_company_profit_account = ($profit / 100) * 15;
                        // $sql = "UPDATE accounts SET amount = amount + {$profit_for_company_profit_account} WHERE account_name = 'Company Profit'";
                        // insert_query($sql, "send Company Profit Profit");


                        // Advance Hambuna Salli Cash in Hand Ekata ekathu wenawa
                        $sql = "UPDATE accounts SET amount = amount + {$bill_advance} WHERE account_name = 'cash_in_hand'";
                        insert_query($sql, "Add Advance Money to Cash in Hand");


                        // ========== Wikunapu Product eka Stock eken adu wenawa. ==========
                        // -------- Get Product makeProduct to Array --------
                        $sql = "SELECT item_name FROM makeProduct WHERE product_name='{$product}'";
                        $result = mysqli_query($con, $sql);
                        $ingridians_list = array();
                        if (mysqli_num_rows($result) > 0) {
                            while ($recoard = mysqli_fetch_assoc($result)) {
                                array_push($ingridians_list, $recoard["item_name"]);
                            }
                        }

                        // --------  Fall Product Items from Item List -------
                        // Select Item Qty of Product
                        for ($i = 0; $i < count($ingridians_list); $i++) {
                            $selected_item = $ingridians_list[$i];
                            $ingridians_product_item_qty = "SELECT qty FROM makeProduct WHERE item_name = '{$selected_item}' AND product_name = '{$product}';";
                            $result = mysqli_query($con, $ingridians_product_item_qty);
                            $product_item_qty = mysqli_fetch_array($result)['qty'] * $qty;

                            // Fall Item qty
                            $fall_item = "UPDATE `items` SET qty= qty - {$product_item_qty} WHERE item_name = '{$selected_item}'";
                            insert_query($fall_item, "fall item from stock");
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
                        insert_query($sql, "Update Product available Qty");

                        /// ========== update Has_Stock state ==========
                        if ($min_ingridians_qty > 0) {
                            $sql = "UPDATE products SET has_stock = 1 WHERE product_name = '{$product}'";
                            insert_query($sql, "Update Product Has_Stock State");
                        } else {
                            $sql = "UPDATE products SET has_stock = 1 WHERE product_name = '{$product}'";
                            insert_query($sql, "Update Product Has_Stock State");
                        }
                    }
                }
            } else {
                continue;
            }
            echo $bill_no . "<br>";
            sales_arry('product', 'description', 'qty', 'rate', 'amount', 'worker');
            echo "<br>";
        }

        // Prfit
        $total_bill_profit = $bill_advance - $total_bill_cost;

        // Update Invoice Total Profit And Cost
        $sql = "UPDATE invoice SET cost = {$total_bill_cost}, profit = {$total_bill_profit} WHERE invoice_number = {$bill_no}";
        insert_query($sql, "Update Invoice Total Profit And Cost");


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