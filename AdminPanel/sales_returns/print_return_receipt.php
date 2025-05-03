<?php
require_once('../../inc/config.php');

// Check if return ID is provided
if (!isset($_GET['return_id']) || empty($_GET['return_id'])) {
    echo "Return ID is required.";
    exit;
}

$return_id = intval($_GET['return_id']);

// Fetch return details
$return_query = "SELECT sr.*, 
                 i.invoice_number, 
                 c.customer_name as customer_name, c.customer_mobile as customer_phone,
                 e.emp_name as employee_name 
                 FROM sales_returns sr 
                 LEFT JOIN invoice i ON sr.invoice_id = i.invoice_number
                 LEFT JOIN customers c ON sr.customer_id = c.id
                 LEFT JOIN employees e ON sr.user_id = e.employ_id
                 WHERE sr.return_id = $return_id";

$return_data = fetch_data($return_query);

if (empty($return_data)) {
    echo "Return not found.";
    exit;
}

$return = $return_data[0];

// Get return items
$items_query = "SELECT sri.*, 
                p.product_name as item_name, 
                pb.batch_number 
                FROM sales_return_items sri
                LEFT JOIN products p ON sri.product_id = p.product_id
                LEFT JOIN product_batch pb ON sri.batch_id = pb.batch_id
                WHERE sri.return_id = $return_id";

$items = fetch_data($items_query);

// Format date
$return_date = date('d M Y h:i A', strtotime($return['return_date']));
$company_name = $GLOBALS['ERP_COMPANY_NAME'];
$company_address = $GLOBALS['ERP_COMPANY_ADDRESS'];
$company_phone = $GLOBALS['ERP_COMPANY_PHONE'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Receipt #<?php echo $return_id; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .receipt {
            width: 80mm;
            margin: 0 auto;
            padding: 5mm;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 5mm;
        }
        .receipt-header h1 {
            margin: 0;
            font-size: 14px;
        }
        .receipt-header p {
            margin: 2px 0;
        }
        .receipt-info {
            margin-bottom: 5mm;
        }
        .receipt-info p {
            margin: 2px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        .items-table th, .items-table td {
            text-align: left;
            padding: 2px;
        }
        .items-table th {
            border-bottom: 1px solid #000;
        }
        .items-table tr.total-row td {
            border-top: 1px solid #000;
            font-weight: bold;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 10mm;
            border-top: 1px dashed #000;
            padding-top: 3mm;
        }
        .print-button {
            text-align: center;
            margin: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #4a6fdc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="print-button">
        <button onclick="window.print()">Print Receipt</button>
    </div>
    
    <div class="receipt">
        <div class="receipt-header">
            <h1><?php echo $company_name; ?></h1>
            <p><?php echo $company_address; ?></p>
            <p>Tel: <?php echo $company_phone; ?></p>
            <h2>RETURN RECEIPT</h2>
        </div>
        
        <div class="receipt-info">
            <p><strong>Return ID:</strong> <?php echo $return_id; ?></p>
            <p><strong>Date:</strong> <?php echo $return_date; ?></p>
            <p><strong>Invoice #:</strong> <?php echo $return['invoice_number']; ?></p>
            <p><strong>Customer:</strong> <?php echo $return['customer_name'] ?? 'Walk-in Customer'; ?></p>
            <?php if (!empty($return['customer_phone'])): ?>
            <p><strong>Phone:</strong> <?php echo $return['customer_phone']; ?></p>
            <?php endif; ?>
            <p><strong>Refund Method:</strong> <?php echo $return['refund_method']; ?></p>
            <p><strong>Return Reason:</strong> <?php echo $return['return_reason']; ?></p>
            <p><strong>Processed By:</strong> <?php echo $return['employee_name']; ?></p>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?php echo $item['item_name']; ?></td>
                    <td><?php echo $item['quantity_returned']; ?></td>
                    <td><?php echo number_format($item['return_price'], 2); ?></td>
                    <td><?php echo number_format($item['return_price'] * $item['quantity_returned'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3">Total Return Amount</td>
                    <td><?php echo number_format($return['return_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>
        
        <?php if (!empty($return['return_note'])): ?>
        <div class="receipt-note">
            <p><strong>Note:</strong> <?php echo $return['return_note']; ?></p>
        </div>
        <?php endif; ?>
        
        <div class="receipt-footer">
            <p>Thank you for your business!</p>
            <p>Returns are subject to store policy.</p>
            <p>This receipt is evidence of your return transaction.</p>
            <p><?php echo date('d/m/Y h:i A'); ?></p>
        </div>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>