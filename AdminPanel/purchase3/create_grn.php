<!-- File: /AdminPanel/purchase/create_grn.php -->
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
$grnId = $isEdit ? intval($_GET['id']) : null;
$poId = isset($_GET['po_id']) ? intval($_GET['po_id']) : null;

$grn = null;
$po = null;

if ($isEdit) {
    // Fetch GRN details if editing
    $stmt = mysqli_prepare(
        $con,
        "SELECT grn.*, po.po_number, s.supplier_name 
         FROM goods_receipt_notes grn
         JOIN purchase_orders po ON grn.po_id = po.po_id
         JOIN suppliers s ON po.supplier_id = s.supplier_id
         WHERE grn_id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $grnId);
    mysqli_stmt_execute($stmt);
    $grn = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$grn || $grn['status'] !== 'draft') {
        header('Location: index.php');
        exit();
    }

    $poId = $grn['po_id'];
}

// Fetch PO details
if ($poId) {
    $stmt = mysqli_prepare(
        $con,
        "SELECT po.*, s.supplier_name, s.supplier_tel, s.supplier_address
         FROM purchase_orders po
         JOIN suppliers s ON po.supplier_id = s.supplier_id
         WHERE po_id = ? AND po.status = 'ordered'"
    );
    mysqli_stmt_bind_param($stmt, 'i', $poId);
    mysqli_stmt_execute($stmt);
    $po = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$po && !$isEdit) {
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
    <title><?php echo $isEdit ? 'Edit' : 'Create'; ?> GRN - <?php echo $ERP_COMPANY_NAME; ?></title>

    <?php include '../includes/cdn_includes.php'; ?>
</head>

<body class="dark:bg-gray-900">
    <?php include '../../inc/header.php'; ?>

    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white">
                <i class="fas fa-truck-loading mr-2"></i>
                <?php echo $isEdit ? 'Edit GRN' : 'Create GRN'; ?>
            </h1>
            <button onclick="history.back()" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </button>
        </div>

        <form id="grnForm" class="space-y-6">
            <input type="hidden" id="grnId" value="<?php echo $grnId; ?>">
            <input type="hidden" id="poId" value="<?php echo $poId; ?>">

            <!-- Main Details Card -->
            <div class="card">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 dark:text-white">Purchase Order Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="form-label">PO Number</label>
                                <input type="text" class="form-input bg-gray-100" readonly
                                    value="<?php echo $po['po_number']; ?>">
                            </div>
                            <div>
                                <label class="form-label">Supplier</label>
                                <input type="text" class="form-input bg-gray-100" readonly
                                    value="<?php echo $po['supplier_name']; ?>">
                            </div>
                            <div>
                                <label class="form-label">Order Date</label>
                                <input type="text" class="form-input bg-gray-100" readonly
                                    value="<?php echo date('Y-m-d', strtotime($po['order_date'])); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4 dark:text-white">GRN Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="form-label required" for="receiptDate">Receipt Date</label>
                                <input type="date" id="receiptDate" class="form-input" required
                                    value="<?php echo $isEdit ? $grn['receipt_date'] : date('Y-m-d'); ?>">
                            </div>
                            <div>
                                <label class="form-label" for="invoiceNumber">Invoice Number</label>
                                <input type="text" id="invoiceNumber" class="form-input"
                                    value="<?php echo $isEdit ? $grn['invoice_number'] : ''; ?>">
                            </div>
                            <div>
                                <label class="form-label" for="invoiceDate">Invoice Date</label>
                                <input type="date" id="invoiceDate" class="form-input"
                                    value="<?php echo $isEdit ? $grn['invoice_date'] : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Card -->
            <div class="card">
                <h3 class="text-lg font-semibold mb-4 dark:text-white">Items</h3>
                <div class="overflow-x-auto">
                    <table id="itemsTable" class="w-full">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Ordered Qty</th>
                                <th>Received Qty</th>
                                <th>Cost</th>
                                <th>Selling Price</th>
                                <th>Batch Number</th>
                                <th>Expiry Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items will be loaded dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes Card -->
            <div class="card">
                <label class="form-label" for="notes">Notes</label>
                <textarea id="notes" class="form-input" rows="3"><?php echo $isEdit ? htmlspecialchars($grn['notes']) : ''; ?></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-4">
                <button type="button" id="saveDraftBtn" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                    <i class="fas fa-save mr-2"></i> Save as Draft
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check mr-2"></i> Complete GRN
                </button>
            </div>
        </form>
    </div>

    <script type="module" src="./js/create_grn.js"></script>
</body>

</html>