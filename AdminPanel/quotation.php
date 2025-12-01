<?php
include 'nav.php';
?>
<title>Quotation - Admin Panel</title>

<style>
    body {
        color: white;
    }

    .quotation-container {
        padding: 20px;
        max-width: 1600px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
        color: white;
        font-size: 24px;
    }

    .page-header h1 i {
        color: #28a745;
        margin-right: 10px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-warning {
        background: #ffc107;
        color: #333;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
        margin: 2px;
    }

    .quotation-table-container {
        background: transparent;
        padding: 0;
    }

    table.quotation-table {
        width: 100% !important;
        background: transparent !important;
        color: white !important;
    }

    table.quotation-table thead th {
        background: transparent !important;
        font-weight: 600;
        color: white !important;
        padding: 12px !important;
        border-bottom: 2px solid #444 !important;
    }

    table.quotation-table tbody td {
        padding: 10px !important;
        vertical-align: middle;
        color: white !important;
        border-bottom: 1px solid #444 !important;
    }

    .status-badge {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-draft {
        background: #6c757d;
        color: white;
    }

    .status-sent {
        background: #007bff;
        color: white;
    }

    .status-accepted {
        background: #28a745;
        color: white;
    }

    .status-rejected {
        background: #dc3545;
        color: white;
    }

    .status-expired {
        background: #ffc107;
        color: #333;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
    }

    .modal-content {
        background-color: #2c3e50;
        margin: 5% auto;
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 900px;
        color: white;
        max-height: 85vh;
        overflow-y: auto;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: white;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #555;
        border-radius: 4px;
        background: #34495e;
        color: white;
        box-sizing: border-box;
        font-size: 14px;
    }

    .form-control:focus {
        border-color: #28a745;
        outline: none;
        box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
    }

    .form-control::placeholder {
        color: #aaa;
    }

    .product-row {
        border: 1px solid #555;
        padding: 12px;
        margin-bottom: 8px;
        border-radius: 4px;
        background: #3a4f5f;
        transition: background 0.2s;
    }

    .product-row:hover {
        background: #3d5a6c;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }

    .col {
        flex: 1;
        min-width: 200px;
    }

    .col-auto {
        flex: 0 0 auto;
    }

    .summary-section {
        background: #2c3e50;
        padding: 15px 20px;
        border-radius: 6px;
        margin-top: 20px;
        border: 1px solid #555;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding: 5px 0;
        font-size: 14px;
    }

    .summary-row.total {
        font-weight: bold;
        font-size: 20px;
        border-top: 2px solid #28a745;
        padding-top: 12px;
        margin-top: 8px;
        color: #28a745;
    }

    .btn-group {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 25px;
    }

    .btn i {
        margin-right: 5px;
    }

    datalist {
        background: #34495e;
    }

    /* Toggle Switch Styles */
    .toggle-container {
        margin-bottom: 20px;
        padding: 15px;
        background: #34495e;
        border-radius: 6px;
        border: 1px solid #555;
    }

    .toggle-label {
        font-weight: bold;
        margin-bottom: 10px;
        display: block;
        color: #fff;
    }

    .toggle-switch {
        display: inline-flex;
        background: #1e2a38;
        border-radius: 25px;
        padding: 4px;
        gap: 4px;
    }

    .toggle-option {
        padding: 8px 20px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        font-weight: 500;
        border: none;
        background: transparent;
        color: #aaa;
    }

    .toggle-option.active {
        background: #28a745;
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .toggle-option:hover:not(.active) {
        color: #fff;
    }

    .customer-search-container,
    .customer-new-container {
        margin-top: 15px;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding-left: 35px;
    }

    .search-box i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #aaa;
    }
</style>

<div class="quotation-container">
    <div class="page-header">
        <h1><i class="fas fa-file-invoice"></i> Quotations</h1>
        <button class="btn btn-success" onclick="openQuotationModal()">
            <i class="fas fa-plus"></i> New Quotation
        </button>
    </div>

    <div class="quotation-table-container">
        <table class="quotation-table display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>Quote #</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Quotation Modal -->
<div id="quotationModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2 id="modalTitle">New Quotation</h2>
        <form id="quotationForm">
            <input type="hidden" id="quotationId" name="id">

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="quotationNumber">Quotation Number</label>
                        <input type="text" class="form-control" id="quotationNumber" name="quotation_number" readonly>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="quotationDate">Quotation Date</label>
                        <input type="date" class="form-control" id="quotationDate" name="quotation_date" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="validUntil">Valid Until</label>
                        <input type="date" class="form-control" id="validUntil" name="valid_until">
                    </div>
                </div>
            </div>

            <!-- Customer Toggle Section -->
            <div class="toggle-container">
                <label class="toggle-label">Customer Type</label>
                <div class="toggle-switch">
                    <button type="button" class="toggle-option active" onclick="toggleCustomerType('existing')" id="toggleExistingCustomer">
                        <i class="fas fa-users"></i> &nbsp; Existing Customer
                    </button>
                    <button type="button" class="toggle-option" onclick="toggleCustomerType('new')" id="toggleNewCustomer">
                        <i class="fas fa-user-plus"></i> &nbsp; New Customer
                    </button>
                </div>

                <!-- Existing Customer Search -->
                <div class="customer-search-container" id="customerSearchContainer">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="customerSearch">Search Customer *</label>
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" class="form-control" id="customerSearch" placeholder="Search by name or phone" autocomplete="off">
                                </div>
                                <select class="form-control" id="customerSelect" size="5" style="margin-top: 5px; display: none;">
                                    <!-- Options loaded dynamically -->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Customer Form -->
                <div class="customer-new-container" id="customerNewContainer" style="display: none;">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="customerName">Customer Name *</label>
                                <input type="text" class="form-control" id="customerName" name="customer_name" placeholder="Enter customer name" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="customerMobile">Mobile *</label>
                                <input type="text" class="form-control" id="customerMobile" name="customer_mobile" placeholder="Enter mobile number" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="customerAddress">Address</label>
                                <input type="text" class="form-control" id="customerAddress" name="customer_address" placeholder="Enter customer address">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="note">Note</label>
                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
            </div>

            <div style="margin-top: 25px; margin-bottom: 15px;">
                <h3 style="display: inline-block; margin: 0;">Products</h3>
                <button type="button" class="btn btn-info btn-sm" onclick="addProductRow()" style="float: right;">
                    <i class="fas fa-plus"></i> Add Row
                </button>
                <div style="clear: both;"></div>
            </div>
            <div id="productsContainer">
                <!-- Product rows will be added dynamically -->
            </div>

            <div class="summary-section">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="subtotalAmount">Rs. 0.00</span>
                </div>
                <div class="summary-row">
                    <span>Discount:</span>
                    <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="0.00" style="width: 150px; display: inline-block; padding: 5px 10px;" onchange="calculateTotal()">
                </div>
                <div class="summary-row total">
                    <span>TOTAL:</span>
                    <span id="totalAmount">Rs. 0.00</span>
                </div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save
                </button>
                <button type="button" class="btn btn-primary" onclick="saveAndPrint()">
                    <i class="fas fa-print"></i> Save & Print
                </button>
                <button type="button" class="btn btn-info" onclick="saveAndExport()">
                    <i class="fas fa-file-pdf"></i> Save & Export PDF
                </button>
                <button type="button" class="btn btn-danger" onclick="closeQuotationModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let quotationTable;
    let productRowIndex = 0;
    let quotationSettings = {
        validityDays: 30,
        prefix: 'QT',
        autoGenerate: true
    };

    $(document).ready(function() {
        initializeQuotationTable();
        loadCustomers();
        loadProducts();
        loadQuotationSettings();

        // Set default dates
        const today = new Date().toISOString().split('T')[0];
        $('#quotationDate').val(today);
        updateValidUntilDate();
    });

    function loadQuotationSettings() {
        // Load quotation settings from database
        fetch('/AdminPanel/api/update_settings.php?action=get_quotation_settings')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    quotationSettings.validityDays = parseInt(data.settings.quotation_validity_days) || 30;
                    quotationSettings.prefix = data.settings.quotation_prefix || 'QT';
                    quotationSettings.autoGenerate = data.settings.quotation_auto_generate == '1';
                    updateValidUntilDate();
                }
            })
            .catch(error => console.error('Error loading quotation settings:', error));
    }

    function updateValidUntilDate() {
        const quotationDate = $('#quotationDate').val();
        if (quotationDate) {
            const validUntil = new Date(quotationDate);
            validUntil.setDate(validUntil.getDate() + quotationSettings.validityDays);
            $('#validUntil').val(validUntil.toISOString().split('T')[0]);
        }
    }

    // Update valid until when quotation date changes
    $(document).on('change', '#quotationDate', function() {
        updateValidUntilDate();
    });

    function initializeQuotationTable() {
        quotationTable = $('.quotation-table').DataTable({
            ajax: {
                url: '/AdminPanel/api/quotation.php?action=list',
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'quotation_number'
                },
                {
                    data: 'customer_name'
                },
                {
                    data: 'quotation_date'
                },
                {
                    data: 'valid_until'
                },
                {
                    data: 'status',
                    render: function(data) {
                        return `<span class="status-badge status-${data}">${data}</span>`;
                    }
                },
                {
                    data: 'total',
                    render: function(data) {
                        return 'Rs. ' + parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                        <button class="btn btn-info btn-sm" onclick="viewQuotation(${row.id})" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editQuotation(${row.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="printQuotation(${row.id})" title="Print">
                            <i class="fas fa-print"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteQuotation(${row.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    }
                }
            ],
            responsive: true,
            pageLength: 25,
            order: [
                [2, 'desc']
            ], // Order by date descending
            language: {
                emptyTable: "No quotations found"
            }
        });
    }

    function loadCustomers() {
        fetch('/AdminPanel/api/customers.php')
            .then(response => response.json())
            .then(data => {
                const customerList = $('#customersList');
                customerList.empty();

                if (data.status === 'success' && data.data) {
                    // Store customers data globally for autocomplete
                    window.customersData = data.data;

                    data.data.forEach(customer => {
                        customerList.append(`<option value="${customer.name}" 
                        data-mobile="${customer.mobile || ''}" 
                        data-address="${customer.address || ''}">
                    </option>`);
                    });
                }
            })
            .catch(error => console.error('Error loading customers:', error));
    }

    function loadProducts() {
        // This will be used for product dropdown in product rows
        window.productsData = [];
        
        // Try AdminPanel products API first (no authentication required)
        fetch('/AdminPanel/api/products.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && data.data) {
                    window.productsData = data.data;
                    console.log('Products loaded:', window.productsData.length);
                } else {
                    // Fallback to v1 API
                    return fetch('/api/v1/products/list.php?per_page=1000')
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data && data.data.products) {
                                window.productsData = data.data.products;
                            } else if (data.status === 'success' && data.data) {
                                window.productsData = data.data;
                            }
                        });
                }
            })
            .catch(error => console.error('Error loading products:', error));
    }

    // Customer name input change handler for autocomplete
    $(document).on('input', '#customerName', function() {
        const inputValue = $(this).val();
        if (window.customersData) {
            const customer = window.customersData.find(c => c.name === inputValue);
            if (customer) {
                $('#customerMobile').val(customer.mobile || '');
                $('#customerAddress').val(customer.address || '');
            }
        }
    });

    // Customer toggle functions
    function toggleCustomerType(type) {
        if (type === 'existing') {
            $('#toggleExistingCustomer').addClass('active');
            $('#toggleNewCustomer').removeClass('active');
            $('#customerSearchContainer').show();
            $('#customerNewContainer').hide();
            // Clear new customer fields
            $('#customerName').val('').removeAttr('required');
            $('#customerMobile').val('').removeAttr('required');
            $('#customerAddress').val('');
        } else {
            $('#toggleNewCustomer').addClass('active');
            $('#toggleExistingCustomer').removeClass('active');
            $('#customerNewContainer').show();
            $('#customerSearchContainer').hide();
            // Clear search
            $('#customerSearch').val('');
            $('#customerSelect').hide().empty();
            // Make new customer fields required
            $('#customerName').attr('required', 'required');
            $('#customerMobile').attr('required', 'required');
        }
    }

    // Customer search functionality
    $(document).on('input', '#customerSearch', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        const customerSelect = $('#customerSelect');

        if (searchTerm.length < 1) {
            customerSelect.hide().empty();
            return;
        }

        if (window.customersData) {
            const filtered = window.customersData.filter(customer => {
                const name = (customer.name || '').toLowerCase();
                const mobile = (customer.mobile || '').toLowerCase();
                return name.includes(searchTerm) || mobile.includes(searchTerm);
            });

            customerSelect.empty();

            if (filtered.length > 0) {
                filtered.forEach(customer => {
                    const displayText = `${customer.name} ${customer.mobile ? '- ' + customer.mobile : ''}`;
                    customerSelect.append(`<option value='${JSON.stringify(customer)}'>${displayText}</option>`);
                });
                customerSelect.show();
            } else {
                customerSelect.hide();
            }
        }
    });

    // Customer selection
    $(document).on('change', '#customerSelect', function() {
        const selectedValue = $(this).val();
        if (selectedValue) {
            try {
                const customer = JSON.parse(selectedValue);
                $('#customerName').val(customer.name || '');
                $('#customerMobile').val(customer.mobile || '');
                $('#customerAddress').val(customer.address || '');
                $('#customerSearch').val(customer.name || '');
                $(this).hide();
            } catch (e) {
                console.error('Error parsing customer data:', e);
            }
        }
    });

