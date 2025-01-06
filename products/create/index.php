<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php';?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                        <label for="productType" class="form-label">Product Type</label>
                        <select id="productType" class="form-select" required>
                            <option value="standard">Standard Product</option>
                            <option value="combo">Combo Product</option>
                            <option value="digital">Digital Product</option>
                            <option value="service">Service Product</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>

                        <div class="mb-3">
                            <label for="productCode" class="form-label">Product Code / Barcode</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="productCode" required maxlength="13">
                                <button type="button" class="btn btn-secondary" id="generateCodeBtn">
                                    <i class="fas fa-random"></i> Generate
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="barcodeSymbology" class="form-label">Barcode Symbology</label>
                            <select class="form-select" id="barcodeSymbology">
                                <option value="EAN13">EAN-13</option>
                                <option value="EAN8">EAN-8</option>
                                <option value="UPC">UPC</option>
                                <option value="CODE128">Code 128</option>
                                <option value="QR">QR Code</option>
                                <option value="ISBN13">ISBN-13</option>
                                <option value="DATAMATRIX">Data Matrix</option>
                                <option value="CODE39">Code 39</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <div class="input-group">
                                <select id="brand" class="form-select">
                                    <option value="">Select Brand</option>
                                </select>
                                <button type="button" class="btn btn-action" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                                    Add New Brand
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
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
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" required>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
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

                        <div class="mb-3" id="initialStockSection">
                            <label for="initialStock" class="form-label">Initial Stock Quantity</label>
                            <input type="number" class="form-control" id="initialStock">
                        </div>


                        <div class="mb-3">
                            <label for="cost" class="form-label">Cost</label>
                            <input type="number" class="form-control" id="cost" required>
                        </div>

                        <div class="mb-3">
                            <label for="sellingPrice" class="form-label">Selling Price</label>
                            <input type="number" class="form-control" id="sellingPrice" required>
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

                <!-- Variant Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="hasVariant">
                            <label class="form-check-label" for="hasVariant">This Product Has Variant</label>
                        </div>

                        <div id="variantTableContainer" class="hidden">
                            <table id="variantTable" class="table variant-table">
                                <thead>
                                    <tr>
                                        <th>Variation / Batch Name / Batch Number</th>
                                        <th>Additional Note</th>
                                        <th>Initial Stock</th>
                                        <th>Cost</th>
                                        <th>Selling Price</th>
                                        <th>Alert Quantity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="variantTableBody">
                                    <!-- Dynamic variant rows will be added here -->
                                </tbody>
                            </table>
                            <div class="text-end">
                                <button type="button" id="addVariantBtn" class="btn btn-add">
                                    <i class="fas fa-plus"></i> Add Variant
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Combo Product Section -->
                <div id="comboProductSection" class="hidden">
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Combo Product Composition</h4>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Batch Name</th>
                                        <th>Unit Cost</th>
                                        <th>Unit Selling Price</th>
                                        <th>Quantity</th>
                                        <th>Total Cost</th>
                                        <th>Total Selling Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="comboProductTableBody">
                                    <!-- Dynamic combo product rows will be added here -->
                                </tbody>
                            </table>
                            <div class="text-end">
                                <strong>Total Combo Cost: </strong>
                                <span id="totalComboCost">0.00</span>
                            </div>
                            <div class="text-end">
                                <button type="button" id="addComboProductBtn" class="btn btn-add">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
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

            // Initialize Select2 for dropdowns
            $('select').select2();
            loadInitialData(); // Load initial options (brands, categories, suppliers)
            // Load Product list for combo product
            loadProductsForCombo();
            $('#comboProductSelect').on('change', function() {
                const productId = $(this).val();
                if (productId) {
                    fetchBatches(productId);
                } else {
                    resetBatchSelection();
                }
            });

            function fetchBatches(productId) {
                $.ajax({
                    url: '../API/fetch_Single_product_batches.php',
                    type: 'GET',
                    data: {
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(batches) {
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
                        console.error('Error fetching batches:', error);
                        alert('Unable to fetch batches. Please try again.');
                    }
                });
            }

            function prioritizeBatch(batches) {
                console.log(batches);
                return batches
                    .filter(batch => batch.status === 'active' && batch.quantity > 1)
                    .sort((a, b) => new Date(b.restocked_at) - new Date(a.restocked_at))[0];
                console.log(batches);
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
                } else if (productType === 'combo') {
                    $('#initialStockSection, #unitSection').addClass('hidden');
                    $('#comboProductSection').removeClass('hidden');
                } else if (productType === 'digital' || productType === 'service') {
                    $('#unitSection, #initialStockSection').addClass('hidden');
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
                $('#variantTableContainer').toggleClass('hidden', !this.checked);
                $('#variantTableBody').empty();
            });


            let variantCount = 0;

            $('#addVariantBtn').on('click', function() {
                variantCount++;
                const newVariantRow = `
             <tr id="variant-row-${variantCount}">
                <td><input type="text" class="form-control variant-name" required></td>
                <td><input type="text" class="form-control variant-value" required></td>
                <td><input type="number" class="form-control variant-stock" value="0"></td>
                <td><input type="number" class="form-control variant-cost" value="0"></td>
                <td><input type="number" class="form-control variant-price" value="0"></td>
                <td><input type="number" class="form-control variant-alert" value="0"></td>
                 <td><button type="button" class="btn btn-danger deleteVariantBtn" data-variant-id="${variantCount}"><i class="fas fa-trash"></i></button></td>
            </tr>`;
                $('#variantTableBody').append(newVariantRow);
            });
            // Delete variant row
            $(document).on('click', '.deleteVariantBtn', function() {
                const variantId = $(this).data('variant-id');
                $(`#variant-row-${variantId}`).remove();
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
                    <tr id="combo-row-${comboProductCount}" data-product-id="${productId}">
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

                $('#comboProductTableBody tr').each(function() {
                    const costText = $(this).find('.combo-total-cost').text();
                    const sellingPriceText = $(this).find('.combo-total-selling-price').text();

                    const cost = parseFloat(costText) || 0;
                    const sellingPrice = parseFloat(sellingPriceText) || 0;

                    totalCost += cost;
                    totalSellingPrice += sellingPrice;
                });

                $('#totalComboCost').text(totalCost.toFixed(2));
                $('#cost').val(totalCost.toFixed(2));
                $('#sellingPrice').val(totalSellingPrice.toFixed(2));
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
                        $('#comboProductSelect').select2();
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
                const productData = {
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
                    comboProducts: []
                };

                // Add type-specific data
                if (productData.productType === 'standard') {
                    productData.defaultUnit = $('#defaultUnit').val();
                    productData.saleUnit = $('#saleUnit').val();
                    productData.purchaseUnit = $('#purchaseUnit').val();
                    productData.pcsPerBox = $('#pcsPerBox').val();
                    productData.initialStock = parseFloat($('#initialStock').val()) || 0;
                    productData.cost = parseFloat($('#cost').val()) || 0;
                    productData.sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
                }

                // Collect variants if enabled
                if (productData.hasVariant) {
                    $('#variantTableBody tr').each(function(index) {
                        const variant = {
                            variantName: $(this).find('.variant-name').val().trim(),
                            variantValue: $(this).find('.variant-value').val().trim(),
                            variantStock: parseFloat($(this).find('.variant-stock').val()) || 0,
                            variantCost: parseFloat($(this).find('.variant-cost').val()) || 0,
                            variantPrice: parseFloat($(this).find('.variant-price').val()) || 0,
                            variantAlert: parseFloat($(this).find('.variant-alert').val()) || 0
                        };
                        productData.variants.push(variant);
                    });
                }

                // Collect combo products if applicable
                if (productData.productType === 'combo') {
                    $('#comboProductTableBody tr').each(function() {
                        const comboProduct = {
                            productId: $(this).data('product-id'),
                            quantity: parseFloat($(this).find('.combo-quantity').val()) || 0
                        };
                        productData.comboProducts.push(comboProduct);
                    });
                }

                return productData;
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
                    });
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
        });
    </script>
</body>

</html>