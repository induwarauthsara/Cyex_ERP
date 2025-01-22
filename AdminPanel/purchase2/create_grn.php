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
            @apply bg-gray-800 border-gray-600 text-white rounded-lg;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            @apply text-white;
        }

        .select2-dropdown {
            @apply bg-gray-800 border-gray-600;
        }

        .select2-search__field {
            @apply bg-gray-700 text-white;
        }

        .select2-results__option {
            @apply text-white;
        }

        .select2-results__option--highlighted {
            @apply bg-gray-700;
        }

        /* Custom styles for tooltips */
        .tooltip {
            @apply invisible absolute;
        }

        .has-tooltip:hover .tooltip {
            @apply visible z-50;
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
                            <select id="supplier" name="supplier"
                                class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white"
                                required>
                                <option value="">Select Supplier</option>
                            </select>
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
                            <button type="button" onclick="addItem()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Add Item
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700" id="itemsTable">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Product</th>
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
                    <button type="submit"
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
            <td class="px-4 py-2">
                <select class="product-select w-full rounded-md bg-gray-700 border-gray-600 text-white" required>
                    <option value="">Select Product</option>
                </select>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="received-qty w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="1" value="1" required>
            </td>
            <td class="px-4 py-2">
                <input type="text" class="batch-number w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    required>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="cost w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" required>
            </td>
            <td class="px-4 py-2">
                <input type="number" class="selling-price w-full rounded-md bg-gray-700 border-gray-600 text-white"
                    min="0.01" step="0.01" required>
            </td>
            <td class="px-4 py-2">
                <input type="date" class="expiry-date w-full rounded-md bg-gray-700 border-gray-600 text-white">
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
            <td class="px-4 py-