<?php
include 'nav.php';
require_once(__DIR__ . '../../inc/header.php');
require_once(__DIR__ . '../../inc/config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Purchase Bills</title>
</head>

<body>

    <center>
        <h1>Credit Bills</h1>
    </center>

    <?php
    $sql = "SELECT * FROM `purchase` WHERE `balance_payment` > 0 ORDER BY `purchase`.`balance_payment_date` ASC, `purchase`.`balance_payment` ASC, `purchase`.`purchase_date` ASC; ";

    // purchase_id
    // item_name
    // single_unit_cost
    // item_qty
    // payment_account
    // supplier
    // purchased_packet_qty
    // quantity_per_packet
    // packet_price
    // first_payment
    // balance_payment_date
    // balance_payment
    // bill_total
    // bill_payment_type
    // packet_purchase
    // purchase_date
    // purchase_time


    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='DataTable'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>id</th>";
            echo "<th>Item Name</th>";
            echo "<th>Single Unit Cost</th>";
            echo "<th>Item Quantity</th>";
            echo "<th>Payment Account</th>";
            echo "<th>Supplier</th>";
            echo "<th>Purchased Packet Quantity</th>";
            echo "<th>Quantity Per Packet</th>";
            echo "<th>Packet Price</th>";
            echo "<th>First Payment</th>";
            echo "<th>Balance Payment Date</th>";
            echo "<th>Balance Payment</th>";
            echo "<th>Bill Total</th>";
            echo "<th>Bill Payment Type</th>";
            echo "<th>Packet Purchase</th>";
            echo "<th>Purchase Date</th>";
            echo "<th>Purchase Time</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['purchase_id'] . "</td>";
                echo "<td>" . $row['item_name'] . "</td>";
                echo "<td>" . $row['single_unit_cost'] . "</td>";
                echo "<td>" . $row['item_qty'] . "</td>";
                echo "<td>" . $row['payment_account'] . "</td>";
                echo "<td>" . $row['supplier'] . "</td>";
                echo "<td>" . $row['purchased_packet_qty'] . "</td>";
                echo "<td>" . $row['quantity_per_packet'] . "</td>";
                echo "<td>" . $row['packet_price'] . "</td>";
                echo "<td>" . $row['first_payment'] . "</td>";
                echo "<td>" . $row['balance_payment_date'] . "</td>";
                echo "<td>" . $row['balance_payment'] . "</td>";
                echo "<td>" . $row['bill_total'] . "</td>";
                echo "<td>" . $row['bill_payment_type'] . "</td>";
                echo "<td>" . $row['packet_purchase'] . "</td>";
                echo "<td>" . $row['purchase_date'] . "</td>";
                echo "<td>" . $row['purchase_time'] . "</td>";
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

</body>

</html>