// Product toggle function
function toggleProductType(rowIndex, type) {
    const row = $(`.product-row[data-index="${rowIndex}"]`);
    const toggleButtons = row.find('.product-toggle');
    
    toggleButtons.removeClass('active');
    row.find(`.product-toggle[data-row="${rowIndex}"][data-type="${type}"]`).addClass('active');
    
    if (type === 'existing') {
        $(`.product-search-container-${rowIndex}`).show();
        $(`.product-new-container-${rowIndex}`).hide();
        
        // Clear manual entry
        row.find('.product-name-manual').val('');
        
        // Make rate readonly for existing products
        row.find('.rate-input').attr('readonly', 'readonly').val('');
        
        // Clear product data
        row.find('.product-id-input').val('');
        row.find('.product-name-input').val('');
    } else {
        $(`.product-new-container-${rowIndex}`).show();
        $(`.product-search-container-${rowIndex}`).hide();
        
        // Clear search
        row.find('.product-search-input').val('');
        row.find('.product-select').hide().empty();
        
        // Make rate editable for new products
        row.find('.rate-input').removeAttr('readonly').val('');
        
        // Clear product data
        row.find('.product-id-input').val('');
        row.find('.product-name-input').val('');
    }
    
    // Recalculate totals
    calculateRowTotal(row.find('.quantity-input')[0]);
}    // Product search function
    function searchProducts(rowIndex, searchTerm) {
        searchTerm = searchTerm.toLowerCase().trim();
        const row = $(`.product-row[data-index="${rowIndex}"]`);
        const productSelect = row.find('.product-select');

        if (searchTerm.length < 1) {
            productSelect.hide().empty();
            return;
        }

        if (window.productsData) {
            const filtered = window.productsData.filter(product => {
                const name = (product.product_name || '').toLowerCase();
                return name.includes(searchTerm);
            });

            productSelect.empty();

            if (filtered.length > 0) {
                filtered.forEach(product => {
                    const price = product.price || product.selling_price || 0;
                    const displayText = `${product.product_name} - Rs. ${parseFloat(price).toFixed(2)}`;
                    productSelect.append(`<option value='${JSON.stringify(product)}'>${displayText}</option>`);
                });
                productSelect.show();
            } else {
                productSelect.hide();
            }
        }
    }

    // Product selection
    function selectProduct(rowIndex, selectedValue) {
        if (!selectedValue) return;

        try {
            const product = JSON.parse(selectedValue);
            const row = $(`.product-row[data-index="${rowIndex}"]`);

            row.find('.product-id-input').val(product.product_id);
            row.find('.product-name-input').val(product.product_name);
            row.find('.product-search-input').val(product.product_name);
            row.find('.rate-input').val(product.price || product.selling_price || 0);
            row.find('.product-select').hide();

            calculateRowTotal(row.find('.quantity-input')[0]);
        } catch (e) {
            console.error('Error parsing product data:', e);
        }
    }

