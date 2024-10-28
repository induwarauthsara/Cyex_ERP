<?php require_once 'inc/config.php'; ?>
<?php require 'inc/header.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $ERP_COMPANY_NAME; ?> - POS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="logo.png" type="image/x-icon">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }

    .main {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .header,
    .content,
    .button {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }

    .modal.active {
        display: block;
    }

    #product-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
    }

    .product-list .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }
</style>

<body>
    <div class="main">
        <div class="header">
            <a href="/index.php"><img src="logo.png" alt="LOGO"></a>
            <div class="company-details">
                <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
                <h2><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h2>
            </div>
        </div>
        <hr>
        <div class="content">
            <form id="invoice-form">
                <div class="details">
                    <div class="customer-details">
                        <label for="name">Customer Name: </label>
                        <input type="text" name="name" id="name" autocomplete="off" readonly placeholder="Walk-in Customer" onclick="searchCustomer()">
                        <label for="tele">Customer Number: </label>
                        <input type="text" name="tele" id="tele" autocomplete="off" readonly placeholder="0" onclick="searchCustomer()">
                    </div>
                </div>
                <div class="product-container">
                    <input type="text" id="product-input" placeholder="Enter Product Name / SKU / Scan Code" autocomplete="off">
                    <button id="add-product">Add Product</button>
                </div>
                <div class="product-list" id="product-list"></div>
            </form>
        </div>
        <div class="button">
            <button id="pay-btn">Pay</button>
            <button id="credit-btn">Credit</button>
        </div>
    </div>

    <!-- Modal for Batch Selection -->
    <div class="modal" id="batch-modal">
        <h3>Select Batch</h3>
        <div id="batch-list"></div>
        <button id="close-modal">Close</button>
    </div>

    <script>
        let productList = JSON.parse(localStorage.getItem('productList')) || [];
        let productsCache = JSON.parse(localStorage.getItem('productsCache')) || {};

        $(document).ready(function() {
            renderProductList();

            $('#add-product').click(function(e) {
                e.preventDefault();
                let product = $('#product-input').val().trim();
                if (product) {
                    fetchProduct(product);
                }
                $('#product-input').val('');
            });

            $(document).on('keydown', function(event) {
                if (event.key === "Insert") {
                    $('#product-input').focus();
                }
            });

            $('#close-modal').click(function() {
                $('#batch-modal').removeClass('active');
            });

            $(document).on('click', '.select-batch', function() {
                let product = JSON.parse($(this).attr('data-product'));
                let batch = JSON.parse($(this).attr('data-batch'));
                addToCart(product, batch);
                $('#batch-modal').removeClass('active');
            });
        });

        function fetchProduct(product) {
            if (productsCache[product]) {
                handleProductResponse(productsCache[product]);
            } else {
                $.ajax({
                    url: '/inc/fetch_product.php',
                    method: 'GET',
                    data: {
                        search: product
                    },
                    success: function(data) {
                        let response = typeof data === "object" ? data : JSON.parse(data);
                        productsCache[product] = response;
                        localStorage.setItem('productsCache', JSON.stringify(productsCache));
                        handleProductResponse(response);
                    },
                    error: function(xhr) {
                        alert("Failed to fetch product data.");
                    }
                });
            }
        }

        function handleProductResponse(response) {
            if (response.products.length === 1) {
                let product = response.products[0];
                if (response.batches.length === 1) {
                    addToCart(product, response.batches[0]);
                } else if (response.batches.length > 1) {
                    displayBatchModal(product, response.batches);
                } else {
                    addToCart(product);
                }
            } else {
                alert("Multiple or no products found.");
            }
        }

        function displayBatchModal(product, batches) {
            let batchList = batches.map(batch => `
                <div class="batch-item">
                    <span>Batch: ${batch.batch_number}</span>
                    <span>Price: ${batch.selling_price}</span>
                    <span>Expiry: ${batch.expiry_date || 'N/A'}</span>
                    <span>Quantity: ${batch.batch_quantity}</span>
                    <button class="select-batch" data-product='${JSON.stringify(product)}' data-batch='${JSON.stringify(batch)}'>Select</button>
                </div>
            `).join('');
            $('#batch-list').html(batchList);
            $('#batch-modal').addClass('active');
        }

        function addToCart(product, batch = null) {
            let existingProduct = productList.find(p =>
                p.product_id === product.product_id &&
                (!batch || p.batch_id === batch.batch_id)
            );

            if (existingProduct) {
                existingProduct.quantity++;
                existingProduct.subtotal = existingProduct.quantity * existingProduct.price;
            } else {
                productList.push({
                    product_id: product.product_id,
                    batch_id: batch ? batch.batch_id : null,
                    name: product.product_name,
                    price: batch ? batch.selling_price : product.rate,
                    quantity: 1,
                    discount: 0,
                    subtotal: batch ? batch.selling_price : product.rate
                });
            }
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
        }

        function renderProductList() {
            $('#product-list').empty();
            productList.forEach((product, index) => {
                $('#product-list').append(`
                    <div class="product-item">
                        <span>${product.name}</span>
                        <span>Qty: ${product.quantity}</span>
                        <span>Price: ${product.price}</span>
                        <span>Subtotal: ${product.subtotal}</span>
                        <button class="remove-product" onclick="removeProduct(${index})">Remove</button>
                    </div>
                `);
            });
        }

        function removeProduct(index) {
            productList.splice(index, 1);
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
        }
    </script>

</body>

</html>

<script src="inc/searchCustomer function.js"></script>