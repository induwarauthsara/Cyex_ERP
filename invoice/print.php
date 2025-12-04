<?php
require_once '../inc/config.php';
$id = $_GET['id'];
$printType = $_GET['printType'] ?? null; // Don't set default, check if provided

// Fetch print type setting from database if not provided in URL
if ($printType === null) {
    $setting_query = "SELECT setting_value FROM settings WHERE setting_name = 'invoice_print_type' LIMIT 1";
    $setting_result = mysqli_query($con, $setting_query);
    $db_print_type = 'standard'; // Default
    
    if ($setting_result && mysqli_num_rows($setting_result) > 0) {
        $row = mysqli_fetch_assoc($setting_result);
        $db_print_type = $row['setting_value'];
    }

    if ($db_print_type === 'receipt') {
        $printType = 'receipt';
    } elseif ($db_print_type === 'standard') {
        $printType = 'standard';
    }
    // If 'both', $printType remains null, triggering the selector
}

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

// Fetch invoice data first to use in HTML
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

        // Pre-calculate discount information for use in HTML
        $sales_sql = "SELECT * FROM sales WHERE invoice_number = '$id'";
        $sales_result = mysqli_query($con, $sales_sql);
        $sub_total = 0;
        $total_discount = 0;
        $has_individual_discounts = false;
        
        // Array to store product details for console logging
        $products_debug = array();
        
        while ($row = mysqli_fetch_array($sales_result)) {
            $quantity = $row['qty'];
            $price = $row['rate'];
            $discount_value = $row['discount_price'];
            $amount = $row['amount']; // This is the final amount from database
            
            $item_total = $price * $quantity;
            $sub_total += $item_total;
            
            // Calculate actual discount from the difference between item_total and amount
            $item_discount = $item_total - $amount;
            
            if ($item_discount > 0) {
                if ($individual_discount_mode == 1) {
                    $has_individual_discounts = true;
                }
                $total_discount += $item_discount;
            }
            
            // Store product details for debugging
            $products_debug[] = array(
                'product' => $row['product'],
                'qty' => $quantity,
                'rate' => $price,
                'discount_value' => $discount_value,
                'item_total' => $item_total,
                'item_discount' => $item_discount,
                'amount' => $row['amount'],
                'discount_mode' => $individual_discount_mode == 1 ? 'percentage' : 'fixed'
            );
        }
        
        // Reset the result pointer for later use in HTML
        mysqli_data_seek($sales_result, 0);
        
    } else {
        // Invoice not found
        echo "Invoice not found.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Invoice #<?php echo $bill_no; ?></title>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<script>
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            primary: "#22c55e",
            "background-light": "#ffffff",
            "background-dark": "#18181b",
          },
          fontFamily: {
            display: ["Poppins", "sans-serif"],
          },
          borderRadius: {
            DEFAULT: "0.5rem",
          },
        },
      },
    };
