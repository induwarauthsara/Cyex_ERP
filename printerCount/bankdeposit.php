<?php
include 'DataTable_cdn.php';

include 'conn.php';



echo "<h1>Bank Deposit History</h1>";

// Fetch data from bank_deposit table
$sql = "SELECT * FROM printer_counter_bank_deposit";
$result = $con->query($sql);

// Check if the query execution was successful
if ($result === false) {
    echo "Error: " . $sql . "<br>" . $con->error;
} else {
    // Check if there are rows in the result
    if ($result->num_rows > 0) {
        echo '<table id="DataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>';
        while ($row = $result->fetch_assoc()) {
            $date = $row['date'];
            $amount = $row['amount'];

            // Output amount as a currency with 2 decimal places
            $amount = number_format($amount, 2);

            echo '<tr>
                    <td>' . $date . '</td>
                    <td>' . $amount . '</td>
                  </tr>';
        }
        echo '</tbody>
    </table>';
    } else {
        // Output a message if there are no rows in the result
        echo 'No data found.';
    }
}