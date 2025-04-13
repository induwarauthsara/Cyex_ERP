<?php
require_once('../../inc/config.php');

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if PO ID is provided
if (!isset($_GET['po_id'])) {
    header("Location: index.php");
    exit();
}

$po_id = intval($_GET['po_id']);

// Get PO details
$po_query = "SELECT po.*, s.supplier_name
             FROM purchase_orders po
             JOIN suppliers s ON po.supplier_id = s.supplier_id
             WHERE po_id = ? AND status = 'ordered'";

$stmt = mysqli_prepare($con, $po_query);
mysqli_stmt_bind_param($stmt, "i", $po_id);
mysqli_stmt_execute($stmt);
$po = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$po) {
    header("Location: index.php");
    exit();
}

// Get sequence for GRN number
$sequence_query = "SELECT next_value, prefix, padding FROM sequences WHERE name = 'grn'";
$sequence = mysqli_fetch_assoc(mysqli_query($con, $sequence_query));

if (!$sequence) {
    mysqli_query($con, "INSERT INTO sequences (name, prefix, next_value, padding) VALUES ('grn', 'GRN', 1, 5)");
    $sequence = ['prefix' => 'GRN', 'next_value' => 1, 'padding' => 5];
}

$grn_number = $sequence['prefix'] . str_pad($sequence['next_value'], $sequence['padding'], '0', STR_PAD_LEFT);

// Get PO items
$items_query = "SELECT poi.*, p.product_name, p.barcode, p.sku,
                (poi.quantity - COALESCE(
                    (SELECT SUM(gi.received_qty)
                     FROM grn_items gi
                     JOIN goods_receipt_notes grn ON gi.grn_id = grn.grn_id
                     WHERE gi.po_item_id = poi.po_item_id AND grn.status != 'cancelled'
                    ), 0
                )) as remaining_qty
                FROM purchase_order_items poi
                JOIN products p ON poi.product_id = p.product_id
                WHERE po_id = ?";

