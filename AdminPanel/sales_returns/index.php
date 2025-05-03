<?php
include '../nav.php';
?>
<title>Sales Returns - Admin Panel</title>
<link rel="stylesheet" href="sales_returns.css">

<div class="container">
    <h1><i class="fas fa-undo-alt"></i> Sales Returns</h1>
    
    <div class="search-section">
        <h2>Search Invoice</h2>
        <div class="search-box">
            <input type="text" id="invoice-search" placeholder="Enter Invoice Number" autofocus>
            <button id="search-btn" class="btn primary"><i class="fas fa-search"></i> Search</button>
        </div>
        <div id="invoice-result"></div>
    </div>

    <div class="return-section" style="display: none;">
        <h2>Return Items</h2>
        <div class="invoice-details">
            <div class="invoice-info">
                <p><strong>Invoice #:</strong> <span id="invoice-number"></span></p>
                <p><strong>Date:</strong> <span id="invoice-date"></span></p>
                <p><strong>Customer:</strong> <span id="customer-name"></span></p>
            </div>
            <div class="invoice-totals">
                <p><strong>Total Amount:</strong> <span id="invoice-total"></span></p>
                <p><strong>Paid Amount:</strong> <span id="invoice-paid"></span></p>
                <p><strong>Balance:</strong> <span id="invoice-balance"></span></p>
            </div>
        </div>

        <div class="items-table-container">
            <table id="items-table" class="display compact">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Item</th>
                        <th>Batch</th>
                        <th>Price</th>
                        <th>Original Qty</th>
                        <th>Previous Returned</th>
                        <th>Return Qty</th>
                        <th>Return Total</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    <!-- Items will be populated here -->
                </tbody>
            </table>
        </div>

        <div class="return-options">
            <div class="return-config">
                <div class="form-group">
                    <label for="return-reason">Return Reason:</label>
                    <select id="return-reason" required>
                        <option value="">-- Select Reason --</option>
                        <option value="Damaged Product">Damaged Product</option>
                        <option value="Wrong Item">Wrong Item</option>
                        <option value="Change of Mind">Change of Mind</option>
                        <option value="Quality Issue">Quality Issue</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group" id="other-reason-group" style="display: none;">
                    <label for="other-reason">Specify Reason:</label>
                    <input type="text" id="other-reason" placeholder="Enter reason">
                </div>
                <div class="form-group">
                    <label for="refund-method">Refund Method:</label>
                    <select id="refund-method" required>
                        <option value="Store Credit">Store Credit</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="add-to-stock">Add items back to stock?</label>
                    <select id="add-to-stock" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="return-note">Additional Notes:</label>
                    <textarea id="return-note" rows="3" placeholder="Enter any additional notes"></textarea>
                </div>
            </div>

            <div class="return-summary">
                <h3>Return Summary</h3>
                <div class="summary-item">
                    <span>Total Items to Return:</span>
                    <span id="return-items-count">0</span>
                </div>
                <div class="summary-item">
                    <span>Total Return Amount:</span>
                    <span id="return-total-amount">0.00</span>
                </div>
                <div class="summary-actions">
                    <button id="cancel-return" class="btn secondary"><i class="fas fa-times"></i> Cancel</button>
                    <button id="process-return" class="btn primary"><i class="fas fa-check"></i> Process Return</button>
                </div>
            </div>
        </div>
    </div>

    <div class="recent-returns">
        <h2>Recent Returns</h2>
        <table id="recent-returns-table" class="display compact">
            <thead>
                <tr>
                    <th>Return ID</th>
                    <th>Date</th>
                    <th>Invoice #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Reason</th>
                    <th>Processed By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Recent returns will be populated here -->
                <?php
                $returns = fetch_data("SELECT sr.*, i.invoice_number, 
                    COALESCE(c.customer_name, 'Walk-in Customer') as customer_name, 
                    COALESCE(e.emp_name, 'System') as employee_name 
                    FROM sales_returns sr 
                    LEFT JOIN invoice i ON sr.invoice_id = i.invoice_number
                    LEFT JOIN customers c ON sr.customer_id = c.id
                    LEFT JOIN employees e ON sr.user_id = e.employ_id
                    ORDER BY sr.return_date DESC LIMIT 20");
                
                foreach ($returns as $return) {
                    echo "<tr>
                        <td>{$return['return_id']}</td>
                        <td>{$return['return_date']}</td>
                        <td>{$return['invoice_number']}</td>
                        <td>{$return['customer_name']}</td>
                        <td>" . number_format($return['return_amount'], 2) . "</td>
                        <td>{$return['return_reason']}</td>
                        <td>{$return['employee_name']}</td>
                        <td>
                            <button class='view-return btn-sm' data-id='{$return['return_id']}'><i class='fas fa-eye'></i></button>
                            <button class='print-return btn-sm' data-id='{$return['return_id']}'><i class='fas fa-print'></i></button>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Return Details Modal -->
<div id="return-details-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Return Details</h2>
        <div id="return-details-content"></div>
    </div>
</div>

<script src="sales_returns.js"></script>