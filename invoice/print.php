<?php
require_once '../inc/config.php';
$id = $_GET['id'];
$printType = $_GET['printType'] ?? null; // Don't set default, check if provided

if (isset($_GET['invoice'])) {
    $invoice = $_GET['invoice'];
    if (!isset($id)) {
        $id = $invoice;
    }
}

// If printType is not provided, show selection modal
$showPrintTypeSelector = ($printType === null);
if ($showPrintTypeSelector) {
    $printType = 'receipt'; // Default for initial display
}
?>
<style>
    /* Print Type Selection Modal */
    .print-type-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        max-width: 400px;
        width: 90%;
    }

    .modal-content h2 {
        margin-bottom: 20px;
        color: #333;
        font-size: 24px;
    }

    .modal-content p {
        margin-bottom: 25px;
        color: #666;
        font-size: 16px;
    }

    .print-type-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .print-type-btn {
        padding: 15px 25px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s;
        min-width: 140px;
    }

    .receipt-btn {
        background-color: #4CAF50;
        color: white;
    }

    .receipt-btn:hover {
        background-color: #45a049;
    }

    .standard-btn {
        background-color: #2196F3;
        color: white;
    }

    .standard-btn:hover {
        background-color: #1976D2;
    }

    @media print {
        .print-type-modal {
            display: none !important;
        }

        .print-toggle {
            display: none !important;
        }
    }

    .bill {
        width: 80mm;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 12pt;
        margin: 0 auto;
        height: auto;
        color: #000;
    }

    /* Standard Invoice Styles */
    .bill.standard {
        width: 210mm;
        /* A4 width */
        max-width: 210mm;
        padding: 15mm;
        font-size: 14pt;
        background: white;
        border-radius: 8px;
        margin: 20px auto;
    }

    /* Hide HR line for standard invoice */
    .bill.standard hr {
        display: none;
    }

    /* Modern Header - Clean Design with Company Details Highlighting */
    .bill.standard .header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #333;
        padding: 25px 20px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    /* Decorative HR Elements */
    .bill.standard .header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #1d4ed8, #059669, #047857);
        border-radius: 12px 12px 0 0;
    }

    .bill.standard .header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 20px;
        right: 20px;
        height: 1px;
        background: linear-gradient(90deg, transparent, #cbd5e1, transparent);
    }

    .bill.standard .logo-img {
        flex: 0 0 auto;
        margin-bottom: 0;
        text-align: left;
    }

    .bill.standard .logo-img img {
        height: 100px;
        border-radius: 8px;
        padding: 8px;
    }

    .bill.standard .topic {
        flex: 1;
        text-align: center;
        margin-left: 20px;
    }

    .bill.standard .topic h1 {
        font-size: 36pt;
        margin: 0 0 10px 0;
        color: #333;
        font-weight: bold;
    }

    .bill.standard .topic h2 {
        font-size: 16pt;
        margin: 5px 0;
        color: #666;
        line-height: 1.4;
        font-weight: 400;
    }

    /* Modern Invoice Details Section - Compact */
    .bill.standard .details {
        margin-bottom: 20px;
        background: transparent;
        padding: 15px;
        border-radius: 8px;
        border: none;
    }

    .bill.standard .invoice-header-info {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0;
    }

    .bill.standard .invoice-meta {
        text-align: right;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .bill.standard .bill-no {
        font-size: 20pt;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
    }

    .bill.standard .Innerdetails {
        font-size: 12pt;
        margin: 0;
        color: #2c3e50;
    }

    .bill.standard .customer-details {
        background: white;
        padding: 10px;
        border-radius: 6px;
        margin-top: 10px;
        border: 1px solid #dee2e6;
    }

    .bill.standard .customer-name,
    .bill.standard .customer-tele {
        font-size: 12pt;
        margin: 3px 0;
    }

    /* Modern Table Design - Compact & Full Width */
    .bill.standard .main-content {
        display: block;
        margin-bottom: 20px;
    }

    .bill.standard .product-section {
        width: 100%;
        margin-bottom: 15px;
    }

    .bill.standard .summary-section {
        width: 100%;
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-end;
    }

    .bill.standard .summary-container {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        width: 100%;
        max-width: 100%;
    }

    .bill.standard .additional-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        width: 40%;
        flex-shrink: 0;
        box-sizing: border-box;
    }

    .bill.standard .invoice-summary {
        width: 55%;
        flex-shrink: 0;
        margin-left: auto;
        box-sizing: border-box;
    }

    /* When additional info is hidden, invoice summary takes full width on right */
    .bill.standard .summary-container:not(.has-additional-info) .invoice-summary {
        width: 400px;
        max-width: 400px;
    }

    .bill.standard .additional-info h4 {
        margin: 0 0 10px 0;
        color: #2c3e50;
        font-size: 16pt;
        padding-bottom: 5px;
    }

    .bill.standard .additional-info textarea {
        width: 100%;
        min-height: 80px;
        border: none;
        background: transparent;
        resize: vertical;
        font-family: inherit;
        font-size: 12pt;
        color: #2c3e50;
        padding: 10px 0;
    }

    .bill.standard .product-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
    }

    .bill.standard .product-table th {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        font-weight: bold;
        padding: 15px 12px;
        text-align: center;
        font-size: 14pt;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: none;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .bill.standard .product-table td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        font-size: 13pt;
    }

    .bill.standard .product-table .product-row:nth-child(even) {
        background-color: #f8f9fa;
    }

    .bill.standard .product-table .product-row:hover {
        background-color: #e3f2fd;
    }

    .bill.standard .summary-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        margin-top: 10px;
    }

    .bill.standard .summary-table td {
        padding: 8px 15px;
        border-bottom: 1px solid #e9ecef;
        font-size: 12pt;
    }

    .bill.standard .summary-table .summary-label {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
        width: 70%;
    }

    .bill.standard .summary-table .summary-value {
        text-align: right;
        font-weight: 500;
        color: #2c3e50;
    }

    .bill.standard .summary-table .final-total {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
        font-weight: bold;
        font-size: 16pt;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    .bill.standard .summary-table .final-total td {
        border: none;
        padding: 10px;
    }

    /* Payment Information - Simple Design */
    .bill.standard .payment-info {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 15px 0;
        padding: 15px;
        background: #f8f9fa;
        color: #333;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }

    .bill.standard .payment-info .payment-method {
        font-size: 13pt;
        font-weight: bold;
    }

    /* Remove duplicate date - hide bill-date in payment-info for standard invoice */
    .bill.standard .payment-info .bill-date {
        display: none;
    }

    /* Footer Styling - Clean Highlight */
    .bill.standard .footer {
        text-align: center;
        margin-top: 20px;
        padding: 15px;
        background: transparent;
        color: #666;
        border-top: 2px solid #3b82f6;
        border-radius: 0;
        font-size: 11pt;
        font-style: italic;
    }

    .bill.standard .bill-footer {
        margin-top: 15px;
    }

    /* Thank you section - Compact */
    .bill.standard .thank-you {
        margin: 15px 0 10px 0;
        font-weight: bold;
        font-size: 13pt;
        text-align: center;
    }

    /* Receipt Format - 2-line product layout */
    .bill:not(.standard) .product-row td:first-child {
        display: block;
        width: 100%;
        border-bottom: none;
        padding-bottom: 0;
    }

    .bill:not(.standard) .product-row td:not(:first-child) {
        border-top: none;
        padding-top: 0;
    }

    /* Hide default payment-info for receipt, show simple payment-method */
    .bill:not(.standard) .payment-info {
        display: none;
    }

    .bill:not(.standard) .payment-method {
        display: block;
        margin-top: 10px;
        font-size: 12pt;
        text-align: center;
    }

    /* Hide payment-method for standard, show payment-info */
    .bill.standard .payment-method {
        display: none;
    }

    /* Print Toggle Button Styles */
    .print-toggle {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #2196F3;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        transition: background-color 0.3s;
    }

    .print-toggle:hover {
        background-color: #1976D2;
    }

    /* Action Buttons Styles */
    .action-buttons {
        position: fixed;
        top: 20px;
        left: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 1000;
    }

    .action-btn {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s;
        min-width: 150px;
        text-align: center;
    }

    .pdf-btn {
        background-color: #FF5722;
    }

    .pdf-btn:hover {
        background-color: #E64A19;
    }

    .print-btn {
        background-color: #2196F3;
    }

    .print-btn:hover {
        background-color: #1976D2;
    }

    .close-btn {
        background-color: #f44336;
    }

    .close-btn:hover {
        background-color: #d32f2f;
    }

    /* Receipt Format Styles (Original) */
    .bill:not(.standard) .header,
    .bill:not(.standard) .details,
    .bill:not(.standard) .content {
        text-align: center;
        margin-bottom: 10px;
    }

    .bill:not(.standard) .logo-img {
        margin-bottom: 10px;
        text-align: center;
    }

    .bill:not(.standard) .logo-img img {
        height: 130px;
    }

    .bill:not(.standard) .topic h1 {
        margin: 5px 0;
        font-size: 18pt;
    }

    .bill:not(.standard) .topic h2 {
        margin: 8px 0;
        font-size: 12pt;
    }

    .bill:not(.standard) hr {
        border: 1px dashed black;
        margin: 10px 0;
    }

    .bill:not(.standard) table {
        width: 100%;
        border-collapse: collapse;
    }

    .bill:not(.standard) th,
    .bill:not(.standard) tr.product-row {
        border-top: 1px dashed black;
        padding: 8px 5px;
        text-align: left;
        color: #000;
    }

    .bill:not(.standard) th {
        font-weight: bold;
        text-align: left;
        font-size: 13pt;
    }

    .bill:not(.standard) td {
        font-size: 12pt;
    }

    .bill:not(.standard) .price,
    .bill:not(.standard) .bill_sum {
        text-align: right;
    }

    .promotion {
        text-decoration: line-through;
        font-size: 0.9em;
    }

    .discount-price {
        font-weight: bold;
    }

    .bill:not(.standard) .total-summary {
        margin-top: 10px;
        border-top: 2px solid black;
        border-bottom: none;
    }

    .bill:not(.standard) .total-summary td {
        font-size: 13pt;
        border-bottom: none;
    }

    .bill:not(.standard) .final-total td {
        font-size: 15pt;
        font-weight: bold;
        border-top: 1px solid black;
        padding-top: 10px;
    }

    .bill:not(.standard) .bill_sum {
        font-weight: bold;
    }

    .savings-highlight {
        margin: 15px 0;
        padding: 10px;
        border: 2px dashed #000;
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
    }

    .bill:not(.standard) .footer {
        text-align: center;
        margin-top: 25px;
        font-size: 12pt;
        line-height: 1.5;
    }

    .bill:not(.standard) .Innerdetails {
        display: flex;
        justify-content: space-between;
        margin: 8px 0;
        font-size: 12pt;
    }

    .bill:not(.standard) .bill-no {
        font-size: 14pt;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .bill:not(.standard) .thank-you {
        margin-top: 10px;
        font-weight: bold;
        font-size: 13pt;
        text-align: center;
    }

    .bill:not(.standard) .payment-method {
        margin-top: 10px;
        font-size: 12pt;
        text-align: center;
    }

    @media print {
        .print-toggle {
            display: none;
        }

        .action-buttons {
            display: none !important;
        }
    }

    @media print {
        .bill {
            width: 80mm;
            height: auto;
            page-break-after: avoid;
        }

        .bill.standard {
            width: 190mm;
            /* A4 width minus margins */
            max-width: 190mm;
            padding: 10mm;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: auto;
        }

        /* Prevent page breaks inside the table */
        table {
            page-break-inside: avoid;
        }
    }
</style>

<!-- Print Type Selection Modal -->
<?php if ($showPrintTypeSelector): ?>
    <div class="print-type-modal" id="printTypeModal">
        <div class="modal-content">
            <h2>Select Print Type</h2>
            <p>Please choose the type of invoice you want to print:</p>
            <div class="print-type-buttons">
                <button class="print-type-btn receipt-btn" onclick="selectPrintType('receipt')">
                    📄 Receipt Print
                </button>
                <button class="print-type-btn standard-btn" onclick="selectPrintType('standard')">
                    📋 Standard Invoice
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Print Type Toggle Button -->
<button class="print-toggle" onclick="togglePrintType()" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    Switch to <span id="toggle-text"><?php echo $printType === 'receipt' ? 'Standard Invoice' : 'Receipt Print'; ?></span>
</button>

<!-- Action Buttons -->
<div class="action-buttons" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    <button class="action-btn pdf-btn" onclick="exportToPDF()">
        📄 Export as PDF
    </button>
    <button class="action-btn print-btn" onclick="window.print()">
        🖨️ Print
    </button>
    <button class="action-btn" onclick="toggleAdditionalInfo()" style="background-color: #17a2b8;">
        📝 Add Notes
    </button>
    <button class="action-btn close-btn" onclick="closePage()">
        ❌ Close
    </button>
</div>

<div class="bill <?php echo $printType; ?>" id="bill-container" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    <div class="header">
        <div class="logo-img"> <a href="../index.php"> <img src="../logo.png" alt="LOGO"> </a></div>
        <div class="topic">
            <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
            <h2><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h2>
        </div>
    </div>
    <hr />
    <?php
    $invoice_sql = "SELECT i.*, pd.payment_method FROM invoice i 
                    LEFT JOIN payment_details pd ON i.invoice_number = pd.invoice_id 
                    WHERE i.invoice_number = '$id' LIMIT 1;";
    if ($result = mysqli_query($con, $invoice_sql)) {
        if (mysqli_num_rows($result) == 1) {
            $invoice = mysqli_fetch_array($result);
            $customer = $invoice['customer_name'];
            $tele = $invoice['customer_mobile'];
            $bill_no = $invoice['invoice_number'];
            $date = $invoice['invoice_date'];
            $biller_id = $invoice['biller'];
            $total = $invoice['total'];
            $discount = $invoice['discount'];
            $amount_received = $invoice['amount_received'];
            $cash_change = $invoice['cash_change'];
            $advance = $invoice['advance'];
            $balance = $invoice['balance'];
            $full_paid = $invoice['full_paid'];
            $payment_method = $invoice['payment_method'] ?? 'Cash';

            // Get cashier name from the database
            $cashier_name = "";
            $cashier_query = "SELECT emp_name FROM employees WHERE employ_id = '$biller_id' LIMIT 1";
            $cashier_result = mysqli_query($con, $cashier_query);
            if ($cashier_result && mysqli_num_rows($cashier_result) > 0) {
                $cashier_row = mysqli_fetch_assoc($cashier_result);
                $cashier_name = $cashier_row['emp_name'];
            } else {
                $cashier_name = $biller_id; // Fallback to ID if name not found
            }

            // Get individual_discount_mode for this invoice from the sales table
            $mode_query = "SELECT individual_discount_mode FROM sales WHERE invoice_number = '$id' LIMIT 1";
            $mode_result = mysqli_query($con, $mode_query);
            $individual_discount_mode = 0; // Default to 0 (not active)
            if ($mode_result && mysqli_num_rows($mode_result) > 0) {
                $mode_row = mysqli_fetch_assoc($mode_result);
                $individual_discount_mode = (int)$mode_row['individual_discount_mode'];
            }
        } else {
            echo "<h1> Don't Print !!!. Invalid Invoice Number</h1><!--";
        }
    } else {
        echo "<h1> Don't Print !!!. Failed to Load Invoice Data</h1><!--";
    }
    ?> <div class="details">
        <div class="invoice-header-info">
            <div class="customer-info">
                <div class="customer-details">
                    <div class="customer-name">
                        <strong>Customer:</strong> <?php echo $customer; ?>
                    </div>
                    <div class="customer-tele">
                        <strong>Phone:</strong> <?php echo $tele; ?>
                    </div>
                </div>
            </div>
            <div class="invoice-meta">
                <div class="bill-no">Invoice No: <?php echo $bill_no; ?></div>
                <div class="Innerdetails">
                    <strong>Date:</strong> <?php echo $date; ?>
                </div>
                <div class="Innerdetails">
                    <strong>Cashier:</strong> <?php echo $cashier_name; ?>
                </div>
            </div>
        </div>
    </div><!-- Main Content Layout -->
    <div class="main-content">
        <!-- Product Section -->
        <div class="product-section">
            <!-- Product Table -->
            <table class="product-table">
                <thead>
                    <tr>
                        <th style="width: 50%">Product Description</th>
                        <th style="width: 15%">Qty</th>
                        <th style="width: 15%">Unit Price</th>
                        <th style="width: 20%">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_savings = 0;
                    $sql = "SELECT product, qty, rate, discount_price, amount FROM sales WHERE invoice_number = $id";
                    $result = mysqli_query($con, $sql);
                    if ($result) {
                        if (mysqli_num_rows($result) > 0) {
                            while ($sales = mysqli_fetch_array($result)) {
                                $regular_price = $sales["rate"];
                                $discount_price = $sales["discount_price"];
                                $qty = $sales["qty"];

                                // Only show discount price if individual discount mode was active
                                // or if discount price is less than regular price (for cases like promotional items)
                                $is_promotional = $individual_discount_mode && ($discount_price < $regular_price && $discount_price > 0);

                                // Calculate savings for this item, but only if individual discount mode was active
                                if ($is_promotional) {
                                    $item_savings = ($regular_price - $discount_price) * $qty;
                                    $total_savings += $item_savings;
                                }

                                // Different table layouts for standard vs receipt
                                if ($printType === 'standard') {
                                    // Standard format: modern single-line layout
                                    echo '<tr class="product-row">
                                            <td style="text-align: left; padding-left: 15px;">' . $sales["product"] . '</td>
                                            <td style="text-align: center;">' . $sales["qty"] . '</td>
                                            <td style="text-align: right; padding-right: 15px;">';

                                    if ($is_promotional) {
                                        echo '<div class="promotion"> ' . number_format($regular_price, 2) . '</div>' .
                                            '<span class="discount-price"> ' . number_format($discount_price, 2) . '</span>';
                                    } else {
                                        echo ' ' . number_format($regular_price, 2);
                                    }

                                    echo '</td>
                                            <td style="text-align: right; padding-right: 15px; font-weight: 600;"> ' . number_format($sales["amount"], 2) . '</td>
                                        </tr>';
                                } else {
                                    // Receipt format: product name on first line, qty/price/amount on second line
                                    echo '<tr class="product-row">
                                            <td colspan="4">' . $sales["product"] . '</td></tr>
                                            <tr><td colspan="2" style="text-align: right;">' . $sales["qty"] . '</td>
                                            <td style="text-align: right;">';

                                    if ($is_promotional) {
                                        echo '<div class="promotion">' . number_format($regular_price, 2) . '</div>' .
                                            '<span class="discount-price">' . number_format($discount_price, 2) . '</span>';
                                    } else {
                                        echo number_format($regular_price, 2);
                                    }

                                    echo '</td>
                                            <td style="text-align: right;">' . number_format($sales["amount"], 2) . '</td>
                                        </tr>';
                                }
                            }
                        }
                    }

                    // Add promotional savings to the discount value for total savings calculation
                    $total_savings += $discount;

                    // Calculate change if payment was made
                    $change = 0;
                    if ($advance > 0 && $advance > ($total - $discount) && $balance <= 0) {
                        $change = $advance - ($total - $discount);
                    }
                    ?>
                </tbody>
            </table>
        </div> <!-- Summary Section with Side-by-Side Layout -->
        <div class="summary-section">
            <div class="summary-container">
                <!-- Additional Information Section (Left Side) -->
                <div class="additional-info" style="display: none;" id="additional-info-section">
                    <h4>Additional Information</h4>
                    <textarea id="additional-text" placeholder="Add any additional notes or information here..." readonly></textarea>
                </div>

                <!-- Invoice Summary (Right Side) -->
                <div class="invoice-summary">
                    <table class="summary-table">
                        <tr>
                            <td class="summary-label">Subtotal</td>
                            <td class="summary-value">Rs. <?php echo number_format($total, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Discount</td>
                            <td class="summary-value">Rs. <?php echo number_format($discount, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="summary-label">Total</td>
                            <td class="summary-value">Rs. <?php echo number_format($total - $discount, 2); ?></td>
                        </tr>
                        <?php if ($advance > 0) { ?>
                            <tr>
                                <td class="summary-label">Advance</td>
                                <td class="summary-value">Rs. <?php echo number_format($advance, 2); ?></td>
                            </tr>
                        <?php } ?> <?php if ($balance > 0) { ?>
                            <tr>
                                <td class="summary-label">Balance</td>
                                <td class="summary-value">Rs. <?php echo number_format($balance, 2); ?></td>
                            </tr>
                        <?php } ?> <?php if ($amount_received > 0 && $printType !== 'standard') { ?>
                            <tr>
                                <td class="summary-label">Amount Received</td>
                                <td class="summary-value">Rs. <?php echo number_format($amount_received, 2); ?></td>
                            </tr>
                        <?php } ?>
                        <?php if ($cash_change > 0 && $printType !== 'standard') { ?>
                            <tr>
                                <td class="summary-label">Change</td>
                                <td class="summary-value">Rs. <?php echo number_format($cash_change, 2); ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="final-total">
                            <td>NET PAYABLE</td>
                            <td>Rs. <?php echo number_format($total - $discount - $advance, 2); ?></td>
                        </tr>
                    </table>

                    <?php if ($total_savings > 0) { ?>
                        <div style="margin-top: 20px; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
                            <strong style="color: #155724; font-size: 14pt;">
                                🎉 Total Savings: Rs. <?php echo number_format($total_savings, 2); ?>
                            </strong>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div> <!-- Payment Information -->

    <div class="thank-you">
        Thank you! Come again.
    </div>

    <div class="footer">
        Software by <b>CyexTech Solutions</b> <br /> Visit : <b>www.CyexTech.com</b>
    </div>
</div>

<script>
    let currentPrintType = '<?php echo $printType; ?>';
    const showPrintTypeSelector = <?php echo $showPrintTypeSelector ? 'true' : 'false'; ?>;

    function selectPrintType(type) {
        currentPrintType = type;

        // Update the bill container class and show it
        const billContainer = document.getElementById('bill-container');
        billContainer.className = 'bill ' + type;
        billContainer.style.display = 'block';

        // Update the toggle button text
        const toggleText = document.getElementById('toggle-text');
        if (toggleText) {
            toggleText.textContent = type === 'receipt' ? 'Standard Invoice' : 'Receipt Print';
        }

        // Hide the modal
        const modal = document.getElementById('printTypeModal');
        if (modal) {
            modal.style.display = 'none';
        }

        // Show the toggle button
        const toggleButton = document.querySelector('.print-toggle');
        if (toggleButton) {
            toggleButton.style.display = 'block';
        }

        // Show the action buttons
        const actionButtons = document.querySelector('.action-buttons');
        if (actionButtons) {
            actionButtons.style.display = 'flex';
        }

        // Update URL to include print type
        const url = new URL(window.location);
        url.searchParams.set('printType', type);
        window.history.replaceState({}, '', url);

        // Trigger print dialog after a short delay
        setTimeout(() => {
            window.print();
        }, 300);
    }

    function togglePrintType() {
        const billContainer = document.getElementById('bill-container');
        const toggleText = document.getElementById('toggle-text');

        if (currentPrintType === 'receipt') {
            currentPrintType = 'standard';
            billContainer.className = 'bill standard';
            toggleText.textContent = 'Receipt Print';
        } else {
            currentPrintType = 'receipt';
            billContainer.className = 'bill receipt';
            toggleText.textContent = 'Standard Invoice';
        }

        // Update URL to reflect current print type
        const url = new URL(window.location);
        url.searchParams.set('printType', currentPrintType);
        window.history.replaceState({}, '', url);
    }
    // PDF Export Function
    function exportToPDF() {
        // Get invoice number for filename
        const invoiceNumber = '<?php echo $bill_no ?? $id; ?>';
        const filename = `Invoice_${invoiceNumber}.pdf`;

        // Hide action buttons and toggle button temporarily
        const actionButtons = document.querySelector('.action-buttons');
        const toggleButton = document.querySelector('.print-toggle');

        if (actionButtons) actionButtons.style.display = 'none';
        if (toggleButton) toggleButton.style.display = 'none';

        // Use browser's print to PDF functionality
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${filename}</title>
                <style>
                    ${document.querySelector('style').innerHTML}
                    @media print {
                        body { margin: 0; }
                        .action-buttons, .print-toggle { display: none !important; }
                    }
                </style>
            </head>
            <body>
                ${document.getElementById('bill-container').outerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 1000);
                    };
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();

        // Restore buttons after a delay
        setTimeout(() => {
            if (actionButtons) actionButtons.style.display = 'flex';
            if (toggleButton) toggleButton.style.display = 'block';
        }, 1000);
    }
    // Close Page Function
    function closePage() {
        // Try to close the window
        if (window.opener || window.parent !== window) {
            window.close();
        } else {
            // If can't close, redirect to main page
            // window.location.href = '../index.php';

            // If can't close, close !!!
            window.close();

        }
    } // Toggle Additional Info Section
    function toggleAdditionalInfo() {
        const additionalInfoSection = document.getElementById('additional-info-section');
        const additionalText = document.getElementById('additional-text');
        const summaryContainer = document.querySelector('.summary-container');

        if (additionalInfoSection.style.display === 'none') {
            additionalInfoSection.style.display = 'block';
            additionalText.readOnly = false;
            additionalText.focus();
            additionalText.placeholder = 'Add any additional notes or information here...';
            summaryContainer.classList.add('has-additional-info');
        } else {
            additionalInfoSection.style.display = 'none';
            additionalText.readOnly = true;
            summaryContainer.classList.remove('has-additional-info');
        }
    }

    // Only auto-print if print type was already specified in URL
    if (!showPrintTypeSelector) {
        window.print();
    }

    // // after print, close the page 
    // window.onafterprint = function() {
    //     window.close();
    // };

    // // Fallback for browsers that don't support onafterprint
    // setTimeout(function() {
    //     window.close();
    // }, 5000);
</script>