<?php require_once('../nav.php'); ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders & GRN Management</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            "50": "#eff6ff",
                            "100": "#dbeafe",
                            "200": "#bfdbfe",
                            "300": "#93c5fd",
                            "400": "#60a5fa",
                            "500": "#3b82f6",
                            "600": "#2563eb",
                            "700": "#1d4ed8",
                            "800": "#1e40af",
                            "900": "#1e3a8a",
                            "950": "#172554"
                        }
                    }
                }
            }
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <!-- <link href="https://cdn.datatables.net/2.2.1/css/dataTables.tailwindcss.css" rel="stylesheet"> -->
    <link href=" https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/2.2.1/js/dataTables.js"></script>
    <!-- <script src="https://cdn.datatables.net/2.2.1/js/dataTables.tailwind.js"></script> -->
    <!-- https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css -->

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Custom styles for Select2 Dark Theme */
        .select2-container--default .select2-selection--single {
            background-color: #2d2d2d;
            /* Tailwind equivalent: bg-gray-800 */
            border-color: #4a4a4a;
            /* Tailwind equivalent: border-gray-600 */
            color: white;
            border-radius: 0.5rem;
            /* Tailwind equivalent: rounded-lg */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: white;
        }

        .select2-dropdown {
            background-color: #2d2d2d;
            /* bg-gray-800 */
            border-color: #4a4a4a;
            /* border-gray-600 */
            color: white;
        }

        .select2-search__field {
            background-color: #3b3b3b;
            /* bg-gray-700 */
            color: white;
        }

        .select2-results__option {
            color: white;
        }

        .select2-results__option--highlighted {
            background-color: #3b3b3b;
            /* bg-gray-700 */
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-gray-800 border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold">Purchase & GRN</h1>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="index.php" class="bg-gray-900 text-white px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="create_po.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">New Purchase Order</a>
                                <a href="create_grn.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Create GRN</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total POs Card -->
                <div class="bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file-invoice text-blue-500 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">
                                        Total Purchase Orders
                                    </dt>
                                    <dd class="text-lg font-semibold text-white" id="totalPOCount">
                                        Loading...
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending GRNs -->
                <div class="bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-yellow-500 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">
                                        Pending GRNs
                                    </dt>
                                    <dd class="text-lg font-semibold text-white" id="pendingGRNCount">
                                        Loading...
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Amount -->
                <div class="bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-dollar-sign text-green-500 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">
                                        Total Amount (This Month)
                                    </dt>
                                    <dd class="text-lg font-semibold text-white" id="totalAmount">
                                        Loading...
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outstanding Payments -->
                <div class="bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-credit-card text-red-500 text-3xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-400 truncate">
                                        Outstanding Payments
                                    </dt>
                                    <dd class="text-lg font-semibold text-white" id="outstandingAmount">
                                        Loading...
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="mt-8 bg-gray-800 shadow rounded-lg p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Date Range</label>
                        <select id="dateRange" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month" selected>This Month</option>
                            <option value="year">This Year</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Status</label>
                        <select id="status" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            <option value="">All</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="ordered">Ordered</option>
                            <option value="received">Received</option>
                            <option value="cancelled">cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Supplier</label>
                        <select id="supplier" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Payment Status</label>
                        <select id="paymentStatus" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            <option value="">All</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Purchase Orders Table -->
            <div class="mt-8 bg-gray-800 shadow rounded-lg p-6">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-xl font-semibold text-white">Purchase Orders</h2>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                        <a href="create_po.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-plus mr-2"></i>
                            New Purchase Order
                        </a>
                    </div>
                </div>
                <div class="mt-6">
                    <table id="purchaseOrdersTable" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">PO Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Paid</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Balance</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <!-- Table rows will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monthly Purchase Trends Chart -->
            <div class="mt-8 bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-white mb-4">Monthly Purchase Trends</h2>
                <canvas id="purchaseTrendsChart"></canvas>
            </div>
        </main>
    </div>

    <!-- Initialize JavaScript -->
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#supplier').select2({
                theme: 'classic',
                placeholder: 'Select a supplier'
            });

            // Initialize DataTable
            const table = $('#purchaseOrdersTable').DataTable({
                ajax: 'get_purchase_orders.php',
                columns: [{
                        data: 'order_date',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'po_number'
                    },
                    {
                        data: 'supplier_name'
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const statusClasses = {
                                'draft': 'bg-gray-600',
                                'pending': 'bg-yellow-600',
                                'approved': 'bg-green-600',
                                'ordered': 'bg-blue-600',
                                'received': 'bg-green-600',
                                'cancelled': 'bg-red-600'
                            };
                            return `<span class="px-2 py-1 text-xs font-medium rounded-full ${statusClasses[data]}">${data}</span>`;
                        }
                    },
                    {
                        data: 'total_amount',
                        render: function(data) {
                            return new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            }).format(data);
                        }
                    },
                    {
                        data: 'paid_amount',
                        render: function(data) {
                            return new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            }).format(data);
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            const balance = data.total_amount - data.paid_amount;
                            return new Intl.NumberFormat('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            }).format(balance);
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <div class="flex justify-end space-x-2">
                                    <button onclick="viewPO(${data.po_id})" class="text-blue-400 hover:text-blue-300" title="View PO">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    ${data.status !== 'received' ? `
                                        <button onclick="editPO(${data.po_id})" class="text-yellow-400 hover:text-yellow-300" title="Edit PO">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    ` : ''}
                                    ${data.status === 'ordered' ? `
                                        <button onclick="createGRN(${data.po_id})" class="text-green-400 hover:text-green-300" title="Create GRN">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    ` : ''}
                                    <button onclick="viewPayments(${data.po_id})" class="text-purple-400 hover:text-purple-300" title="View Payments">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button onclick="exportPDF(${data.po_id})" class="text-red-400 hover:text-red-300" title="Export to PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    ${data.status === 'draft' ? `
                                        <button onclick="deletePO(${data.po_id})" class="text-red-400 hover:text-red-300" title="Delete PO">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                responsive: true,
                theme: 'dark'
            });

            // Load Summary Data
            loadSummaryData();

            // Initialize Purchase Trends Chart
            initializePurchaseTrendsChart();

            // Event Listeners for Filters
            $('#dateRange, #status, #supplier, #paymentStatus').on('change', function() {
                reloadData();
            });
        });

        // Function to load summary data
        function loadSummaryData() {
            $.ajax({
                url: 'get_summary_data.php',
                method: 'GET',
                success: function(response) {
                    $('#totalPOCount').text(response.totalPO);
                    $('#pendingGRNCount').text(response.pendingGRN);
                    $('#totalAmount').text(new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(response.totalAmount));
                    $('#outstandingAmount').text(new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(response.outstandingAmount));
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to load summary data',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Initialize Purchase Trends Chart
        function initializePurchaseTrendsChart() {
            $.ajax({
                url: 'get_purchase_trends.php',
                method: 'GET',
                success: function(response) {
                    const ctx = document.getElementById('purchaseTrendsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: 'Purchase Amount',
                                data: response.data,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: '#fff'
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    ticks: {
                                        color: '#fff',
                                        callback: function(value) {
                                            return `${value.toLocaleString()}`;
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        color: '#fff'
                                    },
                                    grid: {
                                        color: 'rgba(255, 255, 255, 0.1)'
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }

        // Function to reload data based on filters
        function reloadData() {
            const filters = {
                dateRange: $('#dateRange').val(),
                status: $('#status').val(),
                supplier: $('#supplier').val(),
                paymentStatus: $('#paymentStatus').val()
            };

            $('#purchaseOrdersTable').DataTable().ajax.url(
                `get_purchase_orders.php?${$.param(filters)}`
            ).load();

            loadSummaryData();
        }

        // Action Functions
        function viewPO(poId) {
            window.location.href = `view_po.php?id=${poId}`;
        }

        function editPO(poId) {
            window.location.href = `edit_po.php?id=${poId}`;
        }

        function createGRN(poId) {
            window.location.href = `create_grn.php?po_id=${poId}`;
        }

        function viewPayments(poId) {
            // Show payments modal
            $.ajax({
                url: `get_payments.php?po_id=${poId}`,
                success: function(response) {
                    Swal.fire({
                        title: 'Payment History',
                        html: response,
                        width: '800px',
                        showCloseButton: true,
                        showConfirmButton: false
                    });
                }
            });
        }

        function exportPDF(poId) {
            window.location.href = `export_po.php?id=${poId}`;
        }

        function deletePO(poId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_po.php',
                        method: 'POST',
                        data: {
                            po_id: poId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Purchase order has been deleted.',
                                    'success'
                                );
                                $('#purchaseOrdersTable').DataTable().ajax.reload();
                                loadSummaryData();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    response.message,
                                    'error'
                                );
                            }
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>