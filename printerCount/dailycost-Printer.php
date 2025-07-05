    <?php
    include 'conn.php';
    include 'DataTable_cdn.php';

    // Fetch data from the 'count' table and group by date and Printer Name with cost
    $sql = "SELECT date, printer_counter_printers.printerName, SUM(printer_counter_count.count) AS total_count, SUM(printer_counter_count.cost) AS total_cost
                        FROM printer_counter_count, printer_counter_printers, printer_counter_types
                        WHERE printer_counter_types.typeID = printer_counter_count.typeID 
                            AND printer_counter_printers.printerID = printer_counter_types.printerID
                        GROUP BY date, printer_counter_printers.printerName
                        ORDER BY date DESC, printer_counter_printers.printerName;";
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
                                <th>Printer Name</th>
                                <th>Total Count</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>';
            while ($row = $result->fetch_assoc()) {
                $date = $row['date'];
                $printerName = $row['printerName'];
                $total_count = $row['total_count'];
                $total_cost = $row['total_cost'];

                echo '<tr>
                    <td>' . $date . '</td>
                    <td>' . $printerName . '</td>
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