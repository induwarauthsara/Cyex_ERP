<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTables Example</title>
    <!-- Include DataTables.js CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.2/css/dataTables.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/2.0.2/js/dataTables.min.js"></script>
</head>

<body>

    <?php
    include 'conn.php';
    include 'DataTable_cdn.php'; ?>
    <div style="margin: 20px;">
        <h2>Printers Count</h2>
        <table id="DataTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Printer Name </th>
                    <th>Type Name</th>
                    <th>Count</th>
                    <th>Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php

                // Fetch data from the 'cost' table
                $sql = "SELECT date, time, printer_counter_printers.printerName, printer_counter_types.typeName, printer_counter_count.count, printer_counter_count.cost
                        FROM printer_counter_count, printer_counter_printers, printer_counter_types
                        WHERE printer_counter_types.typeID = printer_counter_count.typeID 
	                        AND printer_counter_printers.printerID = printer_counter_types.printerID
                        ORDER BY date DESC, printer_counter_types.typeID;";
                $result = $con->query($sql);

                // Check if the query execution was successful
                if ($result === false) {
                    echo "Error: " . $sql . "<br>" . $con->error;
                } else {
                    // Check if there are rows in the result
                    if ($result->num_rows > 0) {

                        while ($row = $result->fetch_assoc()) {
                            $date = $row['date'];
                            $time = $row['time'];
                            $printerName = $row['printerName'];
                            $typeName = $row['typeName'];
                            $count = $row['count'];
                            $cost = $row['cost'];

                            echo '<tr>
                    <td>' . $date . '</td>
                    <td>' . $time . '</td>
                    <td>' . $printerName . '</td>
                    <td>' . $typeName . '</td>
                    <td>' . $count . '</td>
                    <td>' . $cost . '</td>
                  </tr>';
                        }
                    } else {
                        // Output a message if there are no rows in the result
                        echo 'No data found.';
                    }
                }

                // Close the database connection
                $con->close(); ?>
            </tbody>
        </table>
    </div>

</body>

</html>