<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

// Only Admin can access this API
session_start();
$employee_role = $_SESSION['employee_role'] ?? '';
if ($employee_role !== "Admin") {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once(__DIR__ . '/../../inc/config.php');

try {
    // Query to get all outstanding GRN payments
    $query = "
        SELECT 
            grn.grn_id,
            grn.grn_number,
            grn.receipt_date,
            grn.invoice_number,
            grn.invoice_date,
            grn.total_amount,
            grn.paid_amount,
            grn.outstanding_amount,
            grn.payment_status,
            grn.payment_method,
            grn.payment_reference,
            grn.payment_notes,
            s.supplier_name,
            s.supplier_tel,
            s.supplier_address,
            s.credit_balance,
            COALESCE(SUM(sp.amount), 0) as total_payments_made
        FROM goods_receipt_notes grn
        LEFT JOIN suppliers s ON grn.supplier_id = s.supplier_id
        LEFT JOIN supplier_payments sp ON grn.grn_id = sp.grn_id
        WHERE grn.payment_status IN ('unpaid', 'partial')
            AND grn.status = 'completed'
            AND grn.outstanding_amount > 0
        GROUP BY grn.grn_id
        ORDER BY grn.receipt_date DESC, grn.grn_number DESC
    ";
    
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        throw new Exception('Database query failed: ' . mysqli_error($con));
    }
    
    $payments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Format the data
        $row['total_amount'] = number_format((float)$row['total_amount'], 2, '.', '');
        $row['paid_amount'] = number_format((float)$row['paid_amount'], 2, '.', '');
        $row['outstanding_amount'] = number_format((float)$row['outstanding_amount'], 2, '.', '');
        $row['total_payments_made'] = number_format((float)$row['total_payments_made'], 2, '.', '');
        $row['credit_balance'] = number_format((float)$row['credit_balance'], 2, '.', '');
        
        // Format dates
        $row['receipt_date'] = date('Y-m-d', strtotime($row['receipt_date']));
        if ($row['invoice_date']) {
            $row['invoice_date'] = date('Y-m-d', strtotime($row['invoice_date']));
        }
        
        $payments[] = $row;
    }
    
    // Get summary statistics with category breakdown
    $summary_query = "
        SELECT 
            COUNT(*) as total_outstanding_grns,
            SUM(outstanding_amount) as total_outstanding_amount,
            SUM(total_amount) as total_grn_amount,
            SUM(paid_amount) as total_paid_amount
        FROM goods_receipt_notes 
        WHERE payment_status IN ('unpaid', 'partial')
            AND status = 'completed'
            AND outstanding_amount > 0
    ";
    
    $summary_result = mysqli_query($con, $summary_query);
    $summary = mysqli_fetch_assoc($summary_result);
    
    if ($summary) {
        $summary['total_outstanding_amount'] = number_format((float)$summary['total_outstanding_amount'], 2, '.', '');
        $summary['total_grn_amount'] = number_format((float)$summary['total_grn_amount'], 2, '.', '');
        $summary['total_paid_amount'] = number_format((float)$summary['total_paid_amount'], 2, '.', '');
    }
    
    // Get category statistics (for future categories)
    $categories_summary = [
        'grn' => [
            'name' => 'GRN Payments',
            'count' => (int)$summary['total_outstanding_grns'],
            'amount' => (float)$summary['total_outstanding_amount'],
            'status' => 'active'
        ],
        'utilities' => [
            'name' => 'Utilities',
            'count' => 0,
            'amount' => 0,
            'status' => 'coming_soon'
        ],
        'rent' => [
            'name' => 'Rent',
            'count' => 0,
            'amount' => 0,
            'status' => 'coming_soon'
        ],
        'salary' => [
            'name' => 'Salary',
            'count' => 0,
            'amount' => 0,
            'status' => 'coming_soon'
        ],
        'printer_rent' => [
            'name' => 'Printer Rent',
            'count' => 0,
            'amount' => 0,
            'status' => 'coming_soon'
        ],
        'loans' => [
            'name' => 'Loans',
            'count' => 0,
            'amount' => 0,
            'status' => 'coming_soon'
        ],
        'subscriptions' => [
            'name' => 'Subscriptions',
            'count' => 0,
            'amount' => 0,
            'status' => 'coming_soon'
        ]
    ];
    
    echo json_encode([
        'success' => true,
        'payments' => $payments,
        'summary' => $summary,
        'categories_summary' => $categories_summary,
        'total_records' => count($payments)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching GRN payments: ' . $e->getMessage()
    ]);
} finally {
    if (isset($con)) {
        mysqli_close($con);
    }
}
?>