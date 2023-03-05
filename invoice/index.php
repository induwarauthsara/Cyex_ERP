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
    <!-- adding jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.5.1.js "></script>

    <!-- adding datatable cdn -->
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>

    <!-- adding datatable style cdn -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">

    <!-- adding cdn to datatable file export -->
    <script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.print.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.5/css/buttons.dataTables.min.css">

    <!--adding cdn to  datatable responsive -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>

    <!-- adding cdn to datatable SearchBuilder -->
    <link rel="stylesheet" href="https://cdn.datatables.net/searchbuilder/1.4.0/css/searchBuilder.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/datetime/1.3.1/css/dataTables.dateTime.min.css">
    <script src="https://cdn.datatables.net/searchbuilder/1.4.0/js/dataTables.searchBuilder.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.3.1/js/dataTables.dateTime.min.js"></script>

    <!-- adding cnd to datatable select -->
    <script src="https://cdn.datatables.net/select/1.6.1/js/dataTables.select.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.1/css/select.dataTables.min.css">

    <!-- initialing datatable -->
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                buttons: ['copy', 'excel', 'pdf', 'print' // 'pageLength',
                ],
                select: true,
                responsive: true,
                dom: 'lBfrtipQ', // Adding Conditon for "Q" at end
                order: [
                    [0, 'desc']
                ],

            });
        });
    </script>
</head>


<body>
    <?php
    $sql = "SELECT * FROM invoice ORDER BY id DESC;";
    if ($result = mysqli_query($con, $sql)) {
        if (mysqli_num_rows($result) > 0) {
            echo "<table id='myTable'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th>No</th>";
            echo "<th>Invoice Number</th>";
            echo "<th>Customer</th>";
            echo "<th>Tele</th>";
            echo "<th>Date</th>";
            // echo "<th>Biller</th>";
            // echo "<th>Worker</th>";
            echo "<th>Total</th>";
            echo "<th>Disco.</th>";
            echo "<th>Advance</th>";
            echo "<th>Balance</th>";
            echo "<th>Full Paid</th>";
            echo "<th>Add <br> Fund</th>";
            echo "<th>Print</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['invoice_number'] . "</td>";
                echo "<td>" . $row['customer_name'] . "</td>";
                echo "<td>" . $row['customer_mobile'] . "</td>";
                echo "<td>" . $row['invoice_date'] . "</td>";
                // echo "<td>" . $row['biller'] . "</td>";
                // echo "<td>" . $row['primary_worker'] . "</td>";
                echo "<td> " . $row['total'] . "</td>";
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
                echo "<td> <a href='add-fund.php?id={$row['invoice_number']}'>Add</a> </td>";
                echo "<td> <a href='print.php?id={$row['invoice_number']}' target='_blanck'>Print</a> </td>";
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

        /* Data Table Style */
        .dt-button,
        .dtsb-add {
            background-color: green !important;
            color: white !important;
        }

        .dataTables_wrapper {
            margin: 15px;
        }

        .dt-buttons {
            margin-left: 30px;
        }

        .dtsb-searchBuilder {
            margin-top: 50px;
        }

        th.sorting:nth-child(2),
        th.sorting:nth-child(7),
        th.sorting:nth-child(11),
        th.sorting:nth-child(12),
        th.sorting:nth-child(10) {
            width: 5px !important;
        }

        th {
            text-align: center !important;
        }
    </style>

</body>

</html>



<?php end_db_con(); ?>