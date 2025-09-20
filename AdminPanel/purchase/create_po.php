<?php
require_once('../nav.php'); ?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Order</title>

    <!-- Include the same CSS and JS as index.php -->
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
        /* Select2 Dark Theme styles from index.php */
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
                            <h1 class="text-xl font-bold">Create Purchase Order</h1>
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
            <form id="purchaseOrderForm" class="bg-gray-800 shadow-md rounded-lg p-6">
                <!-- Header Section -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Date</label>
                        <input type="date" id="orderDate" name="orderDate"
                            class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Supplier</label>
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
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Status</label>
                        <select id="status" name="status"
                            class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"
                            required>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="ordered">Ordered</option>
                        </select>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Order Items</h2>
                        <button type="button" onclick="addItem()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700" id="itemsTable">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Product</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Unit Cost</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Total</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-gray-700">
                                <!-- Items will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals Section -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-400">Notes</label>
                            <textarea id="notes" name="notes" rows="4"
                                class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"></textarea>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <!-- Subtotal -->
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Subtotal:</span>
                            <span id="subtotal" class="text-white font-semibold">0.00</span>
                        </div>

                        <!-- Shipping Fee -->
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Shipping Fee:</span>
                            <input type="number" id="shippingFee" name="shippingFee"
                                class="w-32 rounded-md bg-gray-700 border-gray-600 text-white text-right"
                                value="0" min="0" step="0.01">
                        </div>

                        <!-- Discount -->
                        <div class="flex justify-between items-center space-x-4">
                            <span class="text-gray-400">Discount:</span>
                            <div class="flex items-center space-x-2">
                                <select id="discountType" name="discountType"
                                    class="rounded-md bg-gray-700 border-gray-600 text-white">
                                    <option value="fixed">Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                                <input type="number" id="discountValue" name="discountValue"
                                    class="w-32 rounded-md bg-gray-700 border-gray-600 text-white text-right"
                                    value="0" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Tax -->
                        <div class="flex justify-between items-center space-x-4">
                            <span class="text-gray-400">Tax:</span>
                            <div class="flex items-center space-x-2">
                                <select id="taxType" name="taxType"
                                    class="rounded-md bg-gray-700 border-gray-600 text-white">
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                                <input type="number" id="taxValue" name="taxValue"
                                    class="w-32 rounded-md bg-gray-700 border-gray-600 text-white text-right"
                                    value="0" min="0" step="0.01">
                            </div>
                        </div>

                        <!-- Total Amount -->
                        <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                            <span class="text-gray-400 font-medium">Total Amount:</span>
                            <span id="totalAmount" class="text-white text-xl font-bold">0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <button type="button" onclick="window.history.back()"
                        class="px-4 py-2 border border-gray-600 rounded-lg text-gray-300 hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" onclick="saveDraft()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Save as Draft
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Create Purchase Order
                    </button>
                </div>
            </form>
        </main>
    </div>

    <!-- Item Row Template -->
    <template id="itemRowTemplate">
        <tr class="item-row">
            <td class="px-4 py-2">
                <select class="product-select w-full rounded-md bg-gray-700 border-gray-600 text-white" required>
                    <option value="">Select Product</option>
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="quantity w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="1" value="1" required>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="unit-cost w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" required>
            </td>
            <td class="px-4 py-2">
                <span class="item-total">0.00</span>
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

    <script>
        $(document).ready(function() {
            // Set default date to today
            $('#orderDate').val(new Date().toISOString().split('T')[0]);

            // Initialize Select2 for supplier
            $('#supplier').select2({
                theme: 'classic',
                placeholder: 'Select a supplier',
                ajax: {
                    url: 'search_suppliers.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: data.hasMore
                            }
                        };
                    },
                    cache: true
                }
            });

            // Add first item row
            addItem();

            // Form submission
            $('#purchaseOrderForm').on('submit', function(e) {
                e.preventDefault();
                submitForm('ordered');
            });

            // Calculate totals when inputs change
            $(document).on('change', '.quantity, .unit-cost, #shippingFee, #discountType, #discountValue, #taxType, #taxValue', calculateTotals);
        });

        function addItem() {
            const template = document.getElementById('itemRowTemplate');
            const clone = template.content.cloneNode(true);

            // Initialize Select2 for product
            $(clone).find('.product-select').select2({
                theme: 'classic',
                placeholder: 'Select a product',
                ajax: {
                    url: 'search_products.php',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.items,
                            pagination: {
                                more: data.hasMore
                            }
                        };
                    },
                    cache: true
                }
            });

            document.getElementById('itemsBody').appendChild(clone);
        }

        function removeItem(button) {
            const row = button.closest('tr');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                calculateTotals();
            } else {
                Swal.fire('Error', 'At least one item is required', 'error');
            }
        }

        function calculateTotals() {
            let subtotal = 0;

            // Calculate items subtotal
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitCost = parseFloat(row.querySelector('.unit-cost').value) || 0;
                const total = quantity * unitCost;

                row.querySelector('.item-total').textContent = total.toFixed(2);
                subtotal += total;
            });

            // Get additional values
            const shippingFee = parseFloat($('#shippingFee').val()) || 0;
            const discountType = $('#discountType').val();
            const discountValue = parseFloat($('#discountValue').val()) || 0;
            const taxType = $('#taxType').val();
            const taxValue = parseFloat($('#taxValue').val()) || 0;

            // Calculate discount
            let discountAmount = discountType === 'percentage' ?
                (subtotal * discountValue / 100) : discountValue;

            // Calculate tax
            const afterDiscount = subtotal - discountAmount;
            let taxAmount = taxType === 'percentage' ?
                (afterDiscount * taxValue / 100) : taxValue;

            // Calculate total
            const total = afterDiscount + taxAmount + shippingFee;

            // Update display
            $('#subtotal').text(subtotal.toFixed(2));
            $('#totalAmount').text(total.toFixed(2));
        }

        function saveDraft() {
            submitForm('draft');
        }

        function submitForm(status) {
            // Validate form
            if (!validateForm()) {
                return;
            }

            // Gather items data
            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                items.push({
                    product_id: $(row).find('.product-select').val(),
                    quantity: row.querySelector('.quantity').value,
                    unit_cost: row.querySelector('.unit-cost').value
                });
            });

            // Prepare form data
            const formData = {
                supplier_id: $('#supplier').val(),
                order_date: $('#orderDate').val(),
                status: status,
                shipping_fee: $('#shippingFee').val(),
                discount_type: $('#discountType').val(),
                discount_value: $('#discountValue').val(),
                tax_type: $('#taxType').val(),
                tax_value: $('#taxValue').val(),
                notes: $('#notes').val(),
                items: items
            };

            // Submit form
            $.ajax({
                url: 'save_po.php',
                method: 'POST',
                data: JSON.stringify(formData),
                contentType: 'application/json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Purchase order created successfully',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'index.php';
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to create purchase order', 'error');
                }
            });
        }

        function validateForm() {
            // Basic validation
            if (!$('#supplier').val()) {
                Swal.fire('Error', 'Please select a supplier', 'error');
                return false;
            }

            let valid = true;
            document.querySelectorAll('.item-row').forEach(row => {
                const product = $(row).find('.product-select').val();
                const quantity = row.querySelector('.quantity').value;
                const unitCost = row.querySelector('.unit-cost').value;

                if (!product || !quantity || !unitCost) {
                    valid = false;
                    Swal.fire('Error', 'Please fill in all item details', 'error');
                    return false;
                }
            });

            return valid;
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
            
            // Send AJAX request to create supplier
            fetch('add_supplier.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    supplier_name: supplierName,
                    supplier_tel: supplierTel,
                    supplier_address: supplierAddress,
                    note: note
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Add the new supplier to the dropdown
                    const supplierSelect = $('#supplier');
                    const newOption = new Option(data.supplier.text, data.supplier.id, true, true);
                    supplierSelect.append(newOption).trigger('change');
                    
                    // Close modal
                    closeSupplierModal();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to add supplier'
                });
            });
        }
    </script>
</body>

</html>