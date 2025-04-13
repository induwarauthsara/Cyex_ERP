<!-- File: /AdminPanel/purchase/index.php -->
<?php
require_once '../../inc/config.php';

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('Location: /login/');
    exit();
}

// Only admin users can access this page
if ($_SESSION['employee_role'] !== 'Admin') {
    header('Location: /');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders - <?php echo $ERP_COMPANY_NAME; ?></title>

    <?php include '../includes/cdn_includes.php'; ?>
</head>

<body class="dark:bg-gray-900">
    <?php include '../../inc/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white mb-4 md:mb-0">
                <i class="fas fa-shopping-cart mr-2"></i> Purchase Orders
            </h1>
            <div class="flex flex-wrap gap-2">
                <button id="btnCreatePO" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i> Create Purchase Order
                </button>
                <div class="relative">
                    <button id="btnExport" class="btn bg-secondary hover:bg-secondary-dark text-white">
                        <i class="fas fa-download mr-2"></i> Export
                    </button>
                    <div id="exportMenu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <button class="exportBtn block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" data-type="copy">
                                <i class="far fa-copy mr-2"></i> Copy to Clipboard
                            </button>
                            <button class="exportBtn block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" data-type="excel">
                                <i class="far fa-file-excel mr-2"></i> Export to Excel
                            </button>
                            <button class="exportBtn block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" data-type="pdf">
                                <i class="far fa-file-pdf mr-2"></i> Export to PDF
                            </button>
                            <button class="exportBtn block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left" data-type="print">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="card mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label" for="filterStatus">Status</label>
                    <select id="filterStatus" class="form-input">
                        <option value="">All Statuses</option>
                        <option value="draft">Draft</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="ordered">Ordered</option>
                        <option value="received">Received</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="filterSupplier">Supplier</label>
                    <select id="filterSupplier" class="form-input">
                        <option value="">All Suppliers</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="filterDateFrom">Date From</label>
                    <input type="date" id="filterDateFrom" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="filterDateTo">Date To</label>
                    <input type="date" id="filterDateTo" class="form-input">
                </div>
            </div>
        </div>

        <!-- Main Table -->
        <div class="card">
            <div class="table-responsive">
                <table id="purchaseOrdersTable" class="w-full">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script type="module" src="./js/purchase_orders.js"></script>
</body>

</html>