   <?php
    include 'DataTable_cdn.php';
    include 'conn.php';

    // Fetch data from the 'count' table and group by date with cost
    $sql = "SELECT date, SUM(printer_counter_count.count) AS total_count, SUM(printer_counter_count.cost) AS total_cost
                        FROM printer_counter_count, printer_counter_types
                        WHERE printer_counter_types.typeID = printer_counter_count.typeID 
                        GROUP BY date
                        ORDER BY date DESC;";
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
                                <th>Total Count</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($row = $result->fetch_assoc()) {
                $date = $row['date'];
                $total_count = $row['total_count'];
                $total_cost = $row['total_cost'];

                // Output total_cost as a currency with 2 decimal places
                $total_cost = number_format($total_cost, 2);

                echo '<tr>
                    <td>' . $date . '</td>
                    <td>' . $total_count . '</td>
                    <td>' . $total_cost . '</td>
                  </tr>';
            }
            echo '</tbody>
                    </table>';
        } else {
            // Output a message if there are no rows in the result
            echo 'No data found.';
        }
    }
    ?>

   <style>
       table {
           border-collapse: collapse;
           width: 80%;

       }
   </style>