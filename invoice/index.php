<?php require_once '../inc/config.php';
require_once '../inc/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading Invoices...</p>
        </div>
    </div>

    <center>
        <h1>All Invoices</h1>
    </center>

    <div style="width: 90%; margin: 0 auto;">
        <table id='DataTable' class='display' style='width: 100%'>
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Tele</th>
                    <th>Time</th>
                    <th>Total</th>
                    <th>Disco.</th>
                    <th>Advance</th>
                    <th>Balance</th>
                    <th>Total Cost</th>
                    <th>Total Profit</th>
                    <th>Cashier</th>
                    <th>Full Paid</th>
                    <th>Payment Method</th>
                    <th>Actions</th>
                    <th>Print</th>
                </tr>
            </thead>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="/invoice/addFundInvoiceModal.js"></script>
    
    <script>
        $(document).ready(function() {
            // Show loading overlay
            $('#loadingOverlay').show();
            
            $('#DataTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "/invoice/get_invoices_ajax.php",
                    "type": "POST",
                    "error": function(xhr, error, code) {
                        $('#loadingOverlay').hide();
                        console.log(xhr, error, code);
                        alert('Error loading invoices. Please refresh the page.');
                    }
                },
                "columns": [
                    { "data": 0 }, // Invoice No
                    { "data": 1 }, // Date
                    { "data": 2 }, // Customer Name
                    { "data": 3 }, // Tele
                    { "data": 4 }, // Time
                    { "data": 5 }, // Total
                    { "data": 6 }, // Discount
                    { "data": 7 }, // Advance
                    { "data": 8 }, // Balance
                    { "data": 9 }, // Total Cost
                    { "data": 10 }, // Total Profit
                    { "data": 11 }, // Cashier
                    { "data": 12, "orderable": false }, // Full Paid
                    { "data": 13 }, // Payment Method
                    { "data": 14, "orderable": false }, // Actions
                    { "data": 15, "orderable": false }  // Print
                ],
                "order": [[0, "desc"]], // Default sort by Invoice No descending
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                "language": {
                    "processing": "Loading invoices..."
                },
                "initComplete": function(settings, json) {
                    // Hide loading overlay when table is fully loaded
                    $('#loadingOverlay').fadeOut(300);
                },
                "drawCallback": function(settings) {
                    // Hide loading overlay after each redraw (pagination, search, etc.)
                    $('#loadingOverlay').fadeOut(300);
                },
                "preDrawCallback": function(settings) {
                    // Show loading overlay before each draw
                    $('#loadingOverlay').show();
                }
            });
        });
    </script>

    <style>
        /* Loading Overlay Styles */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-spinner {
            text-align: center;
            color: white;
        }

        .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loading-spinner p {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        /* Data Table Style */
        th.sorting:nth-child(2),
        th.sorting:nth-child(7),
        th.sorting:nth-child(11),
        th.sorting:nth-child(12),
        th.sorting:nth-child(10) {
            width: 5px !important;
        }
    </style>

</body>

</html>

<?php end_db_con(); ?>