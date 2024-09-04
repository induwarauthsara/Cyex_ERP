<?php require_once 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Srijaya Bill</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<style>
    .bill .container {
        grid-template-areas: "Product QTY Rate Amount" "product_list Qty_list Rate_list amount_list" "employ_details AddTodoFieldSet Total_tag Total" "employ_details AddTodoFieldSet Discount_tag Discount" "employ_details AddTodoFieldSet Advance_tag Advance" "employ_details AddTodoFieldSet Balance_tag Balance";
    }
</style>

<body><br>

    <div class="main">
        <div class="bill">
            <div class="header"> <a href="/index.php">
                    <div class="logo-img"> <img src="logo.png" alt="LOGO">
                </a>
            </div>
            <div class="topic">
                <h1>Srijaya Print House</h1>
                <h2>FF26, Megacity, Athurugiriya.
                    <br>071 4730 996
                </h2>
            </div>
            <div class="billHeadButtons">
                <button class="add_pettycash" onclick="add_pettycash()"> Add Pettycash </button>
                <button class="add_pettycash" onclick="add_bankDeposit()"> Bank Deposit </button>
            </div>

        </div>
        <hr>

        <br>
        <form action="submit.php" method="POST">
            <!--  === Invoice Details === -->
            <div class="details">
                <div class="customer-details">
                    <div class="customer-name">
                        <label for="name">Customer Name : </label>
                        <input list="customer_list" type="text" name="name" id="name" onchange="customer_add()" autofocus> <br>
                    </div>

                    <div class="customer-tele">
                        <label for="tele">Customer telephone : </label>
                        <input type="text" name="tele" id="tele" autofocus> <br>
                    </div>
                </div>

                <!--  === Invoice Details === -->
                <div class="bill-details">
                    <div class="date">Date : <input id="date" type="date" value="" name="today"></div>

                    <!-- == Set Today Date == -->
                    <script>
                        var today = new Date();
                        var dd = today.getDate();
                        var mm = today.getMonth() + 1;
                        var yyyy = today.getFullYear();
                        if (mm < 10) {
                            mm = '0' + mm;
                        }
                        if (dd < 10) {
                            dd = '0' + dd;
                        }
                        today = yyyy + '-' + mm + '-' + dd;
                        document.getElementById("date").value = today;
                    </script>
                </div>
            </div>

            <div class="content">
                <!--tabel-->
                <textarea name="InvoiceDescription" id="InvoiceDescription" placeholder="Add Details about this Job / Invoice. Paper Size, Colour, Size, Binding Margin"></textarea>
                <div class="container">
                    <!--Tabel Head Start-->
                    <div class="Product tabel-head">Description</div>
                    <div class="QTY tabel-head">Qty</div>
                    <div class="Rate tabel-head">Rate</div>
                    <div class="Amount tabel-head">Amount</div>
                    <!--Tabel Head End-->
                    <div class="product_list tabel" id="Product">
                        <div id="list"></div>
                        <hr>
                        <input list="products" type="text" id="addproduct">
                        <div onclick="addproduct()" class="add">Add </div>
                    </div>

                    <div class="Qty_list tabel" id="qty"> </div>

                    <div class="Rate_list tabel" id="rate"> </div>

                    <div class="amount_list tabel" id="amount">
                        <div id="amount_list"></div>
                        <div id="remove_button_list"></div>

                    </div>
                    <div class="Total tabel"><input id="total" name="total"></div>
                    <div class="Discount tabel"> <input id="discount" onchange="change_endline()" name="discount">
                    </div>

                    <div class="Advance tabel"><input id="advance" onchange="change_endline()" name="advance"></div>
                    <div class="Balance tabel"><input id="balance" onchange="change_endline()" name="balance"></div>
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
                        <input list="emoloyee_list" type="text" name="default_worker" id="default_worker" onchange="fill_employees()" value="<?php echo $_SESSION['employee_name']; ?>">
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
                        <br><br>
                        <label for="PaymentMethod">Payment Method:</label>
                        <select name="PaymentMethod" id="PaymentMethod">
                            <option value="Cash" selected>Cash</option>
                            <option value="BankTransfer">Bank Transfer (BOC)</option>
                            <option value="CardPayment">Card Payment (DFCC)</option>
                            <option value="Cheque">Cheque (BOC)</option>
                            <option value="QRPayment">QR Payment (BOC)</option>
                        </select>

                    </div>
                    <div id="AddTodoFieldSet">
                        <br>
                        <input type="checkbox" name="add_to_todo" id="add_to_todo" value="1" style="width: 50px; height:30px;">
                        <label for="add_to_todo"> Add to TODO List </label><br>
                        <label for="todoName"> TODO Name : </label> <input type="text" name="todoName" id="todoName" disabled required style="border: 1px solid black; padding:5px; margin: 2px;"><br>
                        <label for="todoTime"> Submission Date & Time : </label> <input type="datetime-local" name="todoTime" disabled required id="todoTime" style="border: 1px solid black; padding:5px;  margin: 2px;">
                    </div>
                </div>

            </div>

            <input type="text" name="no" id="no" value="">
            <div class="button">
                <button id="submit" type="submit" name="submit">Submit</button>
                <button id="submit" type="submit" name="submit_and_print">Submit & Print</button>
                <button id="submit" type="submit" name="submit_and_fullPayment">Submit Full Payment </button>
                <button id="submit" type="submit" name="submit_and_print_fullPayment">Submit Full Payment <br> & Print</button>
            </div>
        </form>

    </div>
    <div class="todo">
        <button class="add_todo" onclick="add_todo()"> Add Todo Work </button>
        <div class="todoList">
            <?php
            // Get all todo list from database
            include 'inc/refresh_todo_section.php';
            ?>
        </div>
    </div>
    </div>

    <script src="/inc/add_petty_cash_modal.js"></script>
    <script src="/inc/add_todo_functions_modals.js"></script>
    <script src="/inc/invoice_main_functions.jsx"></script>
</body>
<!-- == Customer name list get - Datalist from Database == -->
<datalist id="customer_list">
    <?php $customer_list = "SELECT customer_name FROM customers";
    $result = mysqli_query($con, $customer_list);
    if ($result) {
        echo "<ol>";
        while ($recoard = mysqli_fetch_assoc($result)) {
            $customer = $recoard['customer_name'];
            echo "<option value='{$customer}'>";
        }
        echo "</ol>";
    } else {
        echo "<option value='Result 404'>";
    }
    ?>
</datalist>

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

<!-- ======================================== Add Bank Deposit ======================================== -->
<script src="/inc/add_bank_deposit_modal.js"></script>

</html>


<?php end_db_con(); ?>