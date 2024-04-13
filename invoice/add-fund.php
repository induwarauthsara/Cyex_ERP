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
        <form action="add-fund-submit.php" method="POST">
            <!--  === Invoice Details === -->
            <div class="details">
                <div class="customer-details">
                    <div class="customer-name">
                        <label for="name">Customer Name : </label>
                        <input disabled list="customer_list" type="text" name="name" id="name" onchange="customer_add()" required value="<?php echo $customer_name ?>"> <br>

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
                        <input disabled type="text" name="tele" id="tele" value="<?php echo $customer_mobile ?>" required> <br>
                    </div>
                </div>

                <!--  === Invoice Details === -->
                <div class="bill-details">
                    <div class="bill-no">Bill No. <input disabled type="text" value="<?php echo $invoice_number ?>" name="bill-no" required> </div>
                    <div class="date">Date : <input disabled id="date" type="date" value="<?php echo $invoice_date ?>" name="today" required></div>
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
                                    echo "<input disabled id='product_$no' class='$no' onchange=\"change('product', className, id)\" list='products' name='product_$no' value='$product'>";
                                    $no++;
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div onclick="addfund()" class="add">Add Fund</div>

                    <div class="Qty_list tabel" id="qty">
                        <?php
                        $sales_result = mysqli_query($con, $sale_sql);
                        if (mysqli_num_rows($sales_result) > 0) {
                            // output data of each row
                            $no = 0;
                            while ($row = mysqli_fetch_assoc($sales_result)) {
                                $qty = $row['qty'];
                                echo "<input disabled id='qty_$no' type='number' class='$no' onchange=\"change('qty', className, id)\" name='qty_$no' value='$qty'>";
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
                                echo "<input disabled id='rate_$no' class='$no' onchange=\"change('rate', className, id)\" name='rate_$no' value='$rate'>";
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
                                    echo "<input disabled id='amount_$no' class='$no' onchange=\"change('amount', className, id)\" name='amount_$no' value='$amount'>";
                                    $no++;
                                }
                            }
                            ?>
                        </div>

                        <div id="remove_button_list"> </div>

                    </div>
                    <div class="Total tabel"><input disabled id="total" name="total" value="<?php echo $total ?>"></div>
                    <div class="Discount tabel"> <input disabled id="discount" onchange="change_endline()" name="discount" value="<?php echo $discount ?>">
                    </div>

                    <div class="Advance tabel"><input disabled id="advance" onchange="change_endline()" name="advance" value="<?php echo $advance ?>"></div>
                    <div class="Balance tabel"><input disabled id="balance" onchange="change_endline()" name="balance" value="<?php echo $balance ?>"></div>
                    <div class="Total_tag bill-tag">Total</div>
                    <div class="Discount_tag bill-tag">Discount</div>

                    <div class="Advance_tag bill-tag">Advance </div>
                    <div class="Balance_tag bill-tag">Balance</div>
                </div>


            </div>

            <div class="button">
                <button id="submit" onclick="addfund()" class="add">Add Fund</button>
            </div>
        </form>

    </div>

    <script>
        function addfund() {
            alert(<?php echo "'There is a Rs. " .  $balance . " Balance in this bill'"; ?>);

            var fund_amount = Number(prompt(" Add Fund for this bill: Rs. "));
            if (fund_amount !== "" && !isNaN(fund_amount)) {
                // Send Fund Data to Database
                $.ajax({
                    url: "add-fund-submit.php",
                    method: "GET",
                    data: {
                        amount: fund_amount,
                        bill: "<?php echo $invoice_number ?>"
                    },
                    datatype: "text",
                    cache: false,
                    success: function(html) {
                        alert("succesfully added Rs. " + fund_amount + " For  <?php echo $customer_name  ?>' s bill");
                        // alert(html);
                    },
                });
            }

            // Redirect Page
            setTimeout(location.href = 'index.php', 100);

        }

        window.onload = function() {
            setInterval(addfund(), 3000);
        }
    </script>





</html>


<?php end_db_con() ?>