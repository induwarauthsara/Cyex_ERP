<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srijaya Bill</title>
    <link rel="stylesheet" href="../style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<?php
$invoice_number = $_GET['id'];
$sql = "select * from invoice where invoice_number = $invoice_number;";
$result = mysqli_query($con, $sql);
$invoice_details = mysqli_fetch_assoc($result);

// Invoice details
$invoice_number = $invoice_details['invoice_number'];
$invoice_description = $invoice_details['invoice_description'];
$customer_name = $invoice_details['customer_name'];
$customer_mobile = $invoice_details['customer_mobile'];
$invoice_date = $invoice_details['invoice_date'];
$total =  $invoice_details['total'];
$discount =  $invoice_details['discount'];
$advance =  $invoice_details['advance'];
$balance =  $invoice_details['balance'];

// Todo List of Invoice
$todo_sql = "SELECT * FROM `todo` WHERE invoice_number = $invoice_number";
$todo_result = mysqli_query($con, $todo_sql);
$todo_result = mysqli_fetch_assoc($todo_result);
if ($todo_result) {
    $todoName = $todo_result['title'];
    $todoTime = $todo_result['submision_time'];
} else {
    $todoName = "";
    $todoTime = "";
}

// Items of Invoices
$sale_sql = "SELECT * FROM `sales` WHERE invoice_number = $invoice_number";
$sales_result = mysqli_query($con, $sale_sql);
// row count
$sales_row_count = mysqli_num_rows($sales_result);

// One Time Product List
$product_list = "SELECT * FROM oneTimeProducts_sales WHERE invoice_number = $invoice_number";
$OTProduct_result = mysqli_query($con, $product_list);
// row count
$OTProduct_row_count = mysqli_num_rows($OTProduct_result);

$total_row_count = $sales_row_count + $OTProduct_row_count;
?>

