<?php require_once '../inc/config.php';
require_once '../inc/header.php';
include '../inc/DataTable_cdn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
</head>

<body>
    <center>
        <h1>Trasnsaction Log</h1>
    </center>

    <?php
    $sql = "SELECT transaction_log.*, employees.emp_name
            FROM transaction_log,employees
            WHERE transaction_log.employ_id = employees.employ_id ";
    // 	transaction_id
    // transaction_type
    // description	
    // amount	
    // transaction_date
    // transaction_time
    // employ_id 
    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='DataTable'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>tras_id</th>";
            echo "<th>Transaction Type</th>";
            echo "<th>Description</th>";
            echo "<th>Amount</th>";
            echo "<th>Date</th>";
            echo "<th>Time</th>";
            echo "<th>Employee</th>";
            echo "</thead>";
            echo "</tr>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                $transaction_id = $row["transaction_id"];
                $transaction_type = $row["transaction_type"];
                $description = $row["description"];
                $amount = $row["amount"];
                $date = $row["transaction_date"];
                $time = $row["transaction_time"];
                $employ = $row["emp_name"];
                echo "<tr>";
                echo "<td>" . $transaction_id . "</td>";
                echo "<td>" . $transaction_type . "</td>";
                echo "<td>" . $description . "</td>";
                echo "<td>" . $amount . "</td>";
                echo "<td>" . $date . "</td>";
                echo "<td>" . $time . "</td>";
                echo "<td>" . $employ . "</td>";
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