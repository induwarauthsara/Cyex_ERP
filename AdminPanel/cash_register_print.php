<?php
require_once '../inc/config.php';

// Get register ID from URL
$register_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get register details
$sql = "SELECT * FROM cash_register WHERE id = $register_id";
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) !== 1) {
    echo "Invalid register session";
    exit;
}

$register = mysqli_fetch_assoc($result);
$opening_balance = $register['opening_balance'];
$actual_cash = $register['actual_cash'];
$bank_deposit = $register['bank_deposit'];
$opened_at = new DateTime($register['opened_at']);
$closed_at = $register['closed_at'] ? new DateTime($register['closed_at']) : null;
$notes = $register['notes'];
$closing_notes = $register['closing_notes'];

// Format dates
$date = $opened_at->format('Y-m-d');
$open_time = $opened_at->format('H:i:s');
$close_time = $closed_at ? $closed_at->format('H:i:s') : 'Still Open';

// Get sales data
$sales_sql = "SELECT 
                SUM(CASE WHEN pd.payment_method = 'Cash' THEN i.total - i.discount ELSE 0 END) as cash_sales,
                SUM(CASE WHEN pd.payment_method = 'Card' THEN i.advance ELSE 0 END) as card_sales,
                SUM(i.discount) as total_discount,
                COUNT(*) as transaction_count,
                AVG(i.total - i.discount) as avg_transaction
            FROM 
                invoice i
            LEFT JOIN 
                payment_details pd ON i.invoice_number = pd.invoice_id
            WHERE 
                i.invoice_date = '$date' AND
                i.time BETWEEN 
                    '$open_time' 
                AND 
                    " . ($closed_at ? "'" . $close_time . "'" : "CURRENT_TIME()");

$sales_result = mysqli_query($con, $sales_sql);
$sales = mysqli_fetch_assoc($sales_result);

$cash_sales = $sales['cash_sales'] ?? 0;
$card_sales = $sales['card_sales'] ?? 0;
$total_sales = $cash_sales + $card_sales;
$total_discount = $sales['total_discount'] ?? 0;
$transaction_count = $sales['transaction_count'] ?? 0;
$avg_transaction = $sales['avg_transaction'] ?? 0;

// Get cash out transactions
$petty_sql = "SELECT SUM(amount) as total_cash_out FROM pettycash WHERE register_id = $register_id";
$petty_result = mysqli_query($con, $petty_sql);
$petty = mysqli_fetch_assoc($petty_result);
$cash_out = $petty['total_cash_out'] ?? 0;

// Get detailed petty cash records for display
$petty_details_sql = "SELECT * FROM pettycash WHERE register_id = $register_id ORDER BY date, time";
$petty_details_result = mysqli_query($con, $petty_details_sql);

// Calculate expected cash
$expected_cash = $opening_balance + $cash_sales - $cash_out;

// Calculate cash difference
$cash_difference = $actual_cash - $expected_cash;

// Get top selling items for today
$top_items_sql = "SELECT 
                    s.product, 
                    SUM(s.qty) as total_qty, 
                    SUM(s.amount) as total_revenue 
                FROM 
                    sales s
                JOIN 
                    invoice i ON s.invoice_number = i.invoice_number
                WHERE 
                    i.invoice_date = '$date' AND
                    i.time BETWEEN 
                        '$open_time' 
                    AND 
                        " . ($closed_at ? "'" . $close_time . "'" : "CURRENT_TIME()") . "
                GROUP BY 
                    s.product
                ORDER BY 
                    total_revenue DESC
                LIMIT 5";

$top_items_result = mysqli_query($con, $top_items_sql);

// Get payment method breakdown
$payment_methods_sql = "SELECT 
                        pd.payment_method, 
                        COUNT(*) as count, 
                        SUM(i.advance) as total
                    FROM 
                        invoice i
                    JOIN 
                        payment_details pd ON i.invoice_number = pd.invoice_id
                    WHERE 
                        i.invoice_date = '$date' AND
                        i.time BETWEEN 
                            '$open_time' 
                        AND 
                            " . ($closed_at ? "'" . $close_time . "'" : "CURRENT_TIME()") . "
                    GROUP BY 
                        pd.payment_method";

