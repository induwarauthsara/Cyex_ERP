<?php
//  !+!+!+!+!++!+!+!+!++!+!+!+!+!++!+!+!++!+!+ IMPORTANT   : 
// ====================================================================================================================================
// if you wish to Continue this file, Make Sure to add 3 Parameters for insert_query function (insert_query($query, $msg, $action))
// ====================================================================================================================================

//   1. Retrieve Previous Invoice and Sales Data
//   2. Update New Invoice (Check FullPaid) Data and Customer Data (Name, Mobile)
//           Retrieve New Invoice and Sales Data
//           Calculate Cost Profit and Update Invoice and Relevant Account Data (Employee, Biller, Company Profit)
//   If Change Stock is required,
//           4. Compare Previous and New Sales Data
//           5. Insert, Update, Delete Sales Data and New One-Time Product Sales Data
//  If Change Account is required,,
//           6. Update Account Data (Check Payment Method is Changed)
//           8. Transaction Log (Add or Fall Advance Money to Related Account)
//           9. Update Todo Status

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

    if (isset($_POST['submit']) && isset($_POST['bill-no'])) {
        // Set Invoice values
        $invoice_number = $_POST['bill-no'];


        // ================================== Compare Previous Invoice and New Invoice Details ==================================

        // =|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=| 1. Retrieve Previous Invoice and Sales Data

        // ========== Get Previous Invoice Details ==========
        $sql = "SELECT * FROM invoice WHERE invoice_number = $invoice_number";
        $result = mysqli_query($con, $sql);
        $previous_invoice = mysqli_fetch_assoc($result);
        // Get Previous Invoice Profit
        $previous_invoice_profit = $previous_invoice['profit'];
        // echo "Previous Invoice Details : <pre>";
        // print_r($previous_invoice);
        // echo "</pre>";

        // ========== Get Previous Invoice Sales Details ==========
        $previous_sales = array(); // Initialize the array to avoid undefined variable error
        $sql = "SELECT * FROM sales WHERE invoice_number = $invoice_number";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $previous_sales[] = $row;
        }
        // echo "Previous Sales Details : <pre>";
        // print_r($previous_sales);
        // echo "</pre>";

        // ========== Get Previous Invoice One Time Product Sales Details ==========
        $previous_one_time_product_sales = array(); // Initialize the array to avoid undefined variable error
        $sql = "SELECT * FROM oneTimeProducts_sales WHERE invoice_number = $invoice_number";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $previous_one_time_product_sales[] = $row;
        }
        // echo "Previous One Time Product Sales Details : <pre>";
        // print_r($previous_one_time_product_sales);
        // echo "</pre>";

        // =|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=| 2. Update New Invoice (Check FullPaid) Data and Customer Data (Name, Mobile)

        // ========== Get New Invoice Details ==========
        $invoice_number = $_POST['bill-no'];
        $invoice_description = $_POST['InvoiceDescription'];
        $customer_name = $_POST['name'];
        $invoice_date = $_POST['today'];
        $customer_mobile = $_POST['tele'];
        $biller = $_POST['biller'];
        $worker = $_POST['default_worker'];
        $total = floatval($_POST['total']) ?? 0;
        $discount = floatval($_POST['discount']) ?? 0;
        $advance = floatval($_POST['advance']) ?? 0;
        $balance = $_POST['balance'] ?? 0;
        $paymentMethod = $_POST['PaymentMethod'];
        $last_row_no = $_POST['no'] + 1;
        $change_stock = $_POST['ChangeStock'] ?? null;
        $change_account = $_POST['ChangeAccount'] ?? null;

        // Check todo
        if (isset($_POST['add_to_todo']) && isset($_POST['todoName']) && isset($_POST['todoTime'])) {
            $todoName = $_POST['todoName'];
            $todoTime = $_POST['todoTime'];
            // check todo is in database
            $sql = "SELECT COUNT(title) FROM todo WHERE invoice_number = '$invoice_number';";
            $result = mysqli_query($con, $sql);
            $result = mysqli_fetch_array($result);
            if ($result['COUNT(title)'] == 0) {
                $sql = "INSERT INTO `todo`(`invoice_number`, `title`, `submision_time`) VALUES ('$invoice_number','$todoName','$todoTime');";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "$todoName must Complete at $todoTime", "Update Todo Task");
            }else{
                $sql = "UPDATE `todo` SET `title`='$todoName',`submision_time`='$todoTime' WHERE invoice_number = '$invoice_number';";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "$todoName must Complete at $todoTime", "Update Todo Task");
            }
        }

        // get Employee ID of Biller and Default Worker
        $biller_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$biller'"))['employ_id'];
        $worker_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$worker'"))['employ_id'];

        // Check Full Paid
        if ($balance <= 0.00) {
            $full_paid = 1;
        } else {
            $full_paid = 0;
        }

        // Update Customer Data
        // Check Customer is in Database?
        $has_customer_name_in_db = "";
        $sql = "SELECT COUNT(customer_name) FROM customers WHERE customer_name = '$customer_name';";
        $selected_customer = mysqli_query($con, $sql);
        $selected_customer = mysqli_fetch_array($selected_customer);
        if ($selected_customer['COUNT(customer_name)'] == 0) {
            $sql = "INSERT INTO customers (customer_name, customer_mobile) VALUES ('$customer_name', $customer_mobile);";
            echo "<br>EXPLAIN $sql</br>";
            // insert_query($query, $msg, $action)
            insert_query($sql, "Add New Customer" , "Add New Customer");
        }

        // =|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|  Calculate Cost Profit and Update Invoice and Relevant Account Data (Employee, Biller, Company Profit)

        // Calculate Cost 
        $new_invoice_cost = 0;
        $new_invoice_oneTimeProduct_profit = 0; //  One-Time Product  Profit is not added to Invoice Profit Distribution
        // ========== Get New Invoice Sales Details ==========
        $new_sales = [];
        $new_one_time_product_sales = [];

        // Loop through the POST data to organize sales data
        for ($i = 0; $i < $last_row_no; $i++) {
            if (isset($_POST['salesID_' . $i])) {
                $new_sales[] = [
                    'sales_id' => $_POST['salesID_' . $i],
                    'product' => $_POST['product_' . $i],
                    'qty' => $_POST['qty_' . $i],
                    'rate' => $_POST['rate_' . $i],
                    'amount' => $_POST['amount_' . $i],
                ];
            } elseif (isset($_POST['OTProductID_' . $i])) {
                $new_one_time_product_sales[] = [
                    'oneTimeProduct_id' => $_POST['OTProductID_' . $i],
                    'product' => $_POST['product_' . $i],
                    'qty' => $_POST['qty_' . $i],
                    'rate' => $_POST['rate_' . $i],
                    'amount' => $_POST['amount_' . $i],
                ];
            } elseif (isset($_POST['product_' . $i])) {
                // check this product is sales or one time product sales
                // check this is saved product in DB products table
                $product = $_POST['product_' . $i];
                $sql = "SELECT product_name FROM products WHERE product_name = '$product';";
                $result = mysqli_query($con, $sql);
                if (mysqli_num_rows($result) > 0) {
                    // this is New Added product sale
                    $new_sales[] = [
                        'product' => $_POST['product_' . $i],
                        'qty' => $_POST['qty_' . $i],
                        'rate' => $_POST['rate_' . $i],
                        'amount' => $_POST['amount_' . $i],
                    ];
                } else {
                    // this is New Added one time product sale
                    $new_one_time_product_sales[] = [
                        'product' => $_POST['product_' . $i],
                        'qty' => $_POST['qty_' . $i],
                        'rate' => $_POST['rate_' . $i],
                        'amount' => $_POST['amount_' . $i],
                    ];
                }
            }
        }

        // echo "New Sales Details : <pre>"; 
        // print_r($new_sales); // for debugging
        // echo "</pre>";

        // echo "New One Time Product Sales Details : <pre>";  
        // print_r($new_one_time_product_sales); // for debugging
        // echo "</pre>";

        // Cost of Sales Data
        foreach ($new_sales as $sale) {
            if (isset($sale['product'])) {

                if (isset($sale['sales_id'])) {
                    $sales_id = $sale['sales_id'];
                }
                $product = $sale['product'];
                $qty = $sale['qty'];
                $rate = $sale['rate'];
                $amount = $sale['amount'];

                $cost_sql = "SELECT cost FROM products WHERE product_name  = '$product'";
                // echo "<br>ERR : EXPLAIN $cost_sql</br>";
                $cost_result = mysqli_query($con, $cost_sql);
                if ($cost_result && mysqli_num_rows($cost_result) > 0) {
                    $cost = mysqli_fetch_assoc($cost_result);
                    $cost = $cost['cost'] * $qty;
                    $new_invoice_cost += $cost;
                    echo "<br>$product cost: $cost</br>";
                }
            }
        }

        // Cost of One-Time Product Sales Data  ============= One-Time Product  Profit is not added to Invoice Profit Distribution
        foreach ($new_one_time_product_sales as $sale) {
            $product = $sale['product'];
            $qty = $sale['qty'];
            $rate = $sale['rate'];
            $amount = $sale['amount'];
            if (isset($sale['oneTimeProduct_id'])) {
                $oneTimeProduct_id = $sale['oneTimeProduct_id'];


                $cost_sql = "SELECT cost, profit FROM oneTimeProducts_sales WHERE oneTimeProduct_id = '$oneTimeProduct_id'";
                echo "<br>EXPLAIN $cost_sql</br>";
                $cost_result = mysqli_query($con, $cost_sql);
                if ($cost_result && mysqli_num_rows($cost_result) > 0) {
                    $cost_output = mysqli_fetch_assoc($cost_result);
                    $cost = $cost_output['cost'];
                    $new_invoice_cost += $cost; // One-Time Product Profit is not added to Invoice Profit Distribution
                    $OneTimeProduct_profit = $cost_output['profit'];
                    $new_invoice_oneTimeProduct_profit += $OneTimeProduct_profit;
                    echo "<br>$product cost: $cost</br>";
                }
            } else {
                $new_invoice_oneTimeProduct_profit = $amount;
            }
        }

        // Calculate Profit
        echo "Cost : $new_invoice_cost</br>";
        echo "Advance : $advance</br>";
        $profit = $advance - $new_invoice_cost;

        // Update Invoice Data
        $sql = "UPDATE `invoice` SET `invoice_description` = '$invoice_description', `customer_name` = '$customer_name', `invoice_date` = '$invoice_date', `customer_mobile` = '$customer_mobile', `biller` = '$biller',
                    `primary_worker` = '$worker', `total` = '$total', `discount` = '$discount', `advance` = '$advance', `balance` = '$balance', `full_paid` = '$full_paid', `paymentMethod` = '$paymentMethod',
                    `cost` = '$new_invoice_cost', `profit` = '$profit'
                WHERE `invoice_number` = $invoice_number ;";
        echo "<br>EXPLAIN $sql</br>";
        insert_query($sql, "InvoiceNumber : $invoice_number, Invoice Description : $invoice_description, Customer Name : $customer_name, Invoice Date : $invoice_date, Customer Mobile : $customer_mobile, Biller : $biller, Primary Worker : $worker, Total : $total, Discount : $discount, Advance : $advance, Balance : $balance, Full Paid : $full_paid, Payment Method : $paymentMethod, Cost : $new_invoice_cost, Profit : $profit", "Update Invoice");

        // ============= Generated Profit goto Biller & Worker & Company
        echo "<br> New Invoice One-Time Product Profit : $new_invoice_oneTimeProduct_profit</br>";
        $generated_profit = $profit - $previous_invoice_profit;
        $generated_profit -= $new_invoice_oneTimeProduct_profit; // One-Time Product Profit is not added to Invoice Profit Distribution
        echo "</br>Previous Profit : $previous_invoice_profit</br>";
        echo "New Profit : $profit</br>";
        echo "Generated Profit : $generated_profit</br>";
        if ($biller == $worker) {
            $for_biller = ($generated_profit / 100) * 15;
            $for_worker = 0;
            echo "</br>Biller and Worker equal. Profit : $for_biller</br>";
            $sql = "UPDATE employees SET salary = salary + {$for_biller} WHERE emp_name = '{$biller}';";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "send biller Profit : {$biller} Rs. {$for_biller} , when Invoice Edit : $invoice_number", "Add Biller Profit to Employee Table when Invoice Edit");

            $description = "Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
            $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$biller_employee_id', '$for_biller', '$description');";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "Employee ID: $biller_employee_id, Rs. $for_biller , when Invoice Edit : $invoice_number", "Employee Salary Paid - Update Salary Table when Invoice Edit");
        } else { // if Biller is not Worker
            $for_biller = ($generated_profit / 100) * 5;
            $for_worker = ($generated_profit / 100) * 10;
            echo "</br>Biller and Worker not equal. Profit : Biller : $for_biller, Worker : $for_worker</br>";
            // for Biller
            $sql = "UPDATE employees SET salary = salary + {$for_biller} WHERE emp_name = '{$biller}';";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "send biller Profit : {$biller} Rs. {$for_biller} , when Invoice Edit : $invoice_number", "Add Biller Profit to Employee Table when Invoice Edit");

            $description = "Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'>$invoice_number</a>";
            $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$biller_employee_id', '$for_biller', '$description');";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "Employee ID: $biller_employee_id, Rs. $for_biller , when Invoice Edit : $invoice_number", "Employee Salary Paid - Update Salary Table when Invoice Edit");

            // for Worker
            $sql = "UPDATE employees SET salary = salary + {$for_worker} WHERE emp_name = '{$worker}';";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "send worker Profit : {$worker} Rs. {$for_worker} , when Invoice Edit : $invoice_number", "Add Worker Profit to Employee Table");

            $description = "Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
            $sql = "INSERT INTO salary (emp_id, amount, description) VALUES ('$worker_employee_id', '$for_worker', '$description');";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "Employee ID: $worker_employee_id, Rs. $for_worker , when Invoice Edit : $invoice_number", "Employee Salary Paid - Update Salary Table");
        }

        // 85% for Company Profit
        $for_company = $generated_profit - ($for_biller + $for_worker);
        $sql = "UPDATE accounts SET amount = amount + {$for_company} WHERE account_name = 'Company Profit';";
        echo "<br>EXPLAIN $sql</br>";
        insert_query($sql, "Company Profit : Rs. {$for_company}, Invoice $invoice_number Invoice Edit", "Update Company Profit when Invoice Edit");



        // =|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=|=| Update Stock Data and Raw Material Data
        if ($change_stock) {
            // ============== Compare Sales Data ============
            $toInsertSales = [];
            $toUpdateSales = [];
            $toDeleteSales = [];

            foreach ($new_sales as $new_sale) {
                $found = false;
                foreach ($previous_sales as $index => $prev_sale) {
                    if (isset($new_sale['sales_id']) && $new_sale['sales_id'] == $prev_sale['sales_id']) {
                        $found = true;
                        // Check if there are any changes
                        if (
                            $new_sale['product'] != $prev_sale['product'] ||
                            $new_sale['qty'] != $prev_sale['qty'] ||
                            $new_sale['rate'] != $prev_sale['rate'] ||
                            $new_sale['amount'] != $prev_sale['amount']
                        ) {
                            $toUpdateSales[] = $new_sale;
                        }
                        unset($previous_sales[$index]); // Remove matched item from previous_sales
                        break;
                    }
                }
                if (!$found) {
                    $toInsertSales[] = $new_sale;
                }
            }

            // The remaining records in $previous_sales are the ones to delete
            $toDeleteSales = array_merge($toDeleteSales, $previous_sales);

            // ============== Compare One-Time Product Sales Data ============
            $toInsertOneTimeSales = [];
            $toUpdateOneTimeSales = [];
            $toDeleteOneTimeSales = [];

            foreach ($new_one_time_product_sales as $new_sale) {
                $found = false;
                foreach ($previous_one_time_product_sales as $index => $prev_sale) {
                    if ($new_sale['oneTimeProduct_id'] == $prev_sale['oneTimeProduct_id']) {
                        $found = true;
                        // Check if there are any changes
                        if (
                            $new_sale['product'] != $prev_sale['product'] ||
                            $new_sale['qty'] != $prev_sale['qty'] ||
                            $new_sale['rate'] != $prev_sale['rate'] ||
                            $new_sale['amount'] != $prev_sale['amount']
                        ) {
                            $toUpdateOneTimeSales[] = $new_sale;
                        }
                        unset($previous_one_time_product_sales[$index]); // Remove matched item from previous_one_time_product_sales
                        break;
                    }
                }
                if (!$found) {
                    $toInsertOneTimeSales[] = $new_sale;
                }
            }

            // The remaining records in $previous_one_time_product_sales are the ones to delete
            $toDeleteOneTimeSales = array_merge($toDeleteOneTimeSales, $previous_one_time_product_sales);

            //============================= Update Database with the new Sales and One Time Product data

            // Function for Update Raw Item Stock When Product Sales Change (Add, Edit, Delete)
            function updateRawItemStock($product, $qty)
            {
                global $con;

                // ========== Sold Product eka Stock eken adu wenawa. ==========
                // -------- Get Product makeProduct to Array --------
                $sql = "SELECT item_name FROM makeProduct WHERE product_name='{$product}';";
                echo "<br>EXPLAIN $sql</br>";
                $result = mysqli_query($con, $sql);
                $ingredients_list = array();
                if (mysqli_num_rows($result) > 0) {
                    while ($record = mysqli_fetch_assoc($result)) {
                        array_push($ingredients_list, $record["item_name"]);
                    }
                    $thisProductHasRawMaterials = true;
                } else {
                    $thisProductHasRawMaterials = false;
                }

                if ($thisProductHasRawMaterials) {
                    // --------  Fall Product Items from Item List -------
                    // Select Item Qty of Product
                    for ($i = 0; $i < count($ingredients_list); $i++) {
                        $selected_item = $ingredients_list[$i];
                        $ingredients_product_item_qty = "SELECT qty FROM makeProduct WHERE item_name = '{$selected_item}' AND product_name = '{$product}';";
                        $result = mysqli_query($con, $ingredients_product_item_qty);
                        $product_item_qty = mysqli_fetch_array($result)['qty'] * $qty;

                        // Fall Item qty
                        $fall_item = "UPDATE `items` SET qty= qty - {$product_item_qty} WHERE item_name = '{$selected_item}';";
                        echo "<br>EXPLAIN $fall_item</br>";
                        insert_query($fall_item, "Fall $selected_item - $product_item_qty from Stock", "fall item from stock");
                    }


                    /// ========== Stock eke Product wala QTY eka hadanawa ==========

                    // -------- Get QTY of ingredients_list to Array --------
                    $ingredients_requirement_qty_array = array();
                    $item_qty_array = array();
                    $makeable_product_qty_array = array();

                    for ($i = 0; $i < count($ingredients_list); $i++) {
                        $selected_item = $ingredients_list[$i];

                        $selected_item_req_qty_sql = "SELECT qty FROM makeProduct WHERE item_name = '{$selected_item}' AND product_name = '{$product}';";
                        echo "<br>EXPLAIN $selected_item_req_qty_sql</br>";
                        $result = mysqli_query($con, $selected_item_req_qty_sql);
                        $record = mysqli_fetch_assoc($result);
                        array_push($ingredients_requirement_qty_array, $record["qty"]);

                        $selected_item_qty_sql = "SELECT qty FROM items WHERE item_name = '{$selected_item}';";
                        echo "<br>EXPLAIN $selected_item_qty_sql</br>";
                        $result = mysqli_query($con, $selected_item_qty_sql);
                        $record = mysqli_fetch_assoc($result);
                        array_push($item_qty_array, $record["qty"]);

                        $selected_item_ingredients_requirement = $ingredients_requirement_qty_array[$i];
                        $selected_item_qty = $item_qty_array[$i];
                        $makeable_product_qty = $selected_item_qty / $selected_item_ingredients_requirement;
                        array_push($makeable_product_qty_array, $makeable_product_qty);
                    }

                    // -------- Select Min QTY of ingredients_qty --------
                    $min_ingredients_qty = min($makeable_product_qty_array);
                    // -------- Set Product QTY = $min_ingredients_qty --------
                    $sql = "UPDATE products SET stock_qty = {$min_ingredients_qty} WHERE product_name = '{$product}';";
                    echo "<br>EXPLAIN $sql</br>";
                    insert_query($sql, "$product has $min_ingredients_qty qty", "Update Product available Qty");

                    /// ========== update Has_Stock state ==========
                    if ($min_ingredients_qty > 0) {
                        $sql = "UPDATE products SET has_stock = 1 WHERE product_name = '{$product}';";
                        echo "<br>EXPLAIN $sql</br>";
                        insert_query($sql, "$product stock is available", "Update Product Has_Stock State");
                    } else {
                        $sql = "UPDATE products SET has_stock = 0 WHERE product_name = '{$product}';";
                        echo "<br>EXPLAIN $sql</br>";
                        insert_query($sql, "$product stock is not available", "Update Product Has_Stock State");
                    }
                }
            }

            // Insert New Sales Data
            foreach ($toInsertSales as $sale) {
                $product = $sale['product'];
                $qty = $sale['qty'];
                $rate = $sale['rate'];
                $amount = $sale['amount'];

                // Calculate Cost and Profit of This Sale Data
                $cost_sql = "SELECT cost FROM products WHERE product_name = '{$product}';";
                // echo "<br>ERR : EXPLAIN $cost_sql</br>";
                $cost_result = mysqli_query($con, $cost_sql);
                if ($cost_result && mysqli_num_rows($cost_result) > 0) {
                    $cost_row = mysqli_fetch_assoc($cost_result);
                    $cost = $cost_row['cost'] * $qty;
                }
                $profit = $amount - $cost;

                // Send Sales Data to DB
                $sql = "INSERT INTO sales (invoice_number, product, qty, rate, amount, worker, cost, profit)
                                    VALUES ('{$invoice_number}', '{$product}', '{$qty}', '{$rate}', '{$amount}', '{$worker}', '{$cost}', '{$profit}');";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "Add New Sale : $product When Invoice $invoice_number Edit", "Add New Sale");

                // Update Raw Item Stock When Product Sales Change (Add, Edit, Delete)
                updateRawItemStock($product, $qty);
            }

            // Update Sales Data
            foreach ($toUpdateSales as $sale) {
                $sales_id = $sale['sales_id'];
                $product = $sale['product'];
                $qty = $sale['qty'];
                $rate = $sale['rate'];
                $amount = $sale['amount'];

                // Calculate Cost and Profit of This Sale Data
                $cost_sql = "SELECT cost FROM products WHERE product_name = '{$product}';";
                $cost_result = mysqli_query($con, $cost_sql);
                $cost_row = mysqli_fetch_assoc($cost_result);
                $cost = $cost_row['cost'] * $qty;
                $profit = $amount - $cost;

                $sql = "UPDATE sales SET qty = $qty, rate = $rate, amount = $amount, cost = $cost WHERE sales_id = $sales_id;";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "Update Sales Data When Invoice $invoice_number Edit", "Update Sales Data");

                // get changed qty
                $sql = "SELECT qty FROM sales WHERE sales_id = $sales_id;";
                $result = mysqli_query($con, $sql);
                $result = mysqli_fetch_array($result);
                $previous_qty = $result['qty'];
                $changed_qty = $qty - $previous_qty;

                // Update Raw Item Stock When Product Sales Change (Add, Edit, Delete)
                updateRawItemStock($product, $changed_qty);
            }

            // Delete Sales Data
            foreach ($toDeleteSales as $sale) {
                $sales_id = $sale['sales_id'];
                $product = $sale['product'];
                $qty = $sale['qty'];
                $sql = "DELETE FROM sales WHERE sales_id = $sales_id;";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "Delete Sales Data When Invoice $invoice_number Edit", "Delete Sales Data");

                // Update Raw Item Stock When Product Sales Change (Add, Edit, Delete)
                updateRawItemStock($product, -$qty);
            }


            // ======================================== Update One-Time-Product Data

            // Add New One-Time Product Sales Data
            foreach ($toInsertOneTimeSales as $sale) {
                $product = $sale['product'];
                $qty = $sale['qty'];
                $rate = $sale['rate'];
                $amount = $sale['amount'];

                // Send Sales Data to DB
                $sql = "INSERT INTO oneTimeProducts_sales (invoice_number, product, qty, rate, amount)
                                    VALUES ('{$invoice_number}', '{$product}', '{$qty}', '{$rate}', '{$amount}');";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "Add New One-Time Product Sale : $product When Invoice $invoice_number Edit", "Add New One-Time Product Sale");
            }

            // Update One-Time Product Sales Data
            foreach ($toUpdateOneTimeSales as $sale) {
                $oneTimeProduct_id = $sale['oneTimeProduct_id'];
                $product = $sale['product'];
                $qty = $sale['qty'];
                $rate = $sale['rate'];
                $amount = $sale['amount'];

                // Calculate Cost and Profit of This Sale Data
                $cost_sql = "SELECT cost FROM oneTimeProducts_sales WHERE oneTimeProduct_id = $oneTimeProduct_id;";
                $cost_result = mysqli_query($con, $cost_sql);
                $cost_row = mysqli_fetch_assoc($cost_result);
                $cost = $cost_row['cost'];
                $profit = $amount - $cost;

                $sql = "UPDATE oneTimeProducts_sales SET qty = $qty, rate = $rate, amount = $amount, cost = $profit WHERE oneTimeProduct_id = $oneTimeProduct_id;";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "Update One-Time Product Sales Data When Invoice $invoice_number Edit", "Update One-Time Product Sales Data");
            }

            // Delete One-Time Product Sales Data
            foreach ($toDeleteOneTimeSales as $sale) {
                $oneTimeProduct_id = $sale['oneTimeProduct_id'];
                $product = $sale['product'];
                $qty = $sale['qty'];
                $sql = "DELETE FROM oneTimeProducts_sales WHERE oneTimeProduct_id = $oneTimeProduct_id;";
                echo "<br>EXPLAIN $sql</br>";
                insert_query($sql, "Delete One-Time Product Sales Data When Invoice $invoice_number Edit", "Delete One-Time Product Sales Data");
            }


            // Output the comparison results
            echo "<h1>Comparison Results</h1>";
            echo "<h2>Sales Data</h2>";
            echo "<h3>Insert</h3>";
            echo "<pre>";
            print_r($toInsertSales);
            echo "</pre>";

            echo "<h3>Update</h3>";
            echo "<pre>";
            print_r($toUpdateSales);
            echo "</pre>";

            echo "<h3>Delete</h3>";
            echo "<pre>";
            print_r($toDeleteSales);
            echo "</pre>";

            echo "<h2>One-Time Product Sales Data</h2>";
            echo "<h3>Insert</h3>";
            echo "<pre>";
            print_r($toInsertOneTimeSales);
            echo "</pre>";

            echo "<h3>Update</h3>";
            echo "<pre>";
            print_r($toUpdateOneTimeSales);
            echo "</pre>";

            echo "<h3>Delete</h3>";
            echo "<pre>";
            print_r($toDeleteOneTimeSales);
            echo "</pre>";
        }

        if ($change_account) {
            // Update Account Data
            echo "Account Changed<br>";

            // Check Payment Method is Changed

            // Transaction Log (Add or Fall Advance Money to Related Account)
            // Change this as want
            $description = "Profit (Invoice Edit) from Invoice Number : <a href=\'/invoice/print.php?id=$invoice_number\'> $invoice_number </a>";
            $sql = "INSERT INTO transaction_log (transaction_type, description, amount) VALUES ('Invoice - Company Profit', '$description', '$for_company');";
            echo "<br>EXPLAIN $sql</br>";
            insert_query($sql, "Invoice Number : $invoice_number, Payment  : $new_advance_amount, New Invoice Balance : $new_balance_amount ", "Add Invoice Company Profit - transaction_log");

        }
    }
    ?>

    <?php
    echo "    <pre><br><br>";
    print_r($_POST);

    ?>
    </pre>
</body>

</html>

<?php

if (empty($error_array)) {    // Refresh Page
    echo "<script>
            window.history.back();
            window.setTimeout(function() {
                // Get print type from localStorage, default to receipt
                const printType = localStorage.getItem('printType') || 'receipt';
                window.open('/invoice/print.php?id=$invoice_number&printType=' + printType, '_blank');
            }, 100); // 100 milliseconds delay to ensure the back navigation happens first
    </script> ";
} else {
    echo "<h1>Error List</h1>";
    echo "<pre>";
    print_r($error_array);
    echo "</pre>";
}


?>

<?php end_db_con(); ?>