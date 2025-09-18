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

// Check if start and end date parameters are provided
if (!isset($_GET['start']) || !isset($_GET['end'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Start and end date parameters are required']);
    exit;
}

$startDate = $_GET['start'];
$endDate = $_GET['end'];

// Validate date formats
if (!DateTime::createFromFormat('Y-m-d', $startDate) || !DateTime::createFromFormat('Y-m-d', $endDate)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format. Use Y-m-d format']);
    exit;
}

try {
    // Get weekly invoice data
    $invoiceQuery = "SELECT 
                        COUNT(*) as invoice_count,
                        SUM(advance) as cash_in,
                        SUM(profit) as profit,
                        AVG(advance) as avg_invoice_value
                     FROM invoice 
                     WHERE invoice_date BETWEEN ? AND ?";
    
    $stmt = mysqli_prepare($con, $invoiceQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $invoiceResult = mysqli_stmt_get_result($stmt);
    $invoiceData = mysqli_fetch_assoc($invoiceResult);
    
    // Get expense data
    $expenseQuery = "SELECT 
                        SUM(amount) as expenses
                     FROM transaction_log 
                     WHERE transaction_date BETWEEN ? AND ?
                     AND (transaction_type = 'Petty Cash' 
                          OR transaction_type = 'Raw Item Purchase' 
                          OR transaction_type LIKE 'Fall OneTimeProduct Cost from%' 
                          OR transaction_type = 'Salary Payment')";
    
    $stmt = mysqli_prepare($con, $expenseQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $expenseResult = mysqli_stmt_get_result($stmt);
    $expenseData = mysqli_fetch_assoc($expenseResult);
    
    // Get bank deposits
    $bankQuery = "SELECT SUM(amount) as bank_deposit FROM bank_deposits WHERE deposit_date BETWEEN ? AND ?";
    $stmt = mysqli_prepare($con, $bankQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $bankResult = mysqli_stmt_get_result($stmt);
    $bankData = mysqli_fetch_assoc($bankResult);
    
    // Get daily breakdown for the week
    $dailyQuery = "SELECT 
                        invoice_date,
                        COUNT(*) as daily_invoices,
                        SUM(advance) as daily_cash_in,
                        SUM(profit) as daily_profit
                   FROM invoice 
                   WHERE invoice_date BETWEEN ? AND ?
                   GROUP BY invoice_date
                   ORDER BY invoice_date";
    
    $stmt = mysqli_prepare($con, $dailyQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $dailyResult = mysqli_stmt_get_result($stmt);
    
    $dailyBreakdown = [];
    while ($row = mysqli_fetch_assoc($dailyResult)) {
        $dailyBreakdown[] = $row;
    }
    
    // Get top performing days
    $topDaysQuery = "SELECT 
                        invoice_date,
                        SUM(advance) as daily_revenue
                     FROM invoice 
                     WHERE invoice_date BETWEEN ? AND ?
                     GROUP BY invoice_date
                     ORDER BY daily_revenue DESC
                     LIMIT 3";
    
    $stmt = mysqli_prepare($con, $topDaysQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $topDaysResult = mysqli_stmt_get_result($stmt);
    
    $topDays = [];
    while ($row = mysqli_fetch_assoc($topDaysResult)) {
        $topDays[] = $row;
    }
    
    // Calculate metrics
    $totalExpenses = $expenseData['expenses'] ?? 0;
    $netProfit = ($invoiceData['profit'] ?? 0) - $totalExpenses;
    $daysInPeriod = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
    
    // Prepare response
    $response = [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'invoice_count' => $invoiceData['invoice_count'] ?? 0,
        'cash_in' => $invoiceData['cash_in'] ?? 0,
        'profit' => $invoiceData['profit'] ?? 0,
        'expenses' => $totalExpenses,
        'bank_deposit' => $bankData['bank_deposit'] ?? 0,
        'net_profit' => $netProfit,
        'avg_invoice_value' => $invoiceData['avg_invoice_value'] ?? 0,
        'avg_daily_revenue' => ($invoiceData['cash_in'] ?? 0) / $daysInPeriod,
        'avg_daily_invoices' => ($invoiceData['invoice_count'] ?? 0) / $daysInPeriod,
        'daily_breakdown' => $dailyBreakdown,
        'top_performing_days' => $topDays
    ];
    
    // Clean output buffer and send JSON
    ob_clean();
    echo json_encode($response);
    
} catch (Exception $e) {
    // Clean output buffer and send error JSON
    ob_clean();
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

if (isset($con)) {
    mysqli_close($con);
}
exit;