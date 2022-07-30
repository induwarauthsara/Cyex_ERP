<?php
require_once '../inc/config.php';
$id = $_GET['id'];
if (isset($_GET['invoice'])) {
    $invoice = $_GET['invoice'];
    if (!isset($id)) {
        $id = $invoice;
    }
}

?>
<style>
    .details,
    .content {
        font-family: Helvetica, Arial, Sans-Serif;
    }
</style>
<link rel="stylesheet" href="../style.css">
<div class="bill">
    <div class="header">
        <div class="logo-img"> <img src="../logo.JPG" alt="LOGO">
        </div>
        <div class="topic">
            <h1>Srijaya Print House</h1>
            <h2>FF26, Megacity, Athurugiriya.
                <br>071 4730996
            </h2>
        </div>
    </div>
    <hr />
    <?php
    $invoice_sql = "SELECT * FROM invoice WHERE invoice_number = '$id' LIMIT 1;";
    if ($result = mysqli_query($con, $invoice_sql)) {
        if (mysqli_num_rows($result) == 1) {
            $invoice = mysqli_fetch_array($result);
            $customer = $invoice['customer_name'];
            $tele = $invoice['customer_mobile'];
            $bill_no = $invoice['invoice_number'];
            $date = $invoice['invoice_date'];
            $biller = $invoice['biller'];
            $primary_worker = $invoice['primary_worker'];
            $total = $invoice['total'];
            $discount = $invoice['discount'];
            $advance = $invoice['advance'];
            $balance = $invoice['balance'];
            $full_paid = $invoice['full_paid'];
        } else {
            echo "<h1 style='color:red'> Don't Print !!!. Invalid Invoice Number</h1><!--";
        }
    } else {
        echo "<h1 style='color:red'> Don't Print !!!. Faild to Load Invoice Data</h1><!--";
    }
    ?>
    <div class="details">
        <div class="customer-details">
            <div class="customer-name">
                <label for="name">Customer Name : </label>
                <?php echo $customer; ?>
            </div>
            <div class="customer-tele">
                <label for="tele">Customer Telephone : </label>
                <?php echo $tele; ?>
            </div>
        </div>

        <div class="bill-details">
            <div class="bill-no">Bill No. <?php echo $bill_no; ?> </div>
            <div class="date">Date : <?php echo $date; ?></div>
        </div>
    </div>

    <?php
    // Sales Arrays
    $product_array = array();
    $description_array = array();
    $worker_array = array();
    $qty_array = array();
    $rate_array = array();
    $amount_array = array();

    $sql = "SELECT * FROM sales WHERE invoice_number = $id";
    $result = mysqli_query($con, $sql);
    if ($result) {
        // qury success
        if (mysqli_num_rows($result) > 0) {
            while ($sales = mysqli_fetch_array($result)) {
                $sale_product = $sales['product'];
                $sales_description = $sales['description'];
                $sales_worker = $sales['worker'];
                $sales_qty = $sales['qty'];
                $sales_rate = $sales['rate'];
                $sales_amount = $sales['amount'];
                //Push Data
                array_push($product_array, $sale_product);
                array_push($description_array, $sales_description);
                array_push($worker_array, $sales_worker);
                array_push($qty_array, $sales_qty);
                array_push($rate_array, $sales_rate);
                array_push($amount_array, $sales_amount);
            }
        } else {
            echo "<h1 style='color:red'> Don't Print !!!. No added any Item in this Bill</h1><!-- ";
        }
    } else {
        echo "<h1 style='color:red'> Don't Print !!!. Database Query Failed</h1><!--";
    }

    function data_list($row)
    {
        for ($i = 0; count($row) > $i; $i++) {
            $data =  $row[$i];
            if ($data == "") {
                $data = "-";
            }
            echo $data;
            echo "<hr>";
        }
    }
    ?>


    <!-- Sales -->
    <div class="content">
        <!--tabel-->
        <div class="container">
            <!--Tabel Head Start-->
            <div class="Product tabel-head">Product</div>
            <div class="Disc tabel-head">Description</div>
            <div class="worker tabel-head">Worker</div>
            <div class="QTY tabel-head">Qty</div>
            <div class="Rate tabel-head">Rate</div>
            <div class="Amount tabel-head">Amount</div>
            <!--Tabel Head End-->
            <div class="product_list tabel" id="Product"> <?php data_list($product_array); ?> </div>

            <div class="Disc_list tabel" id="Disc"> <?php data_list($description_array); ?> </div>

            <div class="worker_list tabel" id="worker"> <?php data_list($worker_array); ?> </div>

            <div class="Qty_list tabel" id="qty"> <?php data_list($qty_array); ?> </div>

            <div class="Rate_list tabel" id="rate"> <?php data_list($rate_array); ?> </div>

            <div class="amount_list tabel" style="flex-direction: column;"> <?php data_list($amount_array); ?> </div>

            <div class="Total tabel"> <?php echo $total; ?> </div>
            <div class="Discount tabel"> <?php echo $discount; ?> </div>

            <div class="Advance tabel"><?php echo $advance; ?></div>
            <div class="Balance tabel"><?php echo $balance; ?></div>
            <div class="Total_tag bill-tag">Total</div>
            <div class="Discount_tag bill-tag">Discount</div>
            <div class="Advance_tag bill-tag">Advance </div>
            <div class="Balance_tag bill-tag">Balance</div>
            <div class="employ_details bill-tag">
                <label for="biller"> Biller :</label> <?php echo $biller; ?>
                <br>
                <label for="worker"> Employee :</label> <?php echo $primary_worker; ?>
            </div>
        </div>

        <table class="table">
            <tr>
                <th>Product</th>
                <th>Description</th>
                <th>Worker</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </table>


    </div>

    <input type="text" name="no" id="no" value="">
</div>

<script>
    window.print();
</script>