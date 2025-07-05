<?php
include 'conn.php';
// Get The Total Count of Types for the Printer for Row Span
$sql = "SELECT COUNT(printer_counter_types.typeName) 
                    FROM printer_counter_types,printer_counter_printers 
                    WHERE printer_counter_printers.printerName = '$printerName'
                        AND printer_counter_printers.active = '1'
                        AND printer_counter_types.printerID = printer_counter_printers.printerID;";
?>

<!DOCTYPE html>
<html>

<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<table border="1">
    <form action="submit.php" method="post">
        <tr>
            <th>Printer Name</th>
            <th>Print Type</th>
            <th>Printer Count</th>
        </tr>
        <?php
        $sql = "SELECT printerName, ipAddress from printer_counter_printers WHERE active = '1'";
        $resultPrinterNames = $con->query($sql);
        if ($resultPrinterNames->num_rows > 0) {
            while ($rowPrinterNames = $resultPrinterNames->fetch_assoc()) {
                $printerName = $rowPrinterNames["printerName"];
                $ipAddress = $rowPrinterNames["ipAddress"];

                // Get The Total Count of Types for the Printer for Row Span
                $sql = "SELECT COUNT(printer_counter_types.typeName) 
                    FROM printer_counter_types,printer_counter_printers 
                    WHERE `printerName` = '$printerName' 
                        AND printer_counter_printers.active = '1'
                        AND printer_counter_types.printerID = printer_counter_printers.printerID;";

                $result = $con->query($sql);
                $row = $result->fetch_assoc();
                $total = $row["COUNT(printer_counter_types.typeName)"];

                echo "<tr><th rowspan='$total'> <a href='$ipAddress' target='_blank'>$printerName</a> </th>";

                $sql = "SELECT printerName, typeName, printer_counter_types.typeID, printer_counter_types.totalCount
                    FROM printer_counter_types,printer_counter_printers 
                    WHERE `printerName` = '$printerName' 
                        AND printer_counter_printers.active = '1'
                        AND printer_counter_types.printerID = printer_counter_printers.printerID;";
                $result = $con->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $typeName = $row["typeName"];
                        $typeID = $row["typeID"];
                        $LastTotalTypeCount = $row["totalCount"];

                        echo "<td> $typeName </td>";
                        echo "<input name='$typeID' value='typeID' hidden>   ";
                        echo "<td> <input name='$typeID' type='number'> ";
                        // today changes
                        echo "<input name='LastTotalTypeCount_$typeID' value='$LastTotalTypeCount' hidden disabled>";
                        echo "<span id='todayCount_$typeID'></span>";
                        echo "</td> </tr> ";
                    }
                } else {
                    echo "0 results";
                }
            }
        } else {
            echo "0 results";
        }
        $con->close();
        ?>
        <tr>
            <td colspan="3" align="center"><input type="submit" value="Submit"></td>
        </tr>
    </form>
</table>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    td,
    th {
        border: 1px solid black;
        padding: 10px;
    }

    th {
        text-align: center;
    }

    input {
        padding: 10px 20px;
    }
</style>

<script>
    // Please write JS code for get input names list as "typeID-List" where input type is 'number'
    document.addEventListener("DOMContentLoaded", function() {
        var inputs = document.querySelectorAll("input[type='number']");
        var typeIDList = [];
        inputs.forEach(function(input) {
            typeIDList.push(input.getAttribute("name"));
            input.addEventListener("input", function() {
                var typeID = input.getAttribute("name");
                var LastTotalTypeCount = document.querySelector(`input[name='LastTotalTypeCount_${typeID}']`).value;
                var todayCount = document.querySelector(`#todayCount_${typeID}`);
                todayCount.innerHTML = input.value - LastTotalTypeCount;
            });
        });
        console.log(typeIDList);
    });

    // for each typeID in the list, get the value of the input field and subtract the LastTotalTypeCount from it
    // and display the result in the span with id "todayCount_typeID"
    for (var i = 0; i < typeIDList.length; i++) {
        var typeID = typeIDList[i];
        var input = document.querySelector(`input[name='${typeID}']`);
        var LastTotalTypeCount = document.querySelector(`input[name='LastTotalTypeCount_${typeID}']`).value;
        var todayCount = document.querySelector(`#todayCount_${typeID}`);
        todayCount.innerHTML = input.value - LastTotalTypeCount;
    }
</script>