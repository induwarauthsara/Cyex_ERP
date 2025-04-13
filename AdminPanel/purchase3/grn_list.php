<!-- File: /AdminPanel/purchase/grn_list.php -->
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
    <title>Goods Receipt Notes - <?php echo $ERP_COMPANY_NAME; ?></title>

    <?php include '../includes/cdn_includes.php'; ?>
</head>

<body class="dark:bg-gray-900">
    <?php include '../../inc/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white mb-4 md:mb-0">
                <i class="fas fa-truck-loading mr-2"></i> Goods Receipt Notes
            </h1>
            <div class="flex flex-wrap gap-2">
                <button id="btnBarcodePrint" class="btn bg-purple-600 hover:bg-purple-700 text-white">
                    <i class="fas fa-barcode mr-2"></i> Print Barcodes
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
                        <option value="completed">Completed</option>
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
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table id="grnTable" class="w-full">
                    <thead>
                        <tr>
                            <th class="select-cell w-8">
                                <input type="checkbox" id="selectAll" class="form-checkbox">
                            </th>
                            <th>GRN Number</th>
                            <th>PO Number</th>
                            <th>Receipt Date</th>
                            <th>Supplier</th>
                            <th>Invoice Number</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Barcode Print Modal -->
    <div id="barcodePrintModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-4xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Print Barcodes</h3>
                    <button onclick="closeBarcodeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <label class="form-label">Paper Size</label>
                        <select id="paperSize" class="form-input">
                            <option value="36">36mm (1.4 Inch)</option>
                            <option value="24">24mm (0.94 Inch)</option>
                            <option value="18">18mm (0.7 Inch)</option>
                            <option value="30x15">30x15mm 3 up</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="form-label">Print Options</label>
                        <div class="flex flex-wrap gap-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox" checked disabled>
                                <span class="ml-2">Shop Name</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox" checked disabled>
                                <span class="ml-2">Product Name</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox" checked>
                                <span class="ml-2">Price</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2">Unit</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2">Category</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox">
                                <span class="ml-2">Promotional Price</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="overflow-x-auto">
                            <table id="barcodeItemsTable" class="w-full">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Batch</th>
                                        <th>Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
                    <button onclick="closeBarcodeModal()" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                        Cancel
                    </button>
                    <button onclick="printBarcodes()" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="./js/grn_list.js"></script>
</body>

</html>