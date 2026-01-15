<?php 
require_once '../inc/config.php';
require '../inc/header.php'; 

// Fetch Invoice Data
$invoice_number = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($invoice_number === 0) {
    echo "<div class='error'>Invalid Invoice ID</div>";
    exit;
}

$sql = "SELECT i.*, COALESCE(e.emp_name, i.biller) as resolved_biller 
        FROM invoice i 
        LEFT JOIN employees e ON i.biller = CAST(e.employ_id AS CHAR) 
        WHERE i.invoice_number = $invoice_number";
$result = mysqli_query($con, $sql);
$invoice = mysqli_fetch_assoc($result);

if (!$invoice) {
    echo "<div class='error'>Invoice not found</div>";
    exit;
}

// Fetch Items using LEFT JOIN to identify Standard vs OTP
$sales_q = "SELECT s.*, p.product_id as linked_product_id 
            FROM sales s 
            LEFT JOIN products p ON s.product = p.product_name 
            WHERE s.invoice_number = $invoice_number";
$sales_res = mysqli_query($con, $sales_q);

$total_rows = mysqli_num_rows($sales_res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bill #<?php echo $invoice_number; ?> | Srijaya ERP</title>
    
    <!-- External Libs -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    
    <!-- Styles -->
    <link rel="stylesheet" href="../style.css">
    <style>
        .edit-bill-wrapper {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }
        .header-brand h1 { margin: 0; color: var(--primary); }
        .header-meta { text-align: right; color: var(--gray-600); }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--gray-700);
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus { border-color: var(--primary); outline: none; }

        /* Items Table */
        .items-table-container {
            overflow-x: auto;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.items-table th {
            background: var(--light);
            text-align: left;
            padding: 1rem;
            font-weight: 600;
            color: var(--gray-700);
        }
        table.items-table td {
            padding: 0.75rem;
            border-top: 1px solid #eee;
        }
        table.items-table input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-remove {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-remove:hover { background: #fecaca; }

        /* Footer Actions */
        .footer-actions {
            background: var(--gray-100);
            padding: 1.5rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 2rem;
        }
        .totals-section {
            min-width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        .total-row.final {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
            border-top: 1px solid #ddd;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 6px;
            font-size: 1.1rem;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: filter 0.2s;
        }
        .btn-primary:hover { filter: brightness(110%); }
        
        .add-product-bar {
            position: relative;
            margin-top: 1rem;
            background: #f8fafc;
            padding: 1rem;
            border-radius: 6px;
        }
        
        /* Search Results Dropdown */
        #search-results {
            position: absolute;
            background-color: white;
            border: 1px solid #ccc;
            width: calc(100% - 2rem); /* adjusting for padding */
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
            display: none;
            left: 1rem;
            top: 4.5rem; /* Adjust based on input height */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .search-result-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        .search-result-item:hover, .search-highlighted {
            background-color: #f0f7ff;
        }
    </style>
</head>
<body>

<div class="edit-bill-wrapper">
    <div class="header-section">
        <div class="header-brand">
            <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
            <p><?php echo $ERP_COMPANY_ADDRESS; ?></p>
        </div>
        <div class="header-meta">
            <h2>Sort Edit Bill #<?php echo $invoice_number; ?></h2>
            <a href="/index.php" style="color: var(--primary); text-decoration: none;">&larr; Back to Dashboard</a>
        </div>
    </div>

    <form id="editInvoiceForm">
        <div class="card">
            <h3>Customer & Invoice Details</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name</label>
                    <input list="customer_list" name="name" id="name" value="<?php echo htmlspecialchars($invoice['customer_name']); ?>" required onchange="customer_add()">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="tele" id="tele" value="<?php echo htmlspecialchars($invoice['customer_mobile']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Invoice Date</label>
                    <input type="date" name="today" id="date" value="<?php echo $invoice['invoice_date']; ?>" required>
                </div>
                <div class="form-group">
                    <label>Invoice Number</label>
                    <input type="text" name="bill-no" value="<?php echo $invoice_number; ?>" readonly style="background: #f9f9f9;">
                </div>
            </div>
            
            <div class="form-group">
                <label>Description / Notes</label>
                <textarea name="InvoiceDescription" rows="2"><?php echo htmlspecialchars($invoice['invoice_description']); ?></textarea>
            </div>
        </div>

        <div class="card">
            <h3>Items</h3>
            <div class="items-table-container">
                <table class="items-table" id="productsTable">
                    <thead>
                        <tr>
                            <th style="width: 40%;">Description</th>
                            <th style="width: 15%;">Qty</th>
                            <th style="width: 20%;">Rate</th>
                            <th style="width: 20%;">Amount</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="list">
                        <?php 
                        $idx = 0;
                        function renderRow($idx, $item) {
                            $product = htmlspecialchars($item['product']);
                            $qty = $item['qty'];
                            $rate = $item['rate'];
                            $amount = $item['amount'];
                            
                            // Determine if One Time Product (linked_product_id is NULL)
                            $isOtp = empty($item['linked_product_id']);
                            
                            echo "<tr id='row_$idx'>";
                            echo "<td>
                                    <input type='hidden' id='is_otp_$idx' value='" . ($isOtp ? 1 : 0) . "'>
                                    <input id='product_$idx' name='product_$idx' value='$product' class='$idx' readonly style='width:100%; border:1px solid #ddd; padding:5px; background-color: #f0f0f0; cursor: not-allowed;'>
                                  </td>";
                            echo "<td><input type='number' id='qty_$idx' name='qty_$idx' value='$qty' class='$idx' oninput=\"change('qty', '$idx', 'qty_$idx')\" style='width:100%; border:1px solid #ddd; padding:5px;'></td>";
                            echo "<td><input type='number' id='rate_$idx' name='rate_$idx' value='$rate' class='$idx' oninput=\"change('rate', '$idx', 'rate_$idx')\" style='width:100%; border:1px solid #ddd; padding:5px;'></td>";
                            echo "<td><input type='number' id='amount_$idx' name='amount_$idx' value='$amount' class='$idx' onchange=\"change('amount', '$idx', 'amount_$idx')\" style='width:100%; border:1px solid #ddd; padding:5px;'></td>";
                            echo "<td><button type='button' class='btn-remove' onclick=\"remove_row('_$idx', '$idx')\">×</button></td>";
                            echo "</tr>";
                        }

                        // Render All Items
                        while($row = mysqli_fetch_assoc($sales_res)) {
                            renderRow($idx++, $row);
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Product Interface (Modern Search) -->
            <div class="add-product-bar" style="display: flex; gap: 10px;">
                 <div style="flex-grow: 1; position: relative;">
                     <input type="text" id="product-input" placeholder="Search product to add (Name, SKU, Barcode)..." autocomplete="off" style="width: 100%; padding: 0.75rem; border: 1px solid var(--gray-300); border-radius: 4px;">
                     <div id="search-results"></div>
                 </div>
                 <button type="button" class="btn-primary" onclick="addCustomItemModal('')" style="white-space: nowrap;">+ Custom Item</button>
            </div>
        </div>

        <div class="footer-actions">
            <!-- Settings & Staff -->
            <div style="flex: 1; min-width: 300px;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Sales Employee</label>
                        <select name="biller" id="biller" style="width:100%; padding:0.75rem; border:1px solid var(--gray-300); border-radius:6px;">
                            <option value="">Select Employee</option>
                            <?php 
                            $emp_q = mysqli_query($con, "SELECT employ_id, emp_name FROM employees WHERE status = '1'");
                            $found = false;
                            while($emp = mysqli_fetch_assoc($emp_q)) {
                                $selected = '';
                                if ($emp['emp_name'] == $invoice['resolved_biller']) {
                                    $selected = 'selected';
                                    $found = true;
                                } elseif ($emp['employ_id'] == $invoice['biller']) {
                                    $selected = 'selected';
                                    $found = true;
                                }
                                echo "<option value='{$emp['emp_name']}' $selected>{$emp['emp_name']}</option>";
                            }
                            // If the current biller is not in the active list (legacy or inactive), show them anyway
                            if (!$found && !empty($invoice['resolved_biller'])) {
                                 echo "<option value='{$invoice['resolved_biller']}' selected>{$invoice['resolved_biller']} (Inactive/Other)</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="PaymentMethod" id="PaymentMethod">
                            <?php 
                            $methods = ['Cash', 'BankTransfer', 'CardPayment', 'Cheque', 'QRPayment'];
                            foreach($methods as $m) {
                                $sel = ($invoice['paymentMethod'] == $m) ? 'selected' : '';
                                echo "<option value='$m' $sel>$m</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="ChangeStock" id="ChangeStock" value="1" checked style="width: 20px; height: 20px;">
                        <span>Update Stock Inventory?</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-top: 5px;">
                        <input type="checkbox" name="UpdateCommission" id="UpdateCommission" value="1" checked style="width: 20px; height: 20px;">
                        <span>Update Employee Commission?</span>
                    </label>
                </div>
            </div>

            <!-- Totals -->
            <div class="totals-section card">
                <div style="background-color: #ef4444; color: white; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-weight: 500; font-size: 0.9em; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    ⚠️ Remember to change Balance and recheck Balance!
                </div>
                <div class="total-row">
                    <span>Total:</span>
                    <input id="total" name="total" value="<?php echo $invoice['total']; ?>" readonly style="width: 120px; text-align: right; border: none; font-weight: bold;">
                </div>
                <div class="total-row">
                    <span>Discount:</span>
                    <input id="discount" name="discount" value="<?php echo $invoice['discount']; ?>" oninput="change_endline()" style="width: 120px; text-align: right;">
                </div>
                <div class="total-row">
                    <span>Advance:</span>
                    <input id="advance" name="advance" value="<?php echo $invoice['advance']; ?>" oninput="change_endline()" style="width: 120px; text-align: right;">
                </div>
                <div class="total-row final">
                    <span>Balance:</span>
                    <input id="balance" name="balance" value="<?php echo $invoice['balance']; ?>" readonly style="width: 120px; text-align: right; border: none; color: var(--primary);">
                </div>
                
                <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 1.5rem;">
                    💾 Save Changes
                </button>
            </div>
        </div>

        <!-- Hidden Counters -->
        <input type="hidden" name="no" id="no" value="<?php echo $idx; ?>">
    </form>
</div>

<!-- Scripts -->
<script>
    var no = <?php echo $idx; ?>; // Row counter
    var productsCache = {};
    var selectedSearchIndex = -1;
    var lastSearchQuery = '';
</script>

<!-- Datalists -->

<datalist id="customer_list">
     <?php 
    $cust_q = mysqli_query($con, "SELECT customer_name FROM customers");
    while($r = mysqli_fetch_assoc($cust_q)) echo "<option value='{$r['customer_name']}'>";
    ?>
</datalist>

<!-- Core Logic -->
<script src="/inc/invoice_main_functions.jsx"></script> 
<script src="/invoice/invoice-edit-actions.js"></script>

<script>
    /* =========================================
       SEARCH IMPLEMENTATION (Ported from index.php)
       ========================================= */
       
    function debounce(func, delay) {
        let debounceTimer;
        return function(...args) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    $(document).ready(function() {
        add_total(0); // Initial calculation

        // Search Input Handler
        $('#product-input').on('input', debounce(function() {
            selectedSearchIndex = -1;
            const query = $(this).val().trim();
            lastSearchQuery = query;

            if (query.length > 0) {
                fetchProduct(query);
            } else {
                $('#search-results').hide();
            }
        }, 300));

        // Keyboard Navigation
        $('#product-input').on('keydown', function(e) {
            const $searchResults = $('#search-results');
            const $searchItems = $searchResults.find('.search-result-item');

            if (e.key === "ArrowDown") {
                e.preventDefault();
                if ($searchResults.is(':visible')) {
                    selectedSearchIndex = (selectedSearchIndex + 1) % $searchItems.length;
                    highlightSearchResult();
                }
            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                if ($searchResults.is(':visible')) {
                    selectedSearchIndex = selectedSearchIndex <= 0 ? $searchItems.length - 1 : selectedSearchIndex - 1;
                    highlightSearchResult();
                }
            } else if (e.key === "Enter") {
                e.preventDefault();
                if ($searchResults.is(':visible') && selectedSearchIndex >= 0) {
                    $searchItems.eq(selectedSearchIndex).click();
                }
            } else if (e.key === "Escape") {
                $('#search-results').hide();
            }
        });

        // Click Outside to Close
        $(document).on('click', function(event) {
            if (!$(event.target).closest('#product-input, #search-results').length) {
                $('#search-results').hide();
            }
        });

        // Click Result Handler
        $(document).on('click', '.search-result-item', function() {
            let product = JSON.parse($(this).attr('data-product'));
            fetchProduct(product.product_name, true); // Fetch batches
            $('#search-results').hide();
            $('#product-input').val(''); // Clear search
        });
        
        // Select Batch Button Handler (Dynamically added in Modal)
        $(document).on('click', '.select-batch', function() {
            let product = JSON.parse($(this).attr('data-product'));
            let batch = JSON.parse($(this).attr('data-batch'));
            
            // ADD TO BILL
            addproduct(product.product_name, batch.selling_price, 1, false);
            Swal.close();
            
            // Focus on Qty of the newly added row (last row) -- logic in addproduct handles idx
        });
    });

    function fetchProduct(query, selectFirstMatch = false) {
        if (productsCache[query]) {
            handleProductResponse(productsCache[query], selectFirstMatch);
        } else {
            $.ajax({
                url: '/inc/fetch_product.php',
                method: 'GET',
                data: { search: query },
                success: function(data) {
                    let response = typeof data === "object" ? data : JSON.parse(data);
                    productsCache[query] = response;
                    handleProductResponse(response, selectFirstMatch);
                }
            });
        }
    }

    function handleProductResponse(response, selectFirstMatch) {
        if (response.products.length === 1) {
            let product = response.products[0];
            if (response.batches.length === 1) {
                // Direct Add
                addproduct(product.product_name, response.batches[0].selling_price, 1, false);
                $('#search-results').hide();
                $('#product-input').val('');
            } else if (response.batches.length > 1) {
                // Show Batch Modal
                displayBatchModal(product, response.batches);
            } else {
                Swal.fire('Error', 'No batches found', 'error');
            }
        } else if (response.products.length > 1) {
            displaySearchResults(response.products); // Show list
        } else {
             $('#search-results').hide(); 
             // Optional: Show "No results"
        }
    }

    function displaySearchResults(products) {
        let html = products.map(p => `
            <div class="search-result-item" data-product='${JSON.stringify(p)}'>
                ${p.product_name} - ${p.sku || ''}
            </div>
        `).join('');
        $('#search-results').html(html).show();
    }

    function highlightSearchResult() {
        const $items = $('.search-result-item');
        $items.removeClass('search-highlighted');
        if (selectedSearchIndex >= 0) $items.eq(selectedSearchIndex).addClass('search-highlighted');
    }

    function displayBatchModal(product, batches) {
        let html = `
            <h3>${product.product_name}</h3>
            <table style="width:100%; text-align:left; margin-top:10px; border-collapse:collapse;">
                <tr style="background:#f5f5f5;"><th>Batch</th><th>Price</th><th>Expiry</th><th>Qty</th><th>Action</th></tr>
                ${batches.map(b => `
                    <tr>
                        <td style="padding:8px; border-bottom:1px solid #eee;">${b.batch_number}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">${b.selling_price}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">${b.expiry_date || '-'}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">${b.batch_quantity}</td>
                        <td style="padding:8px; border-bottom:1px solid #eee;">
                            <button type="button" class="select-batch btn-primary" style="padding:5px 10px; font-size:12px;" 
                                data-product='${JSON.stringify(product)}' 
                                data-batch='${JSON.stringify(b)}'>Select</button>
                        </td>
                    </tr>
                `).join('')}
            </table>
        `;
        Swal.fire({
            html: html,
            showConfirmButton: false,
            width: '600px'
        });
    }

    /* =========================================
       OVERRIDES (Table Logic)
       ========================================= */

    window.addproduct = function(oneTimeProductName, oneTimeProductRate, oneTimeProductQty, oneTimeProduct) {
        var tbody = document.getElementById('list');
        var product_name = oneTimeProductName || ''; 
        // Note: No document.getElementById('addproduct').value fallback because we use new search input

        if (!product_name) return;

        var idx = window.no; 
        var row = document.createElement('tr');
        row.id = 'row_' + idx;

        var qty = oneTimeProductQty || 1;
        var rate = oneTimeProductRate || 0;
        var amount = (qty * rate).toFixed(2);
        
        // Fetch rate if not provided (fallback for manual entry if we ever allow it)
        if(!oneTimeProductRate) {
             $.ajax({
                url: "/inc/get_product_rate.php",
                method: "POST",
                data: { product: product_name },
                success: function (html) {
                    var r = Number(html).toFixed(2);
                    document.getElementById('rate_' + idx).value = r;
                    document.getElementById('amount_' + idx).value = (qty * r).toFixed(2);
                    add_total(idx);
                }
            });
        }

        var idInput = oneTimeProduct ? 
            `<input type='hidden' name='oneTimeProductID_${idx}' value='new'>` : 
            `<input type='hidden' name='salesID_${idx}' value='new'>`;
            
        var otpVal = oneTimeProduct ? 1 : 0;
        var otpInput = `<input type='hidden' id='is_otp_${idx}' value='${otpVal}'>`;

        row.innerHTML = `
            <td>
                ${idInput}
                ${otpInput}
                <input id='product_${idx}' name='product_${idx}' value='${product_name}' class='${idx}' readonly style="width:100%; border:1px solid #ddd; padding:5px; background-color: #f0f0f0; cursor: not-allowed;">
            </td>
            <td><input type='number' id='qty_${idx}' name='qty_${idx}' value='${qty}' class='${idx}' oninput="change('qty', '${idx}', 'qty_${idx}')" style="width:100%; border:1px solid #ddd; padding:5px;"></td>
            <td><input type='number' id='rate_${idx}' name='rate_${idx}' value='${rate}' class='${idx}' oninput="change('rate', '${idx}', 'rate_${idx}')" style="width:100%; border:1px solid #ddd; padding:5px;"></td>
            <td><input type='number' id='amount_${idx}' name='amount_${idx}' value='${amount}' class='${idx}' onchange="change('amount', '${idx}', 'amount_${idx}')" style="width:100%; border:1px solid #ddd; padding:5px;"></td>
            <td><button type='button' class='btn-remove' onclick="remove_row('_$idx', '${idx}')">×</button></td>
        `;

        tbody.appendChild(row);
        
        window.no++;
        document.getElementById('no').value = window.no;
        
        add_total(idx);
    };

    window.add_total = function(idx) {
        var total = 0;
        var inputs = document.querySelectorAll('input[name^="amount_"]');
        inputs.forEach(function(inp) {
            if(inp.closest('tr') && inp.closest('tr').style.display !== 'none') {
                total += parseFloat(inp.value) || 0;
            }
        });

        var tField = document.getElementById('total');
        if(tField) tField.value = total.toFixed(2);


        var disc = parseFloat(document.getElementById('discount').value) || 0;
        var adv = parseFloat(document.getElementById('advance').value) || 0;
        var bal = total - disc - adv;
        
        document.getElementById('balance').value = bal.toFixed(2);
    };

    // Custom Modal for Edit Bill (Renamed to avoid conflict)
    window.addCustomItemModal = function(productName) {
        Swal.fire({
            title: 'Add One Time Product Details',
            html: `
                <div style="text-align: right; margin-bottom: 10px;">
                    <a href="/products/create/" class="btn btn-success" style="padding: 5px 10px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
                <label for='oneTimeProductName' class='swal2-label'> Product Name:</label>
                <input id="oneTimeProductName" class="swal2-input" value="${productName}" placeholder="Enter Product Name"><br>
                <label for="oneTimeProductRegularPrice" class="swal2-label">Regular Price (Rs.):</label>
                <input id="oneTimeProductRegularPrice" class="swal2-input" type="number" step="0.01" placeholder="Enter Regular Price"><br>
                <label for="oneTimeProductDiscountPrice" class="swal2-label">Discount Price (Rs.):</label>
                <input id="oneTimeProductDiscountPrice" class="swal2-input" type="number" step="0.01" placeholder="Enter Discount Price (optional)"><br>
                <label for="oneTimeProductQty" class="swal2-label">Quantity:</label>
                <input id="oneTimeProductQty" class="swal2-input" type="number" step="0.001" placeholder="Enter Quantity">`,
            focusConfirm: false,
            preConfirm: () => {
                const oneTimeProductName = Swal.getPopup().querySelector('#oneTimeProductName').value;
                const oneTimeProductRegularPrice = Swal.getPopup().querySelector('#oneTimeProductRegularPrice').value;
                const oneTimeProductDiscountPrice = Swal.getPopup().querySelector('#oneTimeProductDiscountPrice').value;
                const oneTimeProductQty = Swal.getPopup().querySelector('#oneTimeProductQty').value;
                
                const finalRate = oneTimeProductDiscountPrice && !isNaN(oneTimeProductDiscountPrice) 
                    ? oneTimeProductDiscountPrice 
                    : oneTimeProductRegularPrice;
                    
                if (oneTimeProductName && oneTimeProductRegularPrice && oneTimeProductQty && 
                    !isNaN(oneTimeProductRegularPrice) && !isNaN(oneTimeProductQty)) {
                    
                    // Directly call our overridden addproduct
                    window.addproduct(oneTimeProductName, finalRate, oneTimeProductQty, true);
                } else {
                    Swal.showValidationMessage(`Please enter all required fields correctly.`);
                }
            }
        });
    };





    window.remove_row = function(id, className) {
        var idx = className;
        var row = document.getElementById('row_' + idx);
        if(row) row.remove();
        add_total(0);
    };

</script>

</body>
</html>
<?php end_db_con(); ?>