<?php
require_once 'inc/config.php';
require 'inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ERP_COMPANY_NAME; ?> - One-Time Products</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .table-container {
            margin-top: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .status-uncleared {
            color: #dc3545;
            font-weight: bold;
        }
        .status-cleared {
            color: #28a745;
            font-weight: bold;
        }
        .status-skip {
            color: #ffc107;
            font-weight: bold;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        .filters {
            margin-bottom: 20px;
        }
        .filters select {
            padding: 8px;
            margin-right: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        .dt-buttons {
            margin-bottom: 15px;
        }
        .dt-button {
            padding: 5px 10px;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            border: none;
            margin-right: 5px;
        }
        .dt-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="back-button">← Back to POS</a>
        <h1>One-Time Products List</h1>
        
        <div class="filters">
            <select id="status-filter" onchange="filterTable()">
                <option value="all">All Status</option>
                <option value="uncleared">Uncleared</option>
                <option value="cleared">Cleared</option>
                <option value="skip">Skipped</option>
            </select>
        </div>

        <div class="table-container">
            <table id="productsTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Invoice #</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Rate</th>
                        <th>Amount</th>
                        <th>Cost</th>
                        <th>Profit</th>
                        <th>Status</th>
                        <th>Cashier</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Change Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query to get one-time products with invoice date and time
                    $sql = "SELECT o.*, i.invoice_date, i.time, e.emp_name
                           FROM oneTimeProducts_sales o 
                           INNER JOIN invoice i ON o.invoice_number = i.invoice_number 
                           INNER JOIN employees e ON i.biller = e.employ_id
                           ORDER BY i.invoice_date DESC, i.time DESC";
                    
                    $result = mysqli_query($con, $sql);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $statusClass = 'status-' . $row['status'];
                            echo "<tr class='product-row' data-status='" . $row['status'] . "' data-product-id='" . $row['oneTimeProduct_id'] . "'>";
                            echo "<td>" . htmlspecialchars($row['oneTimeProduct_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['invoice_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['product']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['qty']) . "</td>";
                            echo "<td>Rs. " . number_format($row['rate'], 2) . "</td>";
                            echo "<td>Rs. " . number_format($row['amount'], 2) . "</td>";
                            echo "<td>Rs. " . ($row['cost'] ? number_format($row['cost'], 2) : '-') . "</td>";
                            echo "<td>Rs. " . ($row['profit'] ? number_format($row['profit'], 2) : '-') . "</td>";
                            echo "<td class='status-cell " . $statusClass . "'>" . ucfirst(htmlspecialchars($row['status'])) . "</td>";
                            echo "<td>" . htmlspecialchars($row['emp_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['invoice_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                            echo "<td>
                                    <select class='status-select' onchange='changeStatus(" . $row['oneTimeProduct_id'] . ", this.value)'>
                                        <option value='uncleared' " . ($row['status'] == 'uncleared' ? 'selected' : '') . ">Uncleared</option>
                                        <option value='cleared' " . ($row['status'] == 'cleared' ? 'selected' : '') . ">Cleared</option>
                                        <option value='skip' " . ($row['status'] == 'skip' ? 'selected' : '') . ">Skip</option>
                                    </select>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='13'>No one-time products found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#productsTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                order: [[10, 'desc'], [11, 'desc']], // Sort by date and time by default
                responsive: true
            });

            // Custom status filter
            $('#status-filter').on('change', function() {
                var status = $(this).val();
                if (status === 'all') {
                    table.column(8).search('').draw();
                } else {
                    table.column(8).search(status).draw();
                }
            });
        });

        function changeStatus(productId, newStatus) {
            fetch('inc/update_one_time_product_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Find the row containing the product
                    const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                    if (row) {
                        // Update the data-status attribute
                        row.dataset.status = newStatus;
                        
                        // Update the status cell's class and text
                        const statusCell = row.querySelector('.status-cell');
                        if (statusCell) {
                            // Remove all status classes
                            statusCell.classList.remove('status-uncleared', 'status-cleared', 'status-skip');
                            // Add new status class
                            statusCell.classList.add(`status-${newStatus}`);
                            // Update the text content
                            statusCell.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        }
                    }
                    // Show success message
                    alert('Status updated successfully');
                } else {
                    alert('Failed to update status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status');
            });
        }
    </script>
</body>
</html> 