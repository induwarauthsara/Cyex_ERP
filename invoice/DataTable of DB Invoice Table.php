    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <?php
    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='DataTable' style='width: 80%'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>Invoice No</th>";
            echo "<th>Date</th>";
            echo "<th>Customer Name</th>";
            echo "<th>Tele</th>";
            echo "<th>Time</th>";
            echo "<th>Total</th>";
            echo "<th>Disco.</th>";
            echo "<th>Advance</th>";
            echo "<th>Balance</th>";
            echo "<th>Total Cost</th>";
            echo "<th>Total Profit</th>";
            echo "<th>Cashier</th>";
            echo "<th>Full Paid</th>";
            echo "<th>Payment Method</th>";
            echo "<th>Actions</th>";
            echo "<th>Print</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                $invoice_date = $row['invoice_date'];
                $invoice_number = $row['invoice_number'];
                $customer_name = $row['customer_name'];
                $customer_mobile = $row['customer_mobile'];
                $time = $row['time'];
                $total = $row['total'];
                $discount = $row['discount'];
                $advance = $row['advance'];
                $balance = $row['balance'];
                $cost = $row['cost'];
                $profit = $row['profit'];
                $biller = $row['emp_name'];
                $paided = $row['full_paid'];
                $paymentMethod = $row['paymentMethod'];
                echo "<tr>";
                echo "<td>" . $invoice_number . "</td>";
                echo "<td>" . $invoice_date . "</td>";
                echo "<td>" . $customer_name . "</td>";
                echo "<td>" . $customer_mobile . "</td>";
                echo "<td>" . $time . "</td>";
                echo "<td>" . $total . "</td>";
                echo "<td>" . $discount . "</td>";
                echo "<td>" . $advance . "</td>";
                echo "<td>" . $balance . "</td>";
                echo "<td>" . $cost . "</td>";
                echo "<td>" . $profit . "</td>";
                echo "<td>" . $biller . "</td>";
                echo "<td>";
                if ($paided == 0) {
                    echo "No <br> <button onclick=' addFund(`$invoice_number`, $balance, `Add Fund`)'>Add Fund </button> <br>  
                        <button onclick=' addFund(`$invoice_number`, $balance, `Full Payment`)'>Full Payment </button>";
                } elseif ($paided == 1) {
                    echo 'Yes';
                }
                echo "</td>";
                echo "<td>" . $paymentMethod . "</td>";
                echo "<td> 
                        <a href='/invoice/edit-bill.php?id={$row['invoice_number']}'>Edit</a> | 
                        <a href='javascript:void(0)' onclick='confirmDeleteInvoice({$row['invoice_number']})' style='color:red;'>Delete</a>
                      </td>";
                echo "<td> <a href='/invoice/print.php?id={$row['invoice_number']}' target='_blanck'>Print</a> </td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            // Free result set
            mysqli_free_result($result);
        } else {
            echo "No records matching your query were found.";
        }
    } else {
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($con);
    }
    ?>

    <style>
        /* Data Table Style */
        th.sorting:nth-child(2),
        th.sorting:nth-child(7),
        th.sorting:nth-child(11),
        th.sorting:nth-child(12),
        th.sorting:nth-child(10) {
            width: 5px !important;
        }
    </style>

    <!-- link test.js -->
    <!-- link test.js -->
    <script src="/invoice/addFundInvoiceModal.js"></script>
    <script src="/invoice/invoice-actions.js"></script>