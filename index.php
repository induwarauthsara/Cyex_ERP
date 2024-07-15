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
                    <br>071 4730996
                </h2>
            </div>
            <button class="add_pettycash" onclick="add_pettycash()"> Add Pettycash </button>

        </div>
        <hr>

        <br>
        <form action="submit.php" method="POST">
            <!--  === Invoice Details === -->
            <div class="details">
                <div class="customer-details">
                    <div class="customer-name">
                        <label for="name">Customer Name : </label>
                        <input list="customer_list" type="text" name="name" id="name" onchange="customer_add()" required autofocus> <br>

                        <!-- == Set Customer Phone Number == -->
                        <script>
                            function customer_add() {
                                // check name available in dB
                                var customer_name = document.getElementById("name").value;
                                var customer_mobile = document.getElementById("tele").value;
                                //alert(customer_name);
                                $.ajax({
                                    url: "inc/get_customer_mobile.php",
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
                <div class="container">
                    <!--Tabel Head Start-->
                    <div class="Product tabel-head">Description</div>
                    <!--<div class="Disc tabel-head">Description</div>-->
                    <!--<div class="worker tabel-head">Employee</div>-->
                    <div class="QTY tabel-head">Qty</div>
                    <div class="Rate tabel-head">Rate</div>
                    <div class="Amount tabel-head">Amount</div>
                    <!--Tabel Head End-->
                    <div class="product_list tabel" id="Product">
                        <!--    Pollymer Seal
                        <hr> Photocopy
                        <hr> Printout
                        <hr> Pollyer Seal
                        <hr>
                        -->
                        <div id="list"></div>
                        <hr>
                        <input list="products" type="text" id="addproduct">
                        <div onclick="addproduct()" class="add">Add Product</div>
                    </div>

                    <!--<div class="Disc_list tabel" id="Disc"></div>-->

                    <!--<div class="worker_list tabel" id="worker"> </div>-->

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
                    </div>
                    <div>
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

    <!-- Add Petty Cash -->
    <script>
        document.querySelector('.add_pettycash').addEventListener('click', function() {
            Swal.fire({
                title: 'Add Petty Cash',
                html: '<label for="petty_cash_for" class="swal2-label">Petty Cash For:</label>' +
                    '<input id="petty_cash_for" class="swal2-input" placeholder="Enter purpose">' +
                    '<label for="petty_cash_amount" class="swal2-label">Amount (Rs.):</label>' +
                    '<input id="petty_cash_amount" class="swal2-input" placeholder="Enter amount">',
                focusConfirm: false,
                preConfirm: () => {
                    const petty_cash_for = Swal.getPopup().querySelector('#petty_cash_for').value;
                    const petty_cash_amount = Swal.getPopup().querySelector('#petty_cash_amount').value;
                    if (petty_cash_for && petty_cash_amount && !isNaN(petty_cash_amount)) {
                        return fetch("inc/add_petty_cash.php?for=" + encodeURIComponent(petty_cash_for) + "&amount=" + petty_cash_amount, {
                                method: 'GET',
                            })
                            .then(response => response.text())
                            .then(html => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: "Successfully added Rs. " + petty_cash_amount + " for " + petty_cash_for,
                                    showConfirmButton: false,
                                    timer: 2000 // Close alert after 2 seconds
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                });
                            });
                    } else {
                        Swal.showValidationMessage(`Please enter both petty cash for and amount (numeric value).`);
                    }
                }
            });
        });

        // Add Todo
        // Active TODO Input Fields accourding to Checkbox
        // Selecting checkbox and input fields
        const checkbox = document.getElementById('add_to_todo');
        const todoNameInput = document.getElementById('todoName');
        const todoTimeInput = document.getElementById('todoTime');

        // Adding event listener to checkbox
        checkbox.addEventListener('change', function() {
            // If checkbox is checked, enable input fields; otherwise, disable them
            if (this.checked) {
                todoNameInput.disabled = false;
                todoTimeInput.disabled = false;
            } else {
                todoNameInput.disabled = true;
                todoTimeInput.disabled = true;
            }
        });

        // Add Todo POP UP BOX
        document.querySelector('.add_todo').addEventListener('click', function() {
            Swal.fire({
                title: 'Add TODO Work',
                html: '<label for="todoName" class="swal2-label">Work Name:</label>' +
                    '<input id="todoName" class="swal2-input" placeholder="Enter Work Name">' +
                    '<label for="todoTime" class="swal2-label">Submission Date & Time</label>' +
                    '<input id="todoTime" class="swal2-input" type="datetime-local" placeholder="Enter amount">',
                focusConfirm: false,
                preConfirm: () => {
                    const todoName = Swal.getPopup().querySelector('#todoName').value;
                    const todoTime = Swal.getPopup().querySelector('#todoTime').value;
                    if (todoName && todoTime) {
                        return fetch("inc/add_todo_item.php?todoName=" + encodeURIComponent(todoName) + "&todoTime=" + todoTime, {
                                method: 'GET',
                            })
                            .then(response => response.text())
                            .then(html => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: "Successfully added TODO : " + todoName + ". It must done at " + todoTime,
                                    showConfirmButton: false,
                                    timer: 2000 // Close alert after 2 seconds
                                });
                                // Refresh todo section after adding new todo item
                                refreshTodoSection();
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Something went wrong!',
                                });
                            });
                    } else {
                        Swal.showValidationMessage(`Please enter both work name and submission time.`);
                    }
                }
            });
        });

        // Function to Submit TODO as Completed
        function complete_todo(todoID) {
            // const todoID = this.getAttribute('data-todo-id');
            fetch("inc/update_todo_status.php?todoId=" + todoID) // Replace with your server-side script to update todo status
                .then(response => response.text())
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data,
                        showConfirmButton: false,
                        timer: 2000 // Close alert after 2 seconds
                    });
                    // Refresh todo section after completing todo item
                    refreshTodoSection();
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                });
        }

        // Function to refresh todo section
        function refreshTodoSection() {
            fetch("inc/refresh_todo_section.php") // Replace with your server-side script to fetch updated todo list
                .then(response => response.text())
                .then(data => {
                    document.querySelector('.todoList').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Handle error if necessary
                });
        }
    </script>

    <script>
        var no = 0;


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
                qty.setAttribute("step", "any")
                qty.setAttribute("name", qty.id);

                // Create a Rate in row
                let rate = document.createElement("input");
                rate.id = "rate_" + no;
                // Set Rate Value
                $.ajax({
                    url: "inc/get_product_rate.php",
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


<?php end_db_con(); ?>