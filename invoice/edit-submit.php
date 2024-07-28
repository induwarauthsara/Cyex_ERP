<?php
//  !+!+!+!+!++!+!+!+!++!+!+!+!+!++!+!+!++!+!+ IMPORTANT   : 
// ====================================================================================================================================
// if you wish to Continue this file, Make Sure to add 3 Parameters for insert_query function (insert_query($query, $msg, $action))
// ====================================================================================================================================

echo "<pre>";
print_r($_POST);
echo "</pre>";
// die();
//Set Error Array
$error_array = array();
require_once '../inc/config.php'; ?>

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
        $bill_no = $_POST['bill-no'];
        $date = $_POST['today'];
        $bill_total = $_POST['total'];
        $bill_discount = $_POST['discount'];
        $bill_advance = $_POST['advance'];
        $bill_balance = $_POST['balance'];
        $bill_rows = $_POST['no'];


        // ========== Full Payment ==========
        if (isset($_POST['submit_and_print_fullPayment']) || isset($_POST['submit_and_fullPayment'])) {
            $bill_advance = $bill_total;
        }

        // check full paid
        if ($bill_balance <= 0.00) {
            $full_paid = 1;
        } else {
            $full_paid = 0;
        }

        // Check Customer is in Database?
        $has_customer_name_in_db = "";
        $sql = "SELECT COUNT(customer_name) FROM customers WHERE customer_name = '$customer_name';";
        $selected_customer = mysqli_query($con, $sql);
        $selected_customer = mysqli_fetch_array($selected_customer);
        if ($selected_customer['COUNT(customer_name)'] == 0) {
            $sql = "INSERT INTO customers (customer_name, customer_mobile) VALUES ('$customer_name', $customer_mobile)";
            insert_query($sql, "Add New Customer");
        }


        // Send Invoice Data to Database
        $sql = "UPDATE `invoice` 
        SET `invoice_number` = '$bill_no', `customer_name` = '$customer_name',`invoice_date` = '$date',`customer_mobile` = '$customer_mobile',
        `total` = '$bill_total',`discount` = '$bill_discount',`advance` = '$bill_advance',`balance` = '$bill_balance',`full_paid` = '$full_paid' 
        WHERE `invoice`.`invoice_number` = $bill_no";

        // $sql = "INSERT INTO invoice (invoice_number, customer_name, invoice_date, customer_mobile, total, discount, advance, balance, full_paid)
        // VALUES ('{$bill_no}', '{$customer_name}', '{$date}', '{$customer_mobile}', '{$bill_total}', '{$bill_discount}', 
        // '{$bill_advance}', '{$bill_balance}', {$full_paid})";
        insert_query($sql, "Send Invoice Data to Database");


        for ($no = 0; $no <= $bill_rows; $no++) {
            if (isset($_POST["product_{$no}"])) { // Check row is removed
                if (!function_exists('sales_arry')) { // voice function re-call in for loop

                    function sales_arry($product, $qty, $rate, $amount)
                    {
                        global $no;
                        global $bill_no;
                        global $con;
                        global $bill_advance;


                        $product = $_POST["{$product}_{$no}"];
                        $qty = $_POST["{$qty}_{$no}"];
                        $rate = $_POST["{$rate}_{$no}"];
                        $amount = $_POST["{$amount}_{$no}"];
                        $todo = "Unchecked";

                        echo $product . "<br>";
                        echo $qty . "<br>";
                        echo $rate . "<br>";
                        echo $amount . "<br>";
                        echo "<br>";

                        // Send Sales Data to DB
                        $sql = "INSERT INTO sales (invoice_number, product, qty, rate, amount, todo)
                        VALUES ('{$bill_no}', '{$product}', '{$qty}', '{$rate}', '{$amount}', '{$todo}')";
                        insert_query($sql, "InvoiceNumber : $bill_no, Product : $product, Qty : $qty, Rate : $rate, Amount : $amount", "Product Sales");

                        /*  //Wikunapu Product eka Stock eken adu wenawa. (meka Automated kala yata)
                        $sql = "UPDATE products SET stock_qty = stock_qty - {$qty} WHERE product_name = '{$product}'";
                        insert_query($sql, "Fall Sell Product Qty from Stock");
                        */

                        // Product Cost eka Stock Account ekata add wenawa
                        $cost_sql = "SELECT cost FROM products WHERE product_name = '{$product}'";
                        $cost_result = mysqli_query($con, $cost_sql);
                        $cost = mysqli_fetch_assoc($cost_result);
                        $cost = $cost['cost'] * $qty;
                        $sql = "UPDATE accounts SET amount = amount + {$cost} WHERE account_name = 'Stock Account'";
                        insert_query($sql, "Add Product Cost to Stock Account : (Product : $product, Cost : $cost)", "Product Sales");

                        // Profit eka accounts walata bedila yanawa
                        $profit = $amount - $cost;

                        $profit_for_utility_bills_account = ($profit / 100) * 20;
                        $sql = "UPDATE accounts SET amount = amount + {$profit_for_utility_bills_account} WHERE account_name = 'Utility Bills'";
                        insert_query($sql, "send Utility Bills Profit");

                        $profit_for_machines_account = ($profit / 100) * 20;
                        $sql = "UPDATE accounts SET amount = amount + {$profit_for_machines_account} WHERE account_name = 'Machines Account'";
                        insert_query($sql, "send Machines Account Profit");

                        $profit_for_stock_account = ($profit / 100) * 8.5;
                        $sql = "UPDATE accounts SET amount = amount + {$profit_for_stock_account} WHERE account_name = 'Stock Account'";
                        insert_query($sql, "send Stock Account Profit");

                        $profit_for_company_profit_account = ($profit / 100) * 15;
                        $sql = "UPDATE accounts SET amount = amount + {$profit_for_company_profit_account} WHERE account_name = 'Company Profit'";
                        insert_query($sql, "send Company Profit Profit");

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
            sales_arry('product', 'qty', 'rate', 'amount');
            echo "<br>";
        }

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
    // // $this_url = "/index.php";
    // /*header("refresh:2; url={$this_url}");*/
    // echo "<script>
    // setTimeout(`location.href = '$this_url';`, 100);
    // </script> ";
} else {
    echo "<h1>Error List</h1>";
    echo "<pre>";
    print_r($error_array);
    echo "</pre>";
}


?>

<?php end_db_con(); ?>