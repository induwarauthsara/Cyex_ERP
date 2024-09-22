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
    .bill {
        width: 70mm;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 10pt;
        margin: 0 auto;
        height: auto;
    }

    .header,
    .details,
    .content {
        text-align: center;
        margin-bottom: 10px;
    }

    .logo-img {
        margin-bottom: 10px;
    }

    .topic h1 {
        margin: 0;
    }

    .topic h2 {
        margin: 5px 0;
        font-size: 12pt;
    }

    hr {
        border: 1px dashed black;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border-bottom: 1px dashed black;
        padding: 5px;
        text-align: left;
    }

    th {
        font-weight: bold;
        text-align: left;
    }

    .price,
    .bill_sum {
        text-align: right;
    }

    .total-summary {
        margin-top: 10px;
    }

    .total-summary td {
        padding: 5px 0;
    }

    .bill_sum {
        font-weight: bold;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
        font-size: 10pt;
    }

    @media print {
        .bill {
            width: 70mm;
            height: auto;
            page-break-after: avoid;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: auto;
        }

        /* Prevent page breaks inside the table */
        table {
            page-break-inside: avoid;
        }
    }
</style>

<div class="bill">
    <div class="header">
        <div class="logo-img"> <a href="../index.php"> <img src="../logo.png" alt="LOGO"> </a></div>
        <div class="topic">
            <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
            <h2><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h2>
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
        echo "<h1 style='color:red'> Don't Print !!!. Failed to Load Invoice Data</h1><!--";
    }
    ?>
    <div class="details">
        <div class="customer-details">
            <div class="customer-name">
                <label for="name">Customer: </label>
                <?php echo $customer; ?>
            </div>
            <div class="customer-tele">
                <label for="tele">Phone: </label>
                <?php echo $tele; ?>
            </div>
        </div>
        <div class="bill-details">
            <div class="bill-no">Invoice No: <?php echo $bill_no; ?></div>
            <div class="date">Date: <?php echo $date; ?></div>
        </div>
    </div>
    <br>
    <table>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Amount</th>
        </tr>
        <?php
        $sql = "SELECT product, qty, rate, amount FROM sales WHERE invoice_number = $id
                UNION ALL
                SELECT product, qty, rate, amount FROM oneTimeProducts_sales WHERE invoice_number = $id;";
        $result = mysqli_query($con, $sql);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($sales = mysqli_fetch_array($result)) {
                    echo '<tr>
                            <td>' . $sales["product"] . '</td>
                            <td>' . $sales["qty"] . '</td>
                            <td class="price">' . $sales["rate"] . '</td>
                            <td class="price">' . $sales["amount"] . '</td>
                        </tr>';
                }
            }
        }
        ?>
        <tr>
            <td colspan="3" class="bill_sum">Total</td>
            <td class="price"><?php echo $total; ?></td>
        </tr>
        <tr>
            <td colspan="3" class="bill_sum">Discount</td>
            <td class="price"><?php echo $discount; ?></td>
        </tr>
        <tr>
            <td colspan="3" class="bill_sum">Advance</td>
            <td class="price"><?php echo $advance; ?></td>
        </tr>
        <tr>
            <td colspan="3" class="bill_sum">Balance</td>
            <td class="price"><?php echo $balance; ?></td>
        </tr>
    </table>

    <div class="footer">
        Powered by <b>CyexTech Solutions</b> <br> <b> CyexTech.com</b>
    </div>
</div>

<script>
    window.print();
</script>