<?php require_once 'inc/config.php';
require_once 'inc/header.php';
include 'inc/DataTable_cdn.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pettycash</title>
</head>

<body>
    <center>
        <h1>Pettycash</h1>
    </center>

    <?php
    $sql = "SELECT * FROM `pettycash` ORDER BY `pettycash`.`id` DESC";

    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='DataTable'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>id</th>";
            echo "<th>Perrycash for</th>";
            echo "<th>Amount</th>";
            echo "<th>Date</th>";
            echo "<th>Time</th>";
            echo "<th>Employee Name</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                $emp_name = $row['emp_name'];
                if ($emp_name == '') {
                    $emp_name = 'Srijaya Print House';
                }
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['perrycash'] . "</td>";
                echo "<td>" . $row['amount'] . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                echo "<td>" . $row['time'] . "</td>";
                echo "<td>" . $emp_name . "</td>";
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



<?php end_db_con(); ?>