<body><br>
    <div class="main">
        <div class="bill">
            <div class="header"> <a href="/index.php">
                    <div class="logo-img"> <img src="/logo.png" alt="LOGO">
                </a>
            </div>
            <div class="topic">
                <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
                <h2><?php echo $ERP_COMPANY_ADDRESS; ?>
                    <br><?php echo $ERP_COMPANY_PHONE; ?>
                </h2>
            </div>
        </div>
        <hr>

        <br>
        <form action="edit-submit.php" method="POST">
            <!--  === Invoice Details === -->
            <div id="errors">
                <p>This changes cannot be undone.</p>
            </div>
            <div class="details">
                <div class="customer-details">
                    <div class="customer-name">
                        <label for="name">Customer Name : </label>
                        <input list="customer_list" type="text" name="name" id="name" onchange="customer_add()" required value="<?php echo $customer_name ?>"> <br>
                    </div>

                    <div class="customer-tele">
                        <label for="tele">Customer telephone : </label>
                        <input type="text" name="tele" id="tele" value="<?php echo $customer_mobile ?>" required> <br>
                    </div>
                </div>

                <!--  === Invoice Details === -->
                <div class="bill-details">
                    <div class="bill-no">Bill No. <input type="text" value="<?php echo $invoice_number ?>" name="bill-no" required> </div>
                    <div class="date">Date : <input id="date" type="date" value="<?php echo $invoice_date ?>" name="today" required></div>
                </div>
            </div>

            <div class="content">
                <!-- tabel -->
                <textarea name="InvoiceDescription" id="InvoiceDescription" placeholder="Add Details about this Job / Invoice. Paper Size, Colour, Size, Binding Margin"><?php echo $invoice_description; ?> </textarea>
                <div class="container">
                    <!--Tabel Head Start-->
                    <div class="Product tabel-head">Description</div>
                    <div class="QTY tabel-head">Qty</div>
                    <div class="Rate tabel-head">Rate</div>
                    <div class="Amount tabel-head">Amount</div>
                    <!--Tabel Head End-->
                    <div class="product_list tabel" id="Product">
                        <div id="list">
                            <?php
                            $no = 0;
                            if (mysqli_num_rows($sales_result) > 0) {
                                // output data of each row
                                while ($row = mysqli_fetch_assoc($sales_result)) {
                                    $product = $row['product'];
                                    echo "<input id='rowID_$no' type='hidden' name='salesID_$no' value='{$row['sales_id']}'>";
                                    echo "<input id='product_$no' class='$no' name='product_$no' value='$product'>";
                                    $no++;
                                }
                            }
                            // One Time Product List
                            if (mysqli_num_rows($OTProduct_result) > 0) {
                                // output data of each row
                                while ($row = mysqli_fetch_assoc($OTProduct_result)) {
                                    $product = $row['product'];
                                    echo "<input id='rowID_$no' type='hidden' name='OTProductID_$no' value='{$row['oneTimeProduct_id']}'>";
                                    echo "<input id='product_$no' class='$no' name='product_$no' value='$product'>";
                                    $no++;
                                }
                            }
                            ?>
                        </div>
                        <hr>
                        <input list="products" type="text" id="addproduct">
                        <div onclick="addproduct()" class="add">Add Product</div>
                    </div>

                    <div class="Qty_list tabel" id="qty">
                        <?php
                        $no = 0;
                        $sales_result = mysqli_query($con, $sale_sql);
                        if (mysqli_num_rows($sales_result) > 0) {
                            // output data of each row
                            while ($row = mysqli_fetch_assoc($sales_result)) {
                                $qty = $row['qty'];
                                echo "<input id='qty_$no' type='number' class='$no' oninput=\"change('qty', className, id)\" name='qty_$no' value='$qty'>";
                                $no++;
                            }
                        }
                        // One Time Product List
                        $OTProduct_result = mysqli_query($con, $product_list);
                        if (mysqli_num_rows($OTProduct_result) > 0) {
                            // output data of each row
                            while ($row = mysqli_fetch_assoc($OTProduct_result)) {
                                $qty = $row['qty'];
                                echo "<input id='qty_$no' type='number' class='$no' oninput=\"change('qty', className, id)\" name='qty_$no' value='$qty'>";
                                $no++;
                            }
                        }
                        ?>
                    </div>

                    <div class="Rate_list tabel" id="rate">
                        <?php
                        $no = 0;
                        $sales_result = mysqli_query($con, $sale_sql);
                        if (mysqli_num_rows($sales_result) > 0) {
                            // output data of each row
                            while ($row = mysqli_fetch_assoc($sales_result)) {
                                $rate = $row['rate'];
                                echo "<input id='rate_$no' class='$no' oninput=\"change('rate', className, id)\" name='rate_$no' value='$rate'>";
                                $no++;
                            }
                        }
                        // One Time Product List
                        $OTProduct_result = mysqli_query($con, $product_list);
                        if (mysqli_num_rows($OTProduct_result) > 0) {
                            // output data of each row
                            while ($row = mysqli_fetch_assoc($OTProduct_result)) {
                                $rate = $row['rate'];
                                echo "<input id='rate_$no' class='$no' oninput=\"change('rate', className, id)\" name='rate_$no' value='$rate'>";
                                $no++;
                            }
                        }
                        ?>
                    </div>

                    <div class="amount_list tabel" id="amount">
                        <div id="amount_list">
                            <?php
                            $no = 0;
                            $sales_result = mysqli_query($con, $sale_sql);
                            if (mysqli_num_rows($sales_result) > 0) {
                                // output data of each row
                                while ($row = mysqli_fetch_assoc($sales_result)) {
                                    $amount = $row['amount'];
                                    echo "<input id='amount_$no' class='$no' onchange=\"change('amount', className, id)\" name='amount_$no' value='$amount'>";
                                    $no++;
                                }
                            }
                            // One Time Product List
                            $OTProduct_result = mysqli_query($con, $product_list);
                            if (mysqli_num_rows($OTProduct_result) > 0) {
                                // output data of each row
                                while ($row = mysqli_fetch_assoc($OTProduct_result)) {
                                    $amount = $row['amount'];
                                    echo "<input id='amount_$no' class='$no' onchange=\"change('amount', className, id)\" name='amount_$no' value='$amount'>";
                                    $no++;
                                }
                            }
                            ?>
                        </div>

                        <div id="remove_button_list">
                            <?php
                            $no = 0;
                            $sales_result = mysqli_query($con, $sale_sql);
                            if (mysqli_num_rows($sales_result) > 0) {
                                // output data of each row
                                while ($row = mysqli_fetch_assoc($sales_result)) {
                                    echo "<button id='_$no' type='button' class='x' onclick='remove_row(id, className);'>[x]</button>";
                                    $no++;
                                }
                            }
                            // One Time Product List
                            $OTProduct_result = mysqli_query($con, $product_list);
                            if (mysqli_num_rows($OTProduct_result) > 0) {
                                // output data of each row
                                while ($row = mysqli_fetch_assoc($OTProduct_result)) {
                                    echo "<button id='_$no' type='button' class='x' onclick='remove_row(id, className);'>[x]</button>";
                                    $no++;
                                }
                            }
                            ?></div>

                    </div>
                    <div class="Total tabel"><input id="total" name="total"></div>
                    <div class="Discount tabel"> <input id="discount" oninput="change_endline()" name="discount" value="<?php echo $discount ?>">
                    </div>

                    <div class="Advance tabel"><input id="advance" oninput="change_endline()" name="advance" value="<?php echo $advance ?>"></div>
                    <div class="Balance tabel"><input id="balance" oninput="change_endline()" name="balance"></div>
                    <div class="Total_tag bill-tag">Total</div>
                    <div class="Discount_tag bill-tag">Discount</div>

                    <div class="Advance_tag bill-tag">Advance </div>
                    <div class="Balance_tag bill-tag">Balance</div>

                    <div class="employ_details bill-tag">
                        <label for="biller">
                            Biller Name:</label>
                        <input list="emoloyee_list" type="text" name="biller" id="biller" required value="<?php echo $_SESSION['employee_name']; ?>">
                        <br>
                        <label for="worker">
                            Employee Name:</label>
                        <input list="emoloyee_list" type="text" name="default_worker" id="default_worker" value="<?php echo $_SESSION['employee_name']; ?>">
                        <script>
                            function fill_employees() {
                                var all_workers = document.getElementById("worker").childNodes;
                                for (i = 0; i < all_workers.length; i++) {
                                    var selected_row = all_workers[i];
                                    if (selected_row.value == "") {
                                        selected_row.value = document.getElementById("default_worker").value;
                                    }
                                }
                            }
                        </script>
                        <br>
                        <label for="PaymentMethod">Payment Method:</label>
                        <select name="PaymentMethod" id="PaymentMethod">
                            <option value="Cash" selected>Cash</option>
                            <option value="BankTransfer">Bank Transfer (BOC)</option>
                            <option value="CardPayment">Card Payment (DFCC)</option>
                            <option value="Cheque">Cheque (BOC)</option>
                            <option value="QRPayment">QR Payment (BOC)</option>
                        </select>


                    </div>
                        <input-field id="changeInvoiceOptions-stock">
                            <input type="checkbox" name="ChangeStock" id="ChangeStock" value="1" style="width: 50px; height:30px;" checked>
                            <label for="ChangeStock">Change Stock </label>
                        </input-field>
                        <br>
                        <input-field id="changeInvoiceOptions-account">
                            <input type="checkbox" name="ChangeAccount" id="ChangeAccount" value="1" style="width: 50px; height:30px;">
                            <label for="ChangeAccount">Change Account Balance</label>
                        </input-field>
                    <div id="AddTodoFieldSet">
                        <input type="checkbox" name="add_to_todo" id="add_to_todo" value="1" style="width: 50px; height:30px;">
                        <label for="add_to_todo"> Edit this Todo Task </label><br>
                        <label for="todoName"> TODO Name : </label> <input type="text" name="todoName" value="<?php echo $todoName ?>" id="todoName" disabled required style="border: 1px solid black; padding:5px; margin: 2px;"><br>
                        <label for="todoTime"> Submission Date & Time : </label> <input type="datetime-local" name="todoTime" value="<?php echo $todoTime ?>" disabled required id="todoTime" style="border: 1px solid black; padding:5px;  margin: 2px;">
                    </div>
                </div>

            </div>

            <input type="text" name="no" id="no" value="<?php echo $total_row_count; ?>">
            <div class="button">
                <button id="submit" type="submit" name="submit">Save</button>
            </div>
        </form>

    </div>
    <div class="todo">
        <button class="add_todo" onclick="add_todo()"> Add Todo Work </button>
        <div class="todoList">
            <?php
            // Get all todo list from database
            require_once '../inc/refresh_todo_section.php';
            ?>
        </div>
    </div>
    </div>

    <script>
        var no = <?php echo $total_row_count; ?>;
    </script>
    <script src="/inc/add_petty_cash_modal.js"></script>
    <script src="/inc/add_todo_functions_modals.js"></script>
    <script src="/inc/invoice_main_functions.jsx"></script>




</body>
<!-- == emoloyee name list - Data List get from Database == -->
<datalist id="emoloyee_list">
    <!-- == Employee == -->
    <?php $emoloyees_list = "SELECT emp_name FROM employees WHERE `status` = '1'";
    $result = mysqli_query($con, $emoloyees_list);
    if ($result) {
        echo "<ol>";
        while ($recoard = mysqli_fetch_assoc($result)) {
            $emoloyee = $recoard['emp_name'];
            echo "<option value='{$emoloyee}'>";
        }
        echo "</ol>";
    } else {
        echo "<option value='Result 404'>";
    }
    ?>
</datalist>


<!-- == Product List - Data List get from Database == -->
<datalist id="products">
    <?php $product_list = "SELECT product_name FROM products";
    $result = mysqli_query($con, $product_list);
    if ($result) {
        echo "<ol>";
        while ($recoard = mysqli_fetch_assoc($result)) {
            $product = $recoard['product_name'];
            echo "<option value='{$product}'>";
        }
        echo "</ol>";
    } else {
        echo "<option value='Result 404'>";
    }
    ?>
</datalist>

</html>


<?php end_db_con() ?>