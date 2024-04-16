<head>
    <title>Sales</title>
    <!-- adding jquery cdn -->
    <script src="https://code.jquery.com/jquery-3.5.1.js "></script>

    <!-- adding datatable cdn -->
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>

    <!-- adding datatable style cdn -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css"> -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/dataTables.semanticui.min.css">
    <script src="https://cdn.datatables.net/1.13.3/js/dataTables.semanticui.min.js"></script>

    <!-- initialing datatable -->
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
</head>

<table id="example">
    <thead>
        <tr>
            <th>Month</th>
            <th>Savings</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>January</td>
            <td>$100</td>
        </tr>
        <tr>
            <td>February</td>
            <td>$80</td>
        </tr>
    </tbody>
    <!-- <tfoot>
        <tr>
            <td>Sum</td>
            <td>$180</td>
        </tr>
    </tfoot> -->
</table>