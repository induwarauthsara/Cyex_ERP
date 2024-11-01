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
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .main {
        max-width: 1200px;
        margin: auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: row;
    }

    #cart-leftSide {
        flex: 2;
        padding: 20px;
    }

    #cart-rightSide {
        flex: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .customer-details {
        margin-bottom: 10px;
    }

    .customer-details input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .product-list table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
    }

    .product-list th,
    .product-list td {
        padding: 12px;
        border: 1px solid #ccc;
        text-align: left;
    }

    .product-list th {
        background-color: #f5f5f5;
    }

    .invoice-summary {
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .invoice-summary p {
        font-size: 1.2em;
        margin: 10px 0;
    }

    .invoice-summary p span {
        font-weight: bold;
    }

    .total-payable {
        font-size: 2em;
        color: #d9534f;
        font-weight: bold;
        text-align: center;
    }

    .button-group {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .button-group button {
        padding: 15px;
        font-size: 1.1em;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #pay-btn {
        background-color: #5cb85c;
        color: #fff;
        font-size: 1.5em;
    }

    #pay-btn:hover {
        background-color: #4cae4c;
    }

    #cancel-btn {
        background-color: #d9534f;
        color: #fff;
    }

    #cancel-btn:hover {
        background-color: #c9302c;
    }

    #credit-btn {
        background-color: #5bc0de;
        color: #fff;
    }

    #credit-btn:hover {
        background-color: #46b8da;
    }

    #card-btn {
        background-color: #f0ad4e;
        color: #fff;
    }

    #card-btn:hover {
        background-color: #ec971f;
    }
</style>

