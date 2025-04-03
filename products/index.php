<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List - Advanced POS System</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
        }

        .card-header {
            background: linear-gradient(to right, #4a90e2, #5a9de2);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px 20px;
        }

        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 5px;
            transition: all 0.2s;
        }

        .btn-view {
            background-color: #4a90e2;
            color: white;
        }

        .btn-edit {
            background-color: #28a745;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .stock-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
        }

        .stock-low {
            background-color: #ffebee;
            color: #c62828;
        }

        .stock-medium {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .stock-high {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 5px 10px;
        }

        .dataTables_wrapper .dataTables_length select {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 5px 30px 5px 10px;
            margin: 0 5px;
            height: auto;
            background-position: right 0.5rem center;
        }

        .dataTables_length {
            margin-bottom: 10px;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .modal-content {
            border-radius: 10px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(to right, #4a90e2, #5a9de2);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .product-details {
            padding: 20px;
        }

        .product-details img {
            max-width: 100%;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .product-info {
            margin-bottom: 20px;
        }

        .product-info h4 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .product-info p {
            margin-bottom: 10px;
            color: #666;
        }

        .product-info strong {
            color: #2c3e50;
        }

        .variant-table {
            margin-top: 20px;
        }

        .variant-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .combo-products {
            margin-top: 20px;
        }

        .combo-product-item {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .combo-product-item h5 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .combo-product-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .combo-product-detail {
            padding: 5px 10px;
            background-color: white;
            border-radius: 3px;
        }

        .combo-product-detail strong {
            color: #2c3e50;
        }

        .loader {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            display: none;
        }

        .loader-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .loader-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4a90e2;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* DataTables specific styling */
        div.dt-button-collection {
            width: auto;
            padding: 10px;
        }

        .dt-button {
            margin: 3px !important;
        }
        
        /* Responsive layout improvements */
        @media (max-width: 768px) {
            .buttons-collection {
                display: block;
                width: 100%;
                margin-bottom: 10px;
            }
            
            .dataTables_filter {
                margin-top: 10px;
                width: 100%;
            }
            
            .dataTables_filter input {
                width: 100%;
            }
            
            .dataTables_length {
                text-align: left;
                float: none !important;
            }
            
            .dataTables_info, .dataTables_paginate {
                text-align: center;
                float: none !important;
                display: block;
                margin: 10px auto;
            }
        }

        .btn-toggle .fa-toggle-on {
            color: #28a745;
            font-size: 1.2em;
        }

        .btn-toggle .fa-toggle-off {
            color: #dc3545;
            font-size: 1.2em;
        }
        
        /* Promotional Price Styles */
        .promo-price {
            color: #28a745;
            font-weight: 500;
        }
        
        .promo-price small {
            display: block;
            font-size: 0.85em;
            font-weight: bold;
            margin-top: 3px;
        }
        
        .promo-badge {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-radius: 4px;
            padding: 2px 4px;
            font-size: 0.7em;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Product List</h4>
                <a href="create/" class="btn btn-light">
                    <i class="fas fa-plus"></i> Add New Product
                </a>
            </div>
            <div class="card-body">
                <table id="productTable" class="table table-striped display responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Barcode</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Promo Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Product View Modal -->
    <div class="modal fade" id="productViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="productDetails" class="product-details">
                        <!-- Product details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader" class="loader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <p>Loading...</p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable with server-side processing
            $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'API/getProductsDataTable.php',
                    type: 'POST'
                },
                columns: [
                    { data: 'product_id' },
                    { data: 'product_name' },
                    { data: 'barcode' },
                    { data: 'sku' },
                    { data: 'category_name' },
                    { data: 'brand_name' },
                    { data: 'product_type' },
                    { data: 'stock' },
                    { data: 'price' },
                    { 
                        data: 'discount_price',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                // Check if discount_price exists and is less than regular price
                                if (data && data != null) {
                                    var regularPrice = parseFloat(row.price.replace('Rs. ', '').replace(/,/g, ''));
                                    var discountPrice = parseFloat(data);
                                    
                                    // Only show if discount price is less than regular price
                                    if (discountPrice < regularPrice) {
                                        // Calculate discount percentage
                                        var discountPercent = Math.round((regularPrice - discountPrice) / regularPrice * 100);
                                        
                                        // Display promo price with discount percentage below in smaller font
                                        return '<div class="promo-price">Rs. ' + 
                                               parseFloat(discountPrice).toFixed(2) + 
                                               '<small><span class="promo-badge">' + discountPercent + '% off</span></small></div>';
                                    }
                                }
                                return '-';
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    }
                ],
                dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>><"row"<"col-sm-12 col-md-2"l><"col-sm-12 col-md-10"r>><"row"<"col-sm-12"t>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="fas fa-download"></i> Export',
                        className: 'btn btn-primary',
                        buttons: [
                            'copy', 
                            'excel', 
                            'csv', 
                            'pdf', 
                            'print'
                        ]
                    }
                ],
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                pageLength: 10,
                order: [[0, 'asc']],
                responsive: {
                    details: {
                        type: 'column',
                        target: 'tr'
                    }
                },
                deferRender: true,
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>'
                    }
                }
            });
        });

        // View Product
        function viewProduct(productId) {
            $('#loader').show();

            $.ajax({
                url: 'API/getProductDetails.php',
                type: 'GET',
                data: {
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        const product = response.data;
                        let html = `
                            <div class="product-details">
                                <div class="row mb-3">
                                    <div class="col-md-4 text-center">
                                        <img src="../uploads/products/${product.image || 'no-image.png'}" alt="${product.product_name}" 
                                            class="img-fluid" style="max-height: 150px; max-width: 100%;">
                                    </div>
                                    <div class="col-md-8">
                                        <h4>${product.product_name}</h4>
                                        <div class="product-meta">
                                            <p><strong>SKU:</strong> ${product.sku}</p>
                                            <p><strong>Barcode:</strong> ${product.barcode}</p>
                                            <p><strong>Category:</strong> ${product.category_name || '-'}</p>
                                            <p><strong>Brand:</strong> ${product.brand_name || '-'}</p>
                                            <p>
                                                <strong>Status:</strong> 
                                                <span class="badge ${product.active_status == 1 ? 'bg-success' : 'bg-danger'}">
                                                    ${product.active_status == 1 ? 'Active' : 'Inactive'}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                        `;

                        // Add variants section if product has variants
                        if (product.batches && product.batches.length > 0) {
                            html += `
                                <div class="variant-table">
                                    <h5 class="mt-3">Pricing & Stock</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Variant/Batch</th>
                                                    <th>Cost</th>
                                                    <th>Price</th>
                                                    <th>Discount</th>
                                                    <th>Stock</th>
                                                    <th>Alert Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;

                            product.batches.forEach(batch => {
                                html += `
                                    <tr>
                                        <td>
                                            <strong>${batch.batch_number}</strong>
                                            ${batch.notes ? '<br><small>' + batch.notes + '</small>' : ''}
                                        </td>
                                        <td>Rs. ${parseFloat(batch.cost).toFixed(2)}</td>
                                        <td>Rs. ${parseFloat(batch.selling_price).toFixed(2)}</td>
                                        <td>${batch.discount_price ? 'Rs. ' + parseFloat(batch.discount_price).toFixed(2) : '-'}</td>
                                        <td>${batch.quantity}</td>
                                        <td>${batch.alert_quantity}</td>
                                    </tr>
                                `;
                            });

                            html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `;
                        }

                        // Add combo products section if it's a combo product
                        if (product.product_type === 'combo' && product.combo_components && product.combo_components.length > 0) {
                            html += `
                                <div class="combo-products mt-3">
                                    <h5>Combo Components</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                            `;
                            
                            product.combo_components.forEach(component => {
                                html += `
                                    <tr>
                                        <td>${component.product_name}</td>
                                        <td>${component.quantity}</td>
                                    </tr>
                                `;
                            });
                            
                            html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            `;
                        }
                        
                        html += '</div>'; // Close product-details div

                        Swal.fire({
                            title: 'Product Details',
                            html: html,
                            width: '800px',
                            showCloseButton: true,
                            showConfirmButton: false,
                            customClass: {
                                container: 'product-view-modal'
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load product details'
                    });
                },
                complete: function() {
                    $('#loader').hide();
                }
            });
        }

        // Edit Product
        function editProduct(productId) {
            window.location.href = `edit.php?id=${productId}`;
        }

        // Delete Product
        function deleteProduct(productId, productName) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete "${productName}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loader').show();
                    
                    $.ajax({
                        url: 'API/deleteProduct.php',
                        type: 'POST',
                        data: {
                            productId: productId
                        },
                        success: function(response) {
                            if (response.success) {
                                let title = 'Success';
                                let icon = 'success';
                                
                                // Different message based on action (deleted or deactivated)
                                if (response.action && response.action === 'deactivated') {
                                    title = 'Product Deactivated';
                                }
                                
                                Swal.fire({
                                    icon: icon,
                                    title: title,
                                    text: response.message,
                                    timer: 2000
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to process product: ' + error
                            });
                        },
                        complete: function() {
                            $('#loader').hide();
                        }
                    });
                }
            });
        }

        // Toggle Product Status (Active/Inactive)
        function toggleProductStatus(productId, currentStatus) {
            // Convert to numeric value
            const isActive = parseInt(currentStatus) === 1;
            const newStatus = isActive ? 0 : 1;
            const action = isActive ? 'deactivate' : 'activate';
            const confirmTitle = isActive ? 'Deactivate Product?' : 'Activate Product?';
            const confirmText = `Are you sure you want to ${action} this product?`;
            
            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#dc3545' : '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: isActive ? 'Yes, deactivate it!' : 'Yes, activate it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loader').show();
                    
                    $.ajax({
                        url: 'API/toggleProductStatus.php',
                        type: 'POST',
                        data: {
                            productId: productId,
                            status: newStatus
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Status Updated',
                                    text: response.message,
                                    timer: 1500
                                }).then(() => {
                                    // Reload the page to show updated status
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update product status: ' + error
                            });
                        },
                        complete: function() {
                            $('#loader').hide();
                        }
                    });
                }
            });
        }
    </script>
</body>

</html>