$payment_methods_result = mysqli_query($con, $payment_methods_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Register Report</title>
    <!-- Include jsPDF library for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #000;
        }
        
        .report {
            width: 80mm;
            margin: 0 auto;
            background-color: white;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            /*margin-bottom: 15px;*/
        }
        
        .logo-img {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .logo-img img {
            height: 100px;
        }
        
        .company-name {
            font-size: 18pt;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .company-address {
            font-size: 10pt;
            margin: 5px 0;
        }
        
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        
        .section {
            margin: 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px dashed #000;
        }
        
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 10pt;
        }
        
        .detail-label {
            font-weight: bold;
        }
        
        .amount {
            text-align: right;
        }
        
        .highlight {
            font-weight: bold;
            font-size: 11pt;
        }
        
        .footer {
            text-align: center;
            font-size: 10pt;
            margin-top: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        
        th, td {
            padding: 3px;
            text-align: left;
        }
        
        th {
            border-bottom: 1px solid #ddd;
        }
        
        .text-right {
            text-align: right;
        }
        
        .notes {
            font-size: 9pt;
            margin-top: 10px;
            font-style: italic;
        }
        
        /* Style for export button */
        .export-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 5px;
            font-weight: bold;
        }
        
        .export-btn:hover {
            background-color: #45a049;
        }
        
        @media print {
            body {
                background-color: white;
            }
            
            .report {
                width: 80mm;
                box-shadow: none;
                padding: 0;
            }
            
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="report" id="report-container">
        <div class="header">
            <div class="logo-img">
                <img src="/logo.jpg" alt="Company Logo">
            </div>
            <div class="company-name"><?php echo $ERP_COMPANY_NAME; ?></div>
            <div class="company-address">
                <?php echo $ERP_COMPANY_ADDRESS; ?><br>
                <?php echo $ERP_COMPANY_PHONE; ?>
            </div>
        </div>
        
        <div class="report-title">Cash Register Report</div>
        
        <div class="section">
            <div class="detail-row">
                <span class="detail-label">Register ID:</span>
                <span><?php echo $register_id; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span><?php echo $date; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Opened:</span>
                <span><?php echo $open_time; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Closed:</span>
                <span><?php echo $close_time; ?></span>
            </div>
            <?php if ($notes): ?>
            <div class="notes">
                <strong>Opening Notes:</strong> <?php echo $notes; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <div class="section-title">Sales Summary</div>
            <div class="detail-row">
                <span class="detail-label">Transactions:</span>
                <span><?php echo $transaction_count; ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Gross Sales:</span>
                <span class="amount">Rs. <?php echo number_format($total_sales + $total_discount, 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Discounts:</span>
                <span class="amount">Rs. <?php echo number_format($total_discount, 2); ?></span>
            </div>
            <div class="detail-row highlight">
                <span class="detail-label">Net Sales:</span>
                <span class="amount">Rs. <?php echo number_format($total_sales, 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Avg. Transaction:</span>
                <span class="amount">Rs. <?php echo number_format($avg_transaction, 2); ?></span>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Payment Methods</div>
            <table>
                <tr>
                    <th>Method</th>
                    <th class="text-right">Count</th>
                    <th class="text-right">Amount</th>
                </tr>
                <?php
                if ($payment_methods_result && mysqli_num_rows($payment_methods_result) > 0) {
                    while ($payment = mysqli_fetch_assoc($payment_methods_result)) {
                        echo "<tr>
                                <td>{$payment['payment_method']}</td>
                                <td class='text-right'>{$payment['count']}</td>
                                <td class='text-right'>Rs. " . number_format($payment['total'], 2) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No payment data available</td></tr>";
                }
                ?>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">Top 5 Selling Items</div>
            <table>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Revenue</th>
                </tr>
                <?php
                if ($top_items_result && mysqli_num_rows($top_items_result) > 0) {
                    while ($item = mysqli_fetch_assoc($top_items_result)) {
                        $product_name = strlen($item['product']) > 15 ? 
                                        substr($item['product'], 0, 15) . '...' : 
                                        $item['product'];
                        
                        echo "<tr>
                                <td title='{$item['product']}'>{$product_name}</td>
                                <td class='text-right'>{$item['total_qty']}</td>
                                <td class='text-right'>Rs. " . number_format($item['total_revenue'], 2) . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No sales data available</td></tr>";
                }
                ?>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">Cash Summary</div>
            <div class="detail-row">
                <span class="detail-label">Opening Float:</span>
                <span class="amount">Rs. <?php echo number_format($opening_balance, 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Cash Sales:</span>
                <span class="amount">Rs. <?php echo number_format($cash_sales, 2); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Cash Out/Petty Cash:</span>
                <span class="amount">Rs. <?php echo number_format($cash_out, 2); ?></span>
            </div>
            <div class="detail-row highlight">
                <span class="detail-label">Expected Cash:</span>
                <span class="amount">Rs. <?php echo number_format($expected_cash, 2); ?></span>
            </div>
            <?php if ($closed_at): ?>
            <div class="detail-row">
                <span class="detail-label">Actual Cash:</span>
                <span class="amount">Rs. <?php echo number_format($actual_cash, 2); ?></span>
            </div>
            <div class="detail-row" style="<?php echo $cash_difference < 0 ? 'color: red;' : ($cash_difference > 0 ? 'color: green;' : ''); ?>">
                <span class="detail-label">Cash Difference:</span>
                <span class="amount">Rs. <?php echo number_format($cash_difference, 2); ?><?php echo $cash_difference < 0 ? ' (Short)' : ($cash_difference > 0 ? ' (Over)' : ''); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Bank Deposit:</span>
                <span class="amount">Rs. <?php echo number_format($bank_deposit, 2); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($notes): ?>
            <div class="notes">
                <strong>Opening Notes:</strong> <?php echo $notes; ?>
            </div>
            <?php endif; ?>
            <?php if ($closing_notes && $closed_at): ?>
            <div class="notes">
                <strong>Closing Notes:</strong> <?php echo $closing_notes; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (mysqli_num_rows($petty_details_result) > 0): ?>
        <div class="section">
            <div class="section-title">Cash Out / Petty Cash Records</div>
            <table>
                <tr>
                    <th>Purpose</th>
                    <th class="text-right">Amount</th>
                    <th>Time</th>
                    <th>Employee</th>
                </tr>
                <?php while ($petty_item = mysqli_fetch_assoc($petty_details_result)): ?>
                <tr>
                    <td><?php echo $petty_item['perrycash']; ?></td>
                    <td class="text-right">Rs. <?php echo number_format($petty_item['amount'], 2); ?></td>
                    <td><?php echo $petty_item['time']; ?></td>
                    <td><?php echo $petty_item['emp_name'] ?: 'N/A'; ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            Printed: <?php echo date('Y-m-d H:i:s'); ?><br>
            Software by CyexTech Solutions<br>
            CyexTech.com
        </div>
    </div>
    
    <!-- Button to print - only shown on screen, not when printing -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" class="export-btn">Print Report</button>
        <button onclick="exportToPDF()" class="export-btn">Export as PDF</button>
        <button onclick="window.close()" class="export-btn">Close</button>
    </div>
    
    <script>
        // Auto print on load
        window.onload = function() {
            window.print();
        };
        
        // Function to export the report as PDF
        function exportToPDF() {
            // Initialize jsPDF
            const { jsPDF } = window.jspdf;
            
            // Get the report container
            const reportElement = document.getElementById('report-container');
            
            // Use html2canvas to capture the report as an image
            html2canvas(reportElement, {
                scale: 2, // Higher scale for better quality
                logging: false,
                useCORS: true,
                allowTaint: true
            }).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                
                // Create PDF document with custom size that matches the receipt width (80mm)
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [80, canvas.height * 80 / canvas.width] // Scale height proportionally
                });
                
                pdf.addImage(imgData, 'PNG', 0, 0, 80, canvas.height * 80 / canvas.width);
                
                // Save the PDF with date in filename
                pdf.save('Cash_Register_Report_<?php echo $date; ?>.pdf');
            });
        }
    </script>
</body>
</html> 