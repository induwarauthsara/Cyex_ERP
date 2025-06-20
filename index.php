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
    <script src="inc/invoice_main_functions.jsx"></script>
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
                    <!-- <button class="print-last-invoice" onclick="printLastInvoice()"><i class="fas fa-print"></i> &nbsp; Print Last Invoice</button> -->
                    <button class="clear-search-cache-button" onclick="clearQuickSearch()"><i class="fas fa-broom"></i> &nbsp; Clear Quick Search</button>
                    <button class="cash-register-button" onclick="openCashRegister()"><i class="fa-solid fa-cash-register"></i> &nbsp; Cash Register</button>
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
                    <input type="text" name="tele" id="tele" autocomplete="off" readonly placeholder="customer tel" onclick="searchCustomer()">
                </div>
            </div>
            <div class="product-container">
                <input type="text" id="product-input" placeholder="Enter Product Name / SKU / Scan Code" autocomplete="off">
                <button id="add-product" style="display: none;">Add Product</button>
                <div id="search-results" style="position: absolute; background-color: white; border: 1px solid #ccc; width: 100%; z-index: 1000; max-height: 200px; overflow-y: auto; display: none;"> </div>
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
        <div id="cart-rightSide">            <!-- Invoice Summary -->
            <div class="invoice-summary">
                <div class="summary-counts">
                    <div class="count-item">
                        <i class="fas fa-box" aria-hidden="true"></i>
                        <div class="count-details">
                            <span class="count-label">Products</span>
                            <span class="count-value" id="product-count">0</span>
                        </div>
                    </div>
                    <div class="count-item">
                        <i class="fas fa-shopping-basket" aria-hidden="true"></i>
                        <div class="count-details">
                            <span class="count-label">Items</span>
                            <span class="count-value" id="item-count">0</span>
                        </div>
                    </div>
                </div>
                <p>Subtotal<span class="total-without-discount"> Rs. <span id="total-without-discount">0.00</span></span></p>
                <hr />
                <p> <span class="discount-edit"> Discount &nbsp; <button onclick="openDiscountModal()"><i class="fas fa-edit"></i> </button></span> <span class="discount-amount"> Rs. <span id="discount-amount">0.00</span> </span></p>
                <hr />

                <!-- Add the individual discount toggle here -->
                <div class="individual-discount-toggle-container">
                    <div class="individual-discount-toggle">
                        <label for="individual-discount-toggle">Individual Item Discount Price</label>
                        <label class="switch">
                            <input type="checkbox" id="individual-discount-toggle" onchange="toggleIndividualDiscountMode()">
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <!-- Add the total savings display that shows when toggle is checked -->
                    <div id="individual-discount-savings" style="display: none;">
                        Total Savings: <span id="individual-discount-savings-amount">Rs. 0.00</span>
                    </div>
                </div>
                <hr />

                <p>Total Payable <span class="total-payable"> Rs. <span id="total-payable">0.00</span></span></p>
            </div>            <!-- Print Preferences -->
            <div class="print-preferences">
                <div class="print-row">
                    <div class="print-checkbox-container">
                        <label for="print-bill-checkbox">
                            <input type="checkbox" id="print-bill-checkbox" onchange="togglePrintTypeOptions()">
                            <span>Print Bill</span>
                        </label>
                    </div>
                    <div id="print-type-container" style="display: none;">
                        <div class="print-type-toggle">
                            <span class="toggle-label">Receipt</span>
                            <label class="switch" for="print-type-toggle">
                                <input type="checkbox" id="print-type-toggle" onchange="updatePrintType()">
                                <span class="slider round"></span>
                            </label>
                            <span class="toggle-label">Standard</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Button Group -->
            <div class="button-group">
                <div id="payment-buttons">
                    <button id="pay-btn" onclick="openConfirmPaymentModal('Cash')"><i class="fas fa-money-bill-alt"></i> Cash Payment <kbd class="small">F1</kbd></button>
                    <button id="card-btn" onclick="openConfirmPaymentModal('Card')"><i class="fas fa-credit-card"></i> Card Payment <kbd class="small">F2</kbd></button>
                </div>
                <div id="cart-action-buttons">
                    <button id="hold-btn" onclick="holdInvoice()"><i class="fas fa-pause-circle"></i> Hold</button>
                    <button id="credit-btn" onclick="openConfirmPaymentModal('Credit')"><i class="fas fa-handshake"></i> Credit</button>
                    <button id="cancel-btn" onclick="cancelBill()"><i class="fas fa-times-circle"></i> Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Software Developed by <a href="https://cyextech.com" target="_blank">CyexTech Solutions</a> &copy; 2021 - <?php echo date('Y'); ?>
    </div>

    <script>
        let productList = JSON.parse(localStorage.getItem('productList')) || [];
        let productsCache = JSON.parse(localStorage.getItem('productsCache')) || {};
        let discountType = localStorage.getItem('discountType') || 'flat'; // Load discount type from localStorage
        let discountValue = parseFloat(localStorage.getItem('discountValue')) || 0; // Load discount value from localStorage
        let individualDiscountMode = JSON.parse(localStorage.getItem('individualDiscountMode')) || false;

        // Ensure user session data is saved to localStorage
        <?php if (isset($_SESSION['employee_id'])): ?>
        if (!localStorage.getItem('employee_id')) {
            localStorage.setItem('employee_id', '<?php echo $_SESSION['employee_id']; ?>');
            localStorage.setItem('employee_name', '<?php echo addslashes($_SESSION['employee_name']); ?>');
            localStorage.setItem('employee_role', '<?php echo addslashes($_SESSION['employee_role']); ?>');
        }
        <?php endif; ?>

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
            });            //Shortcut Key Listeners
            $(document).on('keydown', function(event) {
                // Only trigger if no input fields are focused
                if (!$(event.target).is('input, textarea, select')) {
                    if (event.key === "Insert") {
                        $('#product-input').focus();
                    }
                    if (event.altKey && event.key === 'd') {
                        event.preventDefault();
                        openDiscountModal();
                    }
                    if (event.altKey && event.key === 'h') {
                        event.preventDefault();
                        holdInvoice();
                    }
                    if (event.altKey && event.key === 'l') {
                        event.preventDefault();
                        loadHeldInvoices();
                    }
                    if (event.altKey && event.key === 'c') {
                        event.preventDefault(); // Prevent any default action for Alt + C
                        cancelBill(); // Call the cancel bill function
                    }
                    if (event.altKey && event.key === 'p') {
                        event.preventDefault(); // Prevent any default action for Alt + P
                        printLastInvoice(); // Call the print last invoice function
                    }
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
                    $('#search-results').html(`
                        <div style="padding: 10px; border-bottom: 1px solid #ddd;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span>No product found</span>
                                <button id="add-onetime-product" class="btn-sm btn-success" style="padding: 3px 8px; background-color: #28a745; color: white; border: none; border-radius: 3px; cursor: pointer;">
                                    <i class="fas fa-plus"></i> Add One-Time Product
                                </button>
                            </div>
                        </div>
                    `).show();

                    // Attach event listener to the Add One-Time Product button
                    $('#add-onetime-product').off('click').on('click', function() {
                        const productName = $('#product-input').val().trim();
                        addonetimeproductModal(productName);
                        $('#search-results').hide();
                    });
                }
            }, 500));

            // Hide search results on outside click
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#product-input, #search-results').length) {
                    $('#search-results').hide();
                }
            });

            // Set toggle state from localStorage
            $('#individual-discount-toggle').prop('checked', individualDiscountMode);

            // Render the product list with the appropriate columns
            renderProductList();
            calculateInvoiceTotal();

            // Set toggle state from localStorage and show/hide individual discount savings
            $('#individual-discount-toggle').prop('checked', individualDiscountMode);
            if (individualDiscountMode) {
                $('#individual-discount-savings').show();
                calculateIndividualDiscountSavings();
            } else {
                $('#individual-discount-savings').hide();
            }

            // Render the product list with the appropriate columns
            renderProductList();
            calculateInvoiceTotal();

            // Load print preferences from localStorage
            loadPrintPreferences();
        });        function showShortcuts() {
            Swal.fire({
                title: 'Keyboard Shortcuts',
                html: `<table> <thead> <tr> <th>Shortcut</th> <th>Description</th> </tr> </thead> <tbody>
                            <tr> <td><kbd>Insert</kbd></td> <td>Focus on product input</td> </tr>
                            <tr> <td><kbd>+</kbd></td> <td>Select Customer</td> </tr>
                            <tr> <td><kbd>Alt</kbd> + <kbd>D</kbd></td> <td>Open Discount</td> </tr>
                            <tr> <td><kbd>Alt</kbd> + <kbd>H</kbd></td> <td>Hold Bill</td> </tr>
                            <tr> <td><kbd>Alt</kbd> + <kbd>L</kbd></td> <td>Show Held Bill List</td> </tr>
                            <tr> <td><kbd>Alt</kbd> + <kbd>P</kbd></td> <td>Print Last Invoice</td> </tr>
                            <tr> <td><kbd>Alt</kbd> + <kbd>C</kbd></td> <td>Cancel and Clear Bill</td> </tr>
                            <!-- Additional Shortcuts from the Invoice Confirm -->
                            <tr> <td><kbd>F1</kbd></td> <td><b>Cash Payment </b> </td> </tr>
                            <tr> <td><kbd>F2</kbd></td> <td><b>Card Payment</b> </td> </tr>
                            <!-- <tr> <td><kbd>F3</kbd>&nbsp;to&nbsp;<kbd>F9</kbd></td> <td> Add Notes to Quick Cash </td> </tr> -->
                            <tr> <td><kbd>F10</kbd></td> <td>Clear Quick Cash</td> </tr>
                            <tr> <td><kbd>Enter</kbd>+<kbd>Enter</kbd><br> (Double Press)</td> <td>Finalize Payment Confirm</td> </tr>
                            </tbody> </table> 
                            <br>
                            <p style="font-size: 12px; color: #666; text-align: left;">
                                <strong>Note:</strong> Alt key shortcuts work when you're not typing in input fields.
                            </p>`,
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
            }).then((result) => {
                if (result.isConfirmed) {
                    // Refresh the page after clicking OK
                    window.location.reload();
                }
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
                // Show no product found message with option to add one-time product
                const query = $('#product-input').val().trim();
                $('#search-results').hide();

                Swal.fire({
                    title: 'No Product Found',
                    html: `
                        <p>Would you like to add "${query}" as a one-time product?</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Add One-Time Product',
                    confirmButtonColor: '#28a745',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        addonetimeproductModal(query);
                    }
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
            // console.log('Displaying batch modal for product:', product, 'batches:', batches);
            let batchList = `
            <h3>${product.product_name} - ${product.sku || ''} (${product.barcode || 'No Barcode'})</h3>
            <br> <p>(Use <kbd>Tab</kbd> Key to Move Between Batches)</p>
            <table style="width: 100%; border-collapse: collapse; margin-top: 25px;">
               <thead>
                    <tr style="background-color: #f5f5f5;">
                        <th>Batch</th>
                        <th>Price</th>
                        <th>Discount Price</th>
                        <th>Expiry</th>
                        <th>Restocked Date</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                        <tbody>
                            ${batches.map(batch => {
                                const hasDiscount = batch.discount_price && parseFloat(batch.discount_price) < parseFloat(batch.selling_price);
                                return `
                                <tr>
                                    <td>${batch.batch_number}</td>
                                    <td>${batch.selling_price}</td>
                                    <td style="padding: 8px; border: 1px solid #ddd;"> ${hasDiscount ? `Rs. ${parseFloat(batch.discount_price).toFixed(2)}` : 'N/A'} </td>
                                    <td>${batch.expiry_date || 'N/A'}</td>
                                    <td>${batch.restocked_at}</td>
                                    <td>${batch.batch_quantity}</td>
                                    <td><button class="select-batch" data-product='${JSON.stringify(product)}' data-batch='${JSON.stringify(batch)}'>Select</button></td>
                                </tr>
                            `}).join('')}
                        </tbody>
                    </table>`;

            Swal.fire({
                title: 'Select Batch',
                width: '65em',
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
                existingBatch.subtotal = existingBatch.quantity *
                    (individualDiscountMode ? existingBatch.discount_price : existingBatch.regular_price);
                productIndex = productList.indexOf(existingBatch); // Get index of the existing product in the list
            } else {
                // Regular price is the batch selling price
                const regularPrice = parseFloat(batch.selling_price);
                // Use discount_price from batch if available, otherwise default to regularPrice
                const discountPrice = (batch.discount_price && batch.discount_price !== 'N/A') ? parseFloat(batch.discount_price) : regularPrice;

                // Add new entry if batch doesn't exist in cart
                productList.push({
                    product_id: batch.product_id,
                    batch_id: batch.batch_id,
                    name: batch.product_name || product.product_name, // Use batch name if available, fallback to product name
                    regular_price: regularPrice,
                    discount_price: discountPrice,
                    quantity: 1, // Initialize with a quantity of 1
                    subtotal: individualDiscountMode ? discountPrice : regularPrice // Set initial subtotal based on mode
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

            // Update table headers based on the mode
            if (individualDiscountMode) {
                $('.product-list thead tr').html(`
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Regular Price</th>
                    <th>Discount Price</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                `);
            } else {
                $('.product-list thead tr').html(`
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                `);
            }

            productList.forEach((product, index) => {
                let row = `<tr>
                    <td style="padding: 10px; border: 1px solid #ccc;">${product.name}</td>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <input id="quantity-${index}" type="number" min="0.001" step="0.001" value="${product.quantity}" 
                            onchange="updateQuantity(${index}, this.value)">
                    </td>`;

                if (individualDiscountMode) {
                    // In individual discount mode, show both regular and discount price
                    row += `
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <input type="number" step="0.01" min="0" value="${product.regular_price}" 
                            onchange="updateRegularPrice(${index}, this.value)">
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <input type="number" step="0.01" min="0" value="${product.discount_price}" 
                            onchange="updateDiscountPrice(${index}, this.value)">
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc;">Rs.${formatNumber((product.quantity * product.discount_price).toFixed(2))}</td>`;
                } else {
                    // In regular mode, just show the regular price
                    row += `
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <input type="number" step="0.01" min="0" value="${product.regular_price}" 
                            onchange="updateRegularPrice(${index}, this.value)">
                    </td>
                    <td style="padding: 10px; border: 1px solid #ccc;">Rs.${formatNumber((product.quantity * product.regular_price).toFixed(2))}</td>`;
                }

                row += `
                    <td style="padding: 10px; border: 1px solid #ccc;">
                        <button class="remove-product" onclick="removeProduct(${index})"> <i class="fas fa-trash-alt"></i> </button>
                    </td>
                </tr>`;

                $('#product-table-body').append(row);
            });
        }

        function updateQuantity(index, value) {
            productList[index].quantity = parseFloat(value);

            // Update subtotal based on current mode
            productList[index].subtotal = productList[index].quantity *
                (individualDiscountMode ? productList[index].discount_price : productList[index].regular_price);

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
            let totalWithoutDiscount = 0;

            // Calculate the subtotal based on the current mode
            if (individualDiscountMode) {
                totalWithoutDiscount = productList.reduce((acc, product) =>
                    acc + (product.quantity * product.discount_price), 0);
                // Update individual discount savings display
                calculateIndividualDiscountSavings();
            } else {
                totalWithoutDiscount = productList.reduce((acc, product) =>
                    acc + (product.quantity * product.regular_price), 0);
            }

            // Convert discount value to a number and handle potential non-numeric values
            let discountValueNum = parseFloat(discountValue) || 0;

            // Calculate discount based on type (this is the global discount)
            let discount = discountType === 'percentage' ?
                (totalWithoutDiscount * discountValueNum / 100) :
                discountValueNum;

            // Ensure discount is a number before using toFixed
            discount = parseFloat(discount) || 0;

            let totalPayable = totalWithoutDiscount - discount;

            // Update the DOM elements with formatted values
            $('#total-without-discount').text(formatNumber(totalWithoutDiscount));
            $('#discount-amount').text(formatNumber(discount));
            $('#total-payable').text(formatNumber(totalPayable));

            // Update item counts
            let totalItems = productList.reduce((acc, product) => acc + product.quantity, 0);
            let totalProducts = productList.length;

            $('#product-count').text(totalProducts);
            $('#item-count').text(totalItems);
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

        // Toggle individual discount mode
        function toggleIndividualDiscountMode() {
            individualDiscountMode = $('#individual-discount-toggle').is(':checked');
            localStorage.setItem('individualDiscountMode', JSON.stringify(individualDiscountMode));

            // Display or hide individual discount savings
            if (individualDiscountMode) {
                $('#individual-discount-savings').show();
                calculateIndividualDiscountSavings();
            } else {
                $('#individual-discount-savings').hide();
            }

            // Re-render product list with updated column structure
            renderProductList();
            calculateInvoiceTotal();
        }

        // Add a new function to calculate individual discount savings
        function calculateIndividualDiscountSavings() {
            let totalSavings = 0;

            productList.forEach(product => {
                const regularTotal = product.quantity * product.regular_price;
                const discountTotal = product.quantity * product.discount_price;
                totalSavings += (regularTotal - discountTotal);
            });

            $('#individual-discount-savings-amount').text('Rs. ' + formatNumber(totalSavings.toFixed(2)));

            return totalSavings;
        }

        // Update the updateRegularPrice function
        function updateRegularPrice(index, value) {
            productList[index].regular_price = parseFloat(value);

            // If discount price is not set or higher than regular price, set it to match regular price
            if (!productList[index].discount_price || productList[index].discount_price > productList[index].regular_price) {
                productList[index].discount_price = productList[index].regular_price;
            }

            // Update subtotal based on current mode
            productList[index].subtotal = productList[index].quantity *
                (individualDiscountMode ? productList[index].discount_price : productList[index].regular_price);

            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
            calculateInvoiceTotal();

            // Update individual discount savings if in individual discount mode
            if (individualDiscountMode) {
                calculateIndividualDiscountSavings();
            }
        }

        // Update the updateDiscountPrice function
        function updateDiscountPrice(index, value) {
            const discountPrice = parseFloat(value);

            // Ensure discount price is not higher than regular price
            if (discountPrice > productList[index].regular_price) {
                Swal.fire({
                    title: 'Invalid Discount Price',
                    text: 'Discount price cannot be higher than regular price.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });

                // Reset to regular price
                productList[index].discount_price = productList[index].regular_price;
            } else {
                productList[index].discount_price = discountPrice;
            }

            // Update subtotal based on current mode
            productList[index].subtotal = productList[index].quantity *
                (individualDiscountMode ? productList[index].discount_price : productList[index].regular_price);

            localStorage.setItem('productList', JSON.stringify(productList));
            renderProductList();
            calculateInvoiceTotal();

            // Update individual discount savings if in individual discount mode
            if (individualDiscountMode) {
                calculateIndividualDiscountSavings();
            }
        }

        function printLastInvoice() {
            $.ajax({
                url: '/inc/get_last_invoice.php',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.invoice_id) {
                        // Open print window for last invoice
                        window.open(`invoice/print.php?id=${response.invoice_id}`, '_blank');
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'No Recent Invoice',
                            text: 'There is no recent invoice to print.',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not retrieve the last invoice. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Print Preferences Functions
        function togglePrintTypeOptions() {
            const printCheckbox = document.getElementById('print-bill-checkbox');
            const printTypeContainer = document.getElementById('print-type-container');
            
            if (printCheckbox.checked) {
                printTypeContainer.style.display = 'block';
            } else {
                printTypeContainer.style.display = 'none';
            }
            
            // Save print preference to localStorage
            localStorage.setItem('printBillEnabled', printCheckbox.checked);
        }

        function updatePrintType() {
            const printTypeToggle = document.getElementById('print-type-toggle');
            const printType = printTypeToggle.checked ? 'standard' : 'receipt';
            
            // Save print type to localStorage
            localStorage.setItem('printType', printType);
        }

        function loadPrintPreferences() {
            // Load print bill checkbox state from localStorage
            const printBillEnabled = localStorage.getItem('printBillEnabled');
            if (printBillEnabled !== null) {
                const printCheckbox = document.getElementById('print-bill-checkbox');
                printCheckbox.checked = printBillEnabled === 'true';
                togglePrintTypeOptions(); // Show/hide print type options based on checkbox state
            }
            
            // Load print type from localStorage
            const printType = localStorage.getItem('printType');
            if (printType !== null) {
                const printTypeToggle = document.getElementById('print-type-toggle');
                printTypeToggle.checked = printType === 'standard';
            }
        }
    </script>

    <!-- External Scripts -->
    <script src="https://kit.fontawesome.com/2dccbd7e96.js" crossorigin="anonymous"></script>
    <!-- Include confirm_payment_feature.js only once -->
    <script src="inc/confirm_payment_feature/confirm_payment_feature.js"></script>
    <!-- <script src="inc/confirm_payment_feature/receipt_printer.js"></script> -->

    <!-- Cash Register Script (inline for now) -->
    <script>
        // Cash Register Functionality
        function openCashRegister() {
            // Show loading animation first
            Swal.fire({
                title: 'Loading Cash Register...',
                html: '<div class="loading-spinner"></div>',
                showConfirmButton: false,
                showCancelButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Check register status first
            $.ajax({
                url: '/AdminPanel/api/cash_register.php',
                method: 'POST',
                data: {
                    action: 'get_register_status'
                },
                success: function(response) {
                    try {
                        // Close loading animation
                        Swal.close();

                        // Try to parse as JSON if it's a string
                        const data = typeof response === 'object' ? response : JSON.parse(response);

                        if (data.success) {
                            const registerData = data.data;
                            const isOpen = registerData.is_open;

                            // Build the content based on register status
                            let content = '<div class="register-details">';
                            let buttonsHtml = '';

                            if (registerData.details) {
                                // Register is open, show details
                                const details = registerData.details;
                                content += `
                                    <div class="register-summary">
                                        <h4>Today's Register Summary</h4>
                                        <div class="detail-row">
                                            <span class="detail-label">Opening Balance:</span>
                                            <span class="amount">Rs. ${Number(details.opening_balance).toFixed(2)}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Cash Sales:</span>
                                            <span class="amount">Rs. ${Number(details.cash_sales).toFixed(2)}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Card Sales:</span>
                                            <span class="amount">Rs. ${Number(details.card_sales).toFixed(2)}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Petty Cash:</span>
                                            <span class="amount">Rs. ${Number(details.cash_out).toFixed(2)}</span>
                                        </div>
                                        <div class="detail-row highlight">
                                            <span class="detail-label">Cash Drawer Amount:</span>
                                            <span class="amount">Rs. ${Number(details.expected_cash).toFixed(2)}</span>
                                        </div>
                                        <div class="detail-row">
                                            <span class="detail-label">Transactions:</span>
                                            <span class="amount">${details.transaction_count}</span>
                                        </div>
                                    </div>
                                `;

                                // Only show cash out and close register buttons if the register is open
                                buttonsHtml += `
                                    <button class="swal2-confirm swal2-styled cash-out-btn" onclick="cashOut()">Petty Cash</button>
                                    <button class="swal2-confirm swal2-styled close-register-btn" onclick="closeRegister()">Close Register</button>
                                    <button class="swal2-confirm swal2-styled print-register-btn" onclick="printRegister(${registerData.register_id})">Print Report</button>
                                `;
                            } else {                                // Register is not open, show open button
                                content += `
                                    <div class="register-info">
                                        <p>No open register found.</p>
                                        <p>You need to open the cash register before processing sales.</p>
                                    </div>
                                `;

                                buttonsHtml += `
                                    <button class="swal2-confirm swal2-styled open-register-btn" onclick="openRegister()">Open Register</button>
                                    <button class="swal2-confirm swal2-styled print-last-register-btn" onclick="printLastClosedRegister()">Print Last Register</button>
                                `;
                            }

                            content += '</div>';

                            // Show the modal with register details
                            Swal.fire({
                                title: 'Cash Register',
                                html: content,
                                showConfirmButton: false,
                                showCancelButton: true,
                                cancelButtonText: 'Close',
                                footer: buttonsHtml,
                                customClass: {
                                    container: 'cash-register-modal'
                                }
                            });

                            // Add styles for the details
                            const style = document.createElement('style');
                            style.textContent = `
                                .register-details {
                                    text-align: left;
                                    margin-bottom: 20px;
                                }
                                .register-summary h4 {
                                    margin-bottom: 10px;
                                    border-bottom: 1px solid #eee;
                                    padding-bottom: 5px;
                                }
                                .detail-row {
                                    display: flex;
                                    justify-content: space-between;
                                    margin: 8px 0;
                                }
                                .detail-label {
                                    font-weight: bold;
                                }
                                .amount {
                                    text-align: right;
                                }
                                .highlight {
                                    font-size: 1.1em;
                                    font-weight: bold;
                                }
                                .cash-register-modal .swal2-footer {
                                    justify-content: center;
                                    flex-wrap: wrap;
                                }
                                .cash-register-modal .swal2-footer button {
                                    margin: 5px;
                                }
                            `;
                            document.head.appendChild(style);

                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to get register status',
                                icon: 'error'
                            });
                        }
                    } catch (error) {
                        console.error('Error processing response:', error);
                        console.error('Raw response:', response);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Invalid server response',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to communicate with server',
                        icon: 'error'
                    });
                }
            });
        }

        // Add print function
        function printRegister(registerId) {
            // Open the print page in a new window
            const printWindow = window.open(`/AdminPanel/cash_register_print.php?id=${registerId}`, '_blank');

            // Focus on the new window and print after it loads
            if (printWindow) {
                printWindow.focus();
            }
        }

        // Function to print the last closed register
        function printLastClosedRegister() {
            // Show loading animation
            Swal.fire({
                title: 'Finding last closed register...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch the last closed register
            $.ajax({
                url: '/AdminPanel/api/cash_register.php',
                method: 'POST',
                data: {
                    action: 'get_last_closed_register'
                },
                success: function(response) {
                    Swal.close();

                    try {
                        const data = typeof response === 'object' ? response : JSON.parse(response);
                        if (data.success && data.data) {
                            const registerId = data.data.id;
                            // Open print window
                            printRegister(registerId);
                        } else {
                            Swal.fire({
                                title: 'No Closed Register Found',
                                text: data.message || 'No closed cash register session found.',
                                icon: 'info'
                            });
                        }
                    } catch (e) {
                        console.error('JSON Parse error:', e);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to parse server response.',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.close();

                    Swal.fire({
                        title: 'Error',
                        text: 'Server error: ' + error,
                        icon: 'error'
                    });
                }
            });
        }

        // After the printRegister function

        function openRegister() {
            Swal.fire({
                title: 'Open Cash Register',
                html: `
                    <div class="form-group">
                        <label for="opening_balance">Opening Balance (Rs.)</label>
                        <input type="number" id="opening_balance" class="swal2-input" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="register_notes">Notes (Optional)</label>
                        <textarea id="register_notes" class="swal2-textarea" placeholder="Any notes about this register session..."></textarea>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Open Register',
                preConfirm: () => {
                    const openingBalance = document.getElementById('opening_balance').value;
                    if (!openingBalance || isNaN(parseFloat(openingBalance))) {
                        Swal.showValidationMessage('Please enter a valid opening balance');
                        return false;
                    }
                    return {
                        opening_balance: parseFloat(openingBalance),
                        notes: document.getElementById('register_notes').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = result.value;
                    $.ajax({
                        url: '/AdminPanel/api/cash_register.php',
                        method: 'POST',
                        data: {
                            action: 'open_register',
                            opening_balance: formData.opening_balance,
                            notes: formData.notes
                        },
                        success: function(response) {
                            try {
                                const data = typeof response === 'object' ? response : JSON.parse(response);
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Cash register opened successfully',
                                        icon: 'success'
                                    }).then(() => {
                                        // Refresh the cash register view
                                        openCashRegister();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message || 'Failed to open register',
                                        icon: 'error'
                                    });
                                }
                            } catch (error) {
                                console.error('Error processing response:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Invalid server response',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to communicate with server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function closeRegister() {
            Swal.fire({
                title: 'Close Cash Register',
                html: `
                    <div class="form-group">
                        <label for="cash_out">Cash Out (Rs.)</label>
                        <input type="number" id="cash_out" class="swal2-input" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="cash_drawer_balance">Cash Drawer Balance (Rs.)</label>
                        <input type="number" id="cash_drawer_balance" class="swal2-input" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="closing_notes">Notes (Optional)</label>
                        <textarea id="closing_notes" class="swal2-textarea" placeholder="Any notes about this register session..."></textarea>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Close Register',
                preConfirm: () => {
                    const cash_out = document.getElementById('cash_out').value;
                    const cash_drawer_balance = document.getElementById('cash_drawer_balance').value;
                    if (!cash_out || isNaN(parseFloat(cash_out))) {
                        Swal.showValidationMessage('Please enter the actual cash Out Amount');
                        return false;
                    }
                    if (!cash_drawer_balance || isNaN(parseFloat(cash_drawer_balance))) {
                        Swal.showValidationMessage('Please enter the actual cash Drawer Balance');
                        return false;
                    }
                    return {
                        cash_out: parseFloat(cash_out),
                        cash_drawer_balance: parseFloat(cash_drawer_balance),
                        notes: document.getElementById('closing_notes').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation while processing
                    Swal.fire({
                        title: 'Processing...',
                        html: '<div class="loading-spinner"></div>',
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = result.value;
                    $.ajax({
                        url: '/AdminPanel/api/cash_register.php',
                        method: 'POST',
                        data: {
                            action: 'close_register',
                            cash_out: formData.cash_out,
                            cash_drawer_balance: formData.cash_drawer_balance,
                            notes: formData.notes
                        },
                        success: function(response) {
                            // Close loading animation
                            Swal.close();

                            try {
                                const data = typeof response === 'object' ? response : JSON.parse(response);
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Cash register closed successfully',
                                        icon: 'success',
                                        showCancelButton: true,
                                        confirmButtonText: 'Print Report',
                                        cancelButtonText: 'Close'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            // Print the report
                                            printRegister(data.data.register_id);
                                        }
                                        // Refresh the cash register view
                                        openCashRegister();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message || 'Failed to close register',
                                        icon: 'error'
                                    });
                                }
                            } catch (error) {
                                console.error('Error processing response:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Invalid server response',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // Close loading animation
                            Swal.close();

                            console.error('AJAX error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to communicate with server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }

        function cashOut() {
            Swal.fire({
                title: 'Petty Cash',
                html: `
                    <div class="form-group">
                        <label for="amount">Amount (Rs.)</label>
                        <input type="number" id="amount" class="swal2-input" placeholder="0.00" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" class="swal2-input" placeholder="Purpose of petty cash">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Record Petty Cash',
                preConfirm: () => {
                    const amount = document.getElementById('amount').value;
                    const description = document.getElementById('description').value;

                    if (!amount || isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) {
                        Swal.showValidationMessage('Please enter a valid amount greater than zero');
                        return false;
                    }

                    if (!description.trim()) {
                        Swal.showValidationMessage('Please enter a description');
                        return false;
                    }

                    return {
                        amount: parseFloat(amount),
                        description: description,
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading animation while processing
                    Swal.fire({
                        title: 'Processing...',
                        html: '<div class="loading-spinner"></div>',
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = result.value;
                    $.ajax({
                        url: '/AdminPanel/api/cash_register.php',
                        method: 'POST',
                        data: {
                            action: 'cash_out',
                            amount: formData.amount,
                            description: formData.description,
                            notes: formData.notes
                        },
                        success: function(response) {
                            // Close loading animation
                            Swal.close();

                            try {
                                const data = typeof response === 'object' ? response : JSON.parse(response);
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Petty cash recorded successfully',
                                        icon: 'success'
                                    }).then(() => {
                                        // Refresh the cash register view
                                        openCashRegister();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: data.message || 'Failed to record petty cash',
                                        icon: 'error'
                                    });
                                }
                            } catch (error) {
                                console.error('Error processing response:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Invalid server response',
                                    icon: 'error'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // Close loading animation
                            Swal.close();

                            console.error('AJAX error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to communicate with server',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        }
    </script>

    <!-- Other scripts -->
    <script src="inc/searchCustomer function.js"></script>
    <script src="inc/hold_invoices/hold_invoices_logics.js"></script>    <style>
        .loading-spinner {
            display: inline-block;
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }        /* Print Preferences Styles */
        .print-preferences {
            margin: 15px 0;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            color: #333 !important;
            font-family: inherit;
        }

        .print-preferences * {
            color: inherit !important;
        }

        .print-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 20px;
            flex-wrap: nowrap;
        }

        .print-checkbox-container {
            flex-shrink: 0;
            min-width: fit-content;
        }

        .print-checkbox-container label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-weight: 600;
            color: #2c3e50 !important;
            font-size: 14px;
            white-space: nowrap;
        }

        .print-checkbox-container input[type="checkbox"] {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            accent-color: #2196F3;
            cursor: pointer;
            outline: none;
        }

        #print-type-container {
            flex: 0 0 auto;
            min-width: 180px;
        }

        .print-type-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 12px;
            background-color: #ffffff !important;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 13px;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }

        .toggle-label {
            color: #2c3e50 !important;
            font-weight: 500;
            white-space: nowrap;
            font-size: 12px;
        }

        /* Toggle Switch Styles */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 28px;
            flex-shrink: 0;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 28px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(22px);
        }

        .slider.round {
            border-radius: 28px;
        }

        .slider.round:before {
            border-radius: 50%;
        }        /* Responsive adjustments */
        @media (max-width: 768px) {
            .print-row {
                gap: 15px;
                flex-wrap: wrap;
            }
            
            .print-checkbox-container {
                flex: 0 0 auto;
            }
            
            #print-type-container {
                min-width: 150px;
            }
            
            .print-type-toggle {
                gap: 6px;
                padding: 5px 10px;
            }
            
            .toggle-label {
                font-size: 11px;
            }
        }

        @media (max-width: 480px) {
            .print-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            #print-type-container {
                width: 100%;
                min-width: unset;
            }
            
            .print-type-toggle {
                justify-content: space-between;
                width: 100%;
            }
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
</body>

</html>
