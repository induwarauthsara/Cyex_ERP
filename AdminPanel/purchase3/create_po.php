<!-- File: /AdminPanel/purchase/create_po.php -->
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

$isEdit = isset($_GET['id']);
$poId = $isEdit ? intval($_GET['id']) : null;
$po = null;

if ($isEdit) {
    // Fetch PO details if editing
    $stmt = mysqli_prepare(
        $con,
        "SELECT po.*, s.supplier_name 
         FROM purchase_orders po
         JOIN suppliers s ON po.supplier_id = s.supplier_id
         WHERE po_id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $poId);
    mysqli_stmt_execute($stmt);
    $po = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$po || !in_array($po['status'], ['draft', 'pending'])) {
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Create'; ?> Purchase Order - <?php echo $ERP_COMPANY_NAME; ?></title>

    <?php include '../includes/cdn_includes.php'; ?>
    <link rel="stylesheet" href="css/purchase_orders.css">
</head>

<body class="dark:bg-gray-900">
    <?php include '../../inc/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white">
                <i class="fas fa-shopping-cart mr-2"></i>
                <?php echo $isEdit ? 'Edit Purchase Order' : 'Create Purchase Order'; ?>
            </h1>
            <button onclick="history.back()" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>
        </div>

        <form id="poForm" class="space-y-6">
            <input type="hidden" id="poId" value="<?php echo $poId; ?>">

            <!-- Main Details Card -->
            <div class="card">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Supplier Selection -->
                    <div>
                        <label class="form-label required" for="supplierId">Supplier</label>
                        <select id="supplierId" class="form-input" required
                            <?php echo $isEdit ? 'disabled' : ''; ?>>
                            <?php if ($isEdit): ?>
                                <option value="<?php echo $po['supplier_id']; ?>" selected>
                                    <?php echo htmlspecialchars($po['supplier_name']); ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Order Date -->
                    <div>
                        <label class="form-label required" for="orderDate">Order Date</label>
                        <input type="date" id="orderDate" class="form-input" required
                            value="<?php echo $isEdit ? $po['order_date'] : date('Y-m-d'); ?>">
                    </div>

                    <!-- Delivery Date -->
                    <div>
                        <label class="form-label" for="deliveryDate">Expected Delivery Date</label>
                        <input type="date" id="deliveryDate" class="form-input"
                            value="<?php echo $isEdit ? $po['delivery_date'] : ''; ?>">
                    </div>
                </div>
            </div>

            <!-- Products Card -->
            <div class="card">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold dark:text-white">Products</h2>
                    <button type="button" id="addProductBtn" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Add Product
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table id="productsTable" class="w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Quantity</th>
                                <th class="px-4 py-2">Unit Cost</th>
                                <th class="px-4 py-2">Total</th>
                                <th class="px-4 py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Products will be added here dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Totals Section -->
                <div class="mt-4 space-y-2">
                    <div class="flex justify-end items-center">
                        <span class="mr-4">Subtotal:</span>
                        <span id="subtotal" class="font-semibold">Rs. 0.00</span>
                    </div>

                    <div class="flex justify-end items-center">
                        <label class="mr-2" for="discountType">Discount:</label>
                        <select id="discountType" class="form-input w-32 mr-2">
                            <option value="fixed">Fixed</option>
                            <option value="percentage">Percentage</option>
                        </select>
                        <input type="number" id="discountValue" class="form-input w-32" min="0" step="0.01" value="0">
                    </div>

                    <div class="flex justify-end items-center">
                        <label class="mr-2" for="shippingFee">Shipping Fee:</label>
                        <input type="number" id="shippingFee" class="form-input w-32" min="0" step="0.01" value="0">
                    </div>

                    <div class="flex justify-end items-center font-bold">
                        <span class="mr-4">Total:</span>
                        <span id="total" class="text-xl">Rs. 0.00</span>
                    </div>
                </div>
            </div>

            <!-- Notes Card -->
            <div class="card">
                <label class="form-label" for="notes">Notes</label>
                <textarea id="notes" class="form-input" rows="3"><?php echo $isEdit ? htmlspecialchars($po['notes']) : ''; ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4">
                <button type="button" id="saveDraftBtn" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                    <i class="fas fa-save mr-2"></i> Save as Draft
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Submit
                </button>
            </div>
        </form>
    </div>

    <!-- Product Selection Modal -->
    <div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-2xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold dark:text-white">Add Product</h3>
                </div>
                <div class="p-4">
                    <div class="mb-4">
                        <label class="form-label" for="productSearch">Search Product</label>
                        <select id="productSearch" class="form-input"></select>
                    </div>
                    <div id="productDetails" class="hidden space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label" for="modalQuantity">Quantity</label>
                                <input type="number" id="modalQuantity" class="form-input" min="1" value="1">
                            </div>
                            <div>
                                <label class="form-label" for="modalUnitCost">Unit Cost</label>
                                <input type="number" id="modalUnitCost" class="form-input" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
                    <button type="button" class="btn bg-gray-500 hover:bg-gray-600 text-white" onclick="closeProductModal()">
                        Cancel
                    </button>
                    <button type="button" id="addProductConfirmBtn" class="btn btn-primary">
                        Add Product
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script type="module" src="js/create_po.js"></script>
</body>

</html>