<body>
    <div class="main">
        <!-- Cart Left Side -->
        <div id="cart-leftSide">
            <!-- Customer Details -->
            <div class="customer-details">
                <label for="name">Customer Name:</label>
                <input type="text" name="name" id="name" placeholder="Walk-in Customer" readonly>

                <label for="tele">Customer Number:</label>
                <input type="text" name="tele" id="tele" placeholder="0" readonly>
            </div>
            <!-- Product List Table -->
            <div class="product-list">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="product-table-body">
                        <!-- Dynamic rows will be added here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cart Right Side -->
        <div id="cart-rightSide">
            <!-- Invoice Summary -->
            <div class="invoice-summary">
                <p>Products: <span id="product-count">0</span></p>
                <p>Items: <span id="item-count">0</span></p>
                <p>Total (Without Discount): Rs. <span id="total-without-discount">0.00</span></p>
                <p>Discount: Rs. <span id="discount-amount">0.00</span> <button onclick="openDiscountModal()">Edit</button></p>
                <p class="total-payable">Total Payable: Rs. <span id="total-payable">0.00</span></p>
            </div>

            <!-- Button Group -->
            <div class="button-group">
                <button id="pay-btn">Payment</button>
                <button id="cancel-btn">Cancel</button>
                <button id="credit-btn">Credit</button>
                <button id="card-btn">Card</button>
            </div>
        </div>
    </div>

    <script>
        let productList = JSON.parse(localStorage.getItem('productList')) || [];
        let productsCache = JSON.parse(localStorage.getItem('productsCache')) || {};
        let discountType = localStorage.getItem('discountType') || 'flat'; // Load discount type from localStorage
        let discountValue = parseFloat(localStorage.getItem('discountValue')) || 0; // Load discount value from localStorage

        // Debounce function to limit the rate of search requests
        function debounce(func, delay) {
            let debounceTimer;
            return function(...args) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => func.apply(this, args), delay);
            };
        }

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

            // Keydown event for Enter on the barcode or product input field
            $('#product-input').on('keydown', function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    let query = $(this).val().trim();
                    if (query) {
                        fetchProduct(query, true);
                        // clear input field
                        $('#product-input').val('');
                    }
                }
            });

            //Shortcut Key Listeners
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

            // Trigger search with debouncing
            $('#product-input').off('input').on('input', debounce(function() {
                const query = $(this).val().trim();
                // if search result has only one, fetch it
                if (query.length > 0 && $('#search-results').find('.search-result-item').length === 1) {
                    // fetch result product name                   
                    let productData = $('.search-result-item').data('product');
                    let productName = productData.product_name;
                    fetchProduct(productName, true); // Fetch with product name to match the full data
                }

                if (query.length > 0) {
                    fetchProduct(query, false); // Pass false for regular typing
                } else if (query.length === 0 || query === '') {
                    $('#search-results').hide();
                } else {
                    // $('#search-results').hide();
                    // show as no product found
                    $('#search-results').html(`<div style="padding: 10px; border-bottom: 1px solid #ddd;">No product found</div>`).show();
                }
            }, 500));

            // Hide search results on outside click
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#product-input, #search-results').length) {
                    $('#search-results').hide();
                }
            });
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

        function fetchProduct(query, selectFirstMatch = false) {
            console.log('Fetching product for query:', query, 'selectFirstMatch', selectFirstMatch);
            if (productsCache[query]) {
                // If product is in cache, use it directly
                handleProductResponse(productsCache[query], selectFirstMatch);
            } else {
                // Make AJAX request to fetch product
                $.ajax({
                    url: '/inc/fetch_product.php',
                    method: 'GET',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        let response = typeof data === "object" ? data : JSON.parse(data);
                        // Cache the response for future use
                        productsCache[query] = response;
                        localStorage.setItem('productsCache', JSON.stringify(productsCache));
                        // Handle the response to add product to cart or show batch modal
                        handleProductResponse(response, selectFirstMatch);
                    },
                    error: function() {
                        alert("Failed to fetch product data.");
                    }
                });
            }
        }

        // Fetch product based on search query
        function handleProductResponse(response, selectFirstMatch) {
            console.log('Handling product response:', response, 'selectFirstMatch', selectFirstMatch, "response : ", response);
            if (response.products.length === 1) {
                let product = response.products[0];
                if (response.batches.length === 1) {
                    // Add single batch to cart directly if only one batch exists
                    addToCart(product, response.batches[0]);
                } else if (response.batches.length > 1) {
                    // Show modal for selecting batch if multiple batches exist
                    displayBatchModal(product, response.batches);
                } else {
                    // show error
                    Swal.fire({
                        title: 'No batch found for this product',
                        text: 'Add Batches for this Product',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                $('#search-results').hide();
                $('#product-input').val('');
            } else if (response.products.length > 1) {
                // Show search results if multiple products match
                displaySearchResults(response.products);
            } else if (response.products.length === 0) {
                // Show single product if only one product matches
                $('#search-results').hide();
                Swal.fire({
                    title: 'No Product Found',
                    text: 'Add This Product',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                $('#search-results').hide();
                Swal.fire({
                    title: 'Error',
                    text: 'Error from handleProductResponse',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Display search results in dropdown
        function displaySearchResults(products) {
            let resultsHtml = products.map(product => `
                <div class="search-result-item" style="padding: 10px; cursor: pointer; border-bottom: 1px solid #ddd;" data-product='${JSON.stringify(product)}'>
                    ${product.product_name} - ${product.sku || ''} (${product.barcode || 'No Barcode'})
                </div> `).join('');
            $('#search-results').html(resultsHtml).show();
        }

        // Handle click on search result item
        $(document).on('click', '.search-result-item', function() {
            let product = JSON.parse($(this).attr('data-product'));
            let productName = product.product_name;
            fetchProduct(productName, true);
            $('#search-results').hide();
            $('#product-input').val('');
        });

        function formatNumber(input) {
            // Convert the input to a number
            let number = parseFloat(input);
            // Check if the input is a valid number
            if (isNaN(number)) {
                // return "Invalid input";
                return "0.00";
            }
            // Format the number with commas and two decimal places
            return number.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function displayBatchModal(product, batches) {
            let batchList = `
            <h3>${product.product_name} - ${product.sku || ''} (${product.barcode || 'No Barcode'})</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 25px;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th>Batch</th>
                                <th>Price</th>
                                <th>Expiry</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${batches.map(batch => `
                                <tr>
                                    <td>${batch.batch_number}</td>
                                    <td>${batch.selling_price}</td>
                                    <td>${batch.expiry_date || 'N/A'}</td>
                                    <td>${batch.batch_quantity}</td>
                                    <td><button class="select-batch" data-product='${JSON.stringify(product)}' data-batch='${JSON.stringify(batch)}'>Select</button></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>`;

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

        // Attach click event handler outside the displayBatchModal function
        $(document).off('click', '.select-batch').on('click', '.select-batch', function() {
            let product = JSON.parse($(this).attr('data-product'));
            let batch = JSON.parse($(this).attr('data-batch'));

            addToCart(product, batch); // Add selected batch to cart with correct product name
            Swal.close();
        });


        // Add product to cart with batch details
        function addToCart(product, batch) {
            // Check if product and batch exist in the product list by batch ID
            let existingBatch = productList.find(p => p.product_id === batch.product_id && p.batch_id === batch.batch_id);

            let productIndex; // Store the index of the product to focus on later
            if (existingBatch) {
                // Increment the quantity if the batch already exists in the cart
                existingBatch.quantity++;
                existingBatch.subtotal = existingBatch.quantity * existingBatch.price;
                productIndex = productList.indexOf(existingBatch); // Get index of the existing product in the list
            } else {
                // Add new entry if batch doesn't exist in cart
                productList.push({
                    product_id: batch.product_id,
                    batch_id: batch.batch_id,
                    name: batch.product_name || product.product_name, // Use batch name if available, fallback to product name
                    price: batch.selling_price,
                    quantity: 1, // Initialize with a quantity of 1
                    discount: 0,
                    subtotal: batch.selling_price // Set initial subtotal
                });
                productIndex = productList.length - 1; // New product is at the last index
            }

            // Update local storage and re-render
            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
            calculateInvoiceTotal();

            // Focus on the quantity input field of the added/updated product
            setTimeout(() => {
                const quantityInput = $(`#quantity-${productIndex}`);
                quantityInput.focus();
                quantityInput.select(); // Automatically selects the quantity number for easy replacement   
            }, 400); // Short delay to ensure rendering completes before focusing
        }

        // Render product list in cart
        function renderProductList() {
            $('#product-table-body').empty(); // Clear existing rows

            productList.forEach((product, index) => {
                $('#product-table-body').append(
                    `<tr>
                <td style="padding: 10px; border: 1px solid #ccc;">${product.name}</td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <input id="quantity-${index}" type="text" min="1" value="${product.quantity}" 
                        onchange="updateQuantity(${index}, this.value)" style="width: 60px; padding: 5px;">
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">
                    <input type="number" step="0.01" min="0" value="${product.price}" 
                        onchange="updatePrice(${index}, this.value)" style="width: 80px; padding: 5px;">
                </td>
                <td style="padding: 10px; border: 1px solid #ccc;">Rs.${formatNumber((product.quantity * product.price).toFixed(2))}</td>
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
            let totalWithoutDiscount = productList.reduce((acc, product) => acc + product.quantity * product.price, 0);
            let discount = discountType === 'percentage' ? (totalWithoutDiscount * discountValue / 100) : discountValue;
            let totalAfterDiscount = totalWithoutDiscount - discount;
            $('#product-count').text(productList.length);
            $('#item-count').text(productList.reduce((acc, product) => acc + product.quantity, 0));
            $('#total-without-discount').text(formatNumber(totalWithoutDiscount.toFixed(2)));
            $('#discount-amount').text(formatNumber(discount.toFixed(2)));
            $('#total-payable').text(formatNumber(totalAfterDiscount.toFixed(2)));
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