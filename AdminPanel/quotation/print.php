<?php
require_once '../../inc/config.php';
$id = $_GET['id'];
$printType = 'standard'; // Always use standard print format for quotations

if (isset($_GET['quotation'])) {
    $quotation = $_GET['quotation'];
    if (!isset($id)) {
        $id = $quotation;
    }
}

// Fetch quotation data first to use in HTML
$quotation_sql = "SELECT * FROM quotations WHERE id = '$id' LIMIT 1;";
if ($result = mysqli_query($con, $quotation_sql)) {
    if (mysqli_num_rows($result) == 1) {
        $quotation = mysqli_fetch_array($result);
        $customer = $quotation['customer_name'];
        $tele = $quotation['customer_mobile'];
        $address = $quotation['customer_address'];
        $quote_no = $quotation['quotation_number'];
        $date = $quotation['quotation_date'];
        $valid_until = $quotation['valid_until'];
        $note = $quotation['note'];
        $subtotal = $quotation['subtotal'];
        $discount = $quotation['discount'];
        $total = $quotation['total'];
        $created_by = $quotation['created_by'];

        // Get created by employee name
        $employee_name = "";
        $emp_query = "SELECT emp_name FROM employees WHERE employ_id = '$created_by' LIMIT 1";
        $emp_result = mysqli_query($con, $emp_query);
        if ($emp_result && mysqli_num_rows($emp_result) > 0) {
            $emp_row = mysqli_fetch_assoc($emp_result);
            $employee_name = $emp_row['emp_name'];
        } else {
            $employee_name = $created_by; // Fallback to ID if name not found
        }

        // Get quotation items
        $items_sql = "SELECT * FROM quotation_items WHERE quotation_id = '$id'";
        $items_result = mysqli_query($con, $items_sql);
        
    } else {
        // Quotation not found
        echo "Quotation not found.";
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Quotation #<?php echo $quote_no; ?></title>
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
    
    /* Reduce spacing in summary */
    .summary-row {
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
            <p>Please choose the type of quotation you want to print:</p>
            <div class="print-type-buttons">
                <button class="print-type-btn receipt-btn" onclick="selectPrintType('receipt')">
                    📄 Receipt Print
                </button>
                <button class="print-type-btn standard-btn" onclick="selectPrintType('standard')">
                    📋 Standard Quotation
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Print Toggle Button -->
<button class="print-toggle no-print" onclick="togglePrintType()" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    Switch to <span id="toggle-text"><?php echo $printType === 'receipt' ? 'Standard Quotation' : 'Receipt Print'; ?></span>
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

<!-- Standard Quotation Container (A5) -->
<div id="bill-container" class="bill <?php echo $printType; ?>" <?php echo $showPrintTypeSelector ? 'style="display: none;"' : ''; ?>>
    
    <?php if ($printType === 'standard' || $printType === null): ?>
    <!-- Standard A5 Layout -->
    <div class="standard-content a5-container bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200" <?php echo ($printType === 'receipt') ? 'style="display:none;"' : ''; ?>>
        <div class="flex flex-col h-full">
            <header>
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-4">
                        <!-- Logo Image -->
                        <img src="../../logo.png" alt="Logo" class="h-13 w-13 object-contain">
                        <div>
                            <h1 class="text-xl font-bold text-slate-900 dark:text-white"><?php echo $ERP_COMPANY_NAME; ?></h1>
                            <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo $ERP_COMPANY_ADDRESS; ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo $ERP_COMPANY_PHONE; ?></p>
                            <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo $GLOBALS['ERP_COMPANY_WEBSITE']; ?></p>
                        </div>
                    </div>
                    <div class="text-right pt-5 pr-5">
                        <h2 class="text-3xl font-bold uppercase text-slate-900 dark:text-white">Quotation</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">No. <?php echo $quote_no; ?></p>
                    </div>
                </div>
                <div class="mt-4 border-t-4 border-primary"></div>
            </header>
            <section class="mt-6 grid grid-cols-3 gap-4">
                <div>
                    <h3 class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 green-underline">Quotation To</h3>
                    <p class="mt-1 text-xs font-medium text-slate-700 dark:text-slate-300"><?php echo $customer; ?></p>
                    <?php if ($tele): ?><p class="text-xs text-slate-600 dark:text-slate-400"><?php echo $tele; ?></p><?php endif; ?>
                    <?php if ($address): ?><p class="text-xs text-slate-600 dark:text-slate-400"><?php echo $address; ?></p><?php endif; ?>
                </div>
                <div></div>
                <div class="text-right">
                    <h3 class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 green-underline">Quotation Details</h3>
                    <div class="mt-1 space-y-1 text-xs">
                        <div class="flex justify-end space-x-2">
                            <span class="text-slate-600 dark:text-slate-400">Date:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300"><?php echo $date; ?></span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <span class="text-slate-600 dark:text-slate-400">Valid Until:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300"><?php echo $valid_until; ?></span>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <span class="text-slate-600 dark:text-slate-400">Prepared by:</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300"><?php echo $employee_name; ?></span>
                        </div>
                    </div>
                </div>
            </section>
            <section class="mt-8">
                <div class="flow-root">
                    <div class="-mx-2 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full px-2 py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full">
                                <thead class="border-b-2 border-slate-200 dark:border-slate-700">
                                    <tr>
                                        <th class="py-1 pl-2 pr-1 text-left text-xs font-semibold text-slate-900 dark:text-white sm:pl-0" scope="col">Description</th>
                                        <th class="px-1 py-1 text-right text-xs font-semibold text-slate-900 dark:text-white" scope="col">Price</th>
                                        <th class="px-1 py-1 text-right text-xs font-semibold text-slate-900 dark:text-white" scope="col">Quantity</th>
                                        <th class="py-1 pl-1 pr-2 text-right text-xs font-semibold text-slate-900 dark:text-white sm:pr-0" scope="col">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="text-slate-700 dark:text-slate-300">
                                    <?php
                                    while ($row = mysqli_fetch_array($items_result)) {
                                        $product_name = $row['product_name'];
                                        $quantity = $row['quantity'];
                                        $rate = $row['rate'];
                                        $amount = $row['amount'];
                                    ?>
                                    <tr class="border-b border-slate-200 dark:border-slate-800">
                                        <td class="py-2 pl-2 pr-1 text-sm font-medium sm:pl-0"><?php echo $product_name; ?></td>
                                        <td class="px-1 py-2 text-sm text-right"><?php echo number_format($rate, 2); ?></td>
                                        <td class="px-1 py-2 text-sm text-right"><?php echo $quantity; ?></td>
                                        <td class="py-2 pl-1 pr-2 text-sm font-medium text-primary sm:pr-0 text-right"><?php echo number_format($amount, 2); ?></td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Note and Summary in two columns -->
                <div class="mt-4 ml-2 grid grid-cols-2 gap-8">
                    <!-- Left column: Note -->
                    <div class="flex items-start">
                        <?php if ($note): ?>
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border-l-4 border-blue-500">
                                <p class="text-sm font-semibold text-blue-700 dark:text-blue-300 mb-1">Note:</p>
                                <p class="text-sm text-blue-600 dark:text-blue-400"><?php echo nl2br(htmlspecialchars($note)); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Right column: Summary table -->
                    <div>
                        <table class="min-w-full">
                            <tbody>
                                <?php if ($discount > 0): ?>
                                    <tr class="summary-row">
                                        <th class="pt-2 pl-4 pr-3 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pl-0" colspan="1" scope="row">Sub Total</th>
                                        <td class="pt-2 pl-3 pr-4 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pr-0"><?php echo number_format($subtotal, 2); ?></td>
                                    </tr>
                                    <tr class="summary-row">
                                        <th class="pt-1 pl-4 pr-3 text-right text-sm font-normal text-slate-500 dark:text-slate-400 sm:pl-0" colspan="1" scope="row">Discount</th>
                                        <td class="pt-1 pl-3 pr-4 text-right text-sm font-normal text-green-600 sm:pr-0">-<?php echo number_format($discount, 2); ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr class="summary-row">
                                    <th class="pt-2 pl-4 pr-3 text-right text-base font-semibold text-slate-900 dark:text-white sm:pl-0" colspan="1" scope="row">Total</th>
                                    <td class="pt-2 pl-3 pr-4 text-right text-base font-semibold text-primary sm:pr-0"><?php echo number_format($total, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <footer class="mt-auto pt-6 text-center">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">This quotation is valid until <?php echo date('F d, Y', strtotime($valid_until)); ?></p>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-400 mt-2">Thank you for your business!</p>
                <div class="mt-2 border-t border-slate-200 dark:border-slate-700 pt-2">
                    <p class="text-xs text-slate-500 dark:text-slate-400">Software by <b>CyexTech Solutions</b></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Visit: <b>www.CyexTech.com</b></p>
                </div>
            </footer>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($printType === 'receipt' || $printType === null): ?>
    <!-- Receipt Layout (Hidden for Standard) -->
    <div class="receipt-content" <?php echo ($printType === 'standard') ? 'style="display:none;"' : ''; ?>>
        <div class="header">
            <div class="logo-img"> <img src="../../logo.png" alt="LOGO"> </div>
            <div class="topic">
                <h1><?php echo $ERP_COMPANY_NAME; ?></h1>
                <h2><?php echo $ERP_COMPANY_ADDRESS; ?><br><?php echo $ERP_COMPANY_PHONE; ?></h2>
            </div>
        </div>
        <hr />
        <div class="details">
            <div class="Innerdetails">
                <div style="text-align: left; margin-left: 5px;">
                    Quote No : <span class="bill-no"><?php echo $quote_no; ?></span> <br>
                    Date : <?php echo $date; ?><br>
                    Valid Until : <?php echo $valid_until; ?>
                </div>
                <div style="text-align: right; margin-right: 5px;">
                    Prepared by : <?php echo $employee_name; ?> <br>
                    Customer : <?php echo $customer; ?>
                    <?php if ($tele): ?><br><?php echo $tele; ?><?php endif; ?>
                </div>
            </div>
        </div>
        <div class="content">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="price">Price</th>
                        <th class="price">Qty</th>
                        <th class="price">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Reset pointer for receipt loop
                    mysqli_data_seek($items_result, 0);
                    while ($row = mysqli_fetch_array($items_result)) {
                        $product_name = $row['product_name'];
                        $quantity = $row['quantity'];
                        $rate = $row['rate'];
                        $amount = $row['amount'];
                    ?>
                    <tr class="product-row">
                        <td><?php echo $product_name; ?></td>
                        <td class="price"><?php echo number_format($rate, 2); ?></td>
                        <td class="price"><?php echo $quantity; ?></td>
                        <td class="price"><?php echo number_format($amount, 2); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            
            <?php if ($note): ?>
                <div style="text-align: center; margin: 10px 0; padding: 8px; background-color: #f0f9ff; border: 1px dashed #2196F3; border-radius: 5px;">
                    <strong style="color: #2196F3; font-size: 12px;">Note: <?php echo htmlspecialchars($note); ?></strong>
                </div>
            <?php endif; ?>
            
            <table>
                <tfoot class="total-summary">
                    <?php if ($discount > 0): ?>
                    <tr>
                        <td colspan="3" class="bill_sum">Sub Total :</td>
                        <td class="bill_sum"><?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="bill_sum">Discount :</td>
                        <td class="bill_sum">-<?php echo number_format($discount, 2); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="final-total">
                        <td colspan="3" class="bill_sum">Total :</td>
                        <td class="bill_sum"><?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="validity">
            Valid Until: <?php echo date('M d, Y', strtotime($valid_until)); ?>
        </div>
        <div class="thank-you">
            Thank you for your business!
        </div>
        <div class="footer">
            Software by <b>CyexTech Solutions</b> <br /> Visit : <b>www.CyexTech.com</b>
        </div>
    </div>
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
        const quotationNumber = '<?php echo $quote_no ?? $id; ?>';
        const filename = `Quotation_${quotationNumber}.pdf`;
        
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
</script>
</body>
</html>