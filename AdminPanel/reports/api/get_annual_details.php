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
    // Get annual invoice data
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
    
    // Get monthly breakdown for the year
    $monthlyQuery = "SELECT 
                        YEAR(invoice_date) as year,
                        MONTH(invoice_date) as month,
                        COUNT(*) as monthly_invoices,
                        SUM(advance) as monthly_cash_in,
                        SUM(profit) as monthly_profit
                     FROM invoice 
                     WHERE invoice_date BETWEEN ? AND ?
                     GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                     ORDER BY year, month";
    
    $stmt = mysqli_prepare($con, $monthlyQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $monthlyResult = mysqli_stmt_get_result($stmt);
    
    $monthlyBreakdown = [];
    while ($row = mysqli_fetch_assoc($monthlyResult)) {
        $row['month_name'] = date('F', mktime(0, 0, 0, $row['month'], 1));
        $monthlyBreakdown[] = $row;
    }
    
    // Get quarterly breakdown
    $quarterlyQuery = "SELECT 
                          YEAR(invoice_date) as year,
                          QUARTER(invoice_date) as quarter,
                          COUNT(*) as quarterly_invoices,
                          SUM(advance) as quarterly_cash_in,
                          SUM(profit) as quarterly_profit
                       FROM invoice 
                       WHERE invoice_date BETWEEN ? AND ?
                       GROUP BY YEAR(invoice_date), QUARTER(invoice_date)
                       ORDER BY year, quarter";
    
    $stmt = mysqli_prepare($con, $quarterlyQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $quarterlyResult = mysqli_stmt_get_result($stmt);
    
    $quarterlyBreakdown = [];
    while ($row = mysqli_fetch_assoc($quarterlyResult)) {
        $quarterlyBreakdown[] = $row;
    }
    
    // Get top performing months
    $topMonthsQuery = "SELECT 
                          YEAR(invoice_date) as year,
                          MONTH(invoice_date) as month,
                          SUM(advance) as monthly_revenue
                       FROM invoice 
                       WHERE invoice_date BETWEEN ? AND ?
                       GROUP BY YEAR(invoice_date), MONTH(invoice_date)
                       ORDER BY monthly_revenue DESC
                       LIMIT 5";
    
    $stmt = mysqli_prepare($con, $topMonthsQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $topMonthsResult = mysqli_stmt_get_result($stmt);
    
    $topMonths = [];
    while ($row = mysqli_fetch_assoc($topMonthsResult)) {
        $row['month_name'] = date('F Y', mktime(0, 0, 0, $row['month'], 1, $row['year']));
        $topMonths[] = $row;
    }
    
    // Get annual customer statistics
    $customerStatsQuery = "SELECT 
                              COUNT(DISTINCT customer_id) as total_customers,
                              COUNT(*) / COUNT(DISTINCT customer_id) as avg_orders_per_customer
                           FROM invoice 
                           WHERE invoice_date BETWEEN ? AND ? AND customer_id IS NOT NULL";
    
    $stmt = mysqli_prepare($con, $customerStatsQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $customerStatsResult = mysqli_stmt_get_result($stmt);
    $customerStats = mysqli_fetch_assoc($customerStatsResult);
    
    // Get top products for the year
    $topProductsQuery = "SELECT 
                            s.product as product_name,
                            SUM(s.qty) as qty_sold,
                            SUM(s.amount) as revenue
                         FROM sales s
                         JOIN invoice i ON s.invoice_number = i.invoice_number
                         WHERE i.invoice_date BETWEEN ? AND ?
                         GROUP BY s.product
                         ORDER BY revenue DESC
                         LIMIT 15";
    
    $stmt = mysqli_prepare($con, $topProductsQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $topProductsResult = mysqli_stmt_get_result($stmt);
    
    $topProducts = [];
    while ($row = mysqli_fetch_assoc($topProductsResult)) {
        $topProducts[] = $row;
    }
    
    // Calculate key metrics
    $totalExpenses = $expenseData['expenses'] ?? 0;
    $netProfit = ($invoiceData['profit'] ?? 0) - $totalExpenses;
    $profitMargin = ($invoiceData['cash_in'] ?? 0) > 0 ? (($invoiceData['profit'] ?? 0) / ($invoiceData['cash_in'] ?? 1)) * 100 : 0;
    $year = date('Y', strtotime($startDate));
    
    // Prepare response
    $response = [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'year' => $year,
        'invoice_count' => $invoiceData['invoice_count'] ?? 0,
        'cash_in' => $invoiceData['cash_in'] ?? 0,
        'profit' => $invoiceData['profit'] ?? 0,
        'expenses' => $totalExpenses,
        'bank_deposit' => $bankData['bank_deposit'] ?? 0,
        'net_profit' => $netProfit,
        'avg_invoice_value' => $invoiceData['avg_invoice_value'] ?? 0,
        'profit_margin' => $profitMargin,
        'total_customers' => $customerStats['total_customers'] ?? 0,
        'avg_orders_per_customer' => $customerStats['avg_orders_per_customer'] ?? 0,
        'monthly_breakdown' => $monthlyBreakdown,
        'quarterly_breakdown' => $quarterlyBreakdown,
        'top_performing_months' => $topMonths,
        'top_products' => $topProducts
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