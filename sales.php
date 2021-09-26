<?php require_once 'inc/config.php'; ?>
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
    $sql = "SELECT * FROM sales";
    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr>";
            echo "<th>No</th>";
            echo "<th>Invoice Number</th>";
            echo "<th>Product</th>";
            echo "<th>QTY</th>";
            echo "<th>Rate</th>";
            echo "<th>Amount</th>";
            echo "<th>Employee</th>";
            echo "<th>TODO</th>";
            echo "</tr>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['sales_id'] . "</td>";
                echo "<td>" . $row['invoice_number'] . "</td>";
                echo "<td>" . $row['product'] . "</td>";
                echo "<td>" . $row['qty'] . "</td>";
                echo "<td>" . $row['rate'] . "</td>";
                echo "<td>" . $row['amount'] . "</td>";
                echo "<td>" . $row['worker'] . "</td>";
                echo "<td> " . $row['todo'] . "</td>";
                echo "<td> <a href='edit.php?edit= {$row['sales_id']} ' target='_blanck'>Edit</a> </td>";
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
    </style>

</body>

</html>



<?php end_db_con(); ?>