</script>
<style>
    @media print {
      @page {
        size: A5 portrait;
        margin: 0;
      }
      body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }
      .no-print {
        display: none !important;
      }
      .print-toggle, .action-buttons {
        display: none !important;
      }
      .a5-container {
        width: 148mm;
        height: 210mm;
        margin: 0;
        padding: 5mm !important; /* Reduced padding for print */
        box-shadow: none !important;
        overflow: hidden;
      }
    }
    
    .a5-container {
      width: 148mm;
      min-height: 210mm;
      margin-left: auto;
      margin-right: auto;
      background: white;
      padding-left: 10mm;
      padding-right: 10mm;
    }
    
    /* Reduce spacing in bill summary */
    .bill-summary-row {
      padding-top: 2px !important;
      padding-bottom: 2px !important;
    }
    
    /* Green underline for titles */
    .green-underline {
      border-bottom: 2px solid #22c55e;
      padding-bottom: 2px;
    }
    
    /* Receipt Styles (Hidden for Standard) */
    .bill.receipt {
        width: 80mm;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: bold;
        font-size: 13pt;
        text-align: center;
        margin: 0 auto;
        background: white;
        padding: 5mm;
    }
    .bill.receipt .header, .bill.receipt .details, .bill.receipt .content {
        text-align: center;
        margin-bottom: 10px;
    }
    .bill.receipt .logo-img img { height: 80px; }
    .bill.receipt table { width: 100%; border-collapse: collapse; }
    .bill.receipt th, .bill.receipt tr.product-row { border-top: 1px dashed black; padding: 5px 0; text-align: left; }
    .bill.receipt .price, .bill.receipt .bill_sum { text-align: right; }
    .bill.receipt .total-summary { border-top: 2px solid black; }
    .bill.receipt .final-total td { font-size: 15pt; font-weight: bold; border-top: 1px solid black; }
    
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
    .receipt-btn { background-color: #4CAF50; color: white; }
    .standard-btn { background-color: #2196F3; color: white; }
    
    /* Action Buttons */
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
    .pdf-btn { background-color: #FF5722; }
    .print-btn { background-color: #2196F3; }
    .close-btn { background-color: #f44336; }
    
    /* Print Toggle */
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
    }
</style>
</head>
<body class="bg-slate-100 dark:bg-slate-900 font-display">

<!-- Print Type Selection Modal -->
<?php if ($showPrintTypeSelector): ?>
    <div class="print-type-modal no-print" id="printTypeModal">
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

<!-- Print Toggle Button -->
<button class="print-toggle no-print" onclick="togglePrintType()" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    Switch to <span id="toggle-text"><?php echo $printType === 'receipt' ? 'Standard Invoice' : 'Receipt Print'; ?></span>
</button>

<!-- Action Buttons -->
<div class="action-buttons no-print" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    <button class="action-btn pdf-btn" onclick="exportToPDF()">
        📄 Export as PDF
    </button>
    <button class="action-btn print-btn" onclick="window.print()">
        🖨️ Print
    </button>
    <button class="action-btn close-btn" onclick="closePage()">
        ❌ Close
    </button>
</div>

<!-- Standard Invoice Container (A5) -->
<div id="bill-container" class="bill <?php echo $printType; ?>" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    
    <?php if ($printType === 'standard' || $printType === null): ?>
        <?php include 'print-standard-template.php'; ?>
    <?php endif; ?>

    <?php if ($printType === 'receipt' || $printType === null): ?>
        <?php include 'print-receipt-template.php'; ?>
    <?php endif; ?>

</div>

<script>
    let currentPrintType = '<?php echo $printType; ?>';
    const showPrintTypeSelector = <?php echo $showPrintTypeSelector ? 'true' : 'false'; ?>;

    function selectPrintType(type) {
        currentPrintType = type;
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.set('printType', type);
        window.history.replaceState({}, '', url);
        
        // Reload to apply changes (simplest way to switch between Tailwind and Receipt styles cleanly)
        window.location.reload();
    }

    function togglePrintType() {
        const newType = currentPrintType === 'receipt' ? 'standard' : 'receipt';
        selectPrintType(newType);
    }
    
    function exportToPDF() {
        const invoiceNumber = '<?php echo $bill_no ?? $id; ?>';
        const filename = `Invoice_${invoiceNumber}.pdf`;
        
        // Hide controls
        document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
        
        window.print();
        
        // Show controls back (optional, or reload)
        setTimeout(() => {
             document.querySelectorAll('.no-print').forEach(el => el.style.display = '');
        }, 1000);
    }

    function closePage() {
        if (window.opener || window.parent !== window) {
            window.close();
        } else {
            window.close();
        }
    }
    
    // Auto print if type is set
    if (!showPrintTypeSelector) {
        setTimeout(() => {
            window.print();
        }, 500);
    }
    
    // Console log all data for debugging
    console.log('=== INVOICE DEBUG DATA ===');
    console.log('Invoice Number:', '<?php echo $bill_no; ?>');
    console.log('Customer:', '<?php echo addslashes($customer); ?>');
    console.log('Telephone:', '<?php echo $tele; ?>');
    console.log('Date:', '<?php echo $date; ?>');
    console.log('Cashier ID:', '<?php echo $biller_id; ?>');
    console.log('Cashier Name:', '<?php echo addslashes($cashier_name); ?>');
    console.log('Payment Method:', '<?php echo $payment_method; ?>');
    console.log('---');
    console.log('Sub Total:', <?php echo $sub_total; ?>);
    console.log('Discount:', <?php echo $discount; ?>);
    console.log('Total:', <?php echo $total; ?>);
    console.log('Advance:', <?php echo $advance; ?>);
    console.log('Balance:', <?php echo $balance; ?>);
    console.log('Amount Received:', <?php echo $amount_received; ?>);
    console.log('Cash Change:', <?php echo $cash_change; ?>);
    console.log('Full Paid:', <?php echo $full_paid; ?>);
    console.log('---');
    console.log('Individual Discount Mode:', <?php echo $individual_discount_mode; ?>);
    console.log('Has Individual Discounts:', <?php echo $has_individual_discounts ? 'true' : 'false'; ?>);
    console.log('Total Discount Calculated:', <?php echo $total_discount; ?>);
    console.log('---');
    console.log('Print Type:', '<?php echo $printType; ?>');
    console.log('Show Selector:', <?php echo $showPrintTypeSelector ? 'true' : 'false'; ?>);
    console.log('=========================');
    
    // Console log individual products
    console.log('=== PRODUCTS DETAILS ===');
    const products = <?php echo json_encode($products_debug); ?>;
    console.table(products);
    console.log('Total Products:', products.length);
    products.forEach((product, index) => {
        console.log(`\nProduct ${index + 1}:`);
        console.log('  Name:', product.product);
        console.log('  Quantity:', product.qty);
        console.log('  Rate:', product.rate);
        console.log('  Item Total:', product.item_total);
        console.log('  Discount Value:', product.discount_value);
        console.log('  Discount Mode:', product.discount_mode);
        console.log('  Item Discount:', product.item_discount);
        console.log('  Final Amount:', product.amount);
    });
    console.log('========================');

</script>
</body>
</html>