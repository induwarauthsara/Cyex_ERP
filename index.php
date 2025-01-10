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


<body>
    <div class="main">
        <!-- Cart Left Side -->
        <div id="cart-leftSide">
            <!-- Cart Heder -->
            <div id="cart-left-header">
                <img src="logo.png" alt="Company LOGO">
                <div class="company-details">
                    <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
                    <h3><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h3>
                </div>
                <div id="cart-left-top-bottons">
                    <button class="shortcut-button" onclick="showShortcuts()"><i class="fas fa-keyboard"></i> &nbsp; Shortcuts</button>
                    <button class="view-held-invoices" onclick="loadHeldInvoices()"><svg style="width: 20px; color: #fff;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zM9 8.25a.75.75 0 00-.75.75v6c0 .414.336.75.75.75h.75a.75.75 0 00.75-.75V9a.75.75 0 00-.75-.75H9zm5.25 0a.75.75 0 00-.75.75v6c0 .414.336.75.75.75H15a.75.75 0 00.75-.75V9a.75.75 0 00-.75-.75h-.75z" clip-rule="evenodd"></path>
                        </svg> &nbsp; Held Invoices List</button>
                    <button class="shortcut-button" onclick="clearQuickSearch()"><i class="fas fa-broom"></i> &nbsp; Clear Quick Search</button>
                </div>
            </div>
            <!-- Customer Details -->
            <div class="customer-details">
                <div>
                    <label for="name">Customer Name:</label>
                    <input type="text" name="name" id="name" autocomplete="off" readonly placeholder="Walk-in Customer" onclick="searchCustomer()">
                </div>

                <div>
                    <label for="tele">Customer Number:</label>
                    <input type="text" name="tele" id="tele" autocomplete="off" readonly placeholder="0" onclick="searchCustomer()">
                </div>
            </div>
            <div class="product-container">
                <input type="text" id="product-input" placeholder="Enter Product Name / SKU / Scan Code" autocomplete="off">
                <button id="add-product" style="display: none;">Add Product</button>
                <div id="search-results" style="position: absolute; background-color: white; border: 1px solid #ccc; width: 100%; z-index: 1000; max-height: 200px; overflow-y: auto; display: none; transform: translateY(40px);"> </div>
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
                <div class="summery-count">
                    <p>Products <span id="product-count">0</span></p>
                    <p>Items <span id="item-count">0</span></p>
                </div>
                <hr />
                <p>Subtotal<span class="total-without-discount"> Rs. <span id="total-without-discount">0.00</span></span></p>
                <hr />
                <p> <span class="discount-edit">Discount &nbsp; <button onclick="openDiscountModal()"><i class="fas fa-edit"></i> </button></span> <span class="discount-amount"> Rs. <span id="discount-amount">0.00</span> </span></p>
                <hr />
                <p>Total Payable <span class="total-payable"> Rs. <span id="total-payable">0.00</span></span></p>
            </div>

            <!-- Button Group -->
            <div class="button-group">
                <div id="payment-buttons">
                    <button id="pay-btn" onclick="openConfirmPaymentModal('Cash')">Cash Payment</button>
                    <button id="card-btn" onclick="openConfirmPaymentModal('Card')">Card Payment</button>
                </div>
                <div id="cart-action-buttons">
                    <button id="hold-btn" onclick="holdInvoice()">Hold</button>
                    <button id="credit-btn" onclick="openConfirmPaymentModal('Credit')">Credit</button>
                    <button id="cancel-btn" onclick="cancelBill()">Cancel</button>
                </div>
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
                if (event.shiftKey && event.key === 'H') {
                    holdInvoice();
                }
                if (event.ctrlKey && event.key === 'h') {
                    event.preventDefault();
                    loadHeldInvoices();
                }
                if (event.shiftKey && event.key === 'H') {
                    holdInvoice();
                }
                if (event.shiftKey && event.key === 'C') {
                    event.preventDefault(); // Prevent any default action for Shift + C
                    cancelBill(); // Call the cancel bill function
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
                            <tr> <td><kbd>Ctrl</kbd> + <kbd>H</kbd></td> <td>Show Held Bill List</td> </tr>
                            <!-- Additional Shortcuts from the Invoice Confirm -->
                            <tr> <td><kbd>F1</kbd></td> <td><b>Cash Payment </b> </td> </tr>
                            <tr> <td><kbd>F2</kbd></td> <td><b>Card Payment</b> </td> </tr>
                            <tr> <td><kbd>F3</kbd>&nbsp;to&nbsp;<kbd>F9</kbd></td> <td> Add Notes to Quick Cash </td> </tr>
                            <tr> <td><kbd>F10</kbd></td> <td>Clear Quick Cash</td> </tr>
                            <tr> <td><kbd>Enter</kbd>+<kbd>Enter</kbd><br> (Double Press)</td> <td>Finalize Payment Confirm</td> </tr>
                            <tr> <td><kbd>Shift</kbd> + <kbd>C</kbd></td> <td>Cancel and Clear Bill</td> </tr>
                            </tbody> </table> `,
                icon: 'info',
                showConfirmButton: false
            });
        }

        function clearQuickSearch() {
            // Clear Local Storage > productsCache key values
            localStorage.removeItem('productsCache');
            Swal.fire({
                title: 'Quick Search Cleared',
                text: 'Quick Search Cache Cleared Successfully',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }

        function fetchProduct(query, selectFirstMatch = false) {
            // console.log('Fetching product for query:', query, 'selectFirstMatch', selectFirstMatch);
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
            // console.log('Handling product response:', response, 'selectFirstMatch', selectFirstMatch, "response : ", response);
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
            // console.log('Displaying batch modal for product:', product, 'batches:', batches);
            let batchList = `
            <h3>${product.product_name} - ${product.sku || ''} (${product.barcode || 'No Barcode'})</h3>
            <br> <p>(Use <kbd>Tab</kbd> Key to Move Between Batches)</p>
            <table style="width: 100%; border-collapse: collapse; margin-top: 25px;">
                        <thead>
                            <tr style="background-color: #f5f5f5;">
                                <th>Batch</th>
                                <th>Price</th>
                                <th>Expiry</th>
                                <th>Restocked Date</th>
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
                                    <td>${batch.restocked_at}</td>
                                    <td>${batch.batch_quantity}</td>
                                    <td><button class="select-batch" data-product='${JSON.stringify(product)}' data-batch='${JSON.stringify(batch)}'>Select</button></td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>`;

            Swal.fire({
                title: 'Select Batch',
                width: '40em',
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
            let discountText = discountType === 'percentage' ? `${formatNumber(discount.toFixed(2))} (${discountValue}%)` : formatNumber(discount.toFixed(2));
            $('#discount-amount').text(discountText);
            $('#total-payable').text(formatNumber(totalAfterDiscount.toFixed(2)));
        }

        // Cancel and clear the bill
        function cancelBill() {
            Swal.fire({
                title: 'Cancel Bill',
                text: 'Are you sure you want to cancel the current bill?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Cancel It',
                cancelButtonText: 'No, Keep It'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, clear the cart and close the modal
                    clearCart();
                    Swal.close();
                }
            });
        }
    </script>

</body>

</html>

<script src="inc/searchCustomer function.js"></script>

<!-- JS Functions for Hold Invoice -->
<script src="inc/hold_invoices/hold_invoices_logics.js"></script>

<!-- JavaScript Code for Confirm Payment Feature -->
<script src="inc/confirm_payment_feature/confirm_payment_feature.js"></script>