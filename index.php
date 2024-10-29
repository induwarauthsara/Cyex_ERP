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
            <button class="view-held-invoices" onclick="viewHeldInvoices()"><svg style="width: 20px; color: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zM9 8.25a.75.75 0 00-.75.75v6c0 .414.336.75.75.75h.75a.75.75 0 00.75-.75V9a.75.75 0 00-.75-.75H9zm5.25 0a.75.75 0 00-.75.75v6c0 .414.336.75.75.75H15a.75.75 0 00.75-.75V9a.75.75 0 00-.75-.75h-.75z" clip-rule="evenodd"></path>
                </svg> Held Invoices</button>
        </div>
        <hr>
        <div class="content">
            <form>
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
            <div class="invoice-summary">
                <p>Products: <span id="product-count">0</span></p>
                <p>Items: <span id="item-count">0</span></p>
                <p>Total (Without Discount): Rs.<span id="total-without-discount">0.00</span></p>
                <p>Discount: Rs.<span id="discount-amount">0.00</span> <button onclick="openDiscountModal()"><i class="fa fa-edit"></i> </button></p>
                <p>Total Payable: Rs.<span id="total-payable">0.00</span></p>
            </div>

            <div class="button-group">
                <button id="pay-btn">Pay</button>
                <button id="credit-btn">Credit</button>
                <button id="hold-btn">Hold</button>
            </div>
        </div>
    </div>

    <script>
        let productList = JSON.parse(localStorage.getItem('productList')) || [];
        let productsCache = JSON.parse(localStorage.getItem('productsCache')) || {};
        let discountType = localStorage.getItem('discountType') || 'flat'; // Load discount type from localStorage
        let discountValue = parseFloat(localStorage.getItem('discountValue')) || 0; // Load discount value from localStorage


        $(document).ready(function() {
            renderProductList();
            calculateInvoiceTotal();

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
                if (event.shiftKey && event.key === 'D') {
                    openDiscountModal();
                }
            });

            // Apply discount from localStorage on load
            applyDiscount();

            // $('#close-modal').click(function() {
            //     $('#batch-modal').removeClass('active');
            // });

            // $(document).on('click', '.select-batch', function() {
            //     let product = JSON.parse($(this).attr('data-product'));
            //     let batch = JSON.parse($(this).attr('data-batch'));
            //     addToCart(product, batch);
            //     $('#batch-modal').removeClass('active');
            // });
        });

        function showShortcuts() {
            Swal.fire({
                title: 'Keyboard Shortcuts',
                html: `<table> <thead> <tr> <th>Shortcut</th> <th>Description</th> </tr> </thead> <tbody>
                            <tr> <td><kbd>Insert</kbd></td> <td>Focus on product input</td> </tr>
                            <tr> <td><kbd>+</kbd></td> <td>Select Customer</td> </tr>
                            <tr> <td><kbd>Shift</kbd> + <kbd>D</kbd></td> <td>Open Discount</td> </tr>
                            <tr> <td><kbd>Shift</kbd> + <kbd>H</kbd></td> <td>Hold Bill</td> </tr>
                         </tbody> </table> `,
                icon: 'info',
                showConfirmButton: false
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
            calculateInvoiceTotal();
        }

        function renderProductList() {
            $('#product-table-body').empty(); // Clear existing rows

            productList.forEach((product, index) => {
                $('#product-table-body').append(
                    `<tr>
                <td style="padding: 10px; border: 1px solid #ccc;">${product.name}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <input type="text" min="1" value="${product.quantity}" onchange="updateQuantity(${index}, this.value)" style="width: 60px; padding: 5px;">
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
            calculateInvoiceTotal();
        }

        function updatePrice(index, value) {
            productList[index].price = parseFloat(value);
            productList[index].subtotal = productList[index].quantity * productList[index].price;
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
            calculateInvoiceTotal();
        }

        function removeProduct(index) {
            productList.splice(index, 1);
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
            calculateInvoiceTotal();
        }

        function openDiscountModal() {
            Swal.fire({
                title: 'Discount',
                didOpen: () => {
                    $('#discount-input').focus();
                },
                html: `
                        <div style="display: flex; justify-content: center; margin-bottom: 15px;">
                            <button id="discount-flat" class="swal2-styled" onclick="setDiscountType('flat')">Flat</button>
                            <button id="discount-percentage" class="swal2-styled" onclick="setDiscountType('percentage')">Percentage</button>
                        </div>
                        <input type="number" id="discount-input" class="swal2-input" placeholder="Enter discount amount" style="margin-top: 10px; width:300px; height:50px; max-width: none;" autofocus>
                    `,
                // showCancelButton: false,
                // showConfirmButton: false,
                confirmButtonText: 'Add Discount',
                preConfirm: () => {
                    discountValue = parseFloat(document.getElementById('discount-input').value) || 0;
                    localStorage.setItem('discountValue', discountValue); // Save discount value to localStorage
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    applyDiscount();
                }
            });

            // Set initial button style
            setDiscountType(discountType);

            // Add Enter key event listener for the discount input
            document.getElementById('discount-input').addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    // Simulate clicking the confirm button
                    Swal.clickConfirm();
                }
            });
        }

        function setDiscountType(type) {
            discountType = type;
            localStorage.setItem('discountType', discountType); // Save discount type to localStorage
            document.getElementById('discount-input').placeholder = `Enter ${type === 'flat' ? 'flat amount' : 'percentage'} discount`;

            // if type == percentage show % sign
            if (type === 'percentage') {
                document.getElementById('discount-input').addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });
            }

            // Change button styles to reflect selected type
            document.getElementById('discount-flat').style.backgroundColor = type === 'flat' ? '#007bff' : '#f0f0f0';
            document.getElementById('discount-flat').style.color = type === 'flat' ? '#fff' : '#000';
            document.getElementById('discount-percentage').style.backgroundColor = type === 'percentage' ? '#007bff' : '#f0f0f0';
            document.getElementById('discount-percentage').style.color = type === 'percentage' ? '#fff' : '#000';
        }

        function applyDiscount() {
            calculateInvoiceTotal();
        }

        function calculateInvoiceTotal() {
            // Calculate the subtotal of all products without any discount
            let totalWithoutDiscount = productList.reduce((acc, product) => acc + product.quantity * product.price, 0);

            // Calculate the discount amount based on the selected discount type (flat or percentage)
            let discount = discountType === 'percentage' ? (totalWithoutDiscount * discountValue / 100) : discountValue;
            let totalAfterDiscount = totalWithoutDiscount - discount;

            // Update the UI elements with the calculated values
            $('#product-count').text(productList.length); // Number of unique products
            $('#item-count').text(productList.reduce((acc, product) => acc + product.quantity, 0)); // Total quantity of items
            $('#total-without-discount').text(totalWithoutDiscount.toFixed(2)); // Display total without discount

            // Display discount amount with both numeric and percentage value if applicable
            if (discountType === 'percentage') {
                $('#discount-amount').html(`${discount.toFixed(2)} ( ${discountValue}% )`);
            } else {
                $('#discount-amount').text(discount.toFixed(2));
            }

            $('#total-payable').text(totalAfterDiscount.toFixed(2)); // Display total after discount
        }

        function viewHeldInvoices() {
            Swal.fire({
                title: 'Held Invoices',
                text: 'List all held invoices here...',
                icon: 'info'
            });
        }
    </script>

</body>

</html>

<script src="inc/searchCustomer function.js"></script>