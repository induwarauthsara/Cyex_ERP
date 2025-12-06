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
    <title>Create GRN - Goods Received Note</title>

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

    <style>
        /* Select2 Dark Theme */
        .select2-container--default .select2-selection--single {
            background-color: #374151 !important;
            border: 1px solid #4b5563 !important;
            color: #ffffff !important;
            border-radius: 0.5rem !important;
            height: 42px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff !important;
            line-height: 40px !important;
        }

        .select2-dropdown {
            background-color: #374151 !important;
            border: 1px solid #4b5563 !important;
        }

        .select2-results__option {
            background-color: #374151 !important;
            color: #ffffff !important;
        }

        .select2-results__option--highlighted {
            background-color: #2563eb !important;
        }
    </style>
</head>

<body class="bg-gray-900 text-white">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-white">
                                <i class="fas fa-box mr-2"></i>Create GRN
                            </h1>
                        </div>
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-6xl mx-auto py-6 px-4">
            <form id="grnForm" class="space-y-6">

                <!-- Basic Information Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Basic Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Receipt Date <span class="text-red-500">*</span></label>
                            <input type="date" id="receiptDate"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Supplier <span class="text-red-500">*</span></label>
                            <?php if ($po_data): ?>
                                <input type="hidden" id="supplier" name="supplier" value="<?= $po_data['supplier_id'] ?>">
                                <div class="px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                                    <?= htmlspecialchars($po_data['supplier_name']) ?>
                                </div>
                            <?php else: ?>
                                <select id="supplier" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" required>
                                    <option value="">Select Supplier</option>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Invoice Number</label>
                            <input type="text" id="invoiceNumber"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="Supplier invoice number">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Invoice Date</label>
                            <input type="date" id="invoiceDate"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Items Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold flex items-center">
                            <i class="fas fa-boxes mr-2 text-green-500"></i>
                            Items Received
                        </h2>
                        <button type="button" onclick="openAddItemModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-gray-400 border-b border-gray-700">
                                    <th class="pb-3 px-2">Product</th>
                                    <th class="pb-3 px-2 w-32">Quantity</th>
                                    <th class="pb-3 px-2 w-32">Cost</th>
                                    <th class="pb-3 px-2 w-32">Selling Price</th>
                                    <th class="pb-3 px-2 w-32">Total</th>
                                    <th class="pb-3 px-2 w-16"></th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-gray-700">
                                <!-- Items will be added here -->
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-600">
                                    <td colspan="4" class="pt-4 px-2 text-right font-semibold">Total Amount:</td>
                                    <td class="pt-4 px-2 font-bold text-green-400" id="totalAmount">Rs. 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <div id="emptyMessage" class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No items added yet. Click "Add Item" to start.</p>
                        </div>
                    </div>
                </div>

                <!-- Payment Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-yellow-500"></i>
                        Payment Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Payment Status</label>
                            <select id="paymentStatus" onchange="handlePaymentStatusChange()"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                                <option value="unpaid">Unpaid (Credit)</option>
                                <option value="partial">Partial Payment</option>
                                <option value="paid">Fully Paid</option>
                            </select>
                        </div>

                        <div id="paidAmountDiv" style="display: none;">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Paid Amount</label>
                            <input type="number" id="paidAmount" step="0.01" min="0"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                                placeholder="0.00" onchange="updateOutstanding()">
                        </div>

                        <div id="paymentMethodDiv" style="display: none;">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                            <select id="paymentMethod"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>
                    </div>

                    <div id="paymentReferenceDiv" style="display: none;" class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Reference</label>
                        <input type="text" id="paymentReference"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                            placeholder="Transaction ID, Cheque No, etc.">
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Outstanding Balance:</span>
                            <span id="outstandingAmount" class="text-xl font-bold text-yellow-400">Rs. 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-purple-500"></i>
                        Notes (Optional)
                    </h2>
                    <textarea id="notes" rows="3"
                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500"
                        placeholder="Add any additional notes or comments..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <a href="index.php"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" onclick="submitGRN()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-semibold">
                        <i class="fas fa-check mr-2"></i>Save GRN
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="fixed inset-0 bg-black bg-opacity-75 hidden flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold">Add Item</h3>
                <button onclick="closeAddItemModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Product <span class="text-red-500">*</span></label>
                    <div class="flex">
                        <select id="productSelect" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                            <option value="">Search for a product...</option>
                        </select>
                        <label class="flex items-center ml-10">
                            <input type="checkbox" id="standardOnlyFilter" checked
                                class="mr-2 rounded bg-gray-700 border-gray-600 text-blue-600">
                            <span class="text-sm text-gray-400">Standard items only</span>
                        </label>
                    </div>
                </div>

                <!-- Batch Selection Section -->
                <div id="batchSection" style="display: none;" class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Batch</label>
                        <select id="existingBatchSelect" class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" onchange="handleBatchSelect()">
                            <option value="">Loading batches...</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">&nbsp;</label>
                        <button type="button" onclick="toggleNewBatch()" id="newBatchBtn"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white hover:bg-gray-600">
                            <i class="fas fa-plus mr-1"></i>New
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Quantity <span class="text-red-500">*</span></label>
                        <input type="number" id="itemQuantity" step="1"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white" value="1">
                    </div>

                    <div id="availableStockDiv" style="display: none;">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Available Stock</label>
                        <div class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-gray-400 flex items-center">
                            <i class="fas fa-box mr-2"></i>
                            <span id="availableStock">0</span>
                        </div>
                    </div>

                    <div id="newBatchNumberDiv" style="display: none;">
                        <label class="block text-sm font-medium text-gray-300 mb-2">New Batch Number</label>
                        <input type="text" id="itemBatch"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                            placeholder="Auto-generated">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Cost Price <span class="text-red-500">*</span></label>
                        <input type="number" id="itemCost" step="0.01" min="0"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                            placeholder="0.00" onchange="calculateProfit()">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Selling Price <span class="text-red-500">*</span></label>
                        <input type="number" id="itemSelling" step="0.01" min="0"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                            placeholder="0.00" onchange="calculateProfit()">
                    </div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-300">Profit Margin:</span>
                        <span id="profitMargin" class="font-semibold text-green-400">0%</span>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" id="hasExpiry" onchange="toggleExpiry()"
                            class="mr-2 rounded bg-gray-700 border-gray-600 text-blue-600">
                        <span class="text-sm text-gray-300">This product has an expiry date</span>
                    </label>
                    <input type="date" id="itemExpiry" disabled
                        class="mt-2 w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeAddItemModal()"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                        Cancel
                    </button>
                    <button type="button" onclick="addItem()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        <i class="fas fa-plus mr-2"></i>Add Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCounter = 0;
        let authToken = localStorage.getItem('auth_token');

        // Check if authenticated
        if (!authToken) {
            Swal.fire({
                icon: 'error',
                title: 'Authentication Required',
                text: 'Please log in to continue',
            }).then(() => {
                window.location.href = '/login/';
            });
        }

        $(document).ready(function() {

            // Initialize supplier select
            $('#supplier').select2({
                placeholder: 'Search for a supplier',
                allowClear: true,
                ajax: {
                    url: '/api/v1/grn/get_suppliers.php',
                    dataType: 'json',
                    delay: 250,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + authToken);
                    },
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        if (data.success && data.data && data.data.suppliers) {
                            return {
                                results: data.data.suppliers.map(function(supplier) {
                                    return {
                                        id: supplier.supplier_id,
                                        text: supplier.supplier_name
                                    };
                                })
                            };
                        }
                        return {
                            results: []
                        };
                    },
                    cache: true
                }
            });

            // Initialize product select
            $('#productSelect').select2({
                placeholder: 'Search for a product',
                allowClear: true,
                dropdownParent: $('#addItemModal'),
                ajax: {
                    url: '/api/v1/grn/search_products.php',
                    dataType: 'json',
                    delay: 250,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('Authorization', 'Bearer ' + authToken);
                    },
                    data: function(params) {
                        return {
                            q: params.term,
                            type: $('#standardOnlyFilter').is(':checked') ? 'standard' : 'all'
                        };
                    },
                    processResults: function(data) {
                        if (data.success && data.data && data.data.products) {
                            return {
                                results: data.data.products.map(function(product) {
                                    return {
                                        id: product.product_id,
                                        text: product.product_name,
                                        cost: product.cost || 0,
                                        selling_price: product.selling_price || 0
                                    };
                                })
                            };
                        }
                        return {
                            results: []
                        };
                    },
                    cache: true
                }
            });

            // Refresh product search when filter changes
            $('#standardOnlyFilter').on('change', function() {
                $('#productSelect').val(null).trigger('change');
            });

            // Auto-fill prices when product is selected
            $('#productSelect').on('select2:select', function(e) {
                const data = e.params.data;
                const productId = data.id;

                // Load batches for this product
                loadProductBatches(productId);

                // Show batch section
                document.getElementById('batchSection').style.display = 'grid';

                // Set default prices
                if (data.cost) $('#itemCost').val(data.cost);
                if (data.selling_price) $('#itemSelling').val(data.selling_price);
                calculateProfit();
            });

            updateEmptyMessage();
        });

        function loadProductBatches(productId) {
            $.ajax({
                url: '/api/v1/grn/get_product_batches.php',
                type: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + authToken
                },
                data: {
                    product_id: productId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data && response.data.batches) {
                        const select = $('#existingBatchSelect');
                        select.html('');

                        if (response.data.batches.length > 0) {
                            response.data.batches.forEach(function(batch, index) {
                                const expiry = batch.expiry_date ? ' - Exp: ' + batch.expiry_date : '';
                                const option = `<option value="${batch.batch_id}" 
                                    data-stock="${batch.quantity}"
                                    data-cost="${batch.cost}"
                                    data-selling="${batch.selling_price}"
                                    data-expiry="${batch.expiry_date || ''}"
                                    data-batch-number="${batch.batch_number}"
                                    ${index === 0 ? 'selected' : ''}>
                                    ${batch.batch_number} (Stock: ${batch.quantity})${expiry}
                                </option>`;
                                select.append(option);
                            });

                            // Show available stock div and auto-select first batch
                            document.getElementById('availableStockDiv').style.display = 'block';
                            handleBatchSelect();
                        } else {
                            select.html('<option value="">No existing batches</option>');
                            // Auto-switch to new batch mode
                            toggleNewBatch();
                        }
                    }
                },
                error: function() {
                    console.error('Failed to load batches');
                    $('#existingBatchSelect').html('<option value="">Error loading batches</option>');
                }
            });
        }

        function toggleNewBatch() {
            const isNewMode = currentBatchMode === 'existing';
            currentBatchMode = isNewMode ? 'new' : 'existing';

            const select = document.getElementById('existingBatchSelect');
            const newBatchDiv = document.getElementById('newBatchNumberDiv');
            const availableStockDiv = document.getElementById('availableStockDiv');
            const btn = document.getElementById('newBatchBtn');

            if (isNewMode) {
                select.disabled = true;
                select.classList.add('opacity-50');
                newBatchDiv.style.display = 'block';
                availableStockDiv.style.display = 'none';
                btn.innerHTML = '<i class="fas fa-undo mr-1"></i>Use Existing';
                btn.classList.add('bg-blue-600');
                btn.classList.remove('bg-gray-700');
            } else {
                select.disabled = false;
                select.classList.remove('opacity-50');
                newBatchDiv.style.display = 'none';
                availableStockDiv.style.display = 'block';
                btn.innerHTML = '<i class="fas fa-plus mr-1"></i>New';
                btn.classList.remove('bg-blue-600');
                btn.classList.add('bg-gray-700');
                handleBatchSelect();
            }
        }

        function handleBatchSelect() {
            if (currentBatchMode === 'new') return;

            const select = document.getElementById('existingBatchSelect');
            const selectedOption = select.options[select.selectedIndex];

            if (select.value && selectedOption) {
                // Auto-fill prices
                document.getElementById('itemCost').value = selectedOption.dataset.cost || '';
                document.getElementById('itemSelling').value = selectedOption.dataset.selling || '';
                document.getElementById('itemBatch').value = selectedOption.dataset.batchNumber || '';

                // Show available stock
                const availableStock = selectedOption.dataset.stock || '0';
                document.getElementById('availableStock').textContent = availableStock;

                calculateProfit();
            }
        }

        function openAddItemModal() {
            document.getElementById('addItemModal').classList.remove('hidden');
            resetItemForm();
        }

        function closeAddItemModal() {
            document.getElementById('addItemModal').classList.add('hidden');
            resetItemForm();
        }

        function resetItemForm() {
            $('#productSelect').val(null).trigger('change');
            document.getElementById('itemQuantity').value = '1';
            document.getElementById('itemBatch').value = '';
            document.getElementById('itemCost').value = '';
            document.getElementById('itemSelling').value = '';
            document.getElementById('hasExpiry').checked = false;
            document.getElementById('itemExpiry').value = '';
            document.getElementById('itemExpiry').disabled = true;
            document.getElementById('profitMargin').textContent = '0%';

            // Reset batch section
            document.getElementById('batchSection').style.display = 'none';
            document.getElementById('availableStockDiv').style.display = 'none';
            currentBatchMode = 'existing';
            document.getElementById('newBatchNumberDiv').style.display = 'none';
            document.getElementById('existingBatchSelect').disabled = false;
            document.getElementById('existingBatchSelect').classList.remove('opacity-50');
            const btn = document.getElementById('newBatchBtn');
            btn.innerHTML = '<i class="fas fa-plus mr-1"></i>New';
            btn.classList.remove('bg-blue-600');
            btn.classList.add('bg-gray-700');
            $('#existingBatchSelect').html('<option value="">Loading batches...</option>');
        }

        function toggleExpiry() {
            const hasExpiry = document.getElementById('hasExpiry').checked;
            document.getElementById('itemExpiry').disabled = !hasExpiry;
            if (!hasExpiry) {
                document.getElementById('itemExpiry').value = '';
            }
        }

        function calculateProfit() {
            const cost = parseFloat(document.getElementById('itemCost').value) || 0;
            const selling = parseFloat(document.getElementById('itemSelling').value) || 0;

            if (cost > 0) {
                const profit = ((selling - cost) / cost * 100).toFixed(2);
                document.getElementById('profitMargin').textContent = profit + '%';
                document.getElementById('profitMargin').className = profit >= 0 ? 'font-semibold text-green-400' : 'font-semibold text-red-400';
            } else {
                document.getElementById('profitMargin').textContent = '0%';
            }
        }

        function addItem() {
            const productSelect = $('#productSelect');
            const productId = productSelect.val();
            const productName = productSelect.select2('data')[0]?.text || '';
            const quantity = parseFloat(document.getElementById('itemQuantity').value);
            const cost = parseFloat(document.getElementById('itemCost').value);
            const selling = parseFloat(document.getElementById('itemSelling').value);
            const hasExpiry = document.getElementById('hasExpiry').checked;
            const expiry = document.getElementById('itemExpiry').value;

            // Get batch option
            let batch = '';
            let batchData = {};

            // Validation
            if (!productId) {
                Swal.fire('Error', 'Please select a product', 'error');
                return;
            }
            if (!quantity || quantity <= 0) {
                Swal.fire('Error', 'Please enter a valid quantity', 'error');
                return;
            }
            if (!cost || cost < 0) {
                Swal.fire('Error', 'Please enter a valid cost price', 'error');
                return;
            }
            if (!selling || selling < 0) {
                Swal.fire('Error', 'Please enter a valid selling price', 'error');
                return;
            }

            // Handle batch based on option
            if (currentBatchMode === 'existing') {
                const existingBatchId = $('#existingBatchSelect').val();
                if (!existingBatchId) {
                    Swal.fire('Error', 'Please select an existing batch or create new', 'error');
                    return;
                }
                const selectedOption = document.getElementById('existingBatchSelect').options[document.getElementById('existingBatchSelect').selectedIndex];
                batch = selectedOption.dataset.batchNumber;
                batchData = {
                    isNew: false,
                    batchId: existingBatchId
                };
            } else {
                batch = document.getElementById('itemBatch').value || 'Auto-generated';
                batchData = {
                    isNew: true,
                    batchNumber: document.getElementById('itemBatch').value || ''
                };
            }

            const total = (quantity * cost).toFixed(2);
            itemCounter++;

            const row = `
                <tr class="item-row" data-item-id="${itemCounter}">
                    <td class="py-3 px-2">
                        <div class="font-medium">${productName}</div>
                        <small class="text-gray-500">Batch: ${batch}</small>
                        <input type="hidden" class="product-id" value="${productId}">
                        ${hasExpiry ? '<br><small class="text-gray-500">Exp: ' + expiry + '</small>' : ''}
                    </td>
                    <td class="py-3 px-2">
                        <input type="number" class="quantity w-full px-2 py-1 rounded bg-gray-700 border border-gray-600 text-white" 
                            value="${quantity}" step="1" min="0.001" onchange="updateRowTotal(this)">
                    </td>
                    <td class="py-3 px-2">
                        <input type="number" class="cost w-full px-2 py-1 rounded bg-gray-700 border border-gray-600 text-white" 
                            value="${cost}" step="0.01" min="0" onchange="updateRowTotal(this)">
                    </td>
                    <td class="py-3 px-2">
                        <input type="number" class="selling w-full px-2 py-1 rounded bg-gray-700 border border-gray-600 text-white" 
                            value="${selling}" step="0.01" min="0">
                    </td>
                    <td class="py-3 px-2 font-semibold row-total">Rs. ${total}</td>
                    <td class="py-3 px-2 text-center">
                        <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-400">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                    <input type="hidden" class="batch" value="${batch}">
                    <input type="hidden" class="expiry" value="${expiry}">
                    <input type="hidden" class="batch-data" value='${JSON.stringify(batchData)}'>
                </tr>
            `;

            document.getElementById('itemsBody').insertAdjacentHTML('beforeend', row);
            updateEmptyMessage();
            updateTotalAmount();
            updateOutstanding();
            closeAddItemModal();
        }

        function removeItem(button) {
            button.closest('tr').remove();
            updateEmptyMessage();
            updateTotalAmount();
            updateOutstanding();
        }

        function updateRowTotal(input) {
            const row = input.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const cost = parseFloat(row.querySelector('.cost').value) || 0;
            const total = (quantity * cost).toFixed(2);
            row.querySelector('.row-total').textContent = 'Rs. ' + total;
            updateTotalAmount();
            updateOutstanding();
        }

        function updateTotalAmount() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const cost = parseFloat(row.querySelector('.cost').value) || 0;
                total += quantity * cost;
            });
            document.getElementById('totalAmount').textContent = 'Rs. ' + total.toFixed(2);
            return total;
        }

        function updateEmptyMessage() {
            const hasItems = document.querySelectorAll('.item-row').length > 0;
            document.getElementById('emptyMessage').style.display = hasItems ? 'none' : 'block';
        }

        function handlePaymentStatusChange() {
            const status = document.getElementById('paymentStatus').value;
            const paidAmountDiv = document.getElementById('paidAmountDiv');
            const paymentMethodDiv = document.getElementById('paymentMethodDiv');
            const paymentReferenceDiv = document.getElementById('paymentReferenceDiv');

            if (status === 'paid') {
                paidAmountDiv.style.display = 'none';
                paymentMethodDiv.style.display = 'block';
                paymentReferenceDiv.style.display = 'block';
                const total = updateTotalAmount();
                document.getElementById('paidAmount').value = total.toFixed(2);
            } else if (status === 'partial') {
                paidAmountDiv.style.display = 'block';
                paymentMethodDiv.style.display = 'block';
                paymentReferenceDiv.style.display = 'block';
                document.getElementById('paidAmount').value = '';
            } else {
                paidAmountDiv.style.display = 'none';
                paymentMethodDiv.style.display = 'none';
                paymentReferenceDiv.style.display = 'none';
                document.getElementById('paidAmount').value = '0';
            }
            updateOutstanding();
        }

        function updateOutstanding() {
            const total = updateTotalAmount();
            const status = document.getElementById('paymentStatus').value;
            let paid = 0;

            if (status === 'paid') {
                paid = total;
            } else if (status === 'partial') {
                paid = parseFloat(document.getElementById('paidAmount').value) || 0;
            }

            const outstanding = total - paid;
            document.getElementById('outstandingAmount').textContent = 'Rs. ' + outstanding.toFixed(2);
        }

        function submitGRN() {
            // Validation
            const supplier = document.getElementById('supplier').value;
            const receiptDate = document.getElementById('receiptDate').value;
            const items = document.querySelectorAll('.item-row');

            if (!supplier) {
                Swal.fire('Error', 'Please select a supplier', 'error');
                return;
            }
            if (!receiptDate) {
                Swal.fire('Error', 'Please select a receipt date', 'error');
                return;
            }
            if (items.length === 0) {
                Swal.fire('Error', 'Please add at least one item', 'error');
                return;
            }

            // Collect data
            const formData = {
                supplier_id: supplier,
                receipt_date: receiptDate,
                invoice_number: document.getElementById('invoiceNumber').value,
                invoice_date: document.getElementById('invoiceDate').value,
                notes: document.getElementById('notes').value,
                items: [],
                payment_data: {
                    payment_status: document.getElementById('paymentStatus').value,
                    paid_amount: parseFloat(document.getElementById('paidAmount').value) || 0,
                    payment_method: document.getElementById('paymentMethod').value,
                    payment_reference: document.getElementById('paymentReference').value
                }
            };

            items.forEach(row => {
                const batchDataElement = row.querySelector('.batch-data');
                let batchData = null;
                if (batchDataElement && batchDataElement.value) {
                    try {
                        batchData = JSON.parse(batchDataElement.value);
                    } catch (e) {
                        console.error('Failed to parse batch data:', e);
                    }
                }

                formData.items.push({
                    product_id: parseInt(row.querySelector('.product-id').value),
                    received_qty: parseFloat(row.querySelector('.quantity').value),
                    cost: parseFloat(row.querySelector('.cost').value),
                    selling_price: parseFloat(row.querySelector('.selling').value),
                    batch_number: row.querySelector('.batch').value,
                    expiry_date: row.querySelector('.expiry').value || null,
                    batch_data: batchData
                });
            });

            // Submit
            Swal.fire({
                title: 'Saving GRN...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '/api/v1/grn/create.php',
                type: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + authToken,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(formData),
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'GRN saved successfully',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'index.php';
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to save GRN', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    let errorMessage = 'Failed to save GRN';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMessage, 'error');
                }
            });
        }


    </script>
</body>

</html>