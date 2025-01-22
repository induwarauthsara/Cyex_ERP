<!-- File: /AdminPanel/barcode-print/batch_print.php -->
<?php
require_once '../../inc/config.php';
require_once 'includes/utils.php';

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

// Get paper sizes for dropdown
$paperSizes = getPaperSizes();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Barcode Printing - <?php echo $ERP_COMPANY_NAME; ?></title>

    <?php include '../includes/cdn_includes.php'; ?>
    <link rel="stylesheet" href="css/barcode_print.css">
</head>

<body class="dark:bg-gray-900">
    <?php include '../../inc/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white">
                <i class="fas fa-print mr-2"></i> Batch Barcode Printing
            </h1>
            <div class="flex gap-2">
                <button id="estimateBtn" class="btn bg-yellow-500 hover:bg-yellow-600 text-white">
                    <i class="fas fa-calculator mr-2"></i> Estimate
                </button>
                <button id="previewBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                    <i class="fas fa-eye mr-2"></i> Preview
                </button>
                <button id="printBtn" class="btn bg-green-500 hover:bg-green-600 text-white">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Batch Selection -->
            <div class="md:col-span-2 space-y-6">
                <!-- Source Selection -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Select Source</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button id="fromGRNBtn" class="btn bg-purple-500 hover:bg-purple-600 text-white">
                            <i class="fas fa-truck-loading mr-2"></i> From GRN
                        </button>
                        <button id="fromPOBtn" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                            <i class="fas fa-shopping-cart mr-2"></i> From PO
                        </button>
                        <button id="manualBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                            <i class="fas fa-plus mr-2"></i> Manual Add
                        </button>
                    </div>
                </div>

                <!-- Batch Groups -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Batch Groups</h2>
                    <div id="batchGroups" class="space-y-4">
                        <!-- Batch groups will be added here dynamically -->
                    </div>
                </div>

                <!-- Product Search -->
                <div class="card hidden" id="productSearchCard">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Add Products</h2>
                    <div class="flex gap-4">
                        <div class="flex-grow">
                            <select id="productSearch" class="form-input w-full" placeholder="Search products..."></select>
                        </div>
                        <button id="addProductBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                            <i class="fas fa-plus mr-2"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <!-- Print Settings -->
            <div class="space-y-6">
                <!-- Paper Settings -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Paper Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label required" for="paperSize">Paper Type</label>
                            <select id="paperSize" class="form-input" required>
                                <optgroup label="Direct Thermal">
                                    <option value="36">36mm (1.4 Inch)</option>
                                    <option value="24">24mm (0.94 Inch)</option>
                                    <option value="18">18mm (0.7 Inch)</option>
                                </optgroup>
                                <optgroup label="Multi-Label">
                                    <option value="30x15-3up">30x15mm 3-up</option>
                                    <option value="50x25-2up">50x25mm 2-up</option>
                                </optgroup>
                                <optgroup label="A4 Sheets">
                                    <option value="a4-65">A4 - 65 Labels (38.1x21.2mm)</option>
                                    <option value="a4-40">A4 - 40 Labels (52.5x29.7mm)</option>
                                </optgroup>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="orientation">Orientation</label>
                            <select id="orientation" class="form-input">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="startPosition">Start Position</label>
                            <input type="number" id="startPosition" class="form-input"
                                value="1" min="1" step="1">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                For A4 sheets: start printing from this label position
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Print Options -->
                <div class="card">
                    <h2 class="text-lg font-semibold mb-4 dark:text-white">Print Options</h2>
                    <div class="space-y-4">
                        <!-- Content toggles -->
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="showShopName" class="form-checkbox" checked disabled>
                                <label for="showShopName">Shop Name</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="showProductName" class="form-checkbox" checked disabled>
                                <label for="showProductName">Product Name</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="showPrice" class="form-checkbox" checked>
                                <label for="showPrice">Price</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="showUnit" class="form-checkbox">
                                <label for="showUnit">Unit</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="showCategory" class="form-checkbox">
                                <label for="showCategory">Category</label>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="showBatch" class="form-checkbox" checked>
                                <label for="showBatch">Batch Number</label>
                            </div>
                        </div>

                        <!-- Advanced options -->
                        <div class="border-t dark:border-gray-700 pt-4">
                            <h3 class="text-md font-semibold mb-2 dark:text-white">Advanced Options</h3>
                            <div class="space-y-2">
                                <div>
                                    <label class="form-label" for="sorting">Sort By</label>
                                    <select id="sorting" class="form-input">
                                        <option value="batch">Batch Number</option>
                                        <option value="product">Product Name</option>
                                        <option value="quantity">Quantity (Descending)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label" for="printMode">Print Mode</label>
                                    <select id="printMode" class="form-input">
                                        <option value="batch">By Batch</option>
                                        <option value="mixed">Mixed (Optimize Paper)</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="skipPartialSheet" class="form-checkbox">
                                    <label for="skipPartialSheet">Skip Partial Sheets</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Source Selection Modals -->
    <!-- GRN Selection Modal -->
    <div id="grnModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-4xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Select GRN</h3>
                    <button onclick="closeModal('grnModal')" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <table id="grnTable" class="w-full">
                        <thead>
                            <tr>
                                <th>GRN Number</th>
                                <th>Receipt Date</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PO Selection Modal -->
    <div id="poModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-4xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Select Purchase Order</h3>
                    <button onclick="closeModal('poModal')" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <table id="poTable" class="w-full">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Order Date</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-5xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Print Preview</h3>
                    <button onclick="closeModal('previewModal')" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4 max-h-[70vh] overflow-auto">
                    <div id="previewContent"></div>
                </div>
                <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
                    <button onclick="closeModal('previewModal')" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                        Cancel
                    </button>
                    <button onclick="printBarcodes()" class="btn bg-green-500 hover:bg-green-600 text-white">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Modal -->
    <div id="statsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-2xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Print Statistics</h3>
                    <button onclick="closeModal('statsModal')" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div id="statsContent" class="space-y-4">
                        <!-- Statistics will be added here dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="./js/batch_print.js"></script>
</body>

</html>