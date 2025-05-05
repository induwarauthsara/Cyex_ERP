<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, width=device-width, initial-scale=1.0">
    <title>Create Product - Advanced POS System</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Select2 for enhanced dropdowns -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-top: 20px;
        }

        .form-label {
            font-weight: 500;
            color: #343a40;
        }

        .form-control,
        .form-select {
            border-radius: 6px;
            border: 1px solid #ced4da;
            box-shadow: none !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25) !important;
        }

        .select2-container--open .select2-selection--single {
            border-color: #4a90e2 !important;
            box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25) !important;
        }

        .btn-action {
            background-color: #4a90e2;
            color: white;
            margin-left: 5px;
            margin-top: 5px;
        }

        .btn-action:hover {
            background-color: #357abd;
        }

        .btn-add {
            background-color: #28a745;
            color: white;
            margin-left: 5px;
        }

        .btn-add:hover {
            background-color: #1f7d33;
        }

        .select2-container {
            width: 100% !important;
        }

        .variant-table {
            background-color: #f8f9fa;
        }

        .hidden {
            display: none !important;
        }

        .image-preview-container {
            width: 300px;
            height: 300px;
            border: 1px dashed #ced4da;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .image-preview-container img {
            max-width: 100%;
            max-height: 100%;
        }

        .select2-dropdown--below {
            z-index: 100000;
        }

        #loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: #555;
            background: rgba(255, 190, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 9999999999;
            top: 182px;
        }

        /* Add these styles to improve the visual hierarchy */
        .card-header {
            background: linear-gradient(to right, #4a90e2, #5a9de2);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 12px 20px;
        }

        .form-section-title {
            color: #4a90e2;
            font-weight: 600;
            margin: 15px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #e0e0e0;
        }

        /* Improved form controls */
        .form-control,
        .form-select {
            transition: box-shadow 0.15s ease-in-out, border-color 0.15s ease-in-out;
            padding: 10px 12px;
        }

        /* Improved focus state */
        .form-control:focus,
        .form-select:focus,
        .select2-container--focus .select2-selection {
            border-color: #4a90e2;
            box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25) !important;
        }

        /* Tooltips for better UX */
        .tooltip-icon {
            margin-left: 5px;
            color: #6c757d;
            cursor: help;
        }

        /* Required field indicator */
        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }

        /* Improved buttons */
        .btn-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: #3a80d2;
            border-color: #3a80d2;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Improved variation table */
        .variant-table th {
            background-color: #f0f7ff;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .variant-row:hover {
            background-color: #f8f9fa;
        }

        /* Better card appearance */
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2 class="mb-4 text-center">Create New Product</h2>
            <form id="productForm">
                <!-- Product Type Selection -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="productType" class="form-label" required-field>Product Type</label>
                        <select id="productType" class="form-select" required>
                            <option value="standard">Standard Product</option>
                            <option value="combo">Combo Product</option>
                            <option value="digital">Digital Product</option>
                            <option value="service">Service </option>
                        </select>
                    </div>
                </div>

                <div class="progress mb-3">
                    <div class="progress-bar" id="form-progress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                </div>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Basic Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="productName" class="form-label required-field">Product Name</label>
                                    <input type="text" class="form-control" id="productName" required>
                                </div>

                                <div class="mb-3">
                                    <label for="productCode" class="form-label required-field">Product Code / Barcode</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="productCode" required maxlength="13">
                                        <button type="button" class="btn btn-secondary" id="generateCodeBtn">
                                            <i class="fas fa-random"></i> Generate
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="barcodeSymbology" class="form-label required-field">
                                        Barcode Symbology
                                        <i class="fas fa-question-circle tooltip-icon"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Select the barcode type that fits your scanning equipment"></i>
                                    </label>
                                    <select class="form-select" id="barcodeSymbology" name="barcodeSymbology">
                                        <option value="CODE128">Code 128</option>
                                        <option value="EAN13">EAN-13</option>
                                        <option value="EAN8">EAN-8</option>
                                        <option value="UPC">UPC</option>
                                        <option value="CODE39">Code 39</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Classification</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="brand" class="form-label required-field">Brand</label>
                                    <div class="input-group">
                                        <select id="brand" class="form-select" required>
                                            <option value="">Select Brand</option>
                                        </select>
                                        <button type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                                            Add New Brand
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label required-field">Category</label>
                                    <div class="input-group">
                                        <select id="category" class="form-select" required>
                                            <option value="">Select Category</option>
                                        </select>
                                        <button type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                            Add New Category
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3" id="skuSection">
                                    <label for="sku" class="form-label required-field">SKU<i class="fas fa-question-circle tooltip-icon"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Stock Keeping Unit. Unique identifier for the product"></i></label>
                                    <input type=" text" class="form-control" id="sku" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Pricing & Inventory</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3" id="initialStockSection">
                                    <label for="initialStock" class="form-label">Initial Stock Quantity</label>
                                    <input type="number" class="form-control" id="initialStock" step="any">
                                </div>

                                <!-- Add Alert Quantity field here -->
                                <div class="mb-3" id="alertQuantitySection">
                                    <label for="alertQuantity" class="form-label">
                                        Alert Quantity <small>(Low stock warning threshold)</small>
                                        <i class="fas fa-question-circle tooltip-icon"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="System will send notifications when stock falls below this value"></i>
                                    </label>
                                    <input type="number" class="form-control" id="alertQuantity" min="0" value="5" step="any">
                                    <small class="text-muted">System will alert when stock falls below this value</small>
                                </div>

                                <div class="mb-3">
                                    <label for="cost" class="form-label required-field">Cost</label>
                                    <input type="number" class="form-control" id="cost" required step="any">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="sellingPrice" class="form-label required-field">Selling Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number" class="form-control" id="sellingPrice" name="sellingPrice" step="any" min="0" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="discountPrice" class="form-label">Discount Price <small>(optional)</small></label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number" class="form-control" id="discountPrice" name="discountPrice" step="any" min="0" placeholder="Optional">
                                            <button class="btn btn-outline-secondary discount-percent-toggle" type="button">
                                                <i class="fas fa-percentage"></i>
                                            </button>
                                        </div>
                                        <div id="discountPercentage" class="text-success small mt-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3" id="unitSectionCard">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Units & Measurement</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3" id="unitSection">
                                    <label for="unitOfMeasurement" class="form-label">Units</label>
                                    <div class="row">
                                        <div class="col">
                                            <select id="defaultUnit" class="form-select">
                                                <option value="">Select Default Unit</option>
                                                <option value="pcs">Pcs</option>
                                                <option value="kg">Kg</option>
                                                <option value="gm">Grams</option>
                                                <option value="ml">ML</option>
                                                <option value="meter">Meter</option>
                                            </select>
                                            <small class="text-muted">Default Unit</small>
                                        </div>
                                        <div class="col">
                                            <select id="saleUnit" class="form-select">
                                            </select>
                                            <small class="text-muted">Sale Unit</small>
                                        </div>
                                        <div class="col">
                                            <select id="purchaseUnit" class="form-select">
                                            </select>
                                            <small class="text-muted">Purchase Unit</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 hidden" id="pcsPerBoxDiv">
                                    <label for="pcsPerBox" class="form-label">Pcs per Box</label>
                                    <input type="number" class="form-control" id="pcsPerBox" value="1">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="ecommerceSection">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="showInEcommerce">
                                <label class="form-check-label" for="showInEcommerce">Show in E-Commerce</label>
                            </div>
                            <div id="imageUploadSection" class="hidden mt-2">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="productImage">
                                <div class="image-preview-container">
                                    <img id="imagePreview" src="#" alt="Image Preview" class="hidden">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add a real-time preview card for product info -->
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Product Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div id="productImagePreviewCard" class="text-center">
                                    <img id="previewProductImage" src="../../assets/img/no-image.png" alt="Product Preview" class="img-fluid mb-2" style="max-height: 150px;">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h4 id="previewProductName">Product Name</h4>
                                <p id="previewProductCode" class="text-muted"><small>Code: </small></p>
                                <p id="previewSKU" class="text-muted"><small>SKU: </small></p>
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>Category:</strong> <span id="previewCategory"></span></p>
                                        <p><strong>Brand:</strong> <span id="previewBrand"></span></p>
                                    </div>
                                    <div class="col-6">
                                        <p><strong>Type:</strong> <span id="previewType"></span></p>
                                        <div id="previewPriceSection">
                                            <p class="mb-0"><strong>Regular Price:</strong> <span id="previewRegularPrice">Rs. 0.00</span></p>
                                            <p class="text-success" id="previewDiscountSection" style="display: none;">
                                                <strong>Discount Price:</strong> <span id="previewDiscountPrice">Rs. 0.00</span>
                                                <span id="previewDiscountPercent" class="badge bg-success ms-2"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Variant Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-switch mb-3 d-flex align-items-center">
                            <label class="form-check-label me-3" for="hasVariant">This Product Has Variants</label>
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="hasVariant" style="width: 3.5em; height: 2em;">
                            </div>
                        </div>

                        <div id="variantTableContainer" class="hidden">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Product Variants</h5>
                                    <button type="button" id="addVariantBtn" class="btn btn-add btn-sm">
                                        <i class="fas fa-plus"></i> Add Variant
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="variantTable" class="table variant-table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 150px;">Variation Name</th>
                                                    <th style="min-width: 150px;">Additional Note</th>
                                                    <th style="min-width: 100px;">Initial Stock</th>
                                                    <th style="min-width: 100px;">Cost</th>
                                                    <th style="min-width: 120px;">Selling Price</th>
                                                    <th style="min-width: 120px;">Discount Price</th>
                                                    <th style="min-width: 100px;">Low stock Alert Qty</th>
                                                    <th style="width: 80px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="variantTableBody">
                                                <!-- Dynamic variant rows will be added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="variant-summary p-3 border-top">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p class="mb-0"><strong>Total Variants:</strong> <span id="variantCount">0</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-0"><strong>Total Stock:</strong> <span id="variantTotalStock">0</span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-0"><strong>Price Range:</strong> <span id="variantPriceRange">Rs. 0.00</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combo Product Section -->
                <div id="comboProductSection" class="hidden">
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Combo Product Composition</h5>
                                    <button type="button" id="addComboProductBtn" class="btn btn-add btn-sm">
                                        <i class="fas fa-plus"></i> Add Product
                                    </button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table variant-table table-striped">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 150px;">Product Name</th>
                                                    <th style="min-width: 120px;">Batch Name</th>
                                                    <th style="min-width: 100px;">Unit Cost</th>
                                                    <th style="min-width: 120px;">Unit Selling Price</th>
                                                    <th style="min-width: 100px;">Quantity</th>
                                                    <th style="min-width: 100px;">Total Cost</th>
                                                    <th style="min-width: 120px;">Total Selling Price</th>
                                                    <th style="width: 80px;">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="comboProductTableBody">
                                                <!-- Dynamic combo product rows will be added here -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="variant-summary p-3 border-top">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-0"><strong>Total Products:</strong> <span id="comboProductCount">0</span></p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <p class="mb-0"><strong>Total Combo Cost: </strong><span id="totalComboCost">Rs. 0.00</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modals for Adding New Items -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">Add New Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="newBrandName" placeholder="Brand Name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNewBrand">Save Brand</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" id="newCategoryName" placeholder="Category Name" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveNewCategory">Save Category</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loader" style="display: none;">Loading Product Data...</div>

    <!-- Modal for adding combo product -->
    <div class="modal fade" id="addComboProductModal" tabindex="-1" aria-labelledby="addComboProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addComboProductModalLabel">Add Product to Combo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comboProductSelect" class="form-label">Select Product</label>
                        <select class="form-select" id="comboProductSelect">
                            <option value="">Select Product</option>
                        </select>
                    </div>
                    <div id="batchSection" class="hidden">
                        <label for="batchSelect" class="form-label">Select Batch</label>
                        <select class="form-select" id="batchSelect">
                            <option value="">Select Batch</option>
                        </select>
                        <div class="mt-2">
                            <strong>Batch Details:</strong>
                            <p id="batchDetails" class="text-muted"></p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="comboProductQty" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="comboProductQty" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addSelectedComboProduct">Add Product</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this discount percentage modal -->
    <div class="modal fade" id="discountPercentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Set Discount Percentage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="discountPercentInput" class="form-label">Discount Percentage</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="discountPercentInput" min="0" max="100" value="0">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="discount-preview">
                        <p class="mb-1">Regular Price: <strong>Rs. <span id="regularPricePreview">0.00</span></strong></p>
                        <p class="mb-1">Discount Amount: <strong>Rs. <span id="discountAmountPreview">0.00</span></strong></p>
                        <p class="mb-0">Final Price: <strong class="text-success">Rs. <span id="finalPricePreview">0.00</span></strong></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyPercentDiscount">Apply Discount</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include necessary scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            // Focus Barcode input when Scan button is pressed
            $(document).on('keydown', function(event) {
                if (event.key === "Insert") {
                    $('#productCode').focus();
                }
            });

            // Initialize Select2 for all selects except barcode symbology
            $('select:not(#barcodeSymbology)').select2();
            
            // Initialize product type based on default selection
            handleProductTypeChange($('#productType').val());
            
            loadInitialData(); // Load initial options (brands, categories, suppliers)
            // Load Product list for combo product
            loadProductsForCombo();
            
            // REMOVE the earlier general select2 initialization and reinitialize each specific select
            // Initialize each select separately to avoid conflicts
            $('#brand').select2();
            $('#category').select2();
            $('#productType').select2();
            $('#defaultUnit').select2();
            $('#saleUnit').select2();
            $('#purchaseUnit').select2();
            
            // Special initialization for the combo product select
            setTimeout(function() {
                $('#comboProductSelect').select2({
                    placeholder: 'Search and select product',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#addComboProductModal'),
                    matcher: function(params, data) {
                        // If there is no search term, return all data
                        if ($.trim(params.term) === '') {
                            return data;
                        }
                        
                        // Search term to lowercase
                        const term = params.term.toLowerCase();
                        
                        // Skip if empty
                        if (!data.text) {
                            return null;
                        }
                        
                        // Search in the text
                        if (data.text.toLowerCase().indexOf(term) > -1) {
                            return data;
                        }
                        
                        // Return `null` if the term should not be displayed
                        return null;
                    }
                });
            }, 500);
            
            $('#comboProductSelect').on('change', function() {
                const productId = $(this).val();
                if (productId) {
                    fetchBatches(productId);
                } else {
                    resetBatchSelection();
                }
            });

            function fetchBatches(productId) {
                // Show the loader
                $('#loader').show();
                $.ajax({
                    url: '../API/fetch_Single_product_batches.php',
                    type: 'GET',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(batches) {
                        // Hide the loader
                        $('#loader').hide();
                        if (batches.length > 0) {
                            // convert cost, profit, selling_price to number
                            batches.forEach(batch => {
                                batch.cost = parseFloat(batch.cost);
                                batch.profit = parseFloat(batch.profit);
                                batch.selling_price = parseFloat(batch.selling_price);
                            });
                            const prioritizedBatch = prioritizeBatch(batches);
                            if (prioritizedBatch) {
                                displayBatchDetails(prioritizedBatch);
                            } else {
                                resetBatchSelection();
                                alert('No suitable batch found for the selected product.');
                            }
                        } else {
                            resetBatchSelection();
                            alert('No batches available for the selected product.');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Hide the loader
                        $('#loader').hide();
                        console.error('Error fetching batches:', error);
                        alert('Unable to fetch batches. Please try again.');
                    }
                });
            }


            // function prioritizeBatch(batches) {
            //     console.log(batches);
            //     return batches
            //         .filter(batch => batch.status === 'active' && batch.quantity > 1)
            //         .sort((a, b) => new Date(b.restocked_at) - new Date(a.restocked_at))[0];
            //     console.log(batches);
            // }

            function prioritizeBatch(batches) {
                console.log(batches);
                return batches
                    .filter(batch => batch.status === 'active')
                    .sort((a, b) => {
                        if (b.quantity !== a.quantity) {
                            return b.quantity - a.quantity; // Sort by quantity in descending order
                        }
                        return new Date(b.restocked_at) - new Date(a.restocked_at); // If quantities are equal, sort by restocked_at
                    })[0];
            }


            function displayBatchDetails(batch) {
                $('#batchSection').addClass('hidden'); // Hide batch selection field
                $('#batchDetails').text(`Auto-selected Batch: ${batch.batch_name}, Selling Price: ${batch.selling_price}, Cost: ${batch.cost}`);
                $('#batchDetails').data('selected-batch', batch); // Store the selected batch for later use
            }

            function resetBatchSelection() {
                $('#batchSection').addClass('hidden');
                $('#batchDetails').text('');
                $('#batchDetails').data('selected-batch', null);
            }

            // Update batch details on batch selection
            $('#batchSelect').on('change', function() {
                const selectedBatch = $(this).find(':selected');
                const sellingPrice = selectedBatch.data('selling-price') || 0;
                const cost = selectedBatch.data('cost') || 0;
                const profit = selectedBatch.data('profit') || 0;
                $('#batchDetails').text(`Selling Price: ${sellingPrice}, Cost: ${cost}, Profit: ${profit}`);
            });

            function resetModalFields() {
                $('#addComboProductModal').modal('hide');
                $('#comboProductQty').val(1);
                $('#comboProductSelect').val('').trigger('change');
                resetBatchSelection();
            }

            //Product Type change handler
            $('#productType').on('change', function() {
                const productType = $(this).val();
                handleProductTypeChange(productType);
            });

            function handleProductTypeChange(productType) {
                // Reset all sections
                $('#initialStockSection, #unitSection, #comboProductSection').addClass('hidden');
                $('#skuSection').removeClass('hidden');
                if (productType === 'standard') {
                    $('#initialStockSection, #unitSection').removeClass('hidden');
                    $('#unitSectionCard').removeClass('hidden');
                } else if (productType === 'combo') {
                    $('#initialStockSection, #unitSection, #unitSectionCard').addClass('hidden');
                    $('#comboProductSection').removeClass('hidden');
                } else if (productType === 'digital' || productType === 'service') {
                    $('#unitSection, #initialStockSection, #unitSectionCard').addClass('hidden');
                }
            }
            // Generate Product Code
            $('#generateCodeBtn').on('click', function() {
                const randomCode = 'PROD-' + Math.floor(Math.random() * 1000000);
                $('#productCode').val(randomCode);
            });
            // Handle default unit change
            $('#defaultUnit').on('change', function() {
                var selectedUnit = $(this).val();
                updateSalePurchaseUnits(selectedUnit);
                $('#pcsPerBoxDiv').toggleClass('hidden', selectedUnit !== 'pcs' && selectedUnit !== 'box');
            });

            function updateSalePurchaseUnits(selectedUnit) {
                $('#saleUnit').empty();
                $('#purchaseUnit').empty();
                if (selectedUnit == 'pcs') {
                    $('#saleUnit').append('<option value="pcs">Pcs</option> <option value="box">Box</option>');
                    $('#purchaseUnit').append('<option value="pcs">Pcs</option> <option value="carton">Carton</option>');
                } else if (selectedUnit == 'kg' || selectedUnit == 'gm') {
                    $('#saleUnit').append('<option value="kg">Kg</option> <option value="gm">Gm</option>');
                    $('#purchaseUnit').append('<option value="kg">Kg</option> <option value="gm">Gm</option>');
                } else if (selectedUnit == 'ml') {
                    $('#saleUnit').append('<option value="ml">Ml</option> <option value="liter">Liter</option>');
                    $('#purchaseUnit').append('<option value="ml">Ml</option> <option value="liter">Liter</option>');
                } else if (selectedUnit == 'meter') {
                    $('#saleUnit').append('<option value="meter">Meter</option> <option value="feet">Feet</option> <option value="sqft">Sqft</option> <option value="cm">Cm</option>');
                    $('#purchaseUnit').append('<option value="meter">Meter</option> <option value="feet">Feet</option> <option value="sqft">Sqft</option> <option value="cm">Cm</option>');
                }
                $('#saleUnit').select2();
                $('#purchaseUnit').select2();
            }

            // Product image preview
            $('#productImage').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).removeClass('hidden');
                        $('.image-preview-container').css('border', 'none');
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').addClass('hidden');
                    $('.image-preview-container').css('border', '1px dashed #ced4da');
                }
            });

            // Show/Hide Image Upload Section
            $('#showInEcommerce').on('change', function() {
                $('#imageUploadSection').toggleClass('hidden', !this.checked);
                $('#imagePreview').toggleClass('hidden', true);
                $('.image-preview-container').css('border', '1px dashed #ced4da');
                $('#productImage').val('');
            });

            // Variant Management
            $('#hasVariant').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('#variantTableContainer').toggleClass('hidden', !isChecked);
                
                // If checked and there are no rows, add initial row
                if (isChecked && $('#variantTableBody tr').length === 0) {
                    // Add initial row
                    addVariantRow();
                }
            });

            // Extract the variant row creation to a separate function so it can be reused
            function addVariantRow() {
                variantCount++;
                const newVariantRow = `
                    <tr id="variant-row-${variantCount}" class="variant-row">
                        <td>
                            <input type="text" class="form-control variant-name" required placeholder="e.g., Color, Size">
                        </td>
                        <td>
                            <input type="text" class="form-control variant-value" required placeholder="e.g., Red, Large">
                        </td>
                        <td>
                            <input type="number" class="form-control variant-stock" value="0" min="0" step="any">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-cost" value="0" min="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-price" value="0" min="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group variant-discount-price-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-discount-price" value="" min="0" placeholder="Optional">
                            </div>
                            <div class="variant-discount-indicator text-success small mt-1"></div>
                        </td>
                        <td>
                            <input type="number" class="form-control variant-alert" value="5" min="0" step="any">
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-danger deleteVariantBtn" data-variant-id="${variantCount}" data-bs-toggle="tooltip" title="Remove Variant">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary duplicateVariantBtn" data-variant-id="${variantCount}" data-bs-toggle="tooltip" title="Duplicate Variant">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                $('#variantTableBody').append(newVariantRow);
                updateVariantSummary();

                // Initialize tooltips for new row
                $(`#variant-row-${variantCount} [data-bs-toggle="tooltip"]`).tooltip();
            }

            // Updated addVariantBtn handler with better styling and tooltips
            $('#addVariantBtn').on('click', function() {
                addVariantRow();
            });

            let variantCount = 0;

            // Add duplicate variant functionality
            $(document).on('click', '.duplicateVariantBtn', function() {
                const variantId = $(this).data('variant-id');
                const $originalRow = $(`#variant-row-${variantId}`);

                variantCount++;
                const newVariantRow = `
                    <tr id="variant-row-${variantCount}" class="variant-row">
                        <td>
                            <input type="text" class="form-control variant-name" required value="${$originalRow.find('.variant-name').val()} (Copy)">
                        </td>
                        <td>
                            <input type="text" class="form-control variant-value" required value="${$originalRow.find('.variant-value').val()}">
                        </td>
                        <td>
                            <input type="number" class="form-control variant-stock" value="${$originalRow.find('.variant-stock').val()}" min="0">
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-cost" value="${$originalRow.find('.variant-cost').val()}" min="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-price" value="${$originalRow.find('.variant-price').val()}" min="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group variant-discount-price-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-discount-price" value="${$originalRow.find('.variant-discount-price').val()}" min="0" placeholder="Optional">
                            </div>
                            <div class="variant-discount-indicator text-success small mt-1"></div>
                        </td>
                        <td>
                            <input type="number" class="form-control variant-alert" value="${$originalRow.find('.variant-alert').val()}" min="0">
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-danger deleteVariantBtn" data-variant-id="${variantCount}" data-bs-toggle="tooltip" title="Remove Variant">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary duplicateVariantBtn" data-variant-id="${variantCount}" data-bs-toggle="tooltip" title="Duplicate Variant">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                $('#variantTableBody').append(newVariantRow);

                // Trigger discount price calculation for the new row
                $(`#variant-row-${variantCount} .variant-discount-price`).trigger('input');

                updateVariantSummary();

                // Initialize tooltips for new row
                $(`#variant-row-${variantCount} [data-bs-toggle="tooltip"]`).tooltip();
            });

            // Update variant summary
            function updateVariantSummary() {
                const variantRows = $('#variantTableBody tr').length;
                $('#variantCount').text(variantRows);

                let totalStock = 0;
                let minPrice = Infinity;
                let maxPrice = 0;

                $('#variantTableBody tr').each(function() {
                    const stock = parseInt($(this).find('.variant-stock').val()) || 0;
                    totalStock += stock;

                    const price = parseFloat($(this).find('.variant-price').val()) || 0;
                    if (price > 0) {
                        minPrice = Math.min(minPrice, price);
                        maxPrice = Math.max(maxPrice, price);
                    }
                });

                $('#variantTotalStock').text(totalStock);

                if (minPrice === Infinity) minPrice = 0;
                if (minPrice === maxPrice) {
                    $('#variantPriceRange').text(`Rs. ${minPrice.toFixed(2)}`);
                } else {
                    $('#variantPriceRange').text(`Rs. ${minPrice.toFixed(2)} - Rs. ${maxPrice.toFixed(2)}`);
                }
            }

            // Update summary when variant data changes
            $(document).on('input', '.variant-stock, .variant-price', function() {
                updateVariantSummary();
            });

            // Delete variant row with confirmation
            $(document).on('click', '.deleteVariantBtn', function() {
                const variantId = $(this).data('variant-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This variant will be removed",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(`#variant-row-${variantId}`).remove();
                        updateVariantSummary();
                    }
                });
            });

            function handleModalSave(modalId, inputId, selectId, fetchUrl) {
                const newName = $(`#${inputId}`).val().trim();
                if (newName) {
                    $.ajax({
                        url: fetchUrl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            name: newName
                        },
                        success: function(response) {
                            if (response && response.id) {
                                $(`#${selectId}`).append(`<option value="${response.id}" selected>${newName}</option>`);
                                $(`#${selectId}`).val(response.id).trigger('change');
                            } else {
                                alert("Something went wrong, try again");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error adding item:', error);
                            let errorMessage = "Something went wrong. Please try again.";
                            if (xhr.status === 404) {
                                errorMessage = "Resource not found, please check URL";
                            } else if (xhr.status === 500) {
                                errorMessage = "Internal server error, contact admin";
                            } else if (xhr.status === 0) {
                                errorMessage = "Network error. Please check your connection.";
                            }
                            alert(errorMessage);
                        },
                        complete: function() {
                            $(`#${modalId}`).modal('hide');
                            $(`#${inputId}`).val('');
                        }
                    });
                } else {
                    alert('Please enter a name.');
                }
            }
            // Brand Modal
            $('#saveNewBrand').on('click', function() {
                handleModalSave('addBrandModal', 'newBrandName', 'brand', '../API/addBrand.php');
            });

            // Category Modal
            $('#saveNewCategory').on('click', function() {
                handleModalSave('addCategoryModal', 'newCategoryName', 'category', '../API/addCategory.php');
            });

            let comboProductCount = 0;
            // Open Combo Product modal
            $('#addComboProductBtn').on('click', function() {
                $('#addComboProductModal').modal('show')
            });

            // Add selected product and batch to the combo table
            $('#addSelectedComboProduct').on('click', function() {
                const productId = $('#comboProductSelect').val();
                const productName = $('#comboProductSelect option:selected').text();
                const selectedBatch = $('#batchDetails').data('selected-batch');

                // wait for batch selection


                if (!productId) {
                    alert('Please select a product');
                    return;
                }

                if (!selectedBatch) {
                    alert('No batch available for the selected product.');
                    return;
                }

                const {
                    batch_name,
                    cost,
                    selling_price
                } = selectedBatch;
                const quantity = parseFloat($('#comboProductQty').val());

                if (isNaN(quantity) || quantity <= 0) {
                    alert('Please enter a valid quantity.');
                    return;
                }

                const totalCost = (cost * quantity).toFixed(2);
                const totalSellingPrice = (selling_price * quantity).toFixed(2);

                comboProductCount++;
                const newComboRow = `
                    <tr id="combo-row-${comboProductCount}" data-product-id="${productId}" class="variant-row">
                        <td>${productName}</td>
                        <td>${batch_name}</td>
                        <td>${cost.toFixed(2)}</td>
                        <td>${selling_price.toFixed(2)}</td>
                        <td><input type="number" class="form-control combo-quantity" data-unit-cost="${cost}" data-unit-selling-price="${selling_price}" value="${quantity}" min="1"></td>
                        <td class="combo-total-cost">${totalCost}</td>
                        <td class="combo-total-selling-price">${totalSellingPrice}</td>
                        <td>
                            <button type="button" class="btn btn-danger deleteComboProductBtn" data-combo-id="${comboProductCount}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#comboProductTableBody').append(newComboRow);
                resetModalFields();
                updateTotalComboCostAndPrice();
            });


            // Delete Combo Product
            $(document).on('click', '.deleteComboProductBtn', function() {
                const comboId = $(this).data('combo-id');
                $(`#combo-row-${comboId}`).remove();
                updateTotalComboCostAndPrice();
            });

            // Update total cost and selling price when quantity changes
            $(document).on('input', '.combo-quantity', function() {
                const quantity = parseFloat($(this).val());
                const unitCost = parseFloat($(this).data('unit-cost'));
                const unitSellingPrice = parseFloat($(this).data('unit-selling-price'));

                if (isNaN(quantity) || quantity <= 0) {
                    alert('Please enter a valid quantity.');
                    $(this).val(1); // Reset to 1 if invalid
                    return;
                }

                // Update row totals
                const totalCost = (unitCost * quantity).toFixed(2);
                const totalSellingPrice = (unitSellingPrice * quantity).toFixed(2);
                $(this).closest('tr').find('.combo-total-cost').text(totalCost);
                $(this).closest('tr').find('.combo-total-selling-price').text(totalSellingPrice);

                // Update overall totals
                updateTotalComboCostAndPrice();
            });

            // Recalculate total combo cost and selling price
            function updateTotalComboCostAndPrice() {
                let totalCost = 0;
                let totalSellingPrice = 0;
                let productCount = $('#comboProductTableBody tr').length;

                $('#comboProductTableBody tr').each(function() {
                    const costText = $(this).find('.combo-total-cost').text();
                    // const sellingPriceText = $(this).find('.combo-total-selling-price').text();

                    const cost = parseFloat(costText) || 0;
                    // const sellingPrice = parseFloat(sellingPriceText) || 0;

                    totalCost += cost;
                    // totalSellingPrice += sellingPrice;
                });

                $('#comboProductCount').text(productCount);
                $('#totalComboCost').text('Rs. ' + totalCost.toFixed(2));
                $('#cost').val(totalCost.toFixed(2));
                // $('#sellingPrice').val(totalSellingPrice.toFixed(2));
            }


            function loadInitialData() {
                loadData('../API/getBrands.php', 'brand');
                loadData('../API/getCategories.php', 'category');
            }

            function loadData(url, selectId) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var select = $(`#${selectId}`);
                        select.empty().append('<option value="">Select</option>');
                        $.each(data, function(index, item) {
                            select.append(`<option value="${item.id}">${item.name}</option>`);
                        });
                        select.select2();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading data:', error);
                        let errorMessage = "Something went wrong. Please try again.";
                        if (xhr.status === 404) {
                            errorMessage = "Resource not found, please check URL";
                        } else if (xhr.status === 500) {
                            errorMessage = "Internal server error, contact admin";
                        } else if (xhr.status === 0) {
                            errorMessage = "Network error. Please check your connection.";
                        }
                        alert(errorMessage);
                    }
                });
            }

            function loadProductsForCombo() {
                $.ajax({
                    url: '../API/getProducts.php', // Replace with your server-side script URL
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#comboProductSelect').empty();
                        $('#comboProductSelect').append('<option value="">Select Product</option>');
                        $.each(data, function(index, product) {
                            $('#comboProductSelect').append(`<option value="${product.id}">${product.name}</option>`);
                        });
                        
                        // Reinitialize select2 for this element
                        $('#comboProductSelect').select2({
                            placeholder: 'Search and select product',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#addComboProductModal'),
                            matcher: function(params, data) {
                                // If there is no search term, return all data
                                if ($.trim(params.term) === '') {
                                    return data;
                                }
                                
                                // Search term to lowercase
                                const term = params.term.toLowerCase();
                                
                                // Skip if empty
                                if (!data.text) {
                                    return null;
                                }
                                
                                // Search in the text
                                if (data.text.toLowerCase().indexOf(term) > -1) {
                                    return data;
                                }
                                
                                // Return `null` if the term should not be displayed
                                return null;
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading products', error);
                        let errorMessage = "Something went wrong. Please try again.";
                        if (xhr.status === 404) {
                            errorMessage = "Resource not found, please check URL";
                        } else if (xhr.status === 500) {
                            errorMessage = "Internal server error, contact admin";
                        } else if (xhr.status === 0) {
                            errorMessage = "Network error. Please check your connection.";
                        }
                        alert(errorMessage);
                    }
                });
            }

            // Initialize form handlers
            $(document).ready(function() {
                $('#productForm').on('submit', function(e) {
                    e.preventDefault();
                    createProduct();
                });

                // Remove validation errors when field is changed
                $(document).on('input change', '.form-control, .form-select', function() {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.invalid-feedback').remove();
                });
            });


            // Main form submission handler
            function createProduct() {
                // Show loading state
                const submitButton = document.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Product...';
                submitButton.disabled = true;

                // Reset previous errors
                resetFormErrors();

                // Collect product data
                const productData = collectProductData();

                // Client-side validation
                try {
                    validateProductData(productData);
                } catch (error) {
                    handleValidationError(error);
                    resetSubmitButton(submitButton, originalButtonText);
                    return;
                }

                // Create FormData for submission
                const formData = new FormData();
                formData.append('productData', JSON.stringify(productData));

                // Add image if present
                const productImageInput = document.getElementById('productImage');
                if (productImageInput.files[0]) {
                    formData.append('productImage', productImageInput.files[0]);
                }

                // Send data to server
                $.ajax({
                    url: 'createProduct.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showSuccessMessage("Product created successfully!");
                            
                            // Reload the product list for combo products after successful creation
                            loadProductsForCombo();
                            
                            resetForm();
                        } else {
                            handleErrorResponse(response);
                        }
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    },
                    complete: function() {
                        resetSubmitButton(submitButton, originalButtonText);
                    }
                });
            }

            // Collect all form data
            function collectProductData() {
                const data = {
                    productType: $('#productType').val(),
                    productName: $('#productName').val().trim(),
                    productCode: $('#productCode').val().trim(),
                    barcodeSymbology: $('#barcodeSymbology').val(),
                    brandId: $('#brand').val(),
                    categoryId: $('#category').val(),
                    sku: $('#sku').val().trim(),
                    showInEcommerce: $('#showInEcommerce').is(':checked'),
                    hasVariant: $('#hasVariant').is(':checked'),
                    variants: [],
                    comboProducts: [],
                    sellingPrice: parseFloat($('#sellingPrice').val()) || 0,
                    discountPrice: parseFloat($('#discountPrice').val()) || null,
                };

                // Based on product type
                if (data.productType === 'standard') {
                    data.defaultUnit = $('#defaultUnit').val();
                    data.saleUnit = $('#saleUnit').val();
                    data.purchaseUnit = $('#purchaseUnit').val();
                    data.pcsPerBox = $('#pcsPerBox').val();
                    data.initialStock = parseFloat(parseFloat($('#initialStock').val()).toFixed(3)) || 0;
                    data.alertQuantity = parseFloat(parseFloat($('#alertQuantity').val()).toFixed(3)) || 5; // Add alert quantity
                    data.cost = parseFloat($('#cost').val()) || 0;
                }

                // Collect variants if enabled
                if (data.hasVariant) {
                    $('.variant-row').each(function () {
                        // Check if all required fields are filled
                        const variantName = $(this).find('.variant-name').val().trim();
                        const variantValue = $(this).find('.variant-value').val().trim();
                        const variantCost = parseFloat($(this).find('.variant-cost').val()) || 0;
                        const variantPrice = parseFloat($(this).find('.variant-price').val()) || 0;
                        
                        // Only add if name and price are valid
                        if (variantName && variantPrice > 0) {
                            data.variants.push({
                                variantName: variantName,
                                variantValue: variantValue,
                                variantCost: variantCost,
                                variantPrice: variantPrice,
                                variantDiscountPrice: parseFloat($(this).find('.variant-discount').val()) || null,
                                variantQuantity: parseFloat(parseFloat($(this).find('.variant-stock').val()).toFixed(3)) || 0,
                                variantAlertQty: parseFloat(parseFloat($(this).find('.variant-alert').val()).toFixed(3)) || 5
                            });
                        }
                    });
                }

                // Collect combo products if applicable
                if (data.productType === 'combo') {
                    $('#comboProductTableBody tr').each(function() {
                        const comboProduct = {
                            productId: $(this).data('product-id'),
                            quantity: parseFloat($(this).find('.combo-quantity').val()) || 0
                        };
                        data.comboProducts.push(comboProduct);
                        data.cost = parseFloat($('#cost').val()) || 0;
                    });
                }

                return data;
            }

            // Client-side validation
            function validateProductData(data) {
                if (!data.productName) {
                    throw new ValidationError("Product name is required", "productName");
                }
                if (!data.productCode) {
                    throw new ValidationError("Product code/barcode is required", "productCode");
                }
                if (!data.sku) {
                    throw new ValidationError("SKU is required", "sku");
                }

                // Type-specific validation
                if (data.productType === 'standard' && !data.hasVariant) {
                    if (!data.cost || data.cost <= 0) {
                        throw new ValidationError("Valid cost is required", "cost");
                    }
                    if (!data.sellingPrice || data.sellingPrice <= 0) {
                        throw new ValidationError("Valid selling price is required", "sellingPrice");
                    }
                    if (data.sellingPrice < data.cost) {
                        throw new ValidationError("Selling price cannot be less than cost", "sellingPrice");
                    }
                }

                // Validate combo products
                if (data.productType === 'combo' && data.comboProducts.length === 0) {
                    throw new ValidationError("Please add at least one product to the combo", "comboProducts");
                }

                // Validate variants
                if (data.hasVariant && data.variants.length === 0) {
                    throw new ValidationError("Please add at least one variant", "variants");
                }

                if (data.hasVariant) {
                    data.variants.forEach((variant, index) => {
                        if (!variant.variantName) {
                            throw new ValidationError(`Variant name is required for variant #${index + 1}`, `variant-name-${index}`);
                        }
                        if (!variant.variantValue) {
                            throw new ValidationError(`Variant value is required for variant #${index + 1}`, `variant-value-${index}`);
                        }
                        if (variant.variantPrice < variant.variantCost) {
                            throw new ValidationError(`Selling price cannot be less than cost for variant #${index + 1}`, `variant-price-${index}`);
                        }
                        if (variant.variantDiscountPrice !== null && variant.variantDiscountPrice > variant.variantPrice) {
                            throw new ValidationError(`Discount price cannot be higher than selling price for variant #${index + 1}`, `variant-discount-price-${index}`);
                        }
                    });
                }

                if (data.discountPrice !== null && data.discountPrice > data.sellingPrice) {
                    throw new ValidationError("Discount price cannot be higher than selling price", "discountPrice");
                }
            }

            // Custom error class for validation
            class ValidationError extends Error {
                constructor(message, field) {
                    super(message);
                    this.name = 'ValidationError';
                    this.field = field;
                }
            }

            // Handle validation errors
            function handleValidationError(error) {
                if (error instanceof ValidationError) {
                    showFieldError(error.field, error.message);
                    showErrorMessage(error.message);
                } else {
                    showErrorMessage("Validation failed. Please check your input.");
                    showErrorMessage(error.message);
                }
            }

            // Handle AJAX errors
            function handleAjaxError(xhr) {
                let errorMessage = "An error occurred while creating the product.";

                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                        if (response.field) {
                            showFieldError(response.field, response.message);
                        }
                    }
                } catch (e) {
                    if (xhr.status === 404) {
                        errorMessage = "Server endpoint not found. Please contact support.";
                    } else if (xhr.status === 500) {
                        errorMessage = "Internal server error. Please try again later.";
                    } else if (xhr.status === 0) {
                        errorMessage = "Network error. Please check your connection.";
                    }
                }

                showErrorMessage(errorMessage);
            }

            // Show error message in UI
            function showErrorMessage(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK',
                });
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;

                const existingAlert = document.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                $('#productForm').prepend(alertHtml);
            }

            // Show success message
            function showSuccessMessage(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: message,
                    confirmButtonText: 'OK',
                });
                const alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('#productForm').prepend(alertHtml);
            }

            // Show field-specific error
            function showFieldError(fieldId, message) {
                const field = $(`#${fieldId}`);
                if (field.length) {
                    field.addClass('is-invalid');

                    // Remove existing error message if any
                    field.siblings('.invalid-feedback').remove();

                    // Add new error message
                    field.after(`<div class="invalid-feedback">${message}</div>`);

                    // Scroll to the field
                    field[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // Reset form errors
            function resetFormErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();
                $('.alert').remove();
            }

            // Reset submit button
            function resetSubmitButton(button, originalText) {
                button.innerHTML = originalText;
                button.disabled = false;
            }

            // Reset form after successful submission
            function resetForm() {
                $('#productForm')[0].reset();
                $('select').val('').trigger('change');
                $('#variantTableBody').empty();
                $('#comboProductTableBody').empty();
                $('#totalComboCost').text('0.00');
                $('#imagePreview').addClass('hidden');
                $('.image-preview-container').css('border', '1px dashed #ced4da');
                resetFormLayout();
            }

            // Reset form layout
            function resetFormLayout() {
                $('#pcsPerBoxDiv').addClass('hidden');
                $('#variantTableContainer').addClass('hidden');
                $('#comboProductSection').addClass('hidden');
                $('#initialStockSection, #unitSection').removeClass('hidden');
                $('#imageUploadSection').addClass('hidden');
            }

            // Calculate and display discount percentage
            $('#sellingPrice, #discountPrice').on('input', function() {
                const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
                const discountPrice = parseFloat($('#discountPrice').val()) || 0;

                if (sellingPrice > 0 && discountPrice > 0 && discountPrice < sellingPrice) {
                    const discountPercentage = ((sellingPrice - discountPrice) / sellingPrice) * 100;
                    $('#discountPercentage').html(`<i class="fas fa-tags"></i> ${discountPercentage.toFixed(2)}% discount`);
                } else {
                    $('#discountPercentage').html('');
                }
            });

            // Also implement for variants
            $(document).on('input', '.variant-price, .variant-discount-price', function() {
                const row = $(this).closest('tr');
                const variantPrice = parseFloat(row.find('.variant-price').val()) || 0;
                const variantDiscountPrice = parseFloat(row.find('.variant-discount-price').val()) || 0;

                if (variantPrice > 0 && variantDiscountPrice > 0 && variantDiscountPrice < variantPrice) {
                    const discountPercentage = ((variantPrice - variantDiscountPrice) / variantPrice) * 100;

                    if (row.find('.variant-discount-indicator').length === 0) {
                        row.find('.variant-discount-price-group').after(`<div class="variant-discount-indicator text-success small mt-1"></div>`);
                    }

                    row.find('.variant-discount-indicator').html(`<i class="fas fa-tags"></i> ${discountPercentage.toFixed(2)}%`);
                } else {
                    row.find('.variant-discount-indicator').remove();
                }
            });

            // Update progress as fields are completed
            function updateFormProgress() {
                const requiredFields = $('#productForm').find('[required]');
                const filledFields = requiredFields.filter(function() {
                    return $(this).val() !== '';
                });

                const progress = Math.round((filledFields.length / requiredFields.length) * 100);
                $('#form-progress').css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
            }

            // Call this on each input change
            $(document).on('input change', '.form-control, .form-select', function() {
                updateFormProgress();
            });

            // Add to document ready
            $('#discountPrice').on('input', function() {
                const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
                const discountPrice = parseFloat($(this).val()) || 0;

                if (discountPrice > sellingPrice) {
                    $(this).addClass('is-invalid');
                    if ($(this).next('.invalid-feedback').length === 0) {
                        $(this).after('<div class="invalid-feedback">Discount price cannot exceed selling price</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            // Similar logic for variant discount prices
            $(document).on('input', '.variant-discount-price', function() {
                const row = $(this).closest('tr');
                const variantPrice = parseFloat(row.find('.variant-price').val()) || 0;
                const variantDiscountPrice = parseFloat($(this).val()) || 0;

                if (variantDiscountPrice > variantPrice) {
                    $(this).addClass('is-invalid');
                    if ($(this).next('.invalid-feedback').length === 0) {
                        $(this).after('<div class="invalid-feedback">Discount price cannot exceed selling price</div>');
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            // Add this to your document ready function:
            $('.discount-percent-toggle').on('click', function() {
                const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
                const discountPrice = parseFloat($('#discountPrice').val()) || 0;
                let currentPercent = 0;
                
                if (sellingPrice > 0 && discountPrice > 0 && discountPrice < sellingPrice) {
                    currentPercent = ((sellingPrice - discountPrice) / sellingPrice) * 100;
                }
                
                $('#discountPercentInput').val(currentPercent.toFixed(2));
                $('#regularPricePreview').text(sellingPrice.toFixed(2));
                
                updateDiscountPreview();
                
                const discountPercentModal = new bootstrap.Modal(document.getElementById('discountPercentModal'));
                discountPercentModal.show();
            });

            $('#discountPercentInput').on('input', function() {
                updateDiscountPreview();
            });

            function updateDiscountPreview() {
                const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
                const discountPercent = parseFloat($('#discountPercentInput').val()) || 0;
                
                const discountAmount = (sellingPrice * discountPercent / 100).toFixed(2);
                const finalPrice = (sellingPrice - discountAmount).toFixed(2);
                
                $('#discountAmountPreview').text(discountAmount);
                $('#finalPricePreview').text(finalPrice);
            }

            $('#applyPercentDiscount').on('click', function() {
                const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
                const discountPercent = parseFloat($('#discountPercentInput').val()) || 0;
                
                if (sellingPrice > 0 && discountPercent > 0 && discountPercent <= 100) {
                    const finalPrice = sellingPrice - (sellingPrice * discountPercent / 100);
                    $('#discountPrice').val(finalPrice.toFixed(2));
                    $('#discountPrice').trigger('input'); // Trigger input to update the UI
                }
                
                bootstrap.Modal.getInstance(document.getElementById('discountPercentModal')).hide();
            });

            // Add this to your document ready function:
            function updateProductPreview() {
                // Update product name
                $('#previewProductName').text($('#productName').val() || 'Product Name');

                // Update product code
                const productCode = $('#productCode').val();
                $('#previewProductCode').html(productCode ? `<small>Code: ${productCode}</small>` : '');

                // Update SKU
                const sku = $('#sku').val();
                $('#previewSKU').html(sku ? `<small>SKU: ${sku}</small>` : '');

                // Update category and brand
                $('#previewCategory').text($('#category option:selected').text() || '');
                $('#previewBrand').text($('#brand option:selected').text() || '');

                // Update type
                $('#previewType').text($('#productType option:selected').text() || '');

                // Update prices
                const regularPrice = parseFloat($('#sellingPrice').val()) || 0;
                $('#previewRegularPrice').text(`Rs. ${regularPrice.toFixed(2)}`);

                const discountPrice = parseFloat($('#discountPrice').val()) || 0;
                if (discountPrice > 0 && discountPrice < regularPrice) {
                    $('#previewDiscountSection').show();
                    $('#previewDiscountPrice').text(`Rs. ${discountPrice.toFixed(2)}`);

                    const discountPercentage = ((regularPrice - discountPrice) / regularPrice) * 100;
                    $('#previewDiscountPercent').text(`${discountPercentage.toFixed(2)}% OFF`);
                } else {
                    $('#previewDiscountSection').hide();
                }

                // Update image preview
                const productImageInput = document.getElementById('productImage');
                if (productImageInput.files && productImageInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#previewProductImage').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(productImageInput.files[0]);
                } else {
                    $('#previewProductImage').attr('src', 'https://www.productsamples.com/wp-content/uploads/2021/10/il-box.png');
                }
            }

            // Attach the preview update to input changes
            $(document).on('input change', '#productName, #productCode, #sku, #category, #brand, #sellingPrice, #discountPrice, #productType', function() {
                updateProductPreview();
            });

            // Handle image changes
            $('#productImage').on('change', function() {
                updateProductPreview();
            });

            // Connect the bottom Add Variant button to the same function
            $('#addVariantBtnBottom').on('click', function() {
                addVariantRow();
                
                // Scroll to the newly added row
                setTimeout(() => {
                    const lastRow = $('#variantTableBody tr:last');
                    lastRow[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    // Focus the first input in the new row
                    lastRow.find('.variant-name').focus();
                }, 100);
            });
        });
    </script>

    <!-- Add this JavaScript before the closing body tag -->
    <script>
        // Auto-detect barcode symbology based on the barcode format
        $(document).ready(function() {
            console.log('Initializing barcode symbology detection...');
            
            // Local detection function for immediate feedback
            function localDetectSymbology(barcode) {
                if (!barcode) return 'CODE128';
                
                // Remove any whitespace
                barcode = barcode.trim();
                
                // Check if it's numeric only
                const isNumeric = /^\d+$/.test(barcode);
                
                if (isNumeric) {
                    // Check length for standard symbologies
                    const length = barcode.length;
                    
                    switch (length) {
                        case 8:
                            return 'EAN8';
                        case 12:
                            return 'UPC';
                        case 13:
                            return 'EAN13';
                    }
                }
                
                // Check for CODE39 patterns (alphanumeric and limited special chars)
                if (/^[A-Z0-9\-\.\$\/\+\%\s]+$/i.test(barcode)) {
                    return 'CODE39';
                }
                
                // Default to CODE128 for anything else
                return 'CODE128';
            }
            
            // Function to detect barcode symbology using the API
            function detectBarcodeSymbology(barcode) {
                if (!barcode) return;
                
                // First apply local detection for immediate feedback
                const localSymbology = localDetectSymbology(barcode);
                document.getElementById('barcodeSymbology').value = localSymbology;
                console.log('Local detection: ' + localSymbology + ' for barcode: ' + barcode);
                
                // Then verify with the API
                $.ajax({
                    url: '../API/detect_barcode_symbology.php',
                    method: 'GET',
                    data: { barcode: barcode },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Direct DOM manipulation instead of using Select2 methods
                            document.getElementById('barcodeSymbology').value = response.symbology;
                            console.log('API detection: ' + response.symbology + ' for barcode: ' + barcode);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error detecting barcode symbology:', error);
                    }
                });
            }
            
            // Detect symbology when barcode field changes - use input event for immediate feedback
            $('#productCode').on('input', function() {
                const barcode = $(this).val();
                if (barcode) {
                    // Use local detection for immediate feedback during typing
                    const localSymbology = localDetectSymbology(barcode);
                    document.getElementById('barcodeSymbology').value = localSymbology;
                }
            });
            
            // Use the API when the user completes their input
            $('#productCode').on('change blur', function() {
                const barcode = $(this).val();
                if (barcode) {
                    detectBarcodeSymbology(barcode);
                }
            });
            
            // Also detect when barcode is generated
            $('#generateCodeBtn').on('click', function() {
                setTimeout(function() {
                    const barcode = $('#productCode').val();
                    if (barcode) {
                        detectBarcodeSymbology(barcode);
                    }
                }, 100); // Small delay to allow the field to be populated
            });
        });
    </script>
</body>

</html>