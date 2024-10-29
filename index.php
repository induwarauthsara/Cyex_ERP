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

    .product-container {
        display: flex;
        gap: 10px;
    }

    .product-list .product-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #ccc;
    }

    .product-item input {
        width: 60px;
        padding: 5px;
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

    .shortcut-button {
        margin-left: 10px;
    }

    #product-input {
        width: 100%;
        padding: 10px;
        font-size: 16px;
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
            <button class="shortcut-button" onclick="showShortcuts()"><i class="fas fa-keyboard"></i> &nbsp; Shortcuts</button>
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
                    <input type="text" id="product-input" placeholder="Enter Product Name / SKU / Scan Code" autocomplete="off" autofocus>
                    <button id="add-product" style="display: none;">Add Product</button>
                </div>
                <div class="product-list" id="product-list">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th style="padding: 10px; border: 1px solid #ccc;">Product Name</th>
                                <th style="padding: 10px; border: 1px solid #ccc;">Quantity</th>
                                <th style="padding: 10px; border: 1px solid #ccc;">Price</th>
                                <th style="padding: 10px; border: 1px solid #ccc;">Subtotal</th>
                                <th style="padding: 10px; border: 1px solid #ccc;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="product-table-body"></tbody>
                    </table>
                </div>
            </form>
        </div>
        <div class="button">
            <button id="pay-btn">Pay</button>
            <button id="credit-btn">Credit</button>
        </div>
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

        function showShortcuts() {
            Swal.fire({
                title: 'Keyboard Shortcuts',
                html: `<p><strong>Insert:</strong> Focus on product input</p>`,
                icon: 'info'
            });
        }

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
            let batchList = `
        <table style="width: 100%; border-collapse: collapse; margin-top: 25px;">
            <thead>
                <tr style="background-color: #f5f5f5; border: 2px solid #4800ff;">
                    <th style="padding: 10px; border: 2px solid #4800ff;">Batch</th>
                    <th style="padding: 10px; border: 2px solid #4800ff;">Price</th>
                    <th style="padding: 10px; border: 2px solid #4800ff;">Expiry</th>
                    <th style="padding: 10px; border: 2px solid #4800ff;">Quantity</th>
                    <th style="padding: 10px; border: 2px solid #4800ff;">Action</th>
                </tr>
            </thead>
            <tbody>
                ${batches.map(batch => `
                    <tr style="border: 2px solid #4800ff;">
                        <td style="padding: 10px; border: 2px solid #4800ff;"><b>${batch.batch_number}</b></td>
                        <td style="padding: 10px; border: 2px solid #4800ff;"><b>${batch.selling_price}</b></td>
                        <td style="padding: 10px; border: 2px solid #4800ff;"><b>${batch.expiry_date || 'N/A'}</b></td>
                        <td style="padding: 10px; border: 2px solid #4800ff;"><b>${batch.batch_quantity}</b></td>
                        <td style="padding: 10px; border: 2px solid #4800ff;">
                            <button class="select-batch" data-product='${JSON.stringify(product)}' data-batch='${JSON.stringify(batch)}' style="margin-top: 10px;">Select</button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;

            Swal.fire({
                title: 'Select Batch',
                html: batchList,
                showConfirmButton: false,
                didOpen: () => {
                    let previouslyFocusedRow = null;

                    // Attach event listeners to each Select button after modal opens
                    const selectButtons = document.querySelectorAll('.select-batch');

                    // If there are any batch items, style the first one
                    if (selectButtons.length > 0) {
                        const firstRow = selectButtons[0].closest('tr');
                        firstRow.style.background = '#0087ff54';
                        firstRow.style.color = 'black';
                        previouslyFocusedRow = firstRow;
                    }

                    selectButtons.forEach(button => {
                        button.addEventListener('click', () => {
                            // Close the modal
                            Swal.close();
                        });

                        button.addEventListener('focus', () => {
                            const currentRow = button.closest('tr');

                            // Reset the style of the previously focused row
                            if (previouslyFocusedRow) {
                                previouslyFocusedRow.style.background = '';
                                previouslyFocusedRow.style.color = '';
                            }

                            // Apply new styles to the currently focused button's row
                            currentRow.style.background = '#0087ff54';
                            currentRow.style.color = 'black';

                            // Update the reference to the currently focused row
                            previouslyFocusedRow = currentRow;
                        });

                        // Optional: Reset the style on blur if needed
                        button.addEventListener('blur', () => {
                            button.closest('tr').style.background = '';
                            button.closest('tr').style.color = '';
                        });
                    });
                }
            });
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
            $('#product-table-body').empty(); // Clear existing rows

            productList.forEach((product, index) => {
                $('#product-table-body').append(
                    `<tr>
                <td style="padding: 10px; border: 1px solid #ccc;">${product.name}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <input type="number" min="1" value="${product.quantity}" onchange="updateQuantity(${index}, this.value)" style="width: 60px; padding: 5px;">
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <input type="number" step="0.01" min="0" value="${product.price}" onchange="updatePrice(${index}, this.value)" style="width: 80px; padding: 5px;">
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">Rs.${(product.quantity * product.price).toFixed(2)}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <button class="remove-product" onclick="removeProduct(${index})"> <i class="fas fa-trash-alt"></i> </button>
                </td>
            </tr>`
                );
            });
        }


        function updateQuantity(index, value) {
            productList[index].quantity = parseInt(value);
            productList[index].subtotal = productList[index].quantity * productList[index].price;
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
        }

        function updatePrice(index, value) {
            productList[index].price = parseFloat(value);
            productList[index].subtotal = productList[index].quantity * productList[index].price;
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
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