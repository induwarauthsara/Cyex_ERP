<?php
include '../inc/DataTable_cdn.php';
if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table id='DataTable'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>Invoice Number</th>";
        echo "<th>Customer</th>";
        echo "<th>Tele</th>";
        echo "<th>Date</th>";
        echo "<th>Time</th>";
        echo "<th>Total</th>";
        echo "<th>Disco.</th>";
        echo "<th>Advance</th>";
        echo "<th>Balance</th>";
        echo "<th>Full Paid</th>";
        echo "<th>Add <br> Fund</th>";
        echo "<th>Print</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['invoice_number'] . "</td>";
            echo "<td>" . $row['customer_name'] . "</td>";
            echo "<td>" . $row['customer_mobile'] . "</td>";
            echo "<td>" . $row['invoice_date'] . "</td>";
            echo "<td>" . $row['time'] . "</td>";
            echo "<td> " . $row['total'] . "</td>";
            echo "<td> " . $row['discount'] . "</td>";
            echo "<td> " . $row['advance'] . "</td>";
            echo "<td> " . $row['balance'] . "</td>";
            echo "<td>";
            $paided = $row['full_paid'];
            if ($paided == 0) {
                echo 'No';
            } elseif ($paided == 1) {
                echo 'Yes';
            }
            echo "</td>";
            echo "<td> <a href='add-fund.php?id={$row['invoice_number']}'>Add</a> </td>";
            echo "<td> <a href='print.php?id={$row['invoice_number']}' target='_blanck'>Print</a> </td>";
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