$stmt = mysqli_prepare($con, $items_query);
mysqli_stmt_bind_param($stmt, "i", $po_id);
mysqli_stmt_execute($stmt);
$items_result = mysqli_stmt_get_result($stmt);
$po_items = [];
while ($item = mysqli_fetch_assoc($items_result)) {
    if ($item['remaining_qty'] > 0) {
        $po_items[] = $item;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create GRN - <?php echo $ERP_COMPANY_NAME; ?></title>

    <!-- Include the same CSS and JS as before -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
</head>

<body class="bg-gray-900 text-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <button onclick="location.href='index.php'" class="text-gray-400 hover:text-white mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h1 class="text-2xl font-bold">Create GRN</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <form id="grnForm" class="bg-gray-800 rounded-lg shadow-lg p-6">
            <input type="hidden" name="po_id" value="<?php echo $po_id; ?>">
            <input type="hidden" name="grn_number" value="<?php echo $grn_number; ?>">

            <!-- GRN Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">GRN Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">GRN Number</label>
                            <input type="text" value="<?php echo $grn_number; ?>"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Receipt Date</label>
                            <input type="date" name="receipt_date" value="<?php echo date('Y-m-d'); ?>" required
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">PO Number</label>
                            <input type="text" value="<?php echo $po['po_number']; ?>"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Supplier</label>
                            <input type="text" value="<?php echo $po['supplier_name']; ?>"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Invoice Number</label>
                            <input type="text" name="invoice_number"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Invoice Date</label>
                            <input type="date" name="invoice_date"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-4">PO Summary</h2>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Barcode Verification</label>
                                <input type="text" id="verifyBarcode"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"
                                    placeholder="Scan or enter barcode to verify">
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium mb-2">Batch Options</label>
                                    <select id="batchOption" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                        <option value="new">Create New Batch</option>
                                        <option value="existing">Update Existing Batch</option>
                                    </select>
                                </div>
                                <div id="existingBatchDiv" class="hidden">
                                    <label class="block text-sm font-medium mb-2">Select Batch</label>
                                    <select id="existingBatch" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                    </select>
                                </div>
                            </div>
                            <div id="newBatchFields">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Batch Number</label>
                                        <input type="text" id="batchNumber" required
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Expiry Date</label>
                                        <input type="date" id="expiryDate"
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Cost</label>
                                        <input type="number" id="cost" step="0.01" required
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2">Selling Price</label>
                                        <input type="number" id="sellingPrice" step="0.01" required
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-2">Notes</label>
                                <textarea id="itemNotes" rows="2"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeProcessModal()"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg">Confirm</button>
                        </div>
        </form>
        </div>
        </div>
        </div>
        </div>

        <script>
            // Store processed items
            let processedItems = {};
            let currentProcessingItem = null;

            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({
                    theme: 'classic',
                    width: '100%'
                });

                // Handle batch option change
                $('#batchOption').on('change', function() {
                    if ($(this).val() === 'existing') {
                        $('#existingBatchDiv').removeClass('hidden');
                        $('#newBatchFields').addClass('hidden');
                        loadExistingBatches();
                    } else {
                        $('#existingBatchDiv').addClass('hidden');
                        $('#newBatchFields').removeClass('hidden');
                    }
                });

                // Handle barcode verification
                $('#verifyBarcode').on('change', function() {
                    const scannedBarcode = $(this).val();
                    const currentBarcode = $('#currentBarcode').val();

                    if (currentBarcode && scannedBarcode !== currentBarcode) {
                        Swal.fire({
                            title: 'Barcode Mismatch',
                            text: 'The scanned barcode does not match the product barcode. Do you want to update the product barcode?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Update Barcode',
                            cancelButtonText: 'Keep Original'
                        }).then((result) => {
                            if (!result.isConfirmed) {
                                $(this).val(currentBarcode);
                            }
                        });
                    }
                });

                // Handle item processing form submission
                $('#processItemForm').on('submit', function(e) {
                    e.preventDefault();

                    const formData = {
                        po_item_id: $('#processing_item_id').val(),
                        batch_option: $('#batchOption').val(),
                        batch_id: $('#batchOption').val() === 'existing' ? $('#existingBatch').val() : null,
                        batch_number: $('#batchNumber').val(),
                        expiry_date: $('#expiryDate').val(),
                        cost: $('#cost').val(),
                        selling_price: $('#sellingPrice').val(),
                        notes: $('#itemNotes').val(),
                        barcode: $('#verifyBarcode').val()
                    };

                    processedItems[formData.po_item_id] = formData;
                    updateItemStatus(formData.po_item_id, 'processed');
                    closeProcessModal();
                });

                // Handle GRN form submission
                $('#grnForm').on('submit', function(e) {
                    e.preventDefault();

                    // Check if all items are processed
                    const unprocessedItems = $('.item-status').filter(function() {
                        return $(this).text() === 'Pending';
                    });

                    if (unprocessedItems.length > 0) {
                        Swal.fire('Error', 'Please process all items before completing the GRN', 'error');
                        return;
                    }

                    const formData = new FormData(this);
                    formData.append('processed_items', JSON.stringify(processedItems));

                    $.ajax({
                        url: 'ajax/save_grn.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success',
                                    showCancelButton: true,
                                    confirmButtonText: 'Add Payment',
                                    cancelButtonText: 'Return to List'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = `index.php?show_payment=1&po_id=${data.po_id}`;
                                    } else {
                                        window.location.href = 'index.php';
                                    }
                                });
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    });
                });
            });

            function processItem(poItemId) {
                currentProcessingItem = poItemId;
                const row = $(`tr[data-item-id="${poItemId}"]`);
                const quantity = row.find('.receiving-qty').val();

                if (!quantity || quantity <= 0) {
                    Swal.fire('Error', 'Please enter a valid quantity', 'error');
                    return;
                }

                // Get item details via AJAX
                $.ajax({
                    url: 'ajax/get_item_details.php',
                    type: 'GET',
                    data: {
                        po_item_id: poItemId
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            populateProcessModal(data.item, quantity);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to get item details', 'error');
                    }
                });
            }

            function populateProcessModal(item, quantity) {
                $('#processing_item_id').val(item.po_item_id);
                $('#processProductName').text(item.product_name);
                $('#currentSku').val(item.sku || '');
                $('#currentBarcode').val(item.barcode || '');
                $('#verifyBarcode').val(item.barcode || '');
                $('#cost').val(item.unit_cost);

                // Calculate suggested selling price (e.g., cost + 30% margin)
                const suggestedPrice = parseFloat(item.unit_cost) * 1.3;
                $('#sellingPrice').val(suggestedPrice.toFixed(2));

                // Generate batch number suggestion
                const today = moment().format('YYYYMMDD');
                $('#batchNumber').val(`${today}-${item.po_item_id}`);

                $('#processItemModal').removeClass('hidden');
            }

            function loadExistingBatches() {
                if (!currentProcessingItem) return;

                $.ajax({
                    url: 'ajax/get_batches.php',
                    type: 'GET',
                    data: {
                        po_item_id: currentProcessingItem
                    },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            const select = $('#existingBatch');
                            select.empty();

                            data.batches.forEach(batch => {
                                select.append(`<option value="${batch.batch_id}">
                                ${batch.batch_number} (Stock: ${batch.quantity})
                            </option>`);
                            });
                        }
                    }
                });
            }

            function closeProcessModal() {
                $('#processItemModal').addClass('hidden');
                $('#processItemForm')[0].reset();
                currentProcessingItem = null;
            }

            function updateItemStatus(poItemId, status) {
                const row = $(`tr[data-item-id="${poItemId}"]`);
                const statusSpan = row.find('.item-status');
                const processBtn = row.find('.process-btn');

                if (status === 'processed') {
                    statusSpan.removeClass('bg-yellow-600').addClass('bg-green-600');
                    statusSpan.text('Processed');
                    processBtn.html('<i class="fas fa-edit"></i> Edit');
                } else {
                    statusSpan.removeClass('bg-green-600').addClass('bg-yellow-600');
                    statusSpan.text('Pending');
                    processBtn.html('<i class="fas fa-cog"></i> Process');
                }
            }

            function formatCurrency(amount) {
                return parseFloat(amount).toLocaleString('en-US', {
                    style: 'currency',
                    currency: 'USD'
                });
            }
        </script>