// Update manual product entry
function updateManualProduct(rowIndex) {
    const row = $(`.product-row[data-index="${rowIndex}"]`);
    const productName = row.find('.product-name-manual').val();
    
    // Set both the hidden input and generate a manual ID
    row.find('.product-id-input').val('MANUAL_' + Date.now());
    row.find('.product-name-input').val(productName);
    
    // Also ensure the visible input in the form has the name attribute value
    row.find('.product-name-manual').attr('value', productName);
}    function openQuotationModal(quotationId = null) {
        if (quotationId) {
            // Edit mode
            $('#modalTitle').text('Edit Quotation');
            loadQuotationData(quotationId);
        } else {
            // Add mode
            $('#modalTitle').text('New Quotation');
            $('#quotationForm')[0].reset();
            $('#quotationId').val('');
            $('#productsContainer').empty();
            generateQuotationNumber();

            // Initialize customer toggle to existing
            toggleCustomerType('existing');

            addProductRow();
            calculateTotal();
        }
        $('#quotationModal').show();
    }

    function closeQuotationModal() {
        $('#quotationModal').hide();
    }

    function generateQuotationNumber() {
        fetch('/AdminPanel/api/quotation.php?action=generate_number')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $('#quotationNumber').val(data.quotation_number);
                }
            });
    }

