<?php include '../nav.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Order - <?php echo $ERP_COMPANY_NAME; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Additional CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">

    <style type="text/tailwindcss">
        @layer components {
            .form-label {
                @apply block text-sm font-medium text-gray-700 mb-1;
            }
            .form-input {
                @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm;
            }
            .form-error {
                @apply mt-1 text-sm text-red-600;
            }
            .btn {
                @apply px-4 py-2 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2;
            }
            .btn-primary {
                @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500;
            }
            .btn-danger {
                @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500;
            }
            .btn-secondary {
                @apply bg-gray-600 text-white hover:bg-gray-700 focus:ring-gray-500;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
            <span class="text-gray-700">Processing...</span>
        </div>
    </div>

    <div class="min-h-screen p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Create Purchase Order</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>
            </div>

            <!-- Main Form -->
            <form id="poForm" class="bg-white shadow-md rounded-lg p-6">
                <!-- Header Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="form-label" for="supplier">Supplier <span class="text-red-600">*</span></label>
                        <select id="supplier" name="supplier_id" class="form-input" required>
                            <option value="">Select Supplier</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="orderDate">Order Date <span class="text-red-600">*</span></label>
                        <input type="date" id="orderDate" name="order_date" class="form-input" required
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="deliveryDate">Expected Delivery Date</label>
                        <input type="date" id="deliveryDate" name="delivery_date" class="form-input"
                            min="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                        <button type="button" class="btn btn-primary" id="addItemBtn">
                            <i class="fas fa-plus mr-2"></i> Add Item
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Cost</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTable">
                                <tr id="noItemsRow">
                                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                        No items added yet. Click "Add Item" to start.
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm font-medium">Subtotal:</td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm font-medium" id="subtotal">Rs. 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm">
                                        <div class="flex items-center justify-end space-x-2">
                                            <span>Discount:</span>
                                            <select name="discount_type" class="w-32 rounded-md border-gray-300 shadow-sm" id="discountType">
                                                <option value="fixed">Fixed Amount</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                            <input type="number" name="discount_value" id="discountValue"
                                                class="w-32 rounded-md border-gray-300 shadow-sm"
                                                min="0" step="0.01" value="0">
                                        </div>
                                    </td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm" id="discountAmount">Rs. 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm">
                                        <div class="flex items-center justify-end space-x-2">
                                            <span>Tax:</span>
                                            <select name="tax_type" class="w-32 rounded-md border-gray-300 shadow-sm" id="taxType">
                                                <option value="percentage">Percentage</option>
                                                <option value="fixed">Fixed Amount</option>
                                            </select>
                                            <input type="number" name="tax_value" id="taxValue"
                                                class="w-32 rounded-md border-gray-300 shadow-sm"
                                                min="0" step="0.01" value="0">
                                        </div>
                                    </td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm" id="taxAmount">Rs. 0.00</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-right text-sm">
                                        <div class="flex items-center justify-end space-x-2">
                                            <span>Shipping Fee:</span>
                                            <input type="number" name="shipping_fee" id="shippingFee"
                                                class="w-32 rounded-md border-gray-300 shadow-sm"
                                                min="0" step="0.01" value="0">
                                        </div>
                                    </td>
                                    <td colspan="2" class="px-4 py-2 text-right text-sm" id="shippingAmount">Rs. 0.00</td>
                                </tr>
                                <tr class="font-bold">
                                    <td colspan="3" class="px-4 py-2 text-right">Total Amount:</td>
                                    <td colspan="2" class="px-4 py-2 text-right" id="totalAmount">Rs. 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="mb-6">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="form-input"></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveAsDraftBtn">
                        <i class="fas fa-save mr-2"></i> Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i> Submit Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl">
                <div class="flex justify-between items-center p-4 border-b">
                    <h3 class="text-lg font-semibold">Add Item</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeItemModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <form id="addItemForm">
                        <div class="mb-4">
                            <label class="form-label" for="product">Product <span class="text-red-600">*</span></label>
                            <select id="product" class="form-input" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="quantity">Quantity <span class="text-red-600">*</span></label>
                            <input type="number" id="quantity" class="form-input" required min="1" value="1">
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="unitCost">Unit Cost <span class="text-red-600">*</span></label>
                            <input type="number" id="unitCost" class="form-input" required min="0" step="0.01">
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" class="btn btn-secondary" onclick="closeItemModal()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#supplier').select2({
                placeholder: 'Select a supplier',
                ajax: {
                    url: 'purchase.php?action=get_suppliers',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(supplier => ({
                                id: supplier.supplier_id,
                                text: supplier.supplier_name
                            }))
                        };
                    },
                    cache: true
                }
            });

            $('#product').select2({
                placeholder: 'Select a product',
                dropdownParent: $('#addItemModal'),
                ajax: {
                    url: 'purchase.php?action=get_products',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data.data.map(product => ({
                                id: product.product_id,
                                text: product.product_name,
                                sku: product.sku,
                                cost: product.last_cost
                            }))
                        };
                    },
                    cache: true
                }
            }).on('select2:select', function(e) {
                const data = e.params.data;
                $('#unitCost').val(data.cost);
            });

            // Show/hide loading overlay
            function showLoading() {
                $('#loadingOverlay').removeClass('hidden');
            }

            function hideLoading() {
                $('#loadingOverlay').addClass('hidden');
            }

            // Format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'LKR'
                }).format(amount);
            }

            // Calculate totals
            function calculateTotals() {
                let subtotal = 0;
                $('#itemsTable tr.item-row').each(function() {
                    const total = parseFloat($(this).find('.item-total').data('amount') || 0);
                    subtotal += total;
                });

                // Update subtotal
                $('#subtotal').text(formatCurrency(subtotal));

                // Calculate discount
                const discountType = $('#discountType').val();
                const discountValue = parseFloat($('#discountValue').val() || 0);
                const discountAmount = discountType === 'percentage' ?
                    (subtotal * discountValue / 100) :
                    discountValue;
                $('#discountAmount').text(formatCurrency(discountAmount));

                // Calculate tax on amount after discount
                const taxableAmount = subtotal - discountAmount;
                const taxType = $('#taxType').val();
                const taxValue = parseFloat($('#taxValue').val() || 0);
                const taxAmount = taxType === 'percentage' ?
                    (taxableAmount * taxValue / 100) :
                    taxValue;
                $('#taxAmount').text(formatCurrency(taxAmount));

                // Add shipping fee
                const shippingFee = parseFloat($('#shippingFee').val() || 0);
                $('#shippingAmount').text(formatCurrency(shippingFee));

                // Calculate total
                const totalAmount = subtotal - discountAmount + taxAmount + shippingFee;
                $('#totalAmount').text(formatCurrency(totalAmount));

                return {
                    subtotal,
                    discountAmount,
                    taxAmount,
                    shippingFee,
                    totalAmount
                };
            }

            // Handle item form submission
            let itemCounter = 0;
            $('#addItemForm').on('submit', function(e) {
                e.preventDefault();

                const productSelect = $('#product');
                const product = productSelect.select2('data')[0];
                const quantity = parseInt($('#quantity').val());
                const unitCost = parseFloat($('#unitCost').val());
                const total = quantity * unitCost;

                // Add item to table
                if ($('#noItemsRow').length) {
                    $('#noItemsRow').remove();
                }

                $('#itemsTable').append(`
            <tr class="item-row" id="item_${itemCounter}">
                <td class="px-4 py-2">
                    <div class="font-medium">${product.text}</div>
                    <div class="text-sm text-gray-500">SKU: ${product.sku}</div>
                    <input type="hidden" name="items[${itemCounter}][product_id]" value="${product.id}">
                </td>
                <td class="px-4 py-2 text-right">
                    <input type="hidden" name="items[${itemCounter}][quantity]" value="${quantity}">
                    ${quantity}
                </td>
                <td class="px-4 py-2 text-right">
                    <input type="hidden" name="items[${itemCounter}][unit_cost]" value="${unitCost}">
                    ${formatCurrency(unitCost)}
                </td>
                <td class="px-4 py-2 text-right item-total" data-amount="${total}">
                    ${formatCurrency(total)}
                </td>
                <td class="px-4 py-2 text-center">
                    <button type="button" class="text-red-600 hover:text-red-800" 
                            onclick="removeItem('item_${itemCounter}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);

                itemCounter++;
                calculateTotals();
                closeItemModal();
            });

            // Add item button click
            $('#addItemBtn').click(function() {
                $('#addItemForm')[0].reset();
                $('#product').val('').trigger('change');
                $('#addItemModal').removeClass('hidden');
            });

            // Remove item
            window.removeItem = function(itemId) {
                Swal.fire({
                    title: 'Remove Item?',
                    text: 'Are you sure you want to remove this item?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#${itemId}`).remove();
                        if ($('#itemsTable tr.item-row').length === 0) {
                            $('#itemsTable').html(`
                        <tr id="noItemsRow">
                            <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                                No items added yet. Click "Add Item" to start.
                            </td>
                        </tr>
                    `);
                        }
                        calculateTotals();
                    }
                });
            };

            // Close item modal
            window.closeItemModal = function() {
                $('#addItemModal').addClass('hidden');
            };

            // Handle amount input changes
            $('#discountType, #discountValue, #taxType, #taxValue, #shippingFee').on('change input', calculateTotals);

            // Save as draft
            $('#saveAsDraftBtn').click(function() {
                submitForm('draft');
            });

            // Submit form
            $('#poForm').on('submit', function(e) {
                e.preventDefault();
                submitForm('pending');
            });

            // Form submission
            async function submitForm(status) {
                // Validate form
                if (!$('#supplier').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please select a supplier'
                    });
                    return;
                }

                if ($('#itemsTable tr.item-row').length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please add at least one item'
                    });
                    return;
                }

                // Gather form data
                const items = [];
                $('#itemsTable tr.item-row').each(function() {
                    const row = $(this);
                    items.push({
                        product_id: row.find('input[name*="[product_id]"]').val(),
                        quantity: row.find('input[name*="[quantity]"]').val(),
                        unit_cost: row.find('input[name*="[unit_cost]"]').val()
                    });
                });

                const totals = calculateTotals();
                const formData = {
                    action: 'create_po',
                    supplier_id: $('#supplier').val(),
                    order_date: $('#orderDate').val(),
                    delivery_date: $('#deliveryDate').val(),
                    shipping_fee: $('#shippingFee').val(),
                    discount_type: $('#discountType').val(),
                    discount_value: $('#discountValue').val(),
                    tax_type: $('#taxType').val(),
                    tax_value: $('#taxValue').val(),
                    notes: $('#notes').val(),
                    status: status,
                    items: JSON.stringify(items)
                };

                try {
                    showLoading();
                    const response = await $.post('purchase.php', formData);

                    if (response.status === 'success') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `Purchase order ${response.po_number} has been created`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        window.location.href = 'index.php';
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message
                    });
                } finally {
                    hideLoading();
                }
            }

            // Set minimum delivery date
            const today = new Date().toISOString().split('T')[0];
            $('#deliveryDate').attr('min', today);
        });
    </script>

</body>

</html>