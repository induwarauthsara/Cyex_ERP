<?php
// Suppress any output and errors before JSON response
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

session_start();

// Clean any previous output
ob_clean();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is admin
if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== 'Admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

require_once(__DIR__ . '/../../../inc/config.php');

// Check if date parameter is provided
if (!isset($_GET['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Date parameter is required']);
    exit;
}

$date = $_GET['date'];

// Validate date format
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format. Use Y-m-d format']);
    exit;
}

try {
    // Check database connection
    if (!$con) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }
    
    // Get daily invoice data
    $invoiceQuery = "SELECT 
                        COUNT(*) as invoice_count,
                        COALESCE(SUM(advance), 0) as cash_in,
                        COALESCE(SUM(profit), 0) as profit,
                        COALESCE(AVG(advance), 0) as avg_invoice_value
                     FROM invoice 
                     WHERE invoice_date = ?";
    
    $stmt = mysqli_prepare($con, $invoiceQuery);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 's', $date);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Execute failed: ' . mysqli_stmt_error($stmt));
    }
    
    $invoiceResult = mysqli_stmt_get_result($stmt);
    $invoiceData = mysqli_fetch_assoc($invoiceResult);
    
    // Get expense breakdown
    $expenseQuery = "SELECT 
                        transaction_type,
                        COALESCE(SUM(amount), 0) as amount
                     FROM transaction_log 
                     WHERE transaction_date = ? 
                     AND (transaction_type = 'Petty Cash' 
                          OR transaction_type = 'Raw Item Purchase' 
                          OR transaction_type LIKE 'Fall OneTimeProduct Cost from%' 
                          OR transaction_type = 'Salary Payment')
                     GROUP BY transaction_type";
    
    $stmt = mysqli_prepare($con, $expenseQuery);
    if (!$stmt) {
        throw new Exception('Expense prepare failed: ' . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 's', $date);
    mysqli_stmt_execute($stmt);
    $expenseResult = mysqli_stmt_get_result($stmt);
    
    $expenseBreakdown = [];
    $totalExpenses = 0;
    while ($row = mysqli_fetch_assoc($expenseResult)) {
        $expenseBreakdown[$row['transaction_type']] = $row['amount'];
        $totalExpenses += $row['amount'];
    }
    
    // Get bank deposits
    $bankQuery = "SELECT COALESCE(SUM(amount), 0) as bank_deposit FROM bank_deposits WHERE deposit_date = ?";
    $stmt = mysqli_prepare($con, $bankQuery);
    if (!$stmt) {
        // If bank_deposits table doesn't exist, set to 0
        $bankData = ['bank_deposit' => 0];
    } else {
        mysqli_stmt_bind_param($stmt, 's', $date);
        mysqli_stmt_execute($stmt);
        $bankResult = mysqli_stmt_get_result($stmt);
        $bankData = mysqli_fetch_assoc($bankResult);
    }
    
    // Get top selling products for the day
    $topProducts = [];
    $productQuery = "SELECT 
                        s.product as product_name,
                        COALESCE(SUM(s.qty), 0) as qty_sold,
                        COALESCE(SUM(s.amount), 0) as revenue
                     FROM sales s
                     JOIN invoice i ON s.invoice_number = i.invoice_number
                     WHERE i.invoice_date = ?
                     GROUP BY s.product
                     ORDER BY qty_sold DESC
                     LIMIT 5";
    
    $stmt = mysqli_prepare($con, $productQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $date);
        if (mysqli_stmt_execute($stmt)) {
            $productResult = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($productResult)) {
                $topProducts[] = $row;
            }
        }
    }
    
    // Calculate net profit
    $netProfit = ($invoiceData['profit'] ?? 0) - $totalExpenses;
    
    // Prepare response
    $response = [
        'date' => $date,
        'invoice_count' => $invoiceData['invoice_count'] ?? 0,
        'cash_in' => $invoiceData['cash_in'] ?? 0,
        'profit' => $invoiceData['profit'] ?? 0,
        'expenses' => $totalExpenses,
        'bank_deposit' => $bankData['bank_deposit'] ?? 0,
        'net_profit' => $netProfit,
        'avg_invoice_value' => $invoiceData['avg_invoice_value'] ?? 0,
        'expense_breakdown' => $expenseBreakdown,
        'top_products' => $topProducts,
        'status' => 'success'
    ];
    
    // Clean output buffer and send JSON
    ob_clean();
    echo json_encode($response);
    
} catch (Exception $e) {
    // Clean output buffer and send error JSON
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'status' => 'error',
        'date' => $date ?? 'unknown'
    ]);
}

if (isset($con)) {
    mysqli_close($con);
}
exit;