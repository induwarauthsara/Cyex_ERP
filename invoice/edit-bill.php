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
</head>

<?php
$invoice_number = $_GET['id'];
$sql = "select * from invoice where invoice_number = $invoice_number;";
$result = mysqli_query($con, $sql);
$invoice_details = mysqli_fetch_assoc($result);

// Invoice details
$invoice_number = $invoice_details['invoice_number'];
$customer_name = $invoice_details['customer_name'];
$customer_mobile = $invoice_details['customer_mobile'];
$invoice_date = $invoice_details['invoice_date'];
$total =  $invoice_details['total'];
$discount =  $invoice_details['discount'];
$advance =  $invoice_details['advance'];
$balance =  $invoice_details['balance'];

// Items of Invoices
$sale_sql = "SELECT * FROM `sales` WHERE invoice_number = $invoice_number";
$sales_result = mysqli_query($con, $sale_sql);
// $saless = mysqli_fetch_array($sales_result);
// echo "<pre>";
// print_r($saless);
// echo 'sad';
// echo "</pre>";
?>

<body><br>
    <div class="main">
        <div class="bill">
            <div class="header">
                <div class="logo-img"> <img src="../logo.png" alt="LOGO">
                </div>
                <div class="topic">
                    <h1>Srijaya Print House</h1>
                    <h2>FF26, Megacity, Athurugiriya.
                        <br>071 4730996
                    </h2>
                </div>
            </div>
            <hr>

            <br>
            <form action="edit-submit.php" method="POST">
                <!--  === Invoice Details === -->
                <div class="details">
                    <div class="customer-details">
                        <div class="customer-name">
                            <label for="name">Customer Name : </label>
                            <input list="customer_list" type="text" name="name" id="name" onchange="customer_add()" required value="<?php echo $customer_name ?>"> <br>

                            <!-- == Set Customer Phone Number == -->
                            <script>
                                function customer_add() {
                                    // check name available in dB
                                    var customer_name = document.getElementById("name").value;
                                    var customer_mobile = document.getElementById("tele").value;
                                    //alert(customer_name);
                                    $.ajax({
                                        url: "/inc/get_customer_mobile.php",
                                        method: "POST",
                                        data: {
                                            cus: customer_name
                                        },
                                        datatype: "text",
                                        cache: false,
                                        success: function(html) {
                                            document.getElementById('tele').value = html;
                                            //alert(html);
                                        },
                                    });
                                }
                            </script>
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
                    <div class="container">
                        <!--Tabel Head Start-->
                        <div class="Product tabel-head">Description</div>
                        <!--<div class="Disc tabel-head">Description</div>-->
                        <!--<div class="worker tabel-head">Employee</div>-->
                        <div class="QTY tabel-head">Qty</div>
                        <div class="Rate tabel-head">Rate</div>
                        <div class="Amount tabel-head">Amount</div>
                        <div class="product_list tabel" id="Product">
                            <div id="list">
                                <?php
                                if (mysqli_num_rows($sales_result) > 0) {
                                    // output data of each row
                                    $no = 0;
                                    while ($row = mysqli_fetch_assoc($sales_result)) {
                                        $product = $row['product'];
                                        echo "<input id='product_$no' class='$no' onchange=\"change('product', className, id)\" list='products' name='product_$no' value='$product'>";
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
                            $sales_result = mysqli_query($con, $sale_sql);
                            if (mysqli_num_rows($sales_result) > 0) {
                                // output data of each row
                                $no = 0;
                                while ($row = mysqli_fetch_assoc($sales_result)) {
                                    $qty = $row['qty'];
                                    echo "<input id='qty_$no' type='number' class='$no' onchange=\"change('qty', className, id)\" name='qty_$no' value='$qty'>";
                                    $no++;
                                }
                            }
                            ?>
                        </div>

                        <div class="Rate_list tabel" id="rate">
                            <?php
                            $sales_result = mysqli_query($con, $sale_sql);
                            if (mysqli_num_rows($sales_result) > 0) {
                                // output data of each row
                                $no = 0;
                                while ($row = mysqli_fetch_assoc($sales_result)) {
                                    $rate = $row['rate'];
                                    echo "<input id='rate_$no' class='$no' onchange=\"change('rate', className, id)\" name='rate_$no' value='$rate'>";
                                    $no++;
                                }
                            }
                            ?>
                        </div>

                        <div class="amount_list tabel" id="amount">
                            <div id="amount_list">
                                <?php
                                $sales_result = mysqli_query($con, $sale_sql);
                                if (mysqli_num_rows($sales_result) > 0) {
                                    // output data of each row
                                    $no = 0;
                                    while ($row = mysqli_fetch_assoc($sales_result)) {
                                        $amount = $row['amount'];
                                        echo "<input id='amount_$no' class='$no' onchange=\"change('amount', className, id)\" name='amount_$no' value='$amount'>";
                                        $no++;
                                    }
                                }
                                ?>
                            </div>

                            <div id="remove_button_list">
                                <?php
                                $sales_result = mysqli_query($con, $sale_sql);
                                if (mysqli_num_rows($sales_result) > 0) {
                                    // output data of each row
                                    $no = 0;
                                    while ($row = mysqli_fetch_assoc($sales_result)) {
                                        echo "<button id='_$no' class='x' onclick='remove_row(id, className);'>[x]</button>";
                                        $no++;
                                    }
                                }
                                ?></div>

                        </div>
                        <div class="Total tabel"><input id="total" name="total"></div>
                        <div class="Discount tabel"> <input id="discount" onchange="change_endline()" name="discount" value="<?php echo $discount ?>">
                        </div>

                        <div class="Advance tabel"><input id="advance" onchange="change_endline()" name="advance" value="<?php echo $advance ?>"></div>
                        <div class="Balance tabel"><input id="balance" onchange="change_endline()" name="balance"></div>
                        <div class="Total_tag bill-tag">Total</div>
                        <div class="Discount_tag bill-tag">Discount</div>

                        <div class="Advance_tag bill-tag">Advance </div>
                        <div class="Balance_tag bill-tag">Balance</div>
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
                require_once '../inc/refresh_todo_section.php';
                ?>
            </div>
        </div>
    </div>



    <script>
        var no = document.getElementById("list").getElementsByTagName('input').length;

        function addproduct() {
            const product_list = document.getElementById('list');
            const disc_list = document.getElementById('Disc');
            const worker_list = document.getElementById('worker');
            const qty_list = document.getElementById('qty');
            const rate_list = document.getElementById('rate');
            const amount_list = document.getElementById('amount_list');
            const remove_button_list = document.getElementById('remove_button_list');


            function add_row(no) {

                //  let product = document.getElementById('addproduct').value;
                var product = document.createElement("input");
                product.value = document.getElementById('addproduct').value;
                product.id = "product_" + no;
                product.className = no, 'ooo';
                product.setAttribute("list", "products");
                product.setAttribute("name", product.id);
                // product.setAttribute("onchange", "change('product')");
                product.setAttribute("onchange", "change('product', className, id)");

                // //  Create Discription in Row
                // let description = document.createElement("input");
                // description.id = "description_" + no;
                // description.className = no;
                // description.setAttribute("name", description.id);

                // // Create a worker in row
                // let worker = document.createElement("input");
                // worker.id = "worker_" + no;
                // worker.className = no;
                // worker.value = document.getElementById("default_worker").value;
                // worker.setAttribute("onchange", "change('worker', className, id)");
                // worker.setAttribute("name", worker.id);
                // worker.setAttribute("list", 'emoloyee_list');

                // Create a Qty in row
                let qty = document.createElement("input");
                qty.id = "qty_" + no;
                qty.className = no;
                qty.type = "number";
                qty.value = 1;
                qty.setAttribute("onchange", "change('qty', className, id)");
                qty.setAttribute("name", qty.id);

                // Create a Rate in row
                let rate = document.createElement("input");
                rate.id = "rate_" + no;
                // Set Rate Value
                $.ajax({
                    url: "/inc/get_product_rate.php",
                    method: "POST",
                    data: {
                        product: product.value
                    },
                    datatype: "text",
                    cache: false,
                    success: function(html) {
                        $rate_db = rate.value = Number(html).toFixed(2);
                        amount.value = Number(qty.value * rate.value).toFixed(2);
                        add_total(no + 1);
                    },
                });
                //
                rate.className = no;
                // rate = rate.value.toFixed(2);
                rate.setAttribute("onchange", "change('rate', className, id)");
                rate.setAttribute("name", rate.id);
                rate.value = Number(rate.value);


                // Create a Amount in row
                let amount = document.createElement("input");
                amount.id = "amount_" + no;
                amount.className = no;
                amount.value = Number(qty.value * rate.value).toFixed(2);
                amount.setAttribute("onchange", "change('amount', className, id)");
                amount.setAttribute("name", amount.id);

                // Create a Close Button in row
                let x = document.createElement("button");
                x.id = "_" + no;
                x.className = "x"
                x.innerText = "[x]"
                x.setAttribute("onclick", 'remove_row(id, className);');


                //  disc_list.innerHTML += product + "<hr>";
                product_list.appendChild(product);
                // disc_list.appendChild(description);
                // worker_list.appendChild(worker);
                //      disc_list.innerHTML += "<hr>";
                qty_list.appendChild(qty);
                rate_list.appendChild(rate);
                amount_list.appendChild(amount);
                remove_button_list.appendChild(x);
            }


            add_row(no);
            document.getElementById('no').value = no;
            no++;
            add_total(no);

            //     return product.value;
            var product_name = document.getElementById('addproduct').value;
            // set_rate(product_name);

            // Clear Product Input field
            document.getElementById('addproduct').value = "";
        }
        /*
                function set_rate(product) {
                    if (product == "a") {
                        //     alert('sdd')
                        let rate = 55;
                        return rate;
                    }
                    // change(, , , rate);
                }
        */

        function change(row, cls, id) {
            // alert(row + " changed.. id: " + id + " Class : " + cls);

            var changed_desc = document.getElementById("product_" + cls).value;
            var changed_qty = document.getElementById("qty_" + cls).value;
            var changed_rate = document.getElementById("rate_" + cls).value;
            var changed_amount = document.getElementById("amount_" + cls).value;

            // Correct Ammount row
            changed_amount = changed_qty * changed_rate;
            document.getElementById("amount_" + cls).value = changed_amount;

            // Correct Total row
            change_endline();

            // correct Total
            // Correct Balance
            // add_total(no);

            //Correct Decimals of Discount & Advance           
            decimal(no); // me line eka oninam ain karanna puluwna
        }

        function change_endline() {
            add_total(no);
            decimal(no);
        }

        /*
            function decimal2(no) {
                var new_no = no;
                new_no--;

                for (; new_no > -1; new_no--) {
                    let decimal_row_amount = "amount_" + new_no;
                    let decimal_row_rate = "rate_" + new_no;

                    //amount
                    document.getElementById(decimal_row_amount).value = Number(document.getElementById(decimal_row_amount).value).toFixed(2);

                    //rate
                    document.getElementById(decimal_row_rate).value = Number(document.getElementById(decimal_row_rate).value).toFixed(2);

                    //Total
                    document.getElementById("total").value = Number(document.getElementById("total").value).toFixed(2);

                    //Discount
                    document.getElementById("discount").value = Number(document.getElementById("discount").value).toFixed(2);

                    // Advance
                    document.getElementById("advance").value = Number(advance.value).toFixed(2);

                    //Balance
                    document.getElementById("balance").value = Number(balance.value).toFixed(2);


                }
                //Advance
                // document.getElementById("advance").value = Number(document.getElementById("advance").value).toFixed(2);

                //Balance
                //document.getElementById("balance").value = Number(document.getElementById("balance").value).toFixed(2);

            }
        */

        function decimal(no) {
            no--;
            for (; no > -1; no--) {
                let decimal_row_amount = "amount_" + no;
                let decimal_row_rate = "rate_" + no;

                //amount
                const all_amount_selector = document.querySelectorAll("#amount_list input");
                for (var i = 0; i < all_amount_selector.length; i++) {
                    all_amount_selector[i].value = Number(all_amount_selector[i].value).toFixed(2);
                }

                //rate
                const all_rate_selector = document.querySelectorAll("#rate input");
                for (var i = 0; i < all_rate_selector.length; i++) {
                    all_rate_selector[i].value = Number(all_rate_selector[i].value).toFixed(2);
                }

                //Total
                document.getElementById("total").value = Number(document.getElementById("total").value).toFixed(2);

                //Discount
                document.getElementById("discount").value = Number(document.getElementById("discount").value).toFixed(2);

                // Advance
                document.getElementById("advance").value = Number(advance.value).toFixed(2);

                //Balance
                document.getElementById("balance").value = Number(balance.value).toFixed(2);


            }
            //Advance
            // document.getElementById("advance").value = Number(document.getElementById("advance").value).toFixed(2);

            //Balance
            //document.getElementById("balance").value = Number(document.getElementById("balance").value).toFixed(2);

        }

        /*
                function add_total2(no) {

                    var line = no - 1;
                    var amount_sum = 0;

                    for (; line > -1; line--) {
                        var amount_no = "amount_" + line;
                        var amount_value = Number(document.getElementById(amount_no).value);
                        amount_sum += amount_value;
                        document.getElementById('total').value = amount_sum;

                    }

                    var discount = document.getElementById("discount");

                    var advance = document.getElementById("advance");

                    var balance = document.getElementById("balance");
                           
                   // discount.setAttribute("onchange", "decimal(no)");
                   // advance.setAttribute("onchange", "decimal(no)");
                   // balance.setAttribute("onchange", "decimal(no)"); 
                  

                    // Make Balance
                    balance.value = Number(total.value) - Number(discount.value) - Number(advance.value)

                    decimal(no);
                }
        */
        function add_total(no) {
            const all_amount_selector = document.querySelectorAll("#amount_list input");

            var amount_sum = 0;

            for (var i = 0; i < all_amount_selector.length; i++) {
                amount_sum += Number(all_amount_selector[i].value);
                document.getElementById('total').value = amount_sum;
            }

            var discount = document.getElementById("discount");
            var advance = document.getElementById("advance");
            var balance = document.getElementById("balance");
            /*        
            discount.setAttribute("onchange", "decimal(no)");
            advance.setAttribute("onchange", "decimal(no)");
            balance.setAttribute("onchange", "decimal(no)"); 
            */

            // Make Balance
            balance.value = Number(total.value) - Number(discount.value) - Number(advance.value)

            decimal(no);

        }

        function remove_row(number, className) {
            let product_remove = document.getElementById("product" + number);
            // let description_remove = document.getElementById("description" + number);
            // let worker_remove = document.getElementById("worker" + number);
            let qty_remove = document.getElementById("qty" + number);
            let rate_remove = document.getElementById("rate" + number);
            let amount_remove = document.getElementById("amount" + number);
            let x_remove = document.getElementById(number);

            // Remove Row
            let remove = [product_remove, /*description_remove, worker_remove,*/ qty_remove, rate_remove, amount_remove, x_remove]
            remove.forEach(element => {
                element.remove();
            });

            /*
                // Correct Above row id
                var removed_line_id = number.replace('_', '');
                removed_line_id = Number(removed_line_id);

                for (removed_line_id; removed_line_id < no - 1; removed_line_id++) {
                    var next_row = removed_line_id + 1;

                    let product_next = document.getElementById("product_" + next_row);
                    let worker_next = document.getElementById("worker_" + next_row);
                    let qty_next = document.getElementById("qty_" + next_row);
                    let rate_next = document.getElementById("rate_" + next_row);
                    let amount_next = document.getElementById("amount_" + next_row);
                    let x_next = document.getElementById("_" + next_row);

                    let make_arry = [product_next, worker_next, qty_next, rate_next, amount_next, x_next];
                    make_arry.forEach(element => {
                        element.className = next_row - 1;

                        var ii = 0;
                        for (var ii = 0; ii < 7; ii++) {
                            return ii;
                        }
                        correct_above_id(element, ii);

                    });

                    function correct_above_id(element, ii) {
                        alert('awwa');
                        const pre_id = ["product_", "worker_", "qty_", "rate_", "amount_", "_"];
                        var next_row = removed_line_id + 1;
                        var all_id = pre_id[ii] + (next_row - 1);
                        element.id = all_id;

                        //  alert(element.value);
                        //  alert(pre_id[ii]);
                    }

                    //alert("id: " + number + " and no: " + no);
                }
                // alert(removed_line_id);
                // Correct ID and Row numbers
                no--; // Correct peli gana
            */

            // Correct Amount
            add_total(no);
            change('remove', className, number);
            change_endline();
        }

        add_total(no);
    </script>
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

<!-- == Product List - Data List get from Database == -->
<datalist id="products">
    <!-- == Employee == -->
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