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

        /* Add pulse animation for Create GRN button */
        .pulse-animation {
            box-shadow: 0 0 0 rgba(52, 211, 153, 0.4);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(52, 211, 153, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(52, 211, 153, 0);
            }
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
                                <a href="create_grn.php" class="px-3 py-2 rounded-md text-sm font-bold bg-green-600 hover:bg-green-700 text-white transform hover:scale-105 transition-all duration-200 shadow-md flex items-center">
                                    <i class="fas fa-truck-loading mr-2"></i>
                                    Create GRN
                                </a>
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

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Purchase Trends -->
                <div class="bg-gray-800 shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-4">Purchase Trends (Last 12 Months)</h2>
                    <div class="relative h-64">
                        <canvas id="purchaseTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Top Suppliers -->
                <div class="bg-gray-800 shadow rounded-lg p-4">
                    <h2 class="text-lg font-semibold mb-4">Top Suppliers (Last 30 Days)</h2>
                    <div class="relative h-64">
                        <canvas id="topSuppliersChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- GRN Section Title -->
            <div class="mt-8 bg-gradient-to-r from-green-800 to-blue-900 rounded-lg p-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-white rounded-full p-2 mr-4">
                            <i class="fas fa-truck-loading text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">Goods Receive Notes (GRN)</h2>
                            <p class="text-gray-300 mt-1">Update inventory when receiving products from suppliers</p>
                        </div>
                    </div>
                    <a href="create_grn.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-bold rounded-md text-green-800 bg-white hover:bg-gray-100 shadow-lg transform hover:scale-105 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        New GRN
                    </a>
                </div>
            </div>

            <!-- Goods Receive Notes (GRN) Table -->
            <div class="mt-4 bg-gray-800 shadow rounded-lg p-6">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-xl font-semibold text-white">Goods Receive Notes (GRN)</h2>
                    </div>
                </div>
                <div class="mt-6">
                    <table id="grnTable" class="min-w-full divide-y divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">GRN Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">PO Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Total Value</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <!-- Table rows will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- GRN Info Box -->
            <div class="mt-3 bg-blue-900 bg-opacity-50 rounded-lg p-4 flex items-start">
                <div class="flex-shrink-0 bg-blue-700 rounded-full p-2 mr-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                <div>
                    <h3 class="font-medium text-blue-300">Why GRN is Important</h3>
                    <p class="text-gray-300 text-sm mt-1">
                        Goods Receive Notes update your inventory when products arrive from suppliers. 
                        Creating a GRN is essential for accurate stock levels, batch tracking, and financial accounting.
                    </p>
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
        </main>
    </div>

    <!-- Select2 Dark Theme CSS -->
    <style>
        /* Select2 Dark Theme Customization */
        .select2-container--classic .select2-selection--single {
            background-color: #2d2d2d !important;
            border: 1px solid #4a5568 !important;
            border-radius: 0.5rem !important;
            height: 38px !important;
            padding: 4px 8px !important;
        }

        .select2-container--classic .select2-selection--single .select2-selection__rendered {
            color: #e2e8f0 !important;
            line-height: 28px !important;
            padding-left: 8px !important;
            padding-right: 20px !important;
        }

        .select2-container--classic .select2-selection--single .select2-selection__placeholder {
            color: #a0aec0 !important;
        }

        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            position: absolute !important;
            top: 1px !important;
            right: 1px !important;
            width: 20px !important;
        }

        .select2-container--classic .select2-selection--single .select2-selection__arrow b {
            border-color: #a0aec0 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            height: 0 !important;
            left: 50% !important;
            margin-left: -4px !important;
            margin-top: -2px !important;
            position: absolute !important;
            top: 50% !important;
            width: 0 !important;
        }

        .select2-container--classic.select2-container--open .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #a0aec0 transparent !important;
            border-width: 0 4px 5px 4px !important;
        }

        .select2-dropdown {
            background-color: #2d2d2d !important;
            border: 1px solid #4a5568 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        .select2-results__options {
            background-color: #2d2d2d !important;
            max-height: 200px !important;
            overflow-y: auto !important;
        }

        .select2-results__option {
            background-color: #2d2d2d !important;
            color: #e2e8f0 !important;
            padding: 8px 12px !important;
            border-bottom: 1px solid #4a5568 !important;
        }

        .select2-results__option:last-child {
            border-bottom: none !important;
        }

        .select2-results__option--highlighted {
            background-color: #3182ce !important;
            color: #ffffff !important;
        }

        .select2-results__option[aria-selected="true"] {
            background-color: #2b6cb0 !important;
            color: #ffffff !important;
        }

        .select2-search--dropdown {
            background-color: #2d2d2d !important;
            border-bottom: 1px solid #4a5568 !important;
            padding: 8px !important;
        }

        .select2-search--dropdown .select2-search__field {
            background-color: #1a202c !important;
            border: 1px solid #4a5568 !important;
            border-radius: 0.375rem !important;
            color: #e2e8f0 !important;
            padding: 6px 8px !important;
            width: 100% !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: #3182ce !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.3) !important;
        }

        .select2-search--dropdown .select2-search__field::placeholder {
            color: #a0aec0 !important;
        }

        /* Focus state for select2 container */
        .select2-container--classic.select2-container--focus .select2-selection--single {
            border-color: #3182ce !important;
            box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.3) !important;
        }

        /* Disabled state */
        .select2-container--classic .select2-selection--single[disabled] {
            background-color: #1a202c !important;
            color: #4a5568 !important;
        }

        /* Clear button styling */
        .select2-selection__clear {
            color: #a0aec0 !important;
            cursor: pointer !important;
            float: right !important;
            font-weight: bold !important;
            margin-right: 10px !important;
        }

        .select2-selection__clear:hover {
            color: #e2e8f0 !important;
        }

        /* Loading state */
        .select2-results__option--loading {
            color: #a0aec0 !important;
        }

        /* No results message */
        .select2-results__message {
            color: #a0aec0 !important;
            padding: 8px 12px !important;
        }

        /* Multiple select styling (for future use) */
        .select2-container--classic .select2-selection--multiple {
            background-color: #2d2d2d !important;
            border: 1px solid #4a5568 !important;
            border-radius: 0.5rem !important;
            min-height: 38px !important;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice {
            background-color: #3182ce !important;
            border: 1px solid #2c5aa0 !important;
            border-radius: 0.375rem !important;
            color: #ffffff !important;
            padding: 2px 8px !important;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice__remove {
            color: #ffffff !important;
            margin-right: 5px !important;
        }

        .select2-container--classic .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #feb2b2 !important;
        }

        /* Ensure proper z-index for dropdown */
        .select2-dropdown {
            z-index: 9999 !important;
        }
    </style>

    <!-- Enhanced JavaScript with improved API integration -->
    <script>
        let purchaseOrdersTable, grnTable;
        
        $(document).ready(function() {
            // Initialize Select2 for all dropdowns
            initializeSelect2Dropdowns();
            
            // Initialize DataTables
            initializeTables();

            // Load initial data
            loadDashboardData();
            
            // Initialize Charts
            initializeCharts();

            // Event Listeners for Filters
            $('#dateRange, #status, #supplier, #paymentStatus').on('change', function() {
                applyFilters();
            });

            // Custom date range handler
            $('#dateRange').on('change', function() {
                if ($(this).val() === 'custom') {
                    showCustomDateModal();
                }
            });
        });

        // Initialize supplier dropdown with search
        function initializeSupplierSelect() {
            $('#supplier').select2({
                theme: 'classic',
                placeholder: 'Select a supplier',
                allowClear: true,
                ajax: {
                    url: '../api/supplier.php?action=search',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                            limit: 10
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.data.map(function(supplier) {
                                return {
                                    id: supplier.supplier_id,
                                    text: supplier.supplier_name + ' (' + supplier.supplier_tel + ')'
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }

        // Initialize all Select2 dropdowns for consistent styling
        function initializeSelect2Dropdowns() {
            // Initialize supplier dropdown with search functionality
            initializeSupplierSelect();
            
            // Initialize filter dropdowns with Select2 for consistent styling
            $('#dateRange').select2({
                theme: 'classic',
                minimumResultsForSearch: Infinity, // Disable search for simple dropdowns
                width: '100%'
            });
            
            $('#status').select2({
                theme: 'classic',
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
            
            $('#paymentStatus').select2({
                theme: 'classic',
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }

        // Initialize DataTables with enhanced functionality
        function initializeTables() {
            // GRN DataTable
            grnTable = $('#grnTable').DataTable({
                ajax: {
                    url: '../api/grn.php?action=list',
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: 'receipt_date',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'grn_number',
                        render: function(data, type, row) {
                            return `<span class="font-mono text-blue-400">${data}</span>`;
                        }
                    },
                    {
                        data: 'po_number',
                        render: function(data) {
                            return data ? `<span class="font-mono text-green-400">${data}</span>` : 
                                         '<span class="text-gray-500">Direct GRN</span>';
                        }
                    },
                    {
                        data: 'supplier_name',
                        render: function(data) {
                            return `<span class="text-white">${data}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const statusConfig = {
                                'draft': { class: 'bg-gray-600', text: 'Draft' },
                                'completed': { class: 'bg-green-600', text: 'Completed' },
                                'cancelled': { class: 'bg-red-600', text: 'Cancelled' }
                            };
                            const config = statusConfig[data] || statusConfig['draft'];
                            return `<span class="px-2 py-1 text-xs font-medium rounded-full ${config.class}">${config.text}</span>`;
                        }
                    },
                    {
                        data: 'item_count',
                        render: function(data) {
                            return `<span class="text-center">${data || 0}</span>`;
                        }
                    },
                    {
                        data: 'grn_total',
                        render: function(data) {
                            return formatCurrency(data || 0);
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data) {
                            return `
                                <div class="flex justify-end space-x-2">
                                    <button onclick="viewGRN(${data.grn_id})" 
                                            class="text-blue-400 hover:text-blue-300 p-1" 
                                            title="View GRN">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="printGRN(${data.grn_id})" 
                                            class="text-green-400 hover:text-green-300 p-1" 
                                            title="Print GRN">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    ${data.status === 'draft' ? `
                                        <button onclick="editGRN(${data.grn_id})" 
                                                class="text-yellow-400 hover:text-yellow-300 p-1" 
                                                title="Edit GRN">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteGRN(${data.grn_id})" 
                                                class="text-red-400 hover:text-red-300 p-1" 
                                                title="Delete GRN">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                    ${data.status === 'draft' ? `
                                        <button onclick="completeGRN(${data.grn_id})" 
                                                class="text-purple-400 hover:text-purple-300 p-1" 
                                                title="Complete GRN">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            `;
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 5,
                responsive: true,
                dom: '<"flex justify-between items-center mb-4"<"flex items-center"l><"flex items-center"f>>rtip',
                language: {
                    search: "Search GRNs:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ GRNs",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Purchase Orders DataTable
            purchaseOrdersTable = $('#purchaseOrdersTable').DataTable({
                ajax: {
                    url: '../api/purchase.php?action=purchase_orders',
                    dataSrc: 'data'
                },
                columns: [
                    {
                        data: 'order_date',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'po_number',
                        render: function(data) {
                            return `<span class="font-mono text-blue-400">${data}</span>`;
                        }
                    },
                    {
                        data: 'supplier_name',
                        render: function(data, type, row) {
                            return `
                                <div>
                                    <div class="text-white font-medium">${data}</div>
                                    <div class="text-gray-400 text-xs">${row.supplier_tel || ''}</div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            const statusConfig = {
                                'draft': { class: 'bg-gray-600', text: 'Draft' },
                                'pending': { class: 'bg-yellow-600', text: 'Pending' },
                                'approved': { class: 'bg-green-600', text: 'Approved' },
                                'ordered': { class: 'bg-blue-600', text: 'Ordered' },
                                'received': { class: 'bg-green-700', text: 'Received' },
                                'cancelled': { class: 'bg-red-600', text: 'Cancelled' }
                            };
                            const config = statusConfig[data] || statusConfig['draft'];
                            return `<span class="px-2 py-1 text-xs font-medium rounded-full ${config.class}">${config.text}</span>`;
                        }
                    },
                    {
                        data: 'total_amount',
                        render: function(data) {
                            return formatCurrency(data);
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            // Calculate paid amount (this would come from payment records)
                            const paidAmount = 0; // You'd get this from the API
                            return formatCurrency(paidAmount);
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            const paidAmount = 0; // You'd get this from the API
                            const balance = parseFloat(data.total_amount) - paidAmount;
                            return `<span class="${balance > 0 ? 'text-red-400' : 'text-green-400'}">${formatCurrency(balance)}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data) {
                            return `
                                <div class="flex justify-end space-x-1">
                                    <button onclick="viewPO(${data.po_id})" 
                                            class="text-blue-400 hover:text-blue-300 p-1" 
                                            title="View PO">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    ${data.status !== 'received' && data.status !== 'cancelled' ? `
                                        <button onclick="editPO(${data.po_id})" 
                                                class="text-yellow-400 hover:text-yellow-300 p-1" 
                                                title="Edit PO">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    ` : ''}
                                    ${data.can_create_grn ? `
                                        <button onclick="createGRN(${data.po_id})" 
                                                class="text-green-400 hover:text-green-300 p-1" 
                                                title="Create GRN">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    ` : ''}
                                    <button onclick="viewPayments(${data.po_id})" 
                                            class="text-purple-400 hover:text-purple-300 p-1" 
                                            title="View Payments">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button onclick="exportPDF(${data.po_id})" 
                                            class="text-red-400 hover:text-red-300 p-1" 
                                            title="Export PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    ${data.status === 'draft' ? `
                                        <button onclick="deletePO(${data.po_id})" 
                                                class="text-red-500 hover:text-red-400 p-1" 
                                                title="Delete PO">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            `;
                        }
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 10,
                responsive: true,
                dom: '<"flex justify-between items-center mb-4"<"flex items-center"l><"flex items-center"f>>rtip',
                language: {
                    search: "Search Purchase Orders:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ purchase orders",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });
        }

        // Load dashboard statistics
        function loadDashboardData() {
            fetch('../api/purchase.php?action=dashboard_stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#totalPOCount').text(data.data.total_pos || 0);
                        $('#pendingGRNCount').text(data.data.pending_grns || 0);
                        $('#totalAmount').text(formatCurrency(data.data.monthly_amount || 0));
                        $('#outstandingAmount').text(formatCurrency(data.data.outstanding_payments || 0));
                    }
                })
                .catch(error => {
                    console.error('Error loading dashboard data:', error);
                    showError('Failed to load dashboard statistics');
                });
        }

        // Initialize charts with real data
        function initializeCharts() {
            // Purchase Trends Chart
            fetch('../api/purchase.php?action=purchase_trends')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        createTrendsChart(data.data);
                    }
                })
                .catch(error => console.error('Error loading purchase trends:', error));

            // Top Suppliers Chart
            fetch('../api/purchase.php?action=top_suppliers')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        createSuppliersChart(data.data);
                    }
                })
                .catch(error => console.error('Error loading top suppliers:', error));
        }

        // Create trends chart
        function createTrendsChart(data) {
            const ctx = document.getElementById('purchaseTrendsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => moment(item.month).format('MMM YYYY')),
                    datasets: [{
                        label: 'Purchase Amount',
                        data: data.map(item => item.total_amount),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#E5E7EB' }
                        },
                        tooltip: {
                            backgroundColor: '#374151',
                            titleColor: '#F3F4F6',
                            bodyColor: '#D1D5DB',
                            callbacks: {
                                label: function(context) {
                                    return `Amount: ${formatCurrency(context.parsed.y)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(75, 85, 99, 0.2)' },
                            ticks: { 
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        },
                        x: {
                            grid: { color: 'rgba(75, 85, 99, 0.2)' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });
        }

        // Create suppliers chart
        function createSuppliersChart(data) {
            const ctx = document.getElementById('topSuppliersChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.supplier_name),
                    datasets: [{
                        label: 'Purchase Amount',
                        data: data.map(item => item.total_amount),
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(249, 115, 22, 0.8)',
                            'rgba(217, 70, 239, 0.8)',
                            'rgba(245, 158, 11, 0.8)'
                        ],
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#374151',
                            titleColor: '#F3F4F6',
                            bodyColor: '#D1D5DB',
                            callbacks: {
                                label: function(context) {
                                    return `Amount: ${formatCurrency(context.parsed.y)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(75, 85, 99, 0.2)' },
                            ticks: { 
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return formatCurrency(value);
                                }
                            }
                        },
                        x: {
                            grid: { color: 'rgba(75, 85, 99, 0.2)' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });
        }

        // Apply filters to tables
        function applyFilters() {
            const filters = {
                status: $('#status').val(),
                supplier_id: $('#supplier').val(),
                date_range: $('#dateRange').val()
            };

            // Update purchase orders table
            const poUrl = new URL('../api/purchase.php', window.location.origin);
            poUrl.searchParams.set('action', 'purchase_orders');
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    poUrl.searchParams.set(key, filters[key]);
                }
            });
            
            purchaseOrdersTable.ajax.url(poUrl.toString()).load();

            // Update GRN table
            const grnUrl = new URL('../api/grn.php', window.location.origin);
            grnUrl.searchParams.set('action', 'list');
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    grnUrl.searchParams.set(key, filters[key]);
                }
            });
            
            grnTable.ajax.url(grnUrl.toString()).load();

            // Reload dashboard stats
            loadDashboardData();
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
            // Implementation for viewing payments
            showPaymentModal(poId);
        }

        function exportPDF(poId) {
            window.location.href = `export_po.php?id=${poId}`;
        }

        function deletePO(poId) {
            confirmAction('Delete Purchase Order', 'Are you sure you want to delete this purchase order?', () => {
                fetch(`../api/purchase.php?action=delete_purchase_order&po_id=${poId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('Purchase order deleted successfully');
                        purchaseOrdersTable.ajax.reload();
                        loadDashboardData();
                    } else {
                        showError(data.error || 'Failed to delete purchase order');
                    }
                })
                .catch(error => {
                    showError('Error deleting purchase order');
                    console.error(error);
                });
            });
        }

        // GRN Action Functions
        function viewGRN(grnId) {
            window.location.href = `view_grn.php?id=${grnId}`;
        }

        function editGRN(grnId) {
            window.location.href = `edit_grn.php?id=${grnId}`;
        }

        function printGRN(grnId) {
            window.open(`print_grn.php?id=${grnId}`, '_blank');
        }

        function deleteGRN(grnId) {
            confirmAction('Delete GRN', 'Are you sure you want to delete this GRN?', () => {
                fetch(`../api/grn.php?action=delete&grn_id=${grnId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('GRN deleted successfully');
                        grnTable.ajax.reload();
                        loadDashboardData();
                    } else {
                        showError(data.error || 'Failed to delete GRN');
                    }
                })
                .catch(error => {
                    showError('Error deleting GRN');
                    console.error(error);
                });
            });
        }

        function completeGRN(grnId) {
            confirmAction('Complete GRN', 'Complete this GRN and update stock levels?', () => {
                fetch(`../api/grn.php?action=complete`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ grn_id: grnId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('GRN completed successfully');
                        grnTable.ajax.reload();
                        loadDashboardData();
                    } else {
                        showError(data.error || 'Failed to complete GRN');
                    }
                })
                .catch(error => {
                    showError('Error completing GRN');
                    console.error(error);
                });
            });
        }

        // Utility Functions
        function formatCurrency(amount) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'LKR',
                minimumFractionDigits: 2
            }).format(amount || 0);
        }

        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                timer: 3000,
                showConfirmButton: false
            });
        }

        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: message,
                confirmButtonText: 'OK'
            });
        }

        function confirmAction(title, message, callback) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!'
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        function showPaymentModal(poId) {
            // Implementation for payment modal
            Swal.fire({
                title: 'Payment History',
                text: 'Payment history functionality will be implemented here',
                icon: 'info'
            });
        }

        function showCustomDateModal() {
            // Implementation for custom date range
            Swal.fire({
                title: 'Custom Date Range',
                text: 'Custom date range functionality will be implemented here',
                icon: 'info'
            });
        }
    </script>
</body>

</html>