</body>

</html>-sm font-medium mb-1">Order Date</label>
<p><?php echo date('Y-m-d', strtotime($po['order_date'])); ?></p>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Delivery Date</label>
    <p><?php echo $po['delivery_date'] ? date('Y-m-d', strtotime($po['delivery_date'])) : '-'; ?></p>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Total Amount</label>
    <p><?php echo number_format($po['total_amount'], 2); ?></p>
</div>
<div>
    <label class="block text-sm font-medium mb-1">Items Remaining</label>
    <p><?php echo count($po_items); ?></p>
</div>
</div>
</div>
</div>
</div>

<!-- Items Table -->
<div class="mb-6">
    <h2 class="text-xl font-semibold mb-4">Receive Items</h2>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-700">
                <tr>
                    <th class="px-4 py-2 text-left">Product</th>
                    <th class="px-4 py-2 text-left">SKU/Barcode</th>
                    <th class="px-4 py-2 text-right">Ordered</th>
                    <th class="px-4 py-2 text-right">Remaining</th>
                    <th class="px-4 py-2 text-right">Receiving</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($po_items as $item): ?>
                    <tr class="border-b border-gray-700" data-item-id="<?php echo $item['po_item_id']; ?>">
                        <td class="px-4 py-2"><?php echo $item['product_name']; ?></td>
                        <td class="px-4 py-2">
                            <?php echo $item['sku'] ? "SKU: {$item['sku']}" : ''; ?>
                            <?php echo $item['barcode'] ? "<br>Barcode: {$item['barcode']}" : ''; ?>
                        </td>
                        <td class="px-4 py-2 text-right"><?php echo $item['quantity']; ?></td>
                        <td class="px-4 py-2 text-right"><?php echo $item['remaining_qty']; ?></td>
                        <td class="px-4 py-2">
                            <input type="number" class="receiving-qty w-24 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-right"
                                min="0" max="<?php echo $item['remaining_qty']; ?>" value="<?php echo $item['remaining_qty']; ?>">
                        </td>
                        <td class="px-4 py-2 text-center">
                            <span class="item-status bg-yellow-600 px-2 py-1 rounded-full text-xs">Pending</span>
                        </td>
                        <td class="px-4 py-2 text-center">
                            <button type="button" onclick="processItem(<?php echo $item['po_item_id']; ?>)"
                                class="process-btn text-blue-400 hover:text-blue-600">
                                <i class="fas fa-cog"></i> Process
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Notes -->
<div class="mb-6">
    <label class="block text-sm font-medium mb-2">Notes</label>
    <textarea name="notes" rows="3"
        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"></textarea>
</div>

<!-- Action Buttons -->
<div class="flex justify-end space-x-4">
    <button type="button" onclick="location.href='index.php'"
        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg">Cancel</button>
    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg">Complete GRN</button>
</div>
</form>
</main>

<!-- Process Item Modal -->
<div id="processItemModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold">Process Item</h3>
                    <button onclick="closeProcessModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="processItemForm">
                    <input type="hidden" id="processing_item_id">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-sm font-medium">Product</label>
                            <span id="processProductName" class="text-lg font-semibold"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Current SKU</label>
                                <input type="text" id="currentSku" readonly
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Current Barcode</label>
                                <input type="text" id="currentBarcode" readonly
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Order Date</label>
                            <p><?php echo date('Y-m-d', strtotime($po['order_date'])); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Delivery Date</label>
                            <p><?php echo $po['delivery_date'] ? date('Y-m-d', strtotime($po['delivery_date'])) : '-'; ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Total Amount</label>
                            <p><?php echo number_format($po['total_amount'], 2); ?></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Items Remaining</label>
                            <p><?php echo count($po_items); ?></p>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>