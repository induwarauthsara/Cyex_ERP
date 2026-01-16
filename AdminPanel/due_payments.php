<?php
include 'nav.php';
?>
<title>Due Payments Management </title>

<style>
    /* Enhanced styling for due payments with glass effect */
    .glass-card {
        background: rgba(32, 34, 49, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }
    
    .glass-card:hover {
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        transform: translateY(-5px);
    }

    .summary-card {
        background: linear-gradient(135deg, rgba(32, 34, 49, 0.95) 0%, rgba(52, 62, 89, 0.9) 100%);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 20px;
        margin: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        transition: all 0.3s ease;
        color: white;
    }
    
    .summary-card:hover {
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    }

    .category-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 20px 0;
    }

    .category-tab {
        background: rgba(32, 34, 49, 0.8);
        backdrop-filter: blur(8px);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        padding: 15px 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
        text-align: center;
        min-width: 150px;
    }
    
    .category-tab:hover {
        border-color: rgba(255, 255, 255, 0.4);
        background: rgba(32, 34, 49, 0.95);
        transform: translateY(-2px);
    }
    
    .category-tab.active {
        border-color: #00ff22;
        background: rgba(0, 255, 34, 0.1);
    }
    
    .category-tab.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: rgba(32, 34, 49, 0.5);
    }

    .payments-table-container {
        background: rgba(32, 34, 49, 0.95);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .btn-pay {
        background: linear-gradient(135deg, #00ff22 0%, #02c91c 100%);
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-pay:hover {
        background: linear-gradient(135deg, #02c91c 0%, #028917 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 255, 34, 0.3);
    }

    .payment-modal .modal-content {
        background: rgba(32, 34, 49, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        color: white;
    }
    
    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 8px;
    }
    
    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #00ff22;
        box-shadow: 0 0 0 0.2rem rgba(0, 255, 34, 0.25);
        color: white;
    }
    
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    #duePaymentsTable {
        background: transparent;
        color: white;
    }
    
    #duePaymentsTable th {
        background: rgba(32, 34, 49, 0.8);
        color: white;
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    #duePaymentsTable td {
        color: white;
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .dataTables_wrapper {
        color: white;
    }
    
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 5px;
    }

    .page-title {
        text-align: center;
        color: white;
        margin-bottom: 30px;
        font-size: 2.5rem;
        font-weight: 300;
    }

        .page-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .page-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.2rem;
        }

        .nav-link-home {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .nav-link-home:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-2px);
        }

        /* Summary Cards */
        .summary-section {
            margin-bottom: 30px;
        }

        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            text-align: center;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.2);
        }

        .summary-card h3 {
            margin: 0 0 15px 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            font-weight: 600;
        }

        .summary-amount {
            font-size: 2rem;
            font-weight: bold;
            color: #ffd700;
            margin: 10px 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .summary-count {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
        }

        /* Payment Categories Tabs */
        .categories-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .categories-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 15px;
        }

        .categories-header h2 {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .btn-refresh {
            background: rgba(40, 167, 69, 0.8);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-refresh:hover {
            background: rgba(40, 167, 69, 1);
            transform: translateY(-2px);
        }

        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 25px;
        }

        .category-tab {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 12px 24px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-tab:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .category-tab.active {
            background: rgba(0, 123, 255, 0.8);
            border-color: rgba(0, 123, 255, 1);
        }

        .category-tab.coming-soon {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .category-tab.coming-soon:hover {
            transform: none;
        }

        .coming-soon-badge {
            background: rgba(255, 167, 38, 0.8);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 5px;
        }

        /* Payments Table Section */
        .payments-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .payments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 15px;
        }

        .payments-header h2 {
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            overflow-x: auto;
        }

        .table {
            color: white;
            margin-bottom: 0;
        }

        .table th {
            background: rgba(0, 123, 255, 0.2);
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px 10px;
        }

        .table td {
            border: none;
            padding: 12px 10px;
            vertical-align: middle;
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-unpaid { 
            background: rgba(220, 53, 69, 0.8); 
            color: white; 
        }
        
        .status-partial { 
            background: rgba(255, 167, 38, 0.8); 
            color: white; 
        }
        
        .status-paid { 
            background: rgba(40, 167, 69, 0.8); 
            color: white; 
        }

        .btn-pay {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
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
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            margin: 3% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .modal-close {
            color: rgba(255, 255, 255, 0.8);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            right: 20px;
            top: 15px;
            transition: color 0.3s ease;
        }

        .modal-close:hover {
            color: white;
        }

        .modal h3 {
            margin: 0 0 25px 0;
            color: white;
            font-weight: 600;
            padding-right: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: white;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: rgba(0, 123, 255, 0.8);
            outline: none;
            background: rgba(255, 255, 255, 0.15);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .grn-details {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
        }

        .btn-secondary {
            background: rgba(108, 117, 125, 0.8);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-right: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(108, 117, 125, 1);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.8);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.6;
        }

        .empty-state h3 {
            margin-bottom: 10px;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 15px;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .summary-cards {
                grid-template-columns: 1fr;
            }

            .category-tabs {
                flex-direction: column;
            }

            .categories-header,
            .payments-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
                padding: 20px;
            }

            .nav-link-home {
                position: relative;
                top: auto;
                left: auto;
                display: inline-block;
                margin-bottom: 20px;
            }
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 5px;
            padding: 5px 10px;
        }

        .dataTables_wrapper .dataTables_length select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            border-radius: 5px;
            padding: 5px;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: rgba(255, 255, 255, 0.8);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: white !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: rgba(255, 255, 255, 0.2) !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
        }
    </style>
</head>
<body>
    <!-- Navigation Link -->
    <a href="../index.php" class="nav-link-home">
        <i class="fas fa-arrow-left"></i> Back to Admin Panel
    </a>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-money-bill-wave"></i> Due Payments Management</h1>
            <p>Comprehensive payment tracking and processing system</p>
        </div>

        <!-- Summary Section -->
        <div class="summary-section">
            <h2><i class="fas fa-chart-bar"></i> Payment Overview</h2>
            <div class="summary-cards" id="summaryCards">
                <!-- Dynamic summary cards will be loaded here -->
                <div class="summary-card">
                    <h3><i class="fas fa-spinner fa-spin"></i> Loading...</h3>
                    <div class="summary-amount">--</div>
                    <div class="summary-count">Please wait</div>
                </div>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="categories-section">
            <div class="categories-header">
                <h2><i class="fas fa-th-large"></i> Payment Categories</h2>
                <button class="btn-refresh" onclick="loadPaymentData()">
                    <i class="fas fa-sync-alt"></i> Refresh Data
                </button>
            </div>
            <div class="category-tabs" id="categoryTabs">
                <!-- Dynamic category tabs will be loaded here -->
            </div>
        </div>

        <!-- Payments List Section -->
        <div class="payments-section" id="paymentsSection">
            <div class="payments-header">
                <h2 id="paymentsTitle"><i class="fas fa-hand-pointer"></i> Select a Payment Category</h2>
            </div>
            <div id="paymentsContent">
                <div class="empty-state">
                    <i class="fas fa-mouse-pointer"></i>
                    <h3>Choose a Category</h3>
                    <p>Select a payment category above to view and manage due payments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h3><i class="fas fa-credit-card"></i> Record Payment</h3>
            <form id="paymentForm">
                <input type="hidden" id="grn_id" name="grn_id">
                
                <div class="form-group">
                    <label>Payment Details:</label>
                    <div id="grn_details" class="grn-details"></div>
                </div>

                <div class="form-group">
                    <label for="payment_amount">Payment Amount (LKR):</label>
                    <input type="number" id="payment_amount" name="payment_amount" class="form-control" step="0.01" required placeholder="0.00">
                    <small style="color: rgba(255,255,255,0.8); margin-top: 5px; display: block;">
                        Outstanding: LKR <span id="outstanding_amount" style="font-weight: bold; color: #ffd700;"></span>
                    </small>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="reference_no">Reference Number:</label>
                    <input type="text" id="reference_no" name="reference_no" class="form-control" placeholder="Cheque number, transfer ID, etc.">
                </div>

                <div class="form-group">
                    <label for="payment_note">Payment Note:</label>
                    <textarea id="payment_note" name="payment_note" class="form-control" rows="3" placeholder="Additional notes about this payment"></textarea>
                </div>

                <div class="form-group" style="text-align: right; margin-top: 30px;">
                    <button type="button" class="btn-secondary" onclick="closePaymentModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentCategory = null;
        let paymentsData = {};
        let paymentsTable = null;

        // Load all payment data on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPaymentData();
        });

        // Function to load all payment data
        function loadPaymentData() {
            fetch('api/fetch_grn_payments.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        paymentsData = data;
                        renderSummaryCards(data.summary);
                        renderCategoryTabs(data.categories_summary);
                    } else {
                        Swal.fire('Error', 'Failed to load payment data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error loading payment data:', error);
                    Swal.fire('Error', 'Failed to load payment data', 'error');
                });
        }

        // Function to render summary cards
        function renderSummaryCards(summary) {
            const container = document.getElementById('summaryCards');
            
            const totalOutstanding = parseFloat(summary.total_outstanding_amount) || 0;
            const totalGRN = parseFloat(summary.total_grn_amount) || 0;
            const totalPaid = parseFloat(summary.total_paid_amount) || 0;
            const progressPercent = totalGRN > 0 ? ((totalPaid / totalGRN) * 100).toFixed(1) : 0;
            
            container.innerHTML = `
                <div class="summary-card">
                    <h3><i class="fas fa-exclamation-triangle"></i> Total Outstanding</h3>
                    <div class="summary-amount">LKR ${totalOutstanding.toLocaleString()}</div>
                    <div class="summary-count">${summary.total_outstanding_grns || 0} pending payments</div>
                </div>
                <div class="summary-card">
                    <h3><i class="fas fa-file-invoice-dollar"></i> Total GRN Value</h3>
                    <div class="summary-amount">LKR ${totalGRN.toLocaleString()}</div>
                    <div class="summary-count">Total invoice amount</div>
                </div>
                <div class="summary-card">
                    <h3><i class="fas fa-check-circle"></i> Total Paid</h3>
                    <div class="summary-amount">LKR ${totalPaid.toLocaleString()}</div>
                    <div class="summary-count">Payments received</div>
                </div>
                <div class="summary-card">
                    <h3><i class="fas fa-chart-line"></i> Payment Progress</h3>
                    <div class="summary-amount">${progressPercent}%</div>
                    <div class="summary-count">of total amount paid</div>
                </div>
            `;
        }

        // Function to render category tabs
        function renderCategoryTabs(categories) {
            const container = document.getElementById('categoryTabs');
            container.innerHTML = '';
            
            const categoryConfig = {
                grn: { icon: 'fas fa-receipt', name: 'GRN Payments' },
                utilities: { icon: 'fas fa-bolt', name: 'Utilities' },
                rent: { icon: 'fas fa-home', name: 'Rent' },
                salary: { icon: 'fas fa-users', name: 'Salary' },
                printer_rent: { icon: 'fas fa-print', name: 'Printer Rent' },
                loans: { icon: 'fas fa-hand-holding-usd', name: 'Loans' },
                subscriptions: { icon: 'fas fa-calendar-check', name: 'Subscriptions' }
            };
            
            Object.keys(categories).forEach(key => {
                const category = categories[key];
                const config = categoryConfig[key] || { icon: 'fas fa-file', name: key };
                const isActive = category.status === 'active';
                const isComingSoon = category.status === 'coming_soon';
                
                const tab = document.createElement('div');
                tab.className = `category-tab ${isComingSoon ? 'coming-soon' : ''}`;
                tab.onclick = isActive ? () => selectCategory(key, config.name) : null;
                
                tab.innerHTML = `
                    <i class="${config.icon}"></i>
                    <span>${config.name}</span>
                    <small>(${category.count})</small>
                    ${isComingSoon ? '<span class="coming-soon-badge">Soon</span>' : ''}
                `;
                
                container.appendChild(tab);
            });
        }

        // Function to select a category
        function selectCategory(categoryKey, categoryName) {
            // Remove active class from all tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Add active class to selected tab
            event.currentTarget.classList.add('active');
            
            currentCategory = categoryKey;
            
            // Update payments section title
            document.getElementById('paymentsTitle').innerHTML = `<i class="fas fa-list"></i> ${categoryName} - Due Payments`;
            
            // Load category-specific payments
            if (categoryKey === 'grn') {
                renderGRNPayments();
            } else {
                renderComingSoon(categoryName);
            }
        }

        // Function to render GRN payments
        function renderGRNPayments() {
            const container = document.getElementById('paymentsContent');
            
            if (!paymentsData.payments || paymentsData.payments.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-check-circle" style="color: #28a745;"></i>
                        <h3>No Outstanding GRN Payments</h3>
                        <p>All GRN payments are up to date!</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="table-container">
                    <table id="paymentsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>GRN Details</th>
                                <th>Supplier</th>
                                <th>Invoice No.</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${paymentsData.payments.map(payment => `
                                <tr>
                                    <td>
                                        <strong>${payment.grn_number}</strong><br>
                                        <small style="color: rgba(255,255,255,0.8);">${payment.receipt_date}</small>
                                    </td>
                                    <td>${payment.supplier_name}</td>
                                    <td>${payment.invoice_number || 'N/A'}</td>
                                    <td><strong>LKR ${parseFloat(payment.total_amount).toLocaleString()}</strong></td>
                                    <td>LKR ${parseFloat(payment.paid_amount).toLocaleString()}</td>
                                    <td><strong style="color: #ffd700;">LKR ${parseFloat(payment.outstanding_amount).toLocaleString()}</strong></td>
                                    <td><span class="status-badge status-${payment.payment_status}">${payment.payment_status}</span></td>
                                    <td>
                                        <button class="btn-pay" onclick="openPaymentModal(${payment.grn_id}, '${payment.grn_number}', '${payment.supplier_name}', ${payment.outstanding_amount})">
                                            <i class="fas fa-credit-card"></i> Pay
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
            
            // Initialize DataTable
            if (paymentsTable) {
                paymentsTable.destroy();
            }
            
            paymentsTable = $('#paymentsTable').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[5, 'desc']], // Sort by outstanding amount
                columnDefs: [
                    { targets: [7], orderable: false } // Action column not sortable
                ]
            });
        }

        // Function to render coming soon message
        function renderComingSoon(categoryName) {
            const container = document.getElementById('paymentsContent');
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-clock" style="color: #ffa726;"></i>
                    <h3>${categoryName} - Coming Soon</h3>
                    <p>This payment category will be available in future updates</p>
                </div>
            `;
        }

        // Function to open payment modal
        function openPaymentModal(grnId, grnNumber, supplierName, outstandingAmount) {
            document.getElementById('grn_id').value = grnId;
            document.getElementById('grn_details').innerHTML = `
                <strong>GRN Number:</strong> ${grnNumber}<br>
                <strong>Supplier:</strong> ${supplierName}<br>
                <strong>Outstanding Amount:</strong> LKR ${parseFloat(outstandingAmount).toLocaleString()}
            `;
            document.getElementById('outstanding_amount').textContent = parseFloat(outstandingAmount).toLocaleString();
            document.getElementById('payment_amount').setAttribute('max', outstandingAmount);
            document.getElementById('paymentModal').style.display = 'block';
        }

        // Function to close payment modal
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentForm').reset();
        }

        // Close modal when clicking outside or on X
        window.onclick = function(event) {
            const modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                closePaymentModal();
            }
        }

        document.querySelector('.modal-close').onclick = function() {
            closePaymentModal();
        }

        // Handle payment form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            Swal.fire({
                title: 'Processing Payment...',
                text: 'Please wait while we process your payment',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('api/process_payment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Payment has been recorded successfully',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        closePaymentModal();
                        loadPaymentData(); // Reload all data
                        // Refresh the current category if it's GRN
                        if (currentCategory === 'grn') {
                            setTimeout(() => {
                                document.querySelector('.category-tab.active')?.click();
                            }, 500);
                        }
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to process payment', 'error');
                }
            })
            .catch(error => {
                console.error('Error processing payment:', error);
                Swal.fire('Error', 'Failed to process payment', 'error');
            });
        });
    </script>
</body>
</html>