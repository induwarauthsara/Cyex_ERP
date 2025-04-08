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
        width: 80mm;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        margin-bottom: 5px;
        text-align: center;
    }

    .logo-img img {
        height: 120px;
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

    td {
        border-bottom: 1px dashed #eee;
    }

    .price,
    .bill_sum {
        text-align: right;
    }

    .promotion {
        text-decoration: line-through;
        font-size: 0.9em;
    }

    .discount-price {
        font-weight: bold;
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

    .savings {
        font-weight: bold;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
        font-size: 10pt;
    }

    .Innerdetails {
        display: flex;
        justify-content: space-between;
        margin: 5px 0;
        font-size: 9pt;
    }

    .bill-no {
        font-size: 11pt;
        margin-bottom: 5px;
    }

    .thank-you {
        margin-top: 10px;
        font-weight: bold;
        font-size: 10pt;
    }

    .payment-method {
        margin-top: 5px;
        font-size: 9pt;
        font-style: italic;
    }

    @media print {
        .bill {
            width: 80mm;
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
        <div class="logo-img"> <a href="../index.php"> <img src="../logo.jpg" alt="LOGO"> </a></div>
        <div class="topic">
            <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
            <h2><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h2>
        </div>
    </div>
    <hr />
    <?php
    $invoice_sql = "SELECT i.*, pd.payment_method FROM invoice i 
                    LEFT JOIN payment_details pd ON i.invoice_number = pd.invoice_id 
                    WHERE i.invoice_number = '$id' LIMIT 1;";
    if ($result = mysqli_query($con, $invoice_sql)) {
        if (mysqli_num_rows($result) == 1) {
            $invoice = mysqli_fetch_array($result);
            $customer = $invoice['customer_name'];
            $tele = $invoice['customer_mobile'];
            $bill_no = $invoice['invoice_number'];
            $date = $invoice['invoice_date'];
            $biller = $invoice['biller'];
            $total = $invoice['total'];
            $discount = $invoice['discount'];
            $advance = $invoice['advance'];
            $balance = $invoice['balance'];
            $full_paid = $invoice['full_paid'];
            $payment_method = $invoice['payment_method'] ?? 'Cash';
            
            // Get individual_discount_mode for this invoice from the sales table
            $mode_query = "SELECT individual_discount_mode FROM sales WHERE invoice_number = '$id' LIMIT 1";
            $mode_result = mysqli_query($con, $mode_query);
            $individual_discount_mode = 0; // Default to 0 (not active)
            if ($mode_result && mysqli_num_rows($mode_result) > 0) {
                $mode_row = mysqli_fetch_assoc($mode_result);
                $individual_discount_mode = (int)$mode_row['individual_discount_mode'];
            }
        } else {
            echo "<h1> Don't Print !!!. Invalid Invoice Number</h1><!--";
        }
    } else {
        echo "<h1> Don't Print !!!. Failed to Load Invoice Data</h1><!--";
    }
    ?>
    <div class="details">
        <div class="bill-no">Invoice No: <b><?php echo $bill_no; ?></b></div>
        <div class="bill-details Innerdetails">
            <div class="date">Date: <b><?php echo $date; ?></b></div>
            <div class="biller">Cashier: <b><?php echo $biller; ?></b></div>
        </div>
        <div class="customer-details Innerdetails">
            <div class="customer-name">
                <label for="name">Customer: <b></label> <?php echo $customer; ?> </b>
            </div>
            <div class="customer-tele">
                <label for="tele">Phone: <b></label> <?php echo $tele; ?> </b>
            </div>
        </div>
    </div>
    <hr>
    <table>
        <tr>
            <th style="width: 40%">Product</th>
            <th style="width: 15%">Qty</th>
            <th style="width: 20%">Price</th>
            <th style="width: 25%">Amount</th>
        </tr>
        <?php
        $total_savings = 0;
        $sql = "SELECT product, qty, rate, discount_price, amount FROM sales WHERE invoice_number = $id";
        $result = mysqli_query($con, $sql);
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                while ($sales = mysqli_fetch_array($result)) {
                    $regular_price = $sales["rate"];
                    $discount_price = $sales["discount_price"];
                    $qty = $sales["qty"];
                    
                    // Only show discount price if individual discount mode was active
                    // or if discount price is less than regular price (for cases like promotional items)
                    $is_promotional = $individual_discount_mode && ($discount_price < $regular_price && $discount_price > 0);

                    // Calculate savings for this item, but only if individual discount mode was active
                    if ($is_promotional) {
                        $item_savings = ($regular_price - $discount_price) * $qty;
                        $total_savings += $item_savings;
                    }

                    echo '<tr>
                            <td>' . $sales["product"] . '</td>
                            <td>' . $sales["qty"] . '</td>
                            <td class="price">';

                    if ($is_promotional) {
                        echo '<div class="promotion">' . number_format($regular_price, 2) . '</div>' .
                             '<span class="discount-price">' . number_format($discount_price, 2) . '</span>';
                    } else {
                        echo number_format($regular_price, 2);
                    }

                    echo '</td>
                            <td class="price">' . number_format($sales["amount"], 2) . '</td>
                        </tr>';
                }
            }
        }

        // Add promotional savings to the discount value for total savings calculation
        $total_savings += $discount;
        ?>
    </table>

    <table class="total-summary">
        <tr>
            <td colspan="3" class="bill_sum">Subtotal</td>
            <td class="price"><?php echo number_format($total, 2); ?></td>
        </tr>
        <?php if ($total_savings > 0) { ?>
            <tr>
                <td colspan="3" class="bill_sum savings">Total Savings</td>
                <td class="price savings"><?php echo number_format($total_savings, 2); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td colspan="3" class="bill_sum">Discount</td>
            <td class="price"><?php echo number_format($discount, 2); ?></td>
        </tr>
        <?php if ($advance > 0) { ?>
            <tr>
                <td colspan="3" class="bill_sum">Paid Amount</td>
                <td class="price"><?php echo number_format($advance, 2); ?></td>
            </tr>
        <?php } ?>
        <?php if ($balance > 0) { ?>
            <tr>
                <td colspan="3" class="bill_sum">Balance Due</td>
                <td class="price"><?php echo number_format($balance, 2); ?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td colspan="3" class="bill_sum">TOTAL</td>
                <td class="price" style="font-size: 12pt;"><?php echo number_format($total - $discount, 2); ?></td>
            </tr>
        <?php } ?>
    </table>

    <div class="payment-method">
        Payment Method: <?php echo $payment_method; ?>
    </div>

    <div class="thank-you">
        Thank you for your business!
    </div>

    <div class="footer">
        Powered by <b>CyexTech Solutions</b> <br> <b>CyexTech.com</b>
    </div>
</div>

<script>
    window.print();
    // after print, close the page 
    window.onafterprint = function() {
        window.close();
    };
    
    // Fallback for browsers that don't support onafterprint
    setTimeout(function() {
        window.close();
    }, 5000);
</script>