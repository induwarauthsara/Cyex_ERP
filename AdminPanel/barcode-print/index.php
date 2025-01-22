<!-- File: /AdminPanel/barcode-print/index.php -->
<?php
// require_once '../../inc/config.php';
include '../nav.php';

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

// Get template data if editing
$templateId = isset($_GET['template_id']) ? intval($_GET['template_id']) : null;
$template = null;

if ($templateId) {
    $stmt = mysqli_prepare($con, "SELECT * FROM barcode_templates WHERE template_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $templateId);
    mysqli_stmt_execute($stmt);
    $template = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// Get GRN ID if coming from GRN
$grnId = isset($_GET['grn_id']) ? intval($_GET['grn_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Printing - <?php echo $ERP_COMPANY_NAME; ?></title>

    <?php include '../includes/cdn_includes.php'; ?>
</head>

<body class="dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white">
                <i class="fas fa-barcode mr-2"></i> Barcode Printing
            </h1>
            <div class="flex gap-2">
                <button id="previewBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                    <i class="fas fa-eye mr-2"></i> Preview
                </button>
                <button id="printBtn" class="btn bg-green-500 hover:bg-green-600 text-white">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Settings -->
            <div class="md:col-span-1 space-y-6">
                <!-- Paper Settings -->
                <div class="card">
                    <h2 class="text-xl font-semibold mb-4 dark:text-white">Paper Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label required" for="paperSize">Paper Size</label>
                            <select id="paperSize" class="form-input" required>
                                <option value="36">36mm (1.4 Inch)</option>
                                <option value="24">24mm (0.94 Inch)</option>
                                <option value="18">18mm (0.7 Inch)</option>
                                <option value="30x15">30x15mm 3 up</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="margin">Margin (mm)</label>
                            <input type="number" id="margin" class="form-input" value="2" min="0" max="10" step="0.5">
                        </div>
                        <div>
                            <label class="form-label" for="gapBetween">Gap Between Labels (mm)</label>
                            <input type="number" id="gapBetween" class="form-input" value="0" min="0" max="5" step="0.5">
                        </div>
                    </div>
                </div>

                <!-- Content Settings -->
                <div class="card">
                    <h2 class="text-xl font-semibold mb-4 dark:text-white">Content Settings</h2>
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="showShopName" class="form-checkbox" checked disabled>
                            <label for="showShopName">Shop Name</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="showProductName" class="form-checkbox" checked disabled>
                            <label for="showProductName">Product Name</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="showPrice" class="form-checkbox" checked>
                            <label for="showPrice">Price</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="showUnit" class="form-checkbox">
                            <label for="showUnit">Unit</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="showCategory" class="form-checkbox">
                            <label for="showCategory">Category</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="showPromoPrice" class="form-checkbox">
                            <label for="showPromoPrice">Promotional Price</label>
                        </div>
                    </div>
                </div>

                <!-- Font Settings -->
                <div class="card">
                    <h2 class="text-xl font-semibold mb-4 dark:text-white">Font Settings</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label" for="fontSize">Base Font Size (pt)</label>
                            <input type="number" id="fontSize" class="form-input" value="8" min="6" max="12" step="0.5">
                        </div>
                        <div>
                            <label class="form-label" for="barcodeHeight">Barcode Height (mm)</label>
                            <input type="number" id="barcodeHeight" class="form-input" value="10" min="5" max="20">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Items -->
            <div class="md:col-span-2 space-y-6">
                <!-- Search and Add -->
                <div class="card">
                    <div class="flex gap-4">
                        <div class="flex-grow">
                            <select id="productSearch" class="form-input w-full" placeholder="Search products..."></select>
                        </div>
                        <button id="addProductBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                            <i class="fas fa-plus mr-2"></i> Add
                        </button>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card">
                    <div class="overflow-x-auto">
                        <table id="itemsTable" class="w-full">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Batch</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Template Actions -->
                <div class="card">
                    <div class="flex justify-between items-center">
                        <div>
                            <label class="form-label" for="templateName">Save as Template</label>
                            <div class="flex gap-2">
                                <input type="text" id="templateName" class="form-input"
                                    placeholder="Enter template name">
                                <button id="saveTemplateBtn" class="btn bg-purple-500 hover:bg-purple-600 text-white">
                                    <i class="fas fa-save mr-2"></i> Save
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Load Template</label>
                            <select id="loadTemplate" class="form-input">
                                <option value="">Select Template</option>
                                <?php
                                $query = "SELECT template_id, template_name FROM barcode_templates ORDER BY template_name";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['template_id']}'>{$row['template_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-4xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Preview</h3>
                    <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div id="previewContent" class="bg-white overflow-auto" style="min-height: 400px;">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
                <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
                    <button onclick="closePreviewModal()" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                        Close
                    </button>
                    <button onclick="printBarcodes()" class="btn bg-green-500 hover:bg-green-600 text-white">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="grnId" value="<?php echo $grnId; ?>">
    <script type="module" src="./js/barcode_print.js"></script>
</body>

</html>