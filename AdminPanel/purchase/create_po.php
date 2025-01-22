<?php
require_once('../nav.php');

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get next PO number
$sequence_query = "SELECT next_value, prefix, padding FROM sequences WHERE name = 'purchase_order'";
$sequence = mysqli_fetch_assoc(mysqli_query($con, $sequence_query));

if (!$sequence) {
    // Create sequence if it doesn't exist
    mysqli_query($con, "INSERT INTO sequences (name, prefix, next_value, padding) VALUES ('purchase_order', 'PO', 1, 5)");
    $sequence = ['prefix' => 'PO', 'next_value' => 1, 'padding' => 5];
}

$po_number = $sequence['prefix'] . str_pad($sequence['next_value'], $sequence['padding'], '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Purchase Order - <?php echo $ERP_COMPANY_NAME; ?></title>

    <!-- Include the same CSS and JS as index.php -->
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
                    <h1 class="text-2xl font-bold">Create Purchase Order</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <form id="purchaseOrderForm" class="bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- PO Details -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Purchase Order Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">PO Number</label>
                            <input type="text" value="<?php echo $po_number; ?>" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2" readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Order Date</label>
                            <input type="date" name="order_date" id="order_date" value="<?php echo date('Y-m-d'); ?>"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-2">Supplier</label>
                            <select name="supplier_id" id="supplier_id" class="select2 w-full" required>
                                <option value="">Select Supplier</option>
                                <?php
                                $suppliers = mysqli_query($con, "SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name");
                                while ($supplier = mysqli_fetch_assoc($suppliers)) {
                                    echo "<option value='{$supplier['supplier_id']}'>{$supplier['supplier_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-2">Delivery Date (Optional)</label>
                            <input type="date" name="delivery_date" id="delivery_date" min="<?php echo date('Y-m-d'); ?>"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                    </div>
                </div>

                <!-- Shipping & Additional Info -->
                <div>
                    <h2 class="text-xl font-semibold mb-4">Additional Details</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Shipping Fee</label>
                            <input type="number" name="shipping_fee" id="shipping_fee" value="0" min="0" step="0.01"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Discount Type</label>
                            <select name="discount_type" id="discount_type"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                <option value="fixed">Fixed Amount</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Discount Value</label>
                            <input type="number" name="discount_value" id="discount_value" value="0" min="0" step="0.01"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Tax Rate (%)</label>
                            <input type="number" name="tax_value" id="tax_value" value="0" min="0" max="100" step="0.01"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Order Items</h2>
                    <button type="button" onclick="addItem()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fas fa-plus mr-2"></i> Add Item
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-right">Quantity</th>
                                <th class="px-4 py-2 text-right">Unit Cost</th>
                                <th class="px-4 py-2 text-right">Total</th>
                                <th class="px-4 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTable">
                            <!-- Items will be added here dynamically -->
                        </tbody>
                        <tfoot class="bg-gray-700">
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                <td class="px-4 py-2 text-right" id="subtotal">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold">Discount:</td>
                                <td class="px-4 py-2 text-right" id="totalDiscount">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold">Tax:</td>
                                <td class="px-4 py-2 text-right" id="totalTax">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold">Shipping:</td>
                                <td class="px-4 py-2 text-right" id="shippingDisplay">0.00</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-right font-semibold">Total:</td>
                                <td class="px-4 py-2 text-right font-bold" id="grandTotal">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium mb-2">Notes</label>
                <textarea name="notes" id="notes" rows="3"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="location.href='index.php'"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg">
                    Cancel
                </button>
                <button type="submit" name="status" value="draft"
                    class="px-6 py-2 bg-gray-500 hover:bg-gray-600 rounded-lg">
                    Save as Draft
                </button>
                <button type="submit" name="status" value="pending"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg">
                    Submit for Approval
                </button>
            </div>
        </form>
    </main>

    <!-- Product Selection Modal -->
    <div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Select Product</h3>
                        <button onclick="closeProductModal()" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <input type="text" id="productSearch" placeholder="Search by name, barcode, or SKU..."
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                    </div>
                    <div class="overflow-y-auto max-h-96">
                        <table class="w-full">
                            <thead class="bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">Name</th>
                                    <th class="px-4 py-2 text-left">SKU</th>
                                    <th class="px-4 py-2 text-right">Last Cost</th>
                                    <th class="px-4 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="productList">
                                <!-- Products will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let items = [];
        let currentItemIndex = null;

        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'classic',
                width: '100%'
            });

            // Initialize product search
            $('#productSearch').on('keyup', function() {
                const search = $(this).val().trim();
                if (search.length >= 2) {
                    searchProducts(search);
                }
            });

            // Form submission
            $('#purchaseOrderForm').on('submit', function(e) {
                e.preventDefault();

                if (items.length === 0) {
                    Swal.fire('Error', 'Please add at least one item to the order', 'error');
                    return;
                }

                const formData = new FormData(this);
                formData.append('items', JSON.stringify(items));
                formData.append('po_number', '<?php echo $po_number; ?>');

                $.ajax({
                    url: 'ajax/save_po.php',
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
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                window.location.href = 'index.php';
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

            // Watch for changes that affect totals
            $('#shipping_fee, #discount_type, #discount_value, #tax_value').on('change', updateTotals);
        });

        function searchProducts(search) {
            $.ajax({
                url: 'ajax/search_products.php',
                type: 'GET',
                data: {
                    search: search
                },
                success: function(response) {
                    const products = JSON.parse(response);
                    let html = '';

                    products.forEach(product => {
                        html += `
                            <tr>
                                <td class="px-4 py-2">${product.product_name}</td>
                                <td class="px-4 py-2">${product.sku || '-'}</td>
                                <td class="px-4 py-2 text-right">${formatCurrency(product.cost)}</td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" onclick='selectProduct(${JSON.stringify(product)})' 
                                            class="text-blue-400 hover:text-blue-600">
                                        <i class="fas fa-plus-circle"></i>
                                    </button>
                                </td>
                            </tr>`;
                    });

                    $('#productList').html(html || '<tr><td colspan="4" class="px-4 py-2 text-center">No products found</td></tr>');
                }
            });
        }

        function selectProduct(product) {
            const index = currentItemIndex !== null ? currentItemIndex : items.length;

            if (currentItemIndex === null) {
                items.push({
                    product_id: product.product_id,
                    product_name: product.product_name,
                    quantity: 1,
                    unit_cost: product.cost,
                    total: product.cost
                });
            } else {
                items[currentItemIndex] = {
                    product_id: product.product_id,
                    product_name: product.product_name,
                    quantity: 1,
                    unit_cost: product.cost,
                    total: product.cost
                };
                currentItemIndex = null;
            }

            closeProductModal();
            renderItems();
            updateTotals();
        }

        function addItem() {
            currentItemIndex = null;
            openProductModal();
        }

        function editItem(index) {
            currentItemIndex = index;
            openProductModal();
        }

        function removeItem(index) {
            items.splice(index, 1);
            renderItems();
            updateTotals();
        }

        function updateItemQuantity(index, quantity) {
            items[index].quantity = parseFloat(quantity);
            items[index].total = items[index].quantity * items[index].unit_cost;
            renderItems();
            updateTotals();
        }

        function updateItemCost(index, cost) {
            items[index].unit_cost = parseFloat(cost);
            items[index].total = items[index].quantity * items[index].unit_cost;
            renderItems();
            updateTotals();
        }

        function renderItems() {
            let html = '';
            items.forEach((item, index) => {
                html += `
                    <tr>
                        <td class="px-4 py-2">${item.product_name}</td>
                        <td class="px-4 py-2">
                            <input type="number" value="${item.quantity}" min="1" 
                                   onchange="updateItemQuantity(${index}, this.value)"
                                   class="w-24 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-right">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" value="${item.unit_cost}" step="0.01" min="0"
                                   onchange="updateItemCost(${index}, this.value)"
                                   class="w-32 bg-gray-700 border border-gray-600 rounded px-2 py-1 text-right">
                        </td>
                        <td class="px-4 py-2 text-right">${formatCurrency(item.total)}</td>
                        <td class="px-4 py-2 text-center">
                            <button type="button" onclick="editItem(${index})" class="text-yellow-400 hover:text-yellow-600 mr-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" onclick="removeItem(${index})" class="text-red-400 hover:text-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
            });

            $('#itemsTable').html(html || '<tr><td colspan="5" class="px-4 py-2 text-center">No items added</td></tr>');
        }

        function updateTotals() {
            const subtotal = items.reduce((sum, item) => sum + item.total, 0);
            const shippingFee = parseFloat($('#shipping_fee').val()) || 0;
            const discountType = $('#discount_type').val();
            const discountValue = parseFloat($('#discount_value').val()) || 0;
            const taxRate = parseFloat($('#tax_value').val()) || 0;

            let discount = 0;
            if (discountType === 'percentage') {
                discount = subtotal * (discountValue / 100);
            } else {
                discount = discountValue;
            }

            const afterDiscount = subtotal - discount;
            const tax = afterDiscount * (taxRate / 100);
            const total = afterDiscount + tax + shippingFee;

            $('#subtotal').text(formatCurrency(subtotal));
            $('#totalDiscount').text(formatCurrency(discount));
            $('#totalTax').text(formatCurrency(tax));
            $('#shippingDisplay').text(formatCurrency(shippingFee));
            $('#grandTotal').text(formatCurrency(total));
        }

        function openProductModal() {
            $('#productModal').removeClass('hidden');
            $('#productSearch').val('').focus();
            $('#productList').html('');
        }

        function closeProductModal() {
            $('#productModal').addClass('hidden');
        }

        function formatCurrency(amount) {
            return parseFloat(amount).toLocaleString('en-US', {
                style: 'currency',
                currency: 'USD'
            });
        }
        // Close modal when clicking outside
        $(document).mouseup(function(e) {
            const modal = $("#productModal");
            if (!modal.hasClass('hidden') && !modal.find('.bg-gray-800').get(0).contains(e.target)) {
            closeProductModal();
            }
        });
        </script>
</body>
</html>