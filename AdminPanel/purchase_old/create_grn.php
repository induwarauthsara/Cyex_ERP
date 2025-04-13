<?php
require_once 'inc/config.php';

// Get PO ID from URL
$poId = isset($_GET['po_id']) ? (int)$_GET['po_id'] : 0;

// Validate PO exists and is in ordered status
$query = "SELECT po.*, s.supplier_name 
          FROM purchase_orders po
          JOIN suppliers s ON s.supplier_id = po.supplier_id
          WHERE po.po_id = $poId AND po.status = 'ordered'";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    // Redirect if PO doesn't exist or is not in ordered status
    header('Location: purchase_orders.php');
    exit;
}

$po = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create GRN - <?php echo $ERP_COMPANY_NAME; ?></title>

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
            .status-badge {
                @apply px-2 py-1 rounded-full text-xs font-medium;
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
                    <h1 class="text-2xl font-bold text-gray-900">
                        Create GRN for PO: <?php echo htmlspecialchars($po['po_number']); ?>
                    </h1>
                    <a href="purchase_orders.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Back to List
                    </a>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                    Supplier: <?php echo htmlspecialchars($po['supplier_name']); ?>
                </div>
            </div>

            <!-- Main Form -->
            <form id="grnForm" class="bg-white shadow-md rounded-lg p-6">
                <input type="hidden" id="poId" value="<?php echo $poId; ?>">

                <!-- Header Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="form-label" for="receiptDate">Receipt Date <span class="text-red-600">*</span></label>
                        <input type="date" id="receiptDate" name="receipt_date" class="form-input" required
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label class="form-label" for="invoiceNumber">Invoice Number</label>
                        <input type="text" id="invoiceNumber" name="invoice_number" class="form-input">
                    </div>
                    <div>
                        <label class="form-label" for="invoiceDate">Invoice Date</label>
                        <input type="date" id="invoiceDate" name="invoice_date" class="form-input">
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ordered</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Receiving</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTable">
                                <!-- Items will be loaded here -->
                            </tbody>
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
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='purchase_orders.php'">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveAsDraftBtn">
                        <i class="fas fa-save mr-2"></i> Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check mr-2"></i> Complete Receiving
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>

    <script>
        $(document).ready(function() {
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

            // Load PO items
            async function loadPoItems() {
                try {
                    showLoading();
                    const response = await $.get('purchase.php', {
                        action: 'get_po_details',
                        po_id: $('#poId').val()
                    });

                    if (response.status === 'success') {
                        const items = response.data.items;
                        items.forEach(item => {
                            const remainingQty = item.quantity - item.total_received_qty;
                            $('#itemsTable').append(`
                        <tr class="item-row border-b" data-po-item-id="${item.po_item_id}">
                            <td class="px-4 py-2">
                                <div class="font-medium">${item.product_name}</div>
                                <div class="text-sm text-gray-500">SKU: ${item.sku}</div>
                            </td>
                            <td class="px-4 py-2 text-right">${item.quantity}</td>
                            <td class="px-4 py-2 text-right">${item.total_received_qty || 0}</td>
                            <td class="px-4 py-2">
                                <input type="number" class="form-input w-24 text-right receiving-qty"
                                       name="items[${item.po_item_id}][received_qty]"
                                       min="0" max="${remainingQty}" value="${remainingQty}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" class="form-input w-32 text-right"
                                       name="items[${item.po_item_id}][cost]"
                                       step="0.01" value="${item.unit_cost}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" class="form-input w-32 text-right"
                                       name="items[${item.po_item_id}][selling_price]"
                                       step="0.01" 
                                       value="${parseFloat(item.unit_cost * 1.25).toFixed(2)}">
                            </td>
                            <td class="px-4 py-2">
                                <input type="date" class="form-input w-40"
                                       name="items[${item.po_item_id}][expiry_date]">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" class="form-input"
                                       name="items[${item.po_item_id}][notes]">
                            </td>
                        </tr>
                    `);
                        });
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

            // Load items on page load
            loadPoItems();

            // Save as draft
            $('#saveAsDraftBtn').click(function() {
                submitForm('draft');
            });

            // Submit form
            $('#grnForm').on('submit', function(e) {
                e.preventDefault();
                submitForm('completed');
            });

            // Form submission
            async function submitForm(status) {
                // Validate form
                if (!validateForm(status)) {
                    return;
                }

                // Gather form data
                const items = [];
                let hasItems = false;

                $('.item-row').each(function() {
                    const row = $(this);
                    const poItemId = row.data('po-item-id');
                    const receivedQty = parseInt(row.find('input[name*="[received_qty]"]').val() || 0);

                    if (receivedQty > 0) {
                        hasItems = true;
                        items.push({
                            po_item_id: poItemId,
                            received_qty: receivedQty,
                            cost: parseFloat(row.find('input[name*="[cost]"]').val()),
                            selling_price: parseFloat(row.find('input[name*="[selling_price]"]').val()),
                            expiry_date: row.find('input[name*="[expiry_date]"]').val(),
                            notes: row.find('input[name*="[notes]"]').val()
                        });
                    }
                });

                if (status === 'completed' && !hasItems) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter received quantity for at least one item'
                    });
                    return;
                }

                const formData = {
                    action: 'create_grn',
                    po_id: $('#poId').val(),
                    receipt_date: $('#receiptDate').val(),
                    invoice_number: $('#invoiceNumber').val(),
                    invoice_date: $('#invoiceDate').val(),
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
                            text: `GRN ${response.grn_number} has been created`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        window.location.href = 'purchase_orders.php';
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

            // Validate form
            function validateForm(status) {
                // Required fields
                if (!$('#receiptDate').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter receipt date'
                    });
                    return false;
                }

                // Invoice date must be less than or equal to receipt date
                const invoiceDate = $('#invoiceDate').val();
                const receiptDate = $('#receiptDate').val();
                if (invoiceDate && invoiceDate > receiptDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Invoice date cannot be later than receipt date'
                    });
                    return false;
                }

                // Validate items if completing GRN
                if (status === 'completed') {
                    let valid = true;

                    $('.item-row').each(function() {
                        const row = $(this);
                        const receivedQty = parseInt(row.find('input[name*="[received_qty]"]').val() || 0);
                        const cost = parseFloat(row.find('input[name*="[cost]"]').val());
                        const sellingPrice = parseFloat(row.find('input[name*="[selling_price]"]').val());

                        if (receivedQty > 0) {
                            if (cost <= 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: 'Please enter a valid cost'
                                });
                                valid = false;
                                return false;
                            }
                            if (sellingPrice <= 0) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: 'Please enter a valid selling price'
                                });
                                valid = false;
                                return false;
                            }
                            if (sellingPrice <= cost) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Warning',
                                    text: 'Selling price is less than or equal to cost price',
                                    showCancelButton: true,
                                    confirmButtonText: 'Continue anyway',
                                    cancelButtonText: 'Go back'
                                }).then((result) => {
                                    if (!result.isConfirmed) {
                                        valid = false;
                                        return false;
                                    }
                                });
                            }
                        }
                    });

                    return valid;
                }

                return true;
            }

            // Numeric input validation
            $(document).on('input', 'input[type="number"]', function() {
                if (this.value < 0) {
                    this.value = 0;
                }
                if (this.hasAttribute('max') && this.value > parseInt(this.getAttribute('max'))) {
                    this.value = this.getAttribute('max');
                }
            });

            // Set minimum dates
            const today = new Date().toISOString().split('T')[0];
            $('#receiptDate').attr('max', today);
            $('#invoiceDate').attr('max', today);

            // Auto-calculate selling price when cost changes
            $(document).on('input', 'input[name*="[cost]"]', function() {
                const cost = parseFloat(this.value || 0);
                const sellingPriceInput = $(this).closest('tr').find('input[name*="[selling_price]"]');
                if (cost > 0) {
                    sellingPriceInput.val((cost * 1.25).toFixed(2));
                }
            });
        });
    </script>

</body>

</html>