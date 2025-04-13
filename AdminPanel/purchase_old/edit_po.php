<?php include '../nav.php';
<?php
require_once 'inc/config.php';

// Get PO ID from URL
$poId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Validate PO exists and is in draft status
$query = "SELECT po.*, s.supplier_name 
          FROM purchase_orders po
          JOIN suppliers s ON s.supplier_id = po.supplier_id
          WHERE po.po_id = $poId AND po.status = 'draft'";
$result = mysqli_query($con, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    // Redirect if PO doesn't exist or is not editable
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
    <title>Edit Purchase Order - <?php echo $ERP_COMPANY_NAME; ?></title>
    
    <!-- CSS includes (same as create_po.php) -->
    <!-- ... -->
</head>
<body class="bg-gray-50">

<!-- Loading Overlay (same as create_po.php) -->
<!-- ... -->

<div class="min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Purchase Order - <?php echo htmlspecialchars($po['po_number']); ?>
                </h1>
                <a href="purchase_orders.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Main Form -->
        <form id="poForm" class="bg-white shadow-md rounded-lg p-6">
            <input type="hidden" id="poId" value="<?php echo $poId; ?>">
            
            <!-- Header Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="form-label" for="supplier">Supplier <span class="text-red-600">*</span></label>
                    <select id="supplier" name="supplier_id" class="form-input" required>
                        <option value="<?php echo $po['supplier_id']; ?>">
                            <?php echo htmlspecialchars($po['supplier_name']); ?>
                        </option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="orderDate">Order Date <span class="text-red-600">*</span></label>
                    <input type="date" id="orderDate" name="order_date" class="form-input" required
                           value="<?php echo $po['order_date']; ?>">
                </div>
                <div>
                    <label class="form-label" for="deliveryDate">Expected Delivery Date</label>
                    <input type="date" id="deliveryDate" name="delivery_date" class="form-input"
                           value="<?php echo $po['delivery_date']; ?>">
                </div>
            </div>

            <!-- Items Section (same structure as create_po.php) -->
            <div class="mb-6">
                <!-- ... -->
            </div>

            <!-- Notes Section -->
            <div class="mb-6">
                <label class="form-label" for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="form-input"><?php 
                    echo htmlspecialchars($po['notes']); 
                ?></textarea>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='purchase_orders.php'">
                    <i class="fas fa-times mr-2"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-2"></i> Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Item Modal (same as create_po.php) -->
<!-- ... -->

<!-- Scripts -->
<script>
$(document).ready(function() {
    // Initialize Select2 (similar to create_po.php)
    // ...

    // Load existing items
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
                    $('#itemsTable').append(`
                        <tr class="item-row" id="item_${itemCounter}">
                            <td class="px-4 py-2">
                                <div class="font-medium">${item.product_name}</div>
                                <div class="text-sm text-gray-500">SKU: ${item.sku}</div>
                                <input type="hidden" name="items[${itemCounter}][product_id]" 
                                       value="${item.product_id}">
                            </td>
                            <td class="px-4 py-2 text-right">
                                <input type="hidden" name="items[${itemCounter}][quantity]" 
                                       value="${item.quantity}">
                                ${item.quantity}
                            </td>
                            <td class="px-4 py-2 text-right">
                                <input type="hidden" name="items[${itemCounter}][unit_cost]" 
                                       value="${item.unit_cost}">
                                ${formatCurrency(item.unit_cost)}
                            </td>
                            <td class="px-4 py-2 text-right item-total" 
                                data-amount="${item.total_cost}">
                                ${formatCurrency(item.total_cost)}
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
                });

                // Set values
                $('#discountType').val(response.data.po.discount_type);
                $('#discountValue').val(response.data.po.discount_value);
                $('#taxType').val(response.data.po.tax_type);
                $('#taxValue').val(response.data.po.tax_value);
                $('#shippingFee').val(response.data.po.shipping_fee);

                $('#noItemsRow').remove();
                calculateTotals();
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

    // Submit form
    $('#poForm').on('submit', async function(e) {
        e.preventDefault();

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
            action: 'update_po',
            po_id: $('#poId').val(),
            supplier_id: $('#supplier').val(),
            order_date: $('#orderDate').val(),
            delivery_date: $('#deliveryDate').val(),
            shipping_fee: $('#shippingFee').val(),
            discount_type: $('#discountType').val(),
            discount_value: $('#discountValue').val(),
            tax_type: $('#taxType').val(),
            tax_value: $('#taxValue').val(),
            notes: $('#notes').val(),
            items: JSON.stringify(items)
        };

        try {
            showLoading();
            const response = await $.post('purchase.php', formData);

            if (response.status === 'success') {
                await Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
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
    });

    // Rest of the functions (calculateTotals, addItem, removeItem, etc.) remain the same as create_po.php
    // ...
});
</script>

</body>
</html>