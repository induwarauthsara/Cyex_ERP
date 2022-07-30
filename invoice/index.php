<?php require_once '../inc/config.php';
require_once '../inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
</head>

<body>
    <?php
    $sql = "SELECT * FROM invoice";
    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>No</th>";
            echo "<th>Invoice Number</th>";
            echo "<th>Customer</th>";
            echo "<th>Tele</th>";
            echo "<th>Date</th>";
            echo "<th>Biller</th>";
            echo "<th>Worker</th>";
            echo "<th>Discount</th>";
            echo "<th>Advance</th>";
            echo "<th>Balance</th>";
            echo "<th>Full Paid ?</th>";
            echo "<th>Print</th>";
            echo "</tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['invoice_number'] . "</td>";
                echo "<td>" . $row['customer_name'] . "</td>";
                echo "<td>" . $row['customer_mobile'] . "</td>";
                echo "<td>" . $row['invoice_date'] . "</td>";
                echo "<td>" . $row['biller'] . "</td>";
                echo "<td>" . $row['primary_worker'] . "</td>";
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
                echo "<td> <a href='print.php?id={$row['invoice_number']}' target='_blanck'>Print</a> </td>";
                echo "</tr>";
            }
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
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }

        tr:hover {
            background-color: lawngreen;
        }
    </style>

</body>

</html>



<?php end_db_con(); ?>