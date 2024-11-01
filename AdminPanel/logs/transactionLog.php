<?php

include '../nav.php';

echo "<h1>Transaction List</h1>";

// Updated SQL query to select from transaction_log and join with employees table
$sql = "SELECT transaction_log.*, employees.emp_name 
        FROM `transaction_log` 
        INNER JOIN employees ON employees.employ_id = transaction_log.employ_id 
        ORDER BY `transaction_log`.`transaction_id` DESC";

$result = mysqli_query($con, $sql);
$resultCheck = mysqli_num_rows($result);

if ($resultCheck > 0) {

    // Define table headers based on transaction_log table columns
    echo "<table id='DataTable'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Employee</th>";
    echo "<th>Transaction Type</th>";
    echo "<th>Description</th>";
    echo "<th>Amount</th>";
    echo "<th>Date</th>";
    echo "<th>Time</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    // Loop through each row to populate the table
    while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['transaction_id'] . "</td>";
        echo "<td>" . $row['emp_name'] . "</td>";
        echo "<td>" . $row['transaction_type'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
        echo "<td>" . $row['amount'] . "</td>";
        echo "<td>" . $row['transaction_date'] . "</td>";
        echo "<td>" . $row['transaction_time'] . "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
} else {
    echo "<p>No transactions found.</p>";
}
