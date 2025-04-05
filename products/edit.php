<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, width=device-width, initial-scale=1.0">
    <title>Edit Product - Advanced POS System</title>
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

        .required-field::after {
            content: "*";
            color: red;
            margin-left: 4px;
        }

        .hidden {
            display: none;
        }

        #imagePreview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }

        .image-preview-container {
            margin-top: 10px;
        }

        /* Variant styles */
        .variant-row {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .variant-control {
            margin-top: 10px;
        }

        /* Combo product styles */
        .combo-item {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .combo-control {
            margin-top: 10px;
        }

        /* Form actions */
        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        /* Alert styles */
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }

        .fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2 class="mb-4 text-center">Edit Product</h2>
            <form id="productForm" novalidate>
                <input type="hidden" id="productId" name="productId" value="">

                <!-- Product Type Selection -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="productType" class="form-label required-field">Product Type</label>
                        <select id="productType" name="productType" class="form-select" required disabled>
                            <option value="standard">Standard Product</option>
                            <option value="combo">Combo Product</option>
                            <option value="digital">Digital Product</option>
                            <option value="service">Service</option>
                        </select>
                        <small class="text-muted">Product type cannot be changed after creation</small>
                    </div>
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
                                    <input type="text" class="form-control" id="productName" name="productName" required>
                                </div>

                                <div class="mb-3">
                                    <label for="productCode" class="form-label required-field">Product Code/Barcode</label>
                                    <input type="text" class="form-control" id="productCode" name="productCode" required>
                                </div>

                                <div class="mb-3">
                                    <label for="barcodeSymbology" class="form-label">Barcode Symbology</label>
                                    <select id="barcodeSymbology" class="form-select" name="barcodeSymbology">
                                        <option value="CODE128">CODE 128</option>
                                        <option value="CODE39">CODE 39</option>
                                        <option value="EAN13">EAN-13</option>
                                        <option value="EAN8">EAN-8</option>
                                        <option value="UPC">UPC</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="brand" class="form-label">Brand</label>
                                    <select id="brand" class="form-select select2" name="brand">
                                        <option value="">Select Brand</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select id="category" class="form-select select2" name="category">
                                        <option value="">Select Category</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="sku" class="form-label required-field">SKU</label>
                                    <input type="text" class="form-control" id="sku" name="sku" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="card mb-3" id="pricingCard">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Pricing & Stock</h5>
                                <div class="form-check mt-2" id="hasVariantContainer">
                                    <input type="checkbox" class="form-check-input" id="hasVariant" name="hasVariant">
                                    <label class="form-check-label" for="hasVariant">This product has multiple variants</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Single variant pricing (default) -->
                                <div id="singleVariantSection">
                                    <input type="hidden" id="batchId" name="batchId" value="0">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="cost" class="form-label required-field">Cost</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control" id="cost" name="cost" min="0" step="any" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="sellingPrice" class="form-label required-field">Selling Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control" id="sellingPrice" name="sellingPrice" min="0" step="any" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="discountPrice" class="form-label">Discount Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control" id="discountPrice" name="discountPrice" min="0" step="any">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="initialStock" class="form-label">Stock Quantity</label>
                                            <input type="number" class="form-control" id="initialStock" name="initialStock" min="0" value="0">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="alertQuantity" class="form-label">Alert Quantity</label>
                                            <input type="number" class="form-control" id="alertQuantity" name="alertQuantity" min="0" value="5">
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple variants section -->
                                <div id="multipleVariantsSection" class="hidden">
                                    <div id="variantsContainer">
                                        <!-- Variant rows will be added here -->
                                    </div>
                                    <button type="button" class="btn btn-outline-primary mt-3" id="addVariantBtn">
                                        <i class="fas fa-plus"></i> Add Variant
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Combo Products Section -->
                        <div class="card mb-3 hidden" id="comboProductsCard">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Combo Products</h5>
                            </div>
                            <div class="card-body">
                                <div id="comboProductsContainer">
                                    <!-- Combo product rows will be added here -->
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-3" id="addComboProductBtn">
                                    <i class="fas fa-plus"></i> Add Product to Combo
                                </button>
                            </div>
                        </div>

                        <div class="mb-3" id="ecommerceSection">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="showInEcommerce" name="showInEcommerce">
                                <label class="form-check-label" for="showInEcommerce">Show in E-Commerce</label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="activeStatus" name="activeStatus" checked>
                                <label class="form-check-label" for="activeStatus">Product is Active</label>
                            </div>
                            <div id="imageUploadSection" class="mt-3">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="productImage" name="productImage">
                                <div class="image-preview-container mt-2">
                                    <img id="imagePreview" src="../uploads/products/no-image.png" alt="Product Image" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php'">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitButton">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loader for AJAX requests -->
    <div id="loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:9999; display:flex; justify-content:center; align-items:center;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Alert container for dynamic alerts -->
    <div class="alert-container"></div>

    <!-- Required JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        let deletedBatches = [];

        function initialize() {
            setupListeners();
            setupFormValidation();
            
            // Don't initialize select2 here - it will be handled in the load functions
            
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');
            
            if (productId) {
                $('#productId').val(productId);
                loadProductData(productId);
            } else {
                showErrorMessage('No product ID specified');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        }

        $(document).ready(function() {
            initialize();
        });

        function loadProductData(productId) {
            $('#loader').show();

            // First ensure dropdowns are fully loaded before fetching product data
            $.when(
                loadBrandsPromise(),
                loadCategoriesPromise()
            ).then(function() {
                // Now fetch product data
                $.ajax({
                    url: 'API/getProductDetails.php',
                    type: 'GET',
                    data: {
                        product_id: productId
                    },
                    success: function(response) {
                        if (response.success) {
                            populateForm(response.data);
                        } else {
                            showErrorMessage(response.message);
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 2000);
                        }
                    },
                    error: function(xhr, status, error) {
                        showErrorMessage('Failed to load product details');
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        $('#loader').hide();
                    }
                });
            });
        }

        // Convert loadBrands to return a promise
        function loadBrandsPromise() {
            var deferred = $.Deferred();
            
            $.ajax({
                url: 'API/getBrands.php',
                type: 'GET',
                success: function(response) {
                    let options = '<option value="">Select Brand</option>';
                    if (response && response.length > 0) {
                        response.forEach(brand => {
                            options += `<option value="${brand.id}">${brand.name}</option>`;
                        });
                    }
                    $('#brand').html(options);
                    // Initialize select2 after populating options
                    $('#brand').select2({
                        width: '100%',
                        placeholder: 'Select Brand'
                    });
                    deferred.resolve();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load brands:', error);
                    deferred.reject();
                }
            });
            
            return deferred.promise();
        }

        // Maintain original loadBrands for backward compatibility
        function loadBrands() {
            loadBrandsPromise();
        }

        // Convert loadCategories to return a promise
        function loadCategoriesPromise() {
            var deferred = $.Deferred();
            
            $.ajax({
                url: 'API/getCategories.php',
                type: 'GET',
                success: function(response) {
                    let options = '<option value="">Select Category</option>';
                    if (response && response.length > 0) {
                        response.forEach(category => {
                            options += `<option value="${category.id}">${category.name}</option>`;
                        });
                    }
                    $('#category').html(options);
                    // Initialize select2 after populating options
                    $('#category').select2({
                        width: '100%',
                        placeholder: 'Select Category'
                    });
                    deferred.resolve();
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load categories:', error);
                    deferred.reject();
                }
            });
            
            return deferred.promise();
        }

        // Maintain original loadCategories for backward compatibility
        function loadCategories() {
            loadCategoriesPromise();
        }

        function populateForm(product) {
            // Set basic product information
            $('#productType').val(product.product_type);
            $('#productName').val(product.product_name);
            $('#productCode').val(product.barcode);
            $('#barcodeSymbology').val(product.barcode_symbology || 'CODE128');
            $('#sku').val(product.sku);
            
            // Set brand and category - need to use select2 properly
            if (product.brand_id) {
                $('#brand').val(product.brand_id);
                $('#brand').trigger('change.select2');
            }
            
            if (product.category_id) {
                $('#category').val(product.category_id);
                $('#category').trigger('change.select2');
            }
            
            $('#showInEcommerce').prop('checked', product.show_in_landing_page == 1);
            $('#activeStatus').prop('checked', product.active_status == 1);

            // Show/hide sections based on product type
            toggleProductTypeSections(product.product_type);

            // Handle image preview
            if (product.image) {
                $('#imagePreview').attr('src', `../uploads/products/${product.image}`).removeClass('hidden');
                $('#imageUploadSection').removeClass('hidden');
            }

            // Toggle e-commerce section based on checkbox state
            if ($('#showInEcommerce').is(':checked')) {
                $('#imageUploadSection').removeClass('hidden');
            }

            // Handle batches/variants
            if (product.batches && product.batches.length > 0) {
                // Check if product has multiple batches (variants)
                if (product.batches.length > 1) {
                    $('#hasVariant').prop('checked', true).trigger('change');
                    $('#variantsContainer').empty();

                    // Add each variant
                    product.batches.forEach(batch => {
                        addVariantRow({
                            batchId: batch.batch_id,
                            name: batch.batch_number,
                            value: batch.notes || '',
                            cost: batch.cost,
                            price: batch.selling_price,
                            discountPrice: batch.discount_price || '',
                            quantity: batch.quantity,
                            alertQty: batch.alert_quantity
                        });
                    });
                } else {
                    // Single batch
                    const batch = product.batches[0];
                    $('#batchId').val(batch.batch_id);
                    $('#cost').val(batch.cost);
                    $('#sellingPrice').val(batch.selling_price);
                    $('#discountPrice').val(batch.discount_price || '');
                    $('#initialStock').val(batch.quantity);
                    $('#alertQuantity').val(batch.alert_quantity);
                }
            }

            // Handle combo products
            if (product.product_type === 'combo' && product.combo_components) {
                $('#comboProductsContainer').empty();

                product.combo_components.forEach(component => {
                    addComboProductRow({
                        id: component.component_product_id,
                        name: component.product_name,
                        quantity: component.quantity
                    });
                });
            }
        }

        function toggleProductTypeSections(productType) {
            // Hide all product type specific sections first
            $('#pricingCard, #comboProductsCard').addClass('hidden');
            $('#hasVariantContainer').addClass('hidden');

            // Show relevant sections based on product type
            switch (productType) {
                case 'standard':
                    $('#pricingCard').removeClass('hidden');
                    $('#hasVariantContainer').removeClass('hidden');
                    break;

                case 'combo':
                    $('#comboProductsCard').removeClass('hidden');
                    break;

                case 'digital':
                case 'service':
                    $('#pricingCard').removeClass('hidden');
                    $('#hasVariant').prop('checked', false).trigger('change');
                    $('#initialStock').val(0).prop('disabled', true);
                    $('#alertQuantity').val(0).prop('disabled', true);
                    break;
            }
        }

        function addVariantRow(data = null) {
            const variantIndex = $('#variantsContainer .variant-row').length;
            const isNew = !data || !data.batchId;

            let html = `
                <div class="variant-row" data-index="${variantIndex}">
                    <input type="hidden" class="variant-batch-id" name="variants[${variantIndex}][batchId]" value="${data ? data.batchId || 0 : 0}">
                    <input type="hidden" class="variant-is-new" name="variants[${variantIndex}][isNew]" value="${isNew ? 1 : 0}">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Variant Name / Batch Number</label>
                            <input type="text" class="form-control variant-name" name="variants[${variantIndex}][name]" value="${data ? data.name || '' : ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Additional Note (Optional)</label>
                            <input type="text" class="form-control variant-value" name="variants[${variantIndex}][value]" value="${data ? data.value || '' : ''}">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label">Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-cost" name="variants[${variantIndex}][cost]" min="0" step="any" value="${data ? data.cost || 0 : 0}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Selling Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-price" name="variants[${variantIndex}][price]" min="0" step="any" value="${data ? data.price || 0 : 0}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Promotional Price</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control variant-discount" name="variants[${variantIndex}][discountPrice]" min="0" step="any" value="${data && data.discountPrice ? data.discountPrice : ''}">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control variant-qty" name="variants[${variantIndex}][quantity]" min="0" value="${data ? data.quantity || 0 : 0}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Alert Quantity</label>
                            <input type="number" class="form-control variant-alert" name="variants[${variantIndex}][alert]" min="0" value="${data ? data.alert || 5 : 5}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-danger remove-variant-btn" onclick="removeVariantRow(this)">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                    <hr>
                </div>
            `;

            $('#variantsContainer').append(html);
        }

        function removeVariantRow(btn) {
            // Get the batch ID from the hidden input
            const batchId = parseInt($(btn).closest('.variant-row').find('.variant-batch-id').val());
            
            // Add batch ID to deleted batches array if it exists
            if (batchId > 0) {
                deletedBatches.push(batchId);
            }

            // Remove the variant row
            $(btn).closest('.variant-row').remove();

            // If no variants left, switch back to single variant
            if ($('#variantsContainer .variant-row').length === 0) {
                $('#hasVariant').prop('checked', false).trigger('change');
            }
        }

        function addComboProductRow(data = null) {
            const comboIndex = $('#comboProductsContainer .combo-item').length;

            let html = `
                <div class="combo-item" data-index="${comboIndex}">
                    <div class="row mb-2">
                        <div class="col-md-8">
                            <label class="form-label">Product</label>
                            <select class="form-select combo-product-select" name="comboProducts[${comboIndex}][productId]" required>
                                <option value="">Select Product</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Quantity</label>
                            <div class="input-group">
                                <input type="number" class="form-control combo-quantity" name="comboProducts[${comboIndex}][quantity]" min="1" value="${data ? data.quantity || 1 : 1}" required>
                                <button type="button" class="btn btn-danger remove-combo-btn" onclick="removeComboProduct(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#comboProductsContainer').append(html);

            // Load products for selection
            const select = $('#comboProductsContainer .combo-item').last().find('.combo-product-select');
            loadProductsForCombo(select, data ? data.id : null);
        }

        function removeComboProduct(btn) {
            $(btn).closest('.combo-item').remove();
        }

        function loadProductsForCombo(select, selectedValue = null) {
            $.ajax({
                url: 'API/getProducts.php',
                type: 'GET',
                success: function(response) {
                    let options = '<option value="">Select Product</option>';
                    if (response && response.length > 0) {
                        response.forEach(product => {
                            options += `<option value="${product.id}" ${selectedValue == product.id ? 'selected' : ''}>${product.name}</option>`;
                        });
                    }
                    $(select).html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load products:', error);
                }
            });
        }

        function showSuccessMessage(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        }

        function showErrorMessage(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });
        }

        function validateForm() {
            // Basic validation
            if (!$('#productName').val().trim()) {
                showErrorMessage('Product name is required');
                $('#productName').focus();
                return false;
            }

            if (!$('#productCode').val().trim()) {
                showErrorMessage('Product code/barcode is required');
                $('#productCode').focus();
                return false;
            }

            if (!$('#sku').val().trim()) {
                showErrorMessage('SKU is required');
                $('#sku').focus();
                return false;
            }

            // Product type specific validation
            const productType = $('#productType').val();

            if (productType === 'standard' || productType === 'digital' || productType === 'service') {
                if (!$('#hasVariant').is(':checked')) {
                    // Single variant validation
                    if (parseFloat($('#cost').val()) <= 0) {
                        showErrorMessage('Cost must be greater than zero');
                        $('#cost').focus();
                        return false;
                    }

                    if (parseFloat($('#sellingPrice').val()) <= 0) {
                        showErrorMessage('Selling price must be greater than zero');
                        $('#sellingPrice').focus();
                        return false;
                    }

                    if (parseFloat($('#sellingPrice').val()) < parseFloat($('#cost').val())) {
                        showErrorMessage('Selling price cannot be less than cost');
                        $('#sellingPrice').focus();
                        return false;
                    }
                } else {
                    // Multiple variants validation
                    if ($('#variantsContainer .variant-row').length === 0) {
                        showErrorMessage('At least one variant is required');
                        return false;
                    }

                    let variantValid = true;
                    $('#variantsContainer .variant-row').each(function() {
                        const name = $(this).find('.variant-name').val().trim();
                        const value = $(this).find('.variant-value').val().trim();
                        const cost = parseFloat($(this).find('.variant-cost').val());
                        const price = parseFloat($(this).find('.variant-price').val());

                        if (!name) {
                            showErrorMessage('Variant name is required for all variants');
                            $(this).find('.variant-name').focus();
                            variantValid = false;
                            return false;
                        }

                        // Make variant value optional
                        // if (!value) {
                        //     showErrorMessage('Variant value is required for all variants');
                        //     $(this).find('.variant-value').focus();
                        //     variantValid = false;
                        //     return false;
                        // }

                        if (cost <= 0) {
                            showErrorMessage('Cost must be greater than zero for all variants');
                            $(this).find('.variant-cost').focus();
                            variantValid = false;
                            return false;
                        }

                        if (price <= 0) {
                            showErrorMessage('Selling price must be greater than zero for all variants');
                            $(this).find('.variant-price').focus();
                            variantValid = false;
                            return false;
                        }

                        if (price < cost) {
                            showErrorMessage('Selling price cannot be less than cost for variant: ' + name);
                            $(this).find('.variant-price').focus();
                            variantValid = false;
                            return false;
                        }
                    });

                    if (!variantValid) return false;
                }
            } else if (productType === 'combo') {
                // Combo product validation
                if ($('#comboProductsContainer .combo-item').length === 0) {
                    showErrorMessage('At least one product must be added to the combo');
                    return false;
                }

                let comboValid = true;
                $('#comboProductsContainer .combo-item').each(function() {
                    const productId = $(this).find('.combo-product-select').val();
                    const quantity = parseInt($(this).find('.combo-quantity').val());

                    if (!productId) {
                        showErrorMessage('Please select a product for all combo items');
                        $(this).find('.combo-product-select').focus();
                        comboValid = false;
                        return false;
                    }

                    if (quantity <= 0) {
                        showErrorMessage('Quantity must be at least 1 for all combo items');
                        $(this).find('.combo-quantity').focus();
                        comboValid = false;
                        return false;
                    }
                });

                if (!comboValid) return false;
            }

            return true;
        }

        function collectFormData() {
            const productType = $('#productType').val();
            const hasVariant = $('#hasVariant').is(':checked');

            const formData = {
                productId: $('#productId').val(),
                productType: productType,
                productName: $('#productName').val().trim(),
                productCode: $('#productCode').val().trim(),
                barcodeSymbology: $('#barcodeSymbology').val(),
                brandId: $('#brand').val() || null,
                categoryId: $('#category').val() || null,
                sku: $('#sku').val().trim(),
                showInEcommerce: $('#showInEcommerce').is(':checked'),
                activeStatus: $('#activeStatus').is(':checked'),
                hasVariant: hasVariant,
                batchesToDelete: deletedBatches
            };

            // Product type specific data
            if (productType === 'standard' || productType === 'digital' || productType === 'service') {
                if (!hasVariant) {
                    // Single variant
                    formData.batchId = $('#batchId').val();
                    formData.cost = parseFloat($('#cost').val());
                    formData.sellingPrice = parseFloat($('#sellingPrice').val());
                    formData.discountPrice = $('#discountPrice').val() ? parseFloat($('#discountPrice').val()) : null;
                    formData.initialStock = parseInt($('#initialStock').val());
                    formData.alertQuantity = parseInt($('#alertQuantity').val());
                } else {
                    // Multiple variants
                    formData.variants = [];
                    $('#variantsContainer .variant-row').each(function() {
                        const discountValue = $(this).find('.variant-discount').val();
                        const noteValue = $(this).find('.variant-value').val().trim();
                        console.log('Discount value:', discountValue);
                        console.log('Note value:', noteValue);
                        
                        const variant = {
                            batchId: $(this).find('.variant-batch-id').val() || 0,
                            isNew: $(this).find('.variant-is-new').val() === '1',
                            variantName: $(this).find('.variant-name').val().trim(),
                            variantValue: noteValue,
                            variantCost: parseFloat($(this).find('.variant-cost').val()),
                            variantPrice: parseFloat($(this).find('.variant-price').val()),
                            variantDiscountPrice: discountValue ? parseFloat(discountValue) : null,
                            variantQuantity: parseInt($(this).find('.variant-qty').val()),
                            variantAlertQty: parseInt($(this).find('.variant-alert').val())
                        };
                        console.log('Variant object:', variant);
                        formData.variants.push(variant);
                    });
                }
            } else if (productType === 'combo') {
                // Combo products
                formData.comboProducts = [];
                $('#comboProductsContainer .combo-item').each(function() {
                    const combo = {
                        productId: $(this).find('.combo-product-select').val(),
                        quantity: parseInt($(this).find('.combo-quantity').val())
                    };
                    formData.comboProducts.push(combo);
                });
            }

            return formData;
        }

        function setupListeners() {
            // Toggle variant sections based on checkbox
            $('#hasVariant').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#singleVariantSection').addClass('hidden');
                    $('#multipleVariantsSection').removeClass('hidden');
                } else {
                    $('#singleVariantSection').removeClass('hidden');
                    $('#multipleVariantsSection').addClass('hidden');
                }
            });

            // Handle product type selection
            $('#productType').on('change', function() {
                const productType = $(this).val();
                toggleProductTypeSections(productType);
            });
            
            // Detect barcode symbology automatically
            $('#productCode').on('input', function() {
                const barcode = $(this).val().trim();
                if (barcode) {
                    const symbology = detectBarcodeSymbology(barcode);
                    $('#barcodeSymbology').val(symbology);
                }
            });

            // Add variant button click
            $('#addVariantBtn').on('click', function() {
                addVariantRow();
            });

            // Add combo product button click
            $('#addComboProductBtn').on('click', function() {
                addComboProductRow();
            });

            // Image preview handling
            $('#productImage').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').attr('src', e.target.result).removeClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            // E-commerce checkbox change
            $('#showInEcommerce').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#imageUploadSection').removeClass('hidden');
                } else {
                    $('#imageUploadSection').addClass('hidden');
                }
            });
        }
        
        // Function to detect barcode symbology based on input
        function detectBarcodeSymbology(barcode) {
            // Remove any whitespace
            barcode = barcode.replace(/\s/g, '');
            
            // Check for EAN-13 (13 digits)
            if (/^\d{13}$/.test(barcode)) {
                return 'EAN13';
            }
            
            // Check for EAN-8 (8 digits)
            if (/^\d{8}$/.test(barcode)) {
                return 'EAN8';
            }
            
            // Check for UPC-A (12 digits)
            if (/^\d{12}$/.test(barcode)) {
                return 'UPC';
            }
            
            // Check for CODE39 (uppercase letters, digits, and some special chars starting and ending with *)
            if (/^[A-Z0-9\-\.\$\/\+\%\s]+$/.test(barcode) && barcode.indexOf('*') !== -1) {
                return 'CODE39';
            }
            
            // Default to CODE128 (most versatile)
            return 'CODE128';
        }

        function setupFormValidation() {
            // Form submission handling - Use JavaScript instead of form attributes
            // This prevents browser validation issues
            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                const productData = collectFormData();
                console.log('Product data to be sent:', productData);
                const productId = $('#productId').val();

                // Create FormData object for file uploads
                const formData = new FormData();
                formData.append('productData', JSON.stringify(productData));
                formData.append('productId', productId);

                // Add image if selected
                if ($('#productImage')[0].files.length > 0) {
                    formData.append('productImage', $('#productImage')[0].files[0]);
                }

                // Disable submit button and show loading
                const submitButton = $('#submitButton');
                const originalButtonText = submitButton.html();
                submitButton.html('<i class="fas fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                $('#loader').show();

                // Send AJAX request
                $.ajax({
                    url: 'API/updateProduct.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            showSuccessMessage(response.message);
                            setTimeout(() => {
                                window.location.href = 'index.php';
                            }, 2000);
                        } else {
                            // Reset the deleted batches array if update failed
                            deletedBatches = [];

                            // Show error message
                            if (response.field) {
                                showErrorMessage(response.message + ` (${response.field})`);
                            } else {
                                showErrorMessage(response.message);
                            }
                        }
                    },
                    error: function(xhr) {
                        // Reset the deleted batches array if update failed
                        deletedBatches = [];

                        try {
                            const response = JSON.parse(xhr.responseText);
                            showErrorMessage(response.message || 'An error occurred while updating the product');
                        } catch (e) {
                            showErrorMessage('An error occurred while updating the product');
                        }
                    },
                    complete: function() {
                        submitButton.html(originalButtonText).prop('disabled', false);
                        $('#loader').hide();
                    }
                });
            });
        }
        
        // Auto detect barcode symbology when the barcode field changes
        function setupBarcodeSymbologyDetection() {
            $('#productCode').on('change', function() {
                const barcode = $(this).val();
                if (!barcode) return;
                
                $.ajax({
                    url: 'API/detect_barcode_symbology.php',
                    method: 'GET',
                    data: { barcode: barcode },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#barcodeSymbology').val(response.symbology);
                            console.log('Detected barcode symbology: ' + response.symbology);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error detecting barcode symbology:', error);
                    }
                });
            });
        }
        
        // Initialize everything when document is ready
        $(document).ready(function() {
            // loadProductData();
            setupFormValidation();
            setupBarcodeSymbologyDetection();
            setupEventHandlers();
        });
    </script>
</body>

</html>