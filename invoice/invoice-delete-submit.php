<?php
// Set Error Array
$error_array = array();
require_once '../inc/config.php'; // Include your database configuration

//  !+!+!+!+!++!+!+!+!++!+!+!+!+!++!+!+!++!+!+ IMPORTANT   : 
// ====================================================================================================================================
// Replace Database Data Updates with insert_query() function from config.php
// Check Code before running. This code is not tested and 100% AI Generated.
// ====================================================================================================================================
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Invoice</title>
</head>

<body>

    <?php
    // Check if Delete Button was Clicked
    if (isset($_POST['delete']) && isset($_POST['bill-no'])) {
        // Retrieve the Invoice Number
        $invoice_number = $_POST['bill-no'];

        // Step 1: Retrieve Related Data

        // Get the Invoice details
        $sql = "SELECT * FROM invoice WHERE invoice_number = $invoice_number";
        $result = mysqli_query($con, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            $error_array[] = "Invoice not found.";
        } else {
            $invoice_data = mysqli_fetch_assoc($result);

            // Get Sales Data related to this Invoice
            $sales_data = array();
            $sql = "SELECT * FROM sales WHERE invoice_number = $invoice_number";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $sales_data[] = $row;
            }

            // Get One-Time Product Sales Data related to this Invoice
            $one_time_sales_data = array();
            $sql = "SELECT * FROM oneTimeProducts_sales WHERE invoice_number = $invoice_number";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                $one_time_sales_data[] = $row;
            }

            // Step 2: Adjust Stock for Products in the Sales Data

            foreach ($sales_data as $sale) {
                $product = $sale['product'];
                $qty = $sale['qty'];

                // Get Product's Raw Material Info
                // Check if makeProduct table exists first
                $tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'makeProduct'");
                if (mysqli_num_rows($tableCheck) > 0) {
                    $sql = "SELECT item_name, qty FROM makeProduct WHERE product_name='$product'";
                    $result = mysqli_query($con, $sql);
                    while ($record = mysqli_fetch_assoc($result)) {
                        $item_name = $record['item_name'];
                        $item_qty = $record['qty'] * $qty;
    
                        // Restore Stock
                        $sql = "UPDATE `items` SET qty = qty + $item_qty WHERE item_name = '$item_name'";
                        if (!mysqli_query($con, $sql)) {
                            $error_array[] = "Failed to update stock for $item_name.";
                        }
                    }
                }

                // Restore Product Stock Quantity
                $sql = "UPDATE products SET stock_qty = stock_qty + $qty WHERE product_name = '$product'";
                if (!mysqli_query($con, $sql)) {
                    $error_array[] = "Failed to update stock for product $product.";
                }
            }

            // Step 3: Delete Sales Data
            $sql = "DELETE FROM sales WHERE invoice_number = $invoice_number";
            if (!mysqli_query($con, $sql)) {
                $error_array[] = "Failed to delete sales data for invoice $invoice_number.";
            }

            // Step 4: Delete One-Time Product Sales Data
            $sql = "DELETE FROM oneTimeProducts_sales WHERE invoice_number = $invoice_number";
            if (!mysqli_query($con, $sql)) {
                $error_array[] = "Failed to delete one-time product sales data for invoice $invoice_number.";
            }

            // Step 5: Adjust Employee Salaries and Company Profit

            // Calculate the amount to deduct from employee salary and company profit
            $profit = $invoice_data['profit'];

            // Get the employee IDs for the biller and worker
            $biller = $invoice_data['biller'];
            $worker = $invoice_data['primary_worker'];

            $biller_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$biller'"))['employ_id'];
            $worker_employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$worker'"))['employ_id'];

            // Deduct the appropriate amounts from employee salaries and company profit
            if ($biller == $worker) {
                $biller_salary_deduction = ($profit / 100) * 15;
                $company_profit_deduction = $profit - $biller_salary_deduction;

                // Update biller salary
                $sql = "UPDATE employees SET salary = salary - $biller_salary_deduction WHERE employ_id = $biller_employee_id";
                if (!mysqli_query($con, $sql)) {
                    $error_array[] = "Failed to update salary for biller $biller.";
                }
            } else {
                $biller_salary_deduction = ($profit / 100) * 5;
                $worker_salary_deduction = ($profit / 100) * 10;
                $company_profit_deduction = $profit - ($biller_salary_deduction + $worker_salary_deduction);

                // Update biller salary
                $sql = "UPDATE employees SET salary = salary - $biller_salary_deduction WHERE employ_id = $biller_employee_id";
                if (!mysqli_query($con, $sql)) {
                    $error_array[] = "Failed to update salary for biller $biller.";
                }

                // Update worker salary
                $sql = "UPDATE employees SET salary = salary - $worker_salary_deduction WHERE employ_id = $worker_employee_id";
                if (!mysqli_query($con, $sql)) {
                    $error_array[] = "Failed to update salary for worker $worker.";
                }
            }

            // Update company profit
            $sql = "UPDATE accounts SET amount = amount - $company_profit_deduction WHERE account_name = 'Company Profit'";
            if (!mysqli_query($con, $sql)) {
                $error_array[] = "Failed to update company profit.";
            }

            // Step 6: Delete the Invoice
            $sql = "DELETE FROM invoice WHERE invoice_number = $invoice_number";
            if (!mysqli_query($con, $sql)) {
                $error_array[] = "Failed to delete invoice $invoice_number.";
            }

            // Step 7: Log the Transaction (Optional)
            $description = "Invoice $invoice_number deleted.";
            $sql = "INSERT INTO transaction_log (transaction_type, description, amount) VALUES ('Invoice Deletion', '$description', '-$profit')";
            if (!mysqli_query($con, $sql)) {
                $error_array[] = "Failed to log the invoice deletion.";
            }

            // Check for Errors
            if (empty($error_array)) {
                echo "<h1>Invoice $invoice_number successfully deleted.</h1>";
            } else {
                echo "<h1>Errors occurred:</h1>";
                echo "<pre>";
                print_r($error_array);
                echo "</pre>";
            }
        }
    }
    ?>

</body>

</html>

<?php end_db_con(); // Close the database connection ?>
