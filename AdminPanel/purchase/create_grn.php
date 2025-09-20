<?php
require_once('../nav.php');
// Check if PO ID is provided
$po_id = isset($_GET['po_id']) ? (int)$_GET['po_id'] : null;
$po_data = null;

if ($po_id) {
    // Get PO details if PO ID is provided
    $po_sql = "
        SELECT 
            po.*,
            s.supplier_name,
            s.supplier_tel,
            s.supplier_address
        FROM purchase_orders po
        LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
        WHERE po.po_id = ? AND po.status = 'ordered'
    ";

    $stmt = mysqli_prepare($con, $po_sql);
    mysqli_stmt_bind_param($stmt, 'i', $po_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $po_data = mysqli_fetch_assoc($result);

    if (!$po_data) {
        die("Invalid or already received purchase order");
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create GRN</title>

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

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <style>
        /* Select2 Dark Theme styles */
        .select2-container--default .select2-selection--single {
            background-color: #374151 !important;
            border: 1px solid #4b5563 !important;
            color: #ffffff !important;
            border-radius: 0.5rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff !important;
            line-height: 28px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #d1d5db !important;
        }

        .select2-dropdown {
            background-color: #374151 !important;
            border: 1px solid #4b5563 !important;
        }

        .select2-search--dropdown .select2-search__field {
            background-color: #4b5563 !important;
            color: #ffffff !important;
            border: 1px solid #6b7280 !important;
        }

        .select2-results__option {
            color: #ffffff !important;
            background-color: #374151 !important;
        }

        .select2-results__option--highlighted {
            background-color: #4b5563 !important;
            color: #ffffff !important;
        }

        .select2-results__option--selected {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        .select2-container--default .select2-selection__arrow {
            height: 26px !important;
        }

        .select2-container--default .select2-selection__arrow b {
            border-color: #ffffff transparent transparent transparent !important;
        }

        /* Custom styles for tooltips */
        .tooltip {
            @apply invisible absolute;
        }

        .has-tooltip:hover .tooltip {
            @apply visible z-50;
        }
        
        /* Fix for product name column width */
        .product-name {
            word-break: break-word;
            min-width: 200px;
            max-width: 300px;
            display: block;
        }
        
        /* Table column widths */
        #itemsTable th:first-child,
        #itemsTable td:first-child {
            width: 25%;
            min-width: 250px;
        }
        
        #itemsTable th:nth-child(2),
        #itemsTable td:nth-child(2),
        #itemsTable th:nth-child(3),
        #itemsTable td:nth-child(3) {
            width: 10%;
        }
        
        #itemsTable th:nth-child(4),
        #itemsTable td:nth-child(4) {
            width: 15%;
        }
        
        #itemsTable th:nth-child(5),
        #itemsTable td:nth-child(5),
        #itemsTable th:nth-child(6),
        #itemsTable td:nth-child(6) {
            width: 12%;
        }
        
        #itemsTable th:nth-child(7),
        #itemsTable td:nth-child(7) {
            width: 15%;
        }
        
        #itemsTable th:last-child,
        #itemsTable td:last-child {
            width: 5%;
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
                            <h1 class="text-xl font-bold">Create GRN</h1>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                    <i class="fas fa-arrow-left mr-2"></i>Back to PO / GRN List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- GRN Form -->
            <form id="grnForm" class="bg-gray-800 shadow-md rounded-lg p-6">
                <!-- GRN Type Selection -->
                <?php if (!$po_id): ?>
                    <div class="mb-6">
                        <div class="flex items-center space-x-4">
                            <button type="button" onclick="setGRNType('po')"
                                class="px-4 py-2 rounded-lg font-medium grn-type-btn"
                                data-type="po">
                                From Purchase Order
                            </button>
                            <button type="button" onclick="setGRNType('direct')"
                                class="px-4 py-2 rounded-lg font-medium grn-type-btn"
                                data-type="direct">
                                Direct GRN
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Header Section -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-6">
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Receipt Date</label>
                        <input type="date" id="receiptDate" name="receiptDate"
                            class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"
                            value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <!-- Supplier Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Supplier</label>
                        <?php if ($po_data): ?>
                            <input type="hidden" id="supplier" name="supplier" value="<?= $po_data['supplier_id'] ?>">
                            <p class="mt-1 text-white"><?= htmlspecialchars($po_data['supplier_name']) ?></p>
                        <?php else: ?>
                            <div class="flex items-center space-x-2">
                                <select id="supplier" name="supplier"
                                    class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"
                                    required>
                                    <option value="">Select Supplier</option>
                                </select>
                                <button type="button" onclick="addNewSupplier()" 
                                    class="mt-1 bg-green-600 hover:bg-green-700 text-white px-2 py-2 rounded-lg" 
                                    title="Add New Supplier">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- PO Selection (for PO-based GRN) -->
                    <div id="poSelection" style="display: none;">
                        <label class="block text-sm font-medium text-gray-400">Purchase Order</label>
                        <select id="purchaseOrder" name="purchaseOrder"
                            class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            <option value="">Select Purchase Order</option>
                        </select>
                    </div>

                    <!-- Invoice Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Invoice Number</label>
                        <input type="text" id="invoiceNumber" name="invoiceNumber"
                            class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-400">Invoice Date</label>
                        <input type="date" id="invoiceDate" name="invoiceDate"
                            class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Received Items</h2>
                        <?php if (!$po_data): ?>
                            <button type="button" onclick="openAddProductModal()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Add Item
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700" id="itemsTable">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase w-1/5">Product</th>
                                    <?php if ($po_data): ?>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Ordered Qty</th>
                                    <?php endif; ?>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Received Qty</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Batch Number</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Cost</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Selling Price</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Expiry Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-gray-700">
                                <?php if ($po_data): ?>
                                    <!-- PO items will be loaded here -->
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-400">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"></textarea>
                </div>

                <!-- Payment Section -->
                <div class="mb-6 bg-gray-700 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-white mb-4">Payment Information</h3>
                    
                    <!-- Total Amount Display -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-gray-600 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-400">Total GRN Amount</label>
                            <div id="totalAmount" class="text-xl font-bold text-white">Rs. 0.00</div>
                        </div>
                        <div class="bg-gray-600 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-400">Outstanding Amount</label>
                            <div id="outstandingAmount" class="text-xl font-bold text-yellow-400">Rs. 0.00</div>
                        </div>
                        <div class="bg-gray-600 rounded-lg p-3">
                            <label class="block text-sm font-medium text-gray-400">Payment Status</label>
                            <div id="paymentStatusDisplay" class="text-lg font-medium text-red-400">Unpaid</div>
                        </div>
                    </div>

                    <!-- Payment Method Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Payment Status</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="paymentType" value="paid" class="text-blue-600 bg-gray-700 border-gray-600" onchange="handlePaymentTypeChange()">
                                    <span class="ml-2 text-green-400">Full Payment</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="paymentType" value="partial" class="text-blue-600 bg-gray-700 border-gray-600" onchange="handlePaymentTypeChange()">
                                    <span class="ml-2 text-yellow-400">Partial Payment</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="paymentType" value="unpaid" class="text-blue-600 bg-gray-700 border-gray-600" checked onchange="handlePaymentTypeChange()">
                                    <span class="ml-2 text-red-400">Credit/Unpaid</span>
                                </label>
                            </div>
                        </div>

                        <div id="paymentMethodSection" style="display: none;">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Payment Method</label>
                            <select id="paymentMethod" name="paymentMethod" class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>

                        <div id="paidAmountSection" style="display: none;">
                            <label class="block text-sm font-medium text-gray-400 mb-2">Paid Amount</label>
                            <input type="number" id="paidAmount" name="paidAmount" step="0.01" min="0" 
                                class="block w-full rounded-md bg-gray-700 border-gray-600 text-white" 
                                placeholder="Enter paid amount" onchange="calculateOutstanding()">
                        </div>
                    </div>

                    <!-- Payment Reference and Notes -->
                    <div id="paymentDetailsSection" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Payment Reference</label>
                            <input type="text" id="paymentReference" name="paymentReference" 
                                class="block w-full rounded-md bg-gray-700 border-gray-600 text-white" 
                                placeholder="Reference number, cheque no, etc.">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-2">Payment Notes</label>
                            <input type="text" id="paymentNotes" name="paymentNotes" 
                                class="block w-full rounded-md bg-gray-700 border-gray-600 text-white" 
                                placeholder="Additional payment notes">
                        </div>
                    </div>

                    <!-- Credit Terms (for unpaid/partial) -->
                    <div id="creditTermsSection" style="display: block;" class="bg-gray-600 rounded-lg p-3">
                        <div class="flex items-center justify-between">
                            <span class="text-yellow-400 font-medium">Credit Terms</span>
                            <span class="text-white">Amount will be added to supplier's outstanding balance</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" onclick="saveDraft()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button type="button" onclick="completeGRN()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Complete GRN
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- Item Row Template for Direct GRN -->
    <template id="directItemTemplate">
        <tr class="item-row">
            <td class="px-4 py-2 w-1/5">
                <input type="hidden" class="product-id">
                <p class="product-name w-full"></p>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="received-qty w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="1" value="1" required oninput="updatePaymentCalculations()">
            </td>
            <td class="px-4 py-2">
                <input type="text" class="batch-number w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    required>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="cost w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" required oninput="updatePaymentCalculations()">
            </td>
            <td class="px-4 py-2">
                <input type="number" class="selling-price w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" required>
            </td>
            <td class="px-4 py-2">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" class="has-expiry rounded bg-gray-700 border-gray-600 text-blue-600" id="has-expiry">
                    <input type="date" class="expiry-date w-full rounded-md bg-gray-700 border-gray-600 text-white" disabled>
                </div>
            </td>
            <td class="px-4 py-2">
                <button type="button" onclick="removeItem(this)"
                    class="text-red-500 hover:text-red-400">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Item Row Template for PO-based GRN -->
    <template id="poItemTemplate">
        <tr class="item-row" data-po-item-id="">
            <td class="px-4 py-2">
                <input type="hidden" class="product-id">
                <span class="product-name"></span>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="ordered-qty w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0" value="0" readonly>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="received-qty w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0" value="0" required oninput="updatePaymentCalculations()">
            </td>
            <td class="px-4 py-2">
                <input type="text" class="batch-number w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    required>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="cost w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" readonly>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="selling-price w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" readonly>
            </td>
            <td class="px-4 py-2">
                <input type="date" class="expiry-date w-full rounded-md bg-gray-700 border-gray-600 text-white" readonly>
            </td>
            <td class="px-4 py-2">
                <button type="button" onclick="removeItem(this)"
                    class="text-red-500 hover:text-red-400">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Quick Add Supplier Modal -->
    <div id="supplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Add New Supplier</h3>
            <form id="quickSupplierForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Supplier Name</label>
                    <input type="text" id="supplierName" name="supplierName"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Phone Number</label>
                    <input type="text" id="supplierTel" name="supplierTel"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Address</label>
                    <textarea id="supplierAddress" name="supplierAddress" rows="2"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Note</label>
                    <textarea id="supplierNote" name="supplierNote" rows="2"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeSupplierModal()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                        Cancel
                    </button>
                    <button type="button" onclick="saveSupplier()"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg">
                        Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-3xl">
            <h3 class="text-xl font-bold mb-4">Add Product to GRN</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-2">Select Product</label>
                <select id="productSelect" class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                    <option value="">Search and select product</option>
                </select>
            </div>
            
            <!-- Product Type Filter Checkbox -->
            <div class="mb-4">
                <label class="flex items-center text-sm text-gray-300">
                    <input type="checkbox" checked id="standardProductsOnly" class="mr-2 rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                    Only Show Standard Product List (hide Combo / Service / Digital Products)
                </label>
            </div>
            
            <!-- Batch Selection Section -->
            <div id="batchSelectionSection" class="mb-4 hidden">
                <h4 class="font-medium text-lg mb-3 text-white">Batch Management</h4>
                
                <!-- Batch Selection Options -->
                <div class="mb-4">
                    <label class="flex items-center mb-2">
                        <input type="radio" name="batchOption" id="selectExistingBatch" value="existing" 
                               class="mr-2 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                        <span class="text-gray-300">Select Existing Batch</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="batchOption" id="createNewBatch" value="new" 
                               class="mr-2 text-blue-600 bg-gray-700 border-gray-600 focus:ring-blue-500">
                        <span class="text-gray-300">Create New Batch</span>
                    </label>
                </div>
                
                <!-- Existing Batches List -->
                <div id="existingBatchesSection" class="hidden mb-4">
                    <label class="block text-sm font-medium text-gray-400 mb-2">Select Batch</label>
                    <select id="batchSelect" class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                        <option value="">Choose a batch...</option>
                    </select>
                    
                    <!-- Selected Batch Details -->
                    <div id="selectedBatchDetails" class="hidden mt-3 bg-gray-600 p-3 rounded-md">
                        <h5 class="font-medium text-white mb-2">Batch Details</h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                            <div>
                                <span class="text-gray-400">Available Qty:</span>
                                <span id="batchAvailableQty" class="text-white font-medium ml-1">-</span>
                            </div>
                            <div>
                                <span class="text-gray-400">Cost:</span>
                                <span id="batchCost" class="text-white font-medium ml-1">-</span>
                            </div>
                            <div>
                                <span class="text-gray-400">Selling Price:</span>
                                <span id="batchSellingPrice" class="text-white font-medium ml-1">-</span>
                            </div>
                            <div>
                                <span class="text-gray-400">Status:</span>
                                <span id="batchStatus" class="text-white font-medium ml-1">-</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm mt-2">
                            <div>
                                <span class="text-gray-400">Alert Qty:</span>
                                <span id="batchAlertQty" class="text-white font-medium ml-1">-</span>
                            </div>
                            <div>
                                <span class="text-gray-400">Expiry Date:</span>
                                <span id="batchExpiryDate" class="text-white font-medium ml-1">-</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- New Batch Creation Section -->
                <div id="newBatchSection" class="hidden">
                    <div class="bg-gray-600 p-4 rounded-md">
                        <h5 class="font-medium text-white mb-3">Create New Batch</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Batch Number (Optional)</label>
                                <input type="text" id="newBatchNumber" placeholder="Auto-generated if empty"
                                       class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Alert Quantity</label>
                                <input type="number" id="newBatchAlertQty" value="5" min="0" step="0.001"
                                       class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Status</label>
                                <select id="newBatchStatus" class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                                    <option value="active" selected>Active (Available for Sale)</option>
                                    <option value="discontinued">Discontinued</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-1">Discount Price (Optional)</label>
                                <input type="number" id="newBatchDiscountPrice" min="0" step="0.01"
                                       class="block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400">Quantity <span class="text-red-400">*</span></label>
                    <input type="number" id="modalQuantity" min="0.001" step="0.001" value="1"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Cost <span class="text-red-400">*</span></label>
                    <input type="number" id="modalCost" min="0.01" step="0.01"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Selling Price <span class="text-red-400">*</span></label>
                    <input type="number" id="modalSellingPrice" min="0.01" step="0.01"
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Profit (Auto-calculated)</label>
                    <input type="number" id="modalProfit" readonly
                        class="mt-1 block w-full rounded-md bg-gray-600 border-gray-600 text-gray-300 cursor-not-allowed">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-400">Batch Number (For existing batch only)</label>
                    <input type="text" id="modalBatchNumber" readonly
                        class="mt-1 block w-full rounded-md bg-gray-600 border-gray-600 text-gray-300 cursor-not-allowed">
                    <small class="text-gray-500">This field is auto-filled when selecting existing batch or creating new batch</small>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400" for="modalHasExpiry">
                        <input type="checkbox" id="modalHasExpiry" class="mr-2 rounded bg-gray-700 border-gray-600 text-blue-600">
                        Has Expiry Date
                    </label>
                    <input type="date" id="modalExpiryDate" disabled
                        class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddProductModal()"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                    Cancel
                </button>
                <button type="button" onclick="addSelectedProduct()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg">
                    Add Product
                </button>
            </div>
        </div>
    </div>

    <script>
    // Global variables to track GRN type
    let currentGRNType = 'direct'; // default
    
    // GRN Type selection function
    function setGRNType(type) {
        currentGRNType = type;
        
        // Update button styles
        document.querySelectorAll('.grn-type-btn').forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white');
            btn.classList.add('bg-gray-600', 'text-gray-300');
        });
        
        const activeBtn = document.querySelector(`[data-type="${type}"]`);
        activeBtn.classList.remove('bg-gray-600', 'text-gray-300');
        activeBtn.classList.add('bg-blue-600', 'text-white');
        
        // Show/hide relevant sections
        if (type === 'po') {
            document.getElementById('poSelection').style.display = 'block';
            // Load purchase orders for the supplier
            loadPurchaseOrders();
        } else {
            document.getElementById('poSelection').style.display = 'none';
            document.getElementById('purchaseOrder').value = '';
            // Clear any PO-related items
            document.getElementById('itemsBody').innerHTML = '';
        }
        
        // Show/hide Add Item button
        const addItemBtn = document.querySelector('button[onclick="openAddProductModal()"]');
        if (addItemBtn) {
            addItemBtn.style.display = type === 'direct' ? 'block' : 'none';
        }
    }
    
    // Load purchase orders for selected supplier
    function loadPurchaseOrders() {
        const supplierId = document.getElementById('supplier').value;
        if (!supplierId) {
            document.getElementById('purchaseOrder').innerHTML = '<option value="">Select Purchase Order</option>';
            return;
        }
        
        $.ajax({
            url: '../api/purchase.php',
            type: 'GET',
            data: {
                action: 'get_pending_orders',
                supplier_id: supplierId
            },
            dataType: 'json',
            success: function(response) {
                const select = document.getElementById('purchaseOrder');
                select.innerHTML = '<option value="">Select Purchase Order</option>';
                
                if (response.success && response.data.length > 0) {
                    response.data.forEach(function(po) {
                        const option = document.createElement('option');
                        option.value = po.po_id;
                        option.textContent = `PO-${po.po_number} - ${po.total_amount} (${po.order_date})`;
                        select.appendChild(option);
                    });
                }
            },
            error: function() {
                console.error('Failed to load purchase orders');
            }
        });
    }
    
    // Handle purchase order selection
    function handlePOSelection() {
        const poId = document.getElementById('purchaseOrder').value;
        if (poId && currentGRNType === 'po') {
            loadPOItems(poId);
        } else {
            document.getElementById('itemsBody').innerHTML = '';
        }
    }
    
    // Quick Add Supplier functionality
    function addNewSupplier() {
        document.getElementById('supplierModal').classList.remove('hidden');
    }

    function closeSupplierModal() {
        document.getElementById('supplierModal').classList.add('hidden');
        document.getElementById('quickSupplierForm').reset();
    }

    function saveSupplier() {
        const supplierName = document.getElementById('supplierName').value;
        const supplierTel = document.getElementById('supplierTel').value;
        const supplierAddress = document.getElementById('supplierAddress').value;
        const note = document.getElementById('supplierNote').value;
        
        if (!supplierName) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Supplier name is required'
            });
            return;
        }
        
        // Send AJAX request to create supplier using suppliers.php
        $.ajax({
            url: '../../suppliers/suppliers.php',
            type: 'POST',
            data: {
                action: 'add_supplier',
                supplier_name: supplierName,
                supplier_tel: supplierTel,
                supplier_address: supplierAddress,
                note: note
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Add the new supplier to the dropdown
                    const supplierSelect = $('#supplier');
                    const newOption = new Option(supplierName, response.supplier_id, true, true);
                    supplierSelect.append(newOption).trigger('change');
                    
                    // Close modal
                    closeSupplierModal();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                } else {
                    throw new Error(response.message);
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add supplier: ' + error
                });
            }
        });
    }
    
    // Product Modal Functionality
    function openAddProductModal() {
        document.getElementById('addProductModal').classList.remove('hidden');
    }
    
    function closeAddProductModal() {
        document.getElementById('addProductModal').classList.add('hidden');
        $('#productSelect').val('').trigger('change');
        $('#batchSelect').val('').trigger('change');
        
        // Reset all form fields
        document.getElementById('modalQuantity').value = '1';
        document.getElementById('modalCost').value = '';
        document.getElementById('modalSellingPrice').value = '';
        document.getElementById('modalProfit').value = '';
        document.getElementById('modalBatchNumber').value = '';
        document.getElementById('modalHasExpiry').checked = false;
        document.getElementById('modalExpiryDate').disabled = true;
        document.getElementById('modalExpiryDate').value = '';
        
        // Reset new batch fields
        document.getElementById('newBatchNumber').value = '';
        document.getElementById('newBatchAlertQty').value = '5';
        document.getElementById('newBatchStatus').value = 'active';
        document.getElementById('newBatchDiscountPrice').value = '';
        
        // Hide all sections
        hideAllBatchSections();
        
        // Uncheck radio buttons
        $('input[name="batchOption"]').prop('checked', false);
    }
    
    // Toggle expiry date field based on checkbox
    document.getElementById('modalHasExpiry').addEventListener('change', function() {
        document.getElementById('modalExpiryDate').disabled = !this.checked;
    });
    
    // Batch Management Functions
    let currentBatches = [];
    
    function loadProductBatches(productId) {
        $.ajax({
            url: '../../products/API/fetch_Single_product_batches.php',
            type: 'GET',
            data: { product_id: productId },
            dataType: 'json',
            success: function(batches) {
                currentBatches = batches;
                if (batches.length > 0) {
                    populateBatchSelection(batches);
                    $('#batchSelectionSection').removeClass('hidden');
                    
                    // Default to existing batch if available
                    $('#selectExistingBatch').prop('checked', true).trigger('change');
                } else {
                    // No batches found, force new batch creation
                    $('#createNewBatch').prop('checked', true).trigger('change');
                    $('#batchSelectionSection').removeClass('hidden');
                    
                    Swal.fire({
                        icon: 'info',
                        title: 'No Existing Batches',
                        text: 'No batches found for this product. You will create a new batch.',
                        timer: 3000
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to fetch product batches'
                });
                hideAllBatchSections();
            }
        });
    }
    
    function populateBatchSelection(batches) {
        const batchSelect = $('#batchSelect');
        batchSelect.empty().append('<option value="">Choose a batch...</option>');
        
        batches.forEach(batch => {
            batchSelect.append(`<option value="${batch.batch_id}">${batch.display_name}</option>`);
        });
    }
    
    function loadBatchDetails(batchId) {
        const batch = currentBatches.find(b => b.batch_id == batchId);
        if (batch) {
            // Display batch details
            $('#batchAvailableQty').text(batch.quantity);
            $('#batchCost').text(batch.cost);
            $('#batchSellingPrice').text(batch.selling_price);
            $('#batchStatus').text(batch.status.charAt(0).toUpperCase() + batch.status.slice(1));
            $('#batchAlertQty').text(batch.alert_quantity);
            $('#batchExpiryDate').text(batch.expiry_date || 'No expiry');
            
            // Auto-fill form fields
            $('#modalBatchNumber').val(batch.batch_name);
            $('#modalCost').val(parseFloat(batch.cost.replace(/,/g, '')));
            $('#modalSellingPrice').val(parseFloat(batch.selling_price.replace(/,/g, '')));
            
            // Handle expiry date
            if (batch.expiry_date) {
                $('#modalHasExpiry').prop('checked', true);
                $('#modalExpiryDate').prop('disabled', false).val(batch.expiry_date);
            } else {
                $('#modalHasExpiry').prop('checked', false);
                $('#modalExpiryDate').prop('disabled', true).val('');
            }
            
            calculateProfit();
            $('#selectedBatchDetails').removeClass('hidden');
        }
    }
    
    function calculateProfit() {
        const cost = parseFloat($('#modalCost').val()) || 0;
        const sellingPrice = parseFloat($('#modalSellingPrice').val()) || 0;
        const profit = sellingPrice - cost;
        $('#modalProfit').val(profit.toFixed(2));
    }
    
    function clearModalFields() {
        $('#modalCost').val('');
        $('#modalSellingPrice').val('');
        $('#modalProfit').val('');
        $('#modalBatchNumber').val('');
        $('#modalHasExpiry').prop('checked', false);
        $('#modalExpiryDate').prop('disabled', true).val('');
        $('#selectedBatchDetails').addClass('hidden');
    }
    
    function hideAllBatchSections() {
        $('#batchSelectionSection').addClass('hidden');
        $('#existingBatchesSection').addClass('hidden');
        $('#newBatchSection').addClass('hidden');
        $('#selectedBatchDetails').addClass('hidden');
        clearModalFields();
    }
    
    // Add selected product to the GRN items table
    function addSelectedProduct() {
        const productSelect = document.getElementById('productSelect');
        const productId = productSelect.value;
        const productName = productSelect.options[productSelect.selectedIndex].text;
        const quantity = parseFloat(document.getElementById('modalQuantity').value);
        const cost = parseFloat(document.getElementById('modalCost').value);
        const sellingPrice = parseFloat(document.getElementById('modalSellingPrice').value);
        const batchNumber = document.getElementById('modalBatchNumber').value;
        const hasExpiry = document.getElementById('modalHasExpiry').checked;
        const expiryDate = document.getElementById('modalExpiryDate').value;
        
        // Get batch option
        const batchOption = $('input[name="batchOption"]:checked').val();
        const selectedBatchId = $('#batchSelect').val();
        
        // Validation
        if (!productId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a product'
            });
            return;
        }
        
        // Validate batch selection
        if (!batchOption) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select whether to use existing batch or create new batch'
            });
            return;
        }
        
        if (batchOption === 'existing' && !selectedBatchId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select an existing batch'
            });
            return;
        }
        
        if (!quantity || quantity <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid quantity'
            });
            return;
        }
        
        if (!cost || cost <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid cost'
            });
            return;
        }
        
        if (!sellingPrice || sellingPrice <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid selling price'
            });
            return;
        }
        
        // For new batch, validate batch number if provided
        if (batchOption === 'new' && batchNumber && batchNumber.trim() === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Batch number cannot be empty if provided'
            });
            return;
        }
        
        if (hasExpiry && !expiryDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter expiry date'
            });
            return;
        }
        
        // Collect batch data for new batch creation
        let batchData = null;
        if (batchOption === 'new') {
            batchData = {
                isNew: true,
                batchNumber: batchNumber || '', // Will be auto-generated if empty
                alertQuantity: parseFloat($('#newBatchAlertQty').val()) || 5,
                status: $('#newBatchStatus').val() || 'active',
                discountPrice: parseFloat($('#newBatchDiscountPrice').val()) || null
            };
        } else {
            batchData = {
                isNew: false,
                batchId: selectedBatchId
            };
        }
        
        // Get the template
        const template = document.getElementById('directItemTemplate');
        const clone = document.importNode(template.content, true);
        
        // Fill in the values
        clone.querySelector('.product-id').value = productId;
        clone.querySelector('.product-name').textContent = productName;
        clone.querySelector('.received-qty').value = quantity;
        clone.querySelector('.batch-number').value = batchNumber || 'Auto-generated';
        clone.querySelector('.cost').value = cost;
        clone.querySelector('.selling-price').value = sellingPrice;
        
        // Store batch data in a hidden field
        const hiddenBatchData = document.createElement('input');
        hiddenBatchData.type = 'hidden';
        hiddenBatchData.className = 'batch-data';
        hiddenBatchData.value = JSON.stringify(batchData);
        clone.querySelector('tr').appendChild(hiddenBatchData);
        
        // Handle expiry date
        const expiryCheckbox = clone.querySelector('.has-expiry');
        const expiryInput = clone.querySelector('.expiry-date');
        expiryCheckbox.checked = hasExpiry;
        expiryInput.disabled = !hasExpiry;
        if (hasExpiry) {
            expiryInput.value = expiryDate;
        }
        
        // Add event listener for expiry checkbox
        expiryCheckbox.addEventListener('change', function() {
            const expiryField = this.closest('tr').querySelector('.expiry-date');
            expiryField.disabled = !this.checked;
            if (!this.checked) {
                expiryField.value = '';
            }
        });
        
        // Add to table
        document.getElementById('itemsBody').appendChild(clone);
        
        // Update payment calculations
        updatePaymentCalculations();
        
        // Close modal
        closeAddProductModal();
    }
    
    // Remove item row
    function removeItem(button) {
        const row = button.closest('tr');
        row.remove();
        
        // Update payment calculations
        updatePaymentCalculations();
    }
    
    // Initialize Select2 for supplier and product selection
    $(document).ready(function() {
        // Initialize supplier select with AJAX
        $('#supplier').select2({
            placeholder: 'Search for a supplier',
            allowClear: true,
            ajax: {
                url: '../../suppliers/suppliers.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        action: 'get_suppliers',
                        search: params.term
                    };
                },
                processResults: function(data) {
                    if (data.status === 'success') {
                        return {
                            results: data.data.map(function(supplier) {
                                return {
                                    id: supplier.supplier_id,
                                    text: supplier.supplier_name
                                };
                            })
                        };
                    }
                    return { results: [] };
                },
                cache: true
            },
            minimumInputLength: 1
        });
        
        // Initialize product select with AJAX
        $('#productSelect').select2({
            placeholder: 'Search for a product',
            allowClear: true,
            dropdownParent: $('#addProductModal'),
            ajax: {
                url: '../../products/API/getProducts.php',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    var data = {
                        search: params.term
                    };
                    
                    // Add product type filter if checkbox is checked
                    if ($('#standardProductsOnly').is(':checked')) {
                        data.product_type = 'standard';
                    }
                    
                    return data;
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(product) {
                            return {
                                id: product.id,
                                text: product.name
                            };
                        })
                    };
                },
                cache: false // Disable cache to reflect filter changes
            },
            minimumInputLength: 1
        });
        
        // Add event listener for checkbox to refresh product search
        $('#standardProductsOnly').on('change', function() {
            // Clear current selection and reset the Select2
            $('#productSelect').val(null).trigger('change');
            
            // Force refresh of Select2 options by clearing cache
            $('#productSelect').select2('destroy').select2({
                placeholder: 'Search for a product',
                allowClear: true,
                dropdownParent: $('#addProductModal'),
                ajax: {
                    url: '../../products/API/getProducts.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        var data = {
                            search: params.term
                        };
                        
                        // Add product type filter if checkbox is checked
                        if ($('#standardProductsOnly').is(':checked')) {
                            data.product_type = 'standard';
                        }
                        
                        return data;
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(product) {
                                return {
                                    id: product.id,
                                    text: product.name
                                };
                            })
                        };
                    },
                    cache: false
                },
                minimumInputLength: 1
            });
        });
        
        // Product selection change handler
        $('#productSelect').on('change', function() {
            const productId = $(this).val();
            if (productId) {
                loadProductBatches(productId);
            } else {
                hideAllBatchSections();
            }
        });
        
        // Batch option radio button handlers
        $('input[name="batchOption"]').on('change', function() {
            const selectedOption = $('input[name="batchOption"]:checked').val();
            
            if (selectedOption === 'existing') {
                $('#existingBatchesSection').removeClass('hidden');
                $('#newBatchSection').addClass('hidden');
                $('#modalBatchNumber').prop('readonly', true).addClass('bg-gray-600 cursor-not-allowed').removeClass('bg-gray-700');
            } else if (selectedOption === 'new') {
                $('#existingBatchesSection').addClass('hidden');
                $('#newBatchSection').removeClass('hidden');
                $('#modalBatchNumber').prop('readonly', false).removeClass('bg-gray-600 cursor-not-allowed').addClass('bg-gray-700');
                clearModalFields();
            }
        });
        
        // Batch selection handler
        $('#batchSelect').on('change', function() {
            const batchId = $(this).val();
            if (batchId) {
                loadBatchDetails(batchId);
            } else {
                $('#selectedBatchDetails').addClass('hidden');
                clearModalFields();
            }
        });
        
        // Cost and selling price change handlers for profit calculation
        $('#modalCost, #modalSellingPrice').on('input', function() {
            calculateProfit();
        });

        // Handle supplier selection change for PO mode
        $('#supplier').on('change', function() {
            if (currentGRNType === 'po') {
                loadPurchaseOrders();
            }
        });
        
        // Handle purchase order selection
        $('#purchaseOrder').on('change', function() {
            handlePOSelection();
        });
        
        // Set default GRN type to direct
        setGRNType('direct');

        <?php if ($po_id): ?>
        // Load PO items if we have a PO ID
        loadPOItems(<?= $po_id ?>);
        <?php endif; ?>
    });
    
    // Function to load PO items
    function loadPOItems(poId) {
        $.ajax({
            url: 'get_po_items.php',
            type: 'GET',
            data: {
                po_id: poId
            },
            dataType: 'json',
            success: function(response) {
                let items = [];
                if (response.success && response.items) {
                    items = response.items;
                } else if (Array.isArray(response)) {
                    items = response; // fallback for old format
                }
                
                if (items.length > 0) {
                    const itemsBody = document.getElementById('itemsBody');
                    itemsBody.innerHTML = ''; // Clear existing items
                    
                    items.forEach(function(item) {
                        const template = document.getElementById('poItemTemplate');
                        if (template) {
                            const clone = document.importNode(template.content, true);
                            
                            clone.querySelector('.product-id').value = item.product_id;
                            clone.querySelector('.product-name').textContent = item.product_name;
                            clone.querySelector('.ordered-qty').value = item.quantity;
                            clone.querySelector('.received-qty').value = item.remaining_qty || item.quantity;
                            clone.querySelector('.cost').value = item.unit_cost;
                            
                            // Generate a batch number (PO number + current date)
                            const today = new Date();
                            const batchNumber = 'PO-' + item.po_number + '-' + today.toISOString().split('T')[0];
                            clone.querySelector('.batch-number').value = batchNumber;
                            
                            // Add to table
                            itemsBody.appendChild(clone);
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Items',
                        text: 'No pending items found for this purchase order.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load PO items'
                });
            }
        });
    }

    // Payment Management Functions
    function handlePaymentTypeChange() {
        const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
        const paymentMethodSection = document.getElementById('paymentMethodSection');
        const paidAmountSection = document.getElementById('paidAmountSection');
        const paymentDetailsSection = document.getElementById('paymentDetailsSection');
        const creditTermsSection = document.getElementById('creditTermsSection');

        // Reset payment fields
        document.getElementById('paymentMethod').value = '';
        document.getElementById('paidAmount').value = '';
        document.getElementById('paymentReference').value = '';
        document.getElementById('paymentNotes').value = '';

        if (paymentType === 'paid') {
            // Full payment
            paymentMethodSection.style.display = 'block';
            paidAmountSection.style.display = 'block';
            paymentDetailsSection.style.display = 'block';
            creditTermsSection.style.display = 'none';
            
            // Set paid amount to total amount
            const totalAmount = calculateTotalAmount();
            document.getElementById('paidAmount').value = totalAmount.toFixed(2);
            document.getElementById('paidAmount').readOnly = true;
            
        } else if (paymentType === 'partial') {
            // Partial payment
            paymentMethodSection.style.display = 'block';
            paidAmountSection.style.display = 'block';
            paymentDetailsSection.style.display = 'block';
            creditTermsSection.style.display = 'block';
            
            document.getElementById('paidAmount').readOnly = false;
            document.getElementById('paidAmount').value = '';
            
        } else {
            // Unpaid/Credit
            paymentMethodSection.style.display = 'none';
            paidAmountSection.style.display = 'none';
            paymentDetailsSection.style.display = 'none';
            creditTermsSection.style.display = 'block';
        }

        calculateOutstanding();
        updatePaymentStatus();
    }

    function calculateTotalAmount() {
        let total = 0;
        const itemRows = document.querySelectorAll('#itemsBody .item-row');
        
        itemRows.forEach(row => {
            const qty = parseFloat(row.querySelector('.received-qty').value) || 0;
            const cost = parseFloat(row.querySelector('.cost').value) || 0;
            total += qty * cost;
        });
        
        return total;
    }

    function calculateOutstanding() {
        const totalAmount = calculateTotalAmount();
        const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
        const outstanding = Math.max(0, totalAmount - paidAmount);

        // Update display
        document.getElementById('totalAmount').textContent = `Rs. ${totalAmount.toFixed(2)}`;
        document.getElementById('outstandingAmount').textContent = `Rs. ${outstanding.toFixed(2)}`;
        
        // Validate paid amount doesn't exceed total
        if (paidAmount > totalAmount) {
            document.getElementById('paidAmount').value = totalAmount.toFixed(2);
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Amount',
                text: 'Paid amount cannot exceed total amount',
                timer: 3000
            });
        }

        updatePaymentStatus();
    }

    function updatePaymentStatus() {
        const totalAmount = calculateTotalAmount();
        const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
        const paymentStatusDisplay = document.getElementById('paymentStatusDisplay');
        
        if (paidAmount === 0) {
            paymentStatusDisplay.textContent = 'Unpaid';
            paymentStatusDisplay.className = 'text-lg font-medium text-red-400';
        } else if (paidAmount >= totalAmount) {
            paymentStatusDisplay.textContent = 'Paid';
            paymentStatusDisplay.className = 'text-lg font-medium text-green-400';
        } else {
            paymentStatusDisplay.textContent = 'Partial';
            paymentStatusDisplay.className = 'text-lg font-medium text-yellow-400';
        }
    }

    // Call calculate functions when items change
    function updatePaymentCalculations() {
        calculateOutstanding();
    }

    // Form submission functions
    function completeGRN() {
        // Validate form data
        const validationResult = validateGRNForm();
        if (!validationResult.valid) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: validationResult.message
            });
            return;
        }

        // Show confirmation dialog
        Swal.fire({
            title: 'Complete GRN?',
            text: 'Are you sure you want to complete this GRN? This will update stock levels.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Complete GRN',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitGRN('completed');
            }
        });
    }

    function saveDraft() {
        // Validate basic required fields
        const supplierId = document.getElementById('supplier').value;
        const receiptDate = document.getElementById('receiptDate').value;

        if (!supplierId) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a supplier'
            });
            return;
        }

        if (!receiptDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a receipt date'
            });
            return;
        }

        submitGRN('draft');
    }

    function validateGRNForm() {
        // Check supplier
        const supplierId = document.getElementById('supplier').value;
        if (!supplierId) {
            return { valid: false, message: 'Please select a supplier' };
        }

        // Check receipt date
        const receiptDate = document.getElementById('receiptDate').value;
        if (!receiptDate) {
            return { valid: false, message: 'Please enter a receipt date' };
        }

        // Check if there are items
        const itemRows = document.querySelectorAll('#itemsBody .item-row');
        if (itemRows.length === 0) {
            return { valid: false, message: 'Please add at least one item to the GRN' };
        }

        // Validate each item
        for (let i = 0; i < itemRows.length; i++) {
            const row = itemRows[i];
            const productId = row.querySelector('.product-id').value;
            const receivedQty = parseFloat(row.querySelector('.received-qty').value);
            const cost = parseFloat(row.querySelector('.cost').value);
            const sellingPrice = parseFloat(row.querySelector('.selling-price').value);
            const batchNumber = row.querySelector('.batch-number').value;

            if (!productId) {
                return { valid: false, message: `Row ${i + 1}: Product is required` };
            }

            if (!receivedQty || receivedQty <= 0) {
                return { valid: false, message: `Row ${i + 1}: Valid received quantity is required` };
            }

            if (!cost || cost <= 0) {
                return { valid: false, message: `Row ${i + 1}: Valid cost is required` };
            }

            if (!sellingPrice || sellingPrice <= 0) {
                return { valid: false, message: `Row ${i + 1}: Valid selling price is required` };
            }

            if (!batchNumber.trim()) {
                return { valid: false, message: `Row ${i + 1}: Batch number is required` };
            }
        }

        return { valid: true };
    }

    function collectFormData() {
        // Collect basic form data
        const formData = {
            supplier_id: document.getElementById('supplier').value,
            receipt_date: document.getElementById('receiptDate').value,
            invoice_number: document.getElementById('invoiceNumber').value || '',
            invoice_date: document.getElementById('invoiceDate').value || '',
            notes: document.getElementById('notes').value || '',
            po_id: currentGRNType === 'po' ? document.getElementById('purchaseOrder').value : null,
            items: []
        };

        // Collect payment data
        const paymentType = document.querySelector('input[name="paymentType"]:checked').value;
        const totalAmount = calculateTotalAmount();
        const paidAmount = parseFloat(document.getElementById('paidAmount').value) || 0;
        
        formData.payment_data = {
            total_amount: totalAmount,
            paid_amount: paidAmount,
            payment_status: paymentType,
            payment_method: document.getElementById('paymentMethod').value || null,
            payment_reference: document.getElementById('paymentReference').value || null,
            payment_notes: document.getElementById('paymentNotes').value || null
        };

        // Collect items data
        const itemRows = document.querySelectorAll('#itemsBody .item-row');
        itemRows.forEach(row => {
            const hasExpiryCheckbox = row.querySelector('.has-expiry');
            const hasExpiry = hasExpiryCheckbox ? hasExpiryCheckbox.checked : false;
            const expiryDate = hasExpiry ? row.querySelector('.expiry-date').value : null;
            
            // Get batch data from hidden field
            const batchDataElement = row.querySelector('.batch-data');
            let batchData = null;
            if (batchDataElement) {
                try {
                    batchData = JSON.parse(batchDataElement.value);
                } catch (e) {
                    console.error('Failed to parse batch data:', e);
                }
            }

            const item = {
                product_id: parseInt(row.querySelector('.product-id').value),
                received_qty: parseFloat(row.querySelector('.received-qty').value),
                cost: parseFloat(row.querySelector('.cost').value),
                selling_price: parseFloat(row.querySelector('.selling-price').value),
                batch_number: row.querySelector('.batch-number').value,
                expiry_date: expiryDate,
                po_item_id: row.dataset.poItemId ? parseInt(row.dataset.poItemId) : null,
                batch_data: batchData // Include batch management data
            };

            formData.items.push(item);
        });

        return formData;
    }

    function submitGRN(status = 'completed') {
        const formData = collectFormData();
        formData.status = status;

        // Show loading
        Swal.fire({
            title: status === 'draft' ? 'Saving Draft...' : 'Completing GRN...',
            text: 'Please wait while we process your request.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit via AJAX
        $.ajax({
            url: 'save_grn.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || `GRN ${status} successfully!`,
                        showConfirmButton: true
                    }).then(() => {
                        // Redirect to dashboard or GRN details
                        if (response.grn_id) {
                            window.location.href = `index.php?created=${response.grn_id}`;
                        } else {
                            window.location.href = 'index.php';
                        }
                    });
                } else {
                    throw new Error(response.message || 'Unknown error occurred');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                
                let errorMessage = 'Failed to save GRN';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        errorMessage = xhr.responseText;
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    footer: 'Please check your data and try again.'
                });
            }
        });
    }
    </script>
</body>
</html>