function addProductRow() {
    productRowIndex++;
    const productRow = `
        <div class="product-row" data-index="${productRowIndex}" style="border: 1px solid #555; padding: 15px; margin-bottom: 15px; border-radius: 6px; background: #1e2a38;">
            <!-- Product Type Toggle -->
            <div style="margin-bottom: 15px;">
                <div class="toggle-switch" style="display: inline-flex;">
                    <button type="button" class="toggle-option active product-toggle" onclick="toggleProductType(${productRowIndex}, 'existing')" data-row="${productRowIndex}" data-type="existing">
                        <i class="fas fa-box"></i> Existing Product
                    </button>
                    <button type="button" class="toggle-option product-toggle" onclick="toggleProductType(${productRowIndex}, 'new')" data-row="${productRowIndex}" data-type="new">
                        <i class="fas fa-plus-circle"></i> New Product
                    </button>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeProductRow(this)" style="float: right;">
                    <i class="fas fa-trash"></i> Remove
                </button>
                <div style="clear: both;"></div>
            </div>

            <!-- Hidden fields for product data -->
            <input type="hidden" class="product-id-input" name="products[${productRowIndex}][product_id]" value="">
            <input type="hidden" class="product-name-input" name="products[${productRowIndex}][product_name]" value="">

            <div class="row">
                <!-- Product Selection Column -->
                <div class="col">
                    <!-- Existing Product Search -->
                    <div class="product-search-container-${productRowIndex}">
                        <label>Search Product *</label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control product-search-input" 
                                   placeholder="Search by product name" 
                                   onkeyup="searchProducts(${productRowIndex}, this.value)" 
                                   autocomplete="off">
                        </div>
                        <select class="form-control product-select" 
                                onchange="selectProduct(${productRowIndex}, this.value)" 
                                size="5" 
                                style="margin-top: 5px; display: none;">
                        </select>
                    </div>

                    <!-- New Product Form -->
                    <div class="product-new-container-${productRowIndex}" style="display: none;">
                        <label>Product Name *</label>
                        <input type="text" class="form-control product-name-manual" 
                               placeholder="Enter product name" 
                               oninput="updateManualProduct(${productRowIndex})" 
                               autocomplete="off">
                    </div>
                </div>

                <!-- Quantity Column -->
                <div class="col">
                    <label>Quantity *</label>
                    <input type="number" step="0.01" class="form-control quantity-input" 
                           name="products[${productRowIndex}][quantity]" 
                           value="1" onchange="calculateRowTotal(this)" required>
                </div>

                <!-- Rate Column -->
                <div class="col">
                    <label>Rate *</label>
                    <input type="number" step="0.01" class="form-control rate-input" 
                           name="products[${productRowIndex}][rate]" 
                           placeholder="0.00" onchange="calculateRowTotal(this)" required>
                </div>

                <!-- Amount Column -->
                <div class="col">
                    <label>Amount</label>
                    <input type="number" step="0.01" class="form-control amount-input" 
                           name="products[${productRowIndex}][amount]" readonly>
                </div>
            </div>
        </div>
    `;
    $('#productsContainer').append(productRow);
    
    // Set initial state to existing product (rate readonly)
    $(`.product-row[data-index="${productRowIndex}"] .rate-input`).attr('readonly', 'readonly');
}    function removeProductRow(button) {
        $(button).closest('.product-row').remove();
        calculateTotal();
    }

    function updateProductInfoFromInput(input) {
        const row = $(input).closest('.product-row');
        const productName = $(input).val();

        if (productName && window.productsData) {
            const product = window.productsData.find(p => p.product_name === productName);
            if (product) {
                row.find('.product-id-input').val(product.product_id);
                row.find('.rate-input').val(product.price || product.selling_price || 0);
            } else {
                // Manual entry - generate temporary ID
                row.find('.product-id-input').val('MANUAL_' + Date.now());
            }
            calculateRowTotal(row.find('.quantity-input')[0]);
        }
    }

    function calculateRowTotal(input) {
        const row = $(input).closest('.product-row');
        const quantity = parseFloat(row.find('.quantity-input').val()) || 0;
        const rate = parseFloat(row.find('.rate-input').val()) || 0;
        const amount = quantity * rate;

        row.find('.amount-input').val(amount.toFixed(2));
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;

        $('.amount-input').each(function() {
            subtotal += parseFloat($(this).val()) || 0;
        });

        const discount = parseFloat($('#discount').val()) || 0;
        const total = subtotal - discount;

        $('#subtotalAmount').text('Rs. ' + subtotal.toFixed(2));
        $('#totalAmount').text('Rs. ' + total.toFixed(2));
    }

    function loadQuotationData(id) {
        fetch(`/AdminPanel/api/quotation.php?action=get&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const quotation = data.data;

                    // Fill form fields
                    $('#quotationId').val(quotation.id);
                    $('#quotationNumber').val(quotation.quotation_number);
                    $('#quotationDate').val(quotation.quotation_date);
                    $('#validUntil').val(quotation.valid_until);
                    $('#customerName').val(quotation.customer_name);
                    $('#customerMobile').val(quotation.customer_mobile);
                    $('#customerAddress').val(quotation.customer_address);
                    $('#note').val(quotation.note);
                    $('#discount').val(quotation.discount);

                    // Clear and add products
                    $('#productsContainer').empty();
                    productRowIndex = 0;

                    quotation.items.forEach(item => {
                        addProductRow();
                        const currentRow = $('.product-row').last();
                        currentRow.find('.product-name-input').val(item.product_name);
                        currentRow.find('.product-id-input').val(item.product_id);
                        currentRow.find('.quantity-input').val(item.quantity);
                        currentRow.find('.rate-input').val(item.rate);
                        currentRow.find('.amount-input').val(item.amount);
                    });

                    calculateTotal();
                }
            });
    }

    // Form submission
    $('#quotationForm').on('submit', function(e) {
        e.preventDefault();
        saveQuotation();
    });

function saveQuotation(callback) {
    // Validate form before submission
    if (!$('#quotationNumber').val()) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Quotation number is required'
        });
        return;
    }

    // Validate customer details
    if (!$('#customerName').val() || !$('#customerMobile').val()) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Customer name and mobile are required'
        });
        return;
    }

    // Validate products - ensure each visible product has required data
    let hasValidProduct = false;
    let validationError = '';

    $('.product-row').each(function() {
        const row = $(this);
        const rowIndex = row.data('index');
        const searchContainer = $(`.product-search-container-${rowIndex}`);
        const newContainer = $(`.product-new-container-${rowIndex}`);

        if (searchContainer.is(':visible')) {
            // Existing product mode
            const productName = row.find('.product-name-input').val();
            const rate = row.find(`.product-search-container-${rowIndex} .rate-input`).val();
            
            if (!productName || !rate || parseFloat(rate) <= 0) {
                validationError = 'Please select a product from the search';
                return false;
            }
            hasValidProduct = true;
        } else if (newContainer.is(':visible')) {
            // New product mode
            const productName = row.find('.product-name-manual').val();
            const rate = row.find(`.product-new-container-${rowIndex} .rate-input`).val();
            
            if (!productName || !rate || parseFloat(rate) <= 0) {
                validationError = 'Please enter product name and rate';
                return false;
            }
            hasValidProduct = true;
        }
    });

    if (!hasValidProduct) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please add at least one product'
        });
        return;
    }

    if (validationError) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: validationError
        });
        return;
    }

    // Build JSON data instead of FormData
    const action = $('#quotationId').val() ? 'update' : 'create';
    const quotationData = {
        action: action,
        id: $('#quotationId').val() || null,
        quotation_number: $('#quotationNumber').val(),
        quotation_date: $('#quotationDate').val(),
        valid_until: $('#validUntil').val(),
        note: $('#note').val(),
        discount: parseFloat($('#discount').val()) || 0
    };
    
    // Get customer data based on which mode is active
    if ($('#customerSearchContainer').is(':visible')) {
        // Existing customer mode - get from hidden/filled fields
        quotationData.customer_name = $('#customerName').val();
        quotationData.customer_mobile = $('#customerMobile').val();
        quotationData.customer_address = $('#customerAddress').val();
    } else {
        // New customer mode - get from new customer fields
        quotationData.customer_name = $('#customerName').val();
        quotationData.customer_mobile = $('#customerMobile').val();
        quotationData.customer_address = $('#customerAddress').val();
    }
    
    // Extract product data
    quotationData.products = [];
    $('.product-row').each(function() {
        const row = $(this);
        const rowIndex = row.data('index');
        const searchContainer = $(`.product-search-container-${rowIndex}`);
        const newContainer = $(`.product-new-container-${rowIndex}`);
        
        let productData = {
            quantity: parseFloat(row.find('.quantity-input').val()) || 1
        };
        
        if (searchContainer.is(':visible')) {
            // Existing product mode
            productData.product_id = row.find('.product-id-input').val();
            productData.product_name = row.find('.product-name-input').val();
            productData.rate = parseFloat(row.find('.rate-input').val()) || 0;
        } else if (newContainer.is(':visible')) {
            // New product mode
            productData.product_id = null;
            productData.product_name = row.find('.product-name-manual').val();
            productData.rate = parseFloat(row.find('.rate-input').val()) || 0;
        }
        
        if (productData.product_name && productData.rate > 0) {
            quotationData.products.push(productData);
        }
    });
    
    // Extract numeric values from display text
    const subtotalText = $('#subtotalAmount').text().replace('Rs. ', '').replace(',', '');
    const totalText = $('#totalAmount').text().replace('Rs. ', '').replace(',', '');
    
    quotationData.subtotal = parseFloat(subtotalText) || 0;
    quotationData.total = parseFloat(totalText) || 0;

    fetch('/AdminPanel/api/quotation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(quotationData)
    })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (callback) {
                            callback(data.quotation_id || $('#quotationId').val());
                        } else {
                            closeQuotationModal();
                            quotationTable.ajax.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while saving the quotation'
                });
            });
    }

    function saveAndPrint() {
        saveQuotation(function(quotationId) {
            window.open(`/AdminPanel/quotation/print.php?id=${quotationId}`, '_blank');
            closeQuotationModal();
            quotationTable.ajax.reload();
        });
    }

    function saveAndExport() {
        saveQuotation(function(quotationId) {
            window.open(`/AdminPanel/quotation/print.php?id=${quotationId}`, '_blank');
            closeQuotationModal();
            quotationTable.ajax.reload();
        });
    }

    function editQuotation(id) {
        openQuotationModal(id);
    }

    function deleteQuotation(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this quotation!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/AdminPanel/api/quotation.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'delete',
                            id: id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Quotation has been deleted.',
                                timer: 1500
                            }).then(() => {
                                quotationTable.ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: data.message
                            });
                        }
                    });
            }
        });
    }

    function printQuotation(id) {
        window.open(`/AdminPanel/quotation/print.php?id=${id}`, '_blank');
    }

    function viewQuotation(id) {
        printQuotation(id);
    }

    // Modal close handlers
    $('.close').click(function() {
        closeQuotationModal();
    });

    $(window).click(function(event) {
        if (event.target.id === 'quotationModal') {
            closeQuotationModal();
        }
    });
</script>