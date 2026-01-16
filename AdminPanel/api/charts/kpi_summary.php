<?php
/**
 * KPI Summary API Endpoint
 * Returns summary data for all KPI cards on the analytics dashboard
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    require_once(__DIR__ . '/../../../inc/config.php');
    
    if (!$con) {
        throw new Exception('Database connection failed: ' . mysqli_connect_error());
    }

    // Get date range parameters
    $startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
    $endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

    // Calculate previous period for trend comparison
    $dateDiff = strtotime($endDate) - strtotime($startDate);
    $prevStartDate = date('Y-m-d', strtotime($startDate) - $dateDiff - 86400);
    $prevEndDate = date('Y-m-d', strtotime($startDate) - 86400);

    $response = [
        'success' => true,
        'date_range' => [
            'start' => $startDate,
            'end' => $endDate
        ],
        'profit' => getProfitData($con, $startDate, $endDate, $prevStartDate, $prevEndDate),
        'sales' => getSalesData($con, $startDate, $endDate, $prevStartDate, $prevEndDate),
        'expenses' => getExpensesData($con, $startDate, $endDate, $prevStartDate, $prevEndDate),
        'salary' => getSalaryData($con, $startDate, $endDate),
        'stock' => getStockData($con, $startDate, $endDate),
        'printer' => getPrinterData($con, $startDate, $endDate),
        'suppliers' => getSuppliersData($con),
        'bank' => getBankData($con),
        'customers' => getCustomersData($con)
    ];

    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}


function getProfitData($con, $startDate, $endDate, $prevStartDate, $prevEndDate) {
    // Current period profit
    $sql = "SELECT SUM(profit) as gross_profit, SUM(advance) as total_sales 
            FROM invoice 
            WHERE invoice_date BETWEEN '$startDate' AND '$endDate' 
            AND is_deleted = 0";
    $result = mysqli_query($con, $sql);
    $current = mysqli_fetch_assoc($result);
    
    // Get expenses for net profit
    $expenseSql = "SELECT SUM(amount) as total_expenses 
                   FROM expenses 
                   WHERE DATE(expense_date) BETWEEN '$startDate' AND '$endDate'";
    $expenseResult = mysqli_query($con, $expenseSql);
    $expenses = mysqli_fetch_assoc($expenseResult);
    
    // Salary expenses
    $salarySql = "SELECT SUM(amount) as total_salary 
                  FROM salary 
                  WHERE date BETWEEN '$startDate' AND '$endDate'";
    $salaryResult = mysqli_query($con, $salarySql);
    $salary = mysqli_fetch_assoc($salaryResult);
    
    // Petty cash
    $pettySql = "SELECT SUM(amount) as total_petty 
                 FROM pettycash 
                 WHERE date BETWEEN '$startDate' AND '$endDate'";
    $pettyResult = mysqli_query($con, $pettySql);
    $petty = mysqli_fetch_assoc($pettyResult);
    
    $grossProfit = floatval($current['gross_profit'] ?? 0);
    $totalExpenses = floatval($expenses['total_expenses'] ?? 0) + 
                     floatval($salary['total_salary'] ?? 0) + 
                     floatval($petty['total_petty'] ?? 0);
    $netProfit = $grossProfit - $totalExpenses;
    
    // Previous period profit for trend
    $prevSql = "SELECT SUM(profit) as gross_profit FROM invoice 
                WHERE invoice_date BETWEEN '$prevStartDate' AND '$prevEndDate' 
                AND is_deleted = 0";
    $prevResult = mysqli_query($con, $prevSql);
    $prev = mysqli_fetch_assoc($prevResult);
    $prevProfit = floatval($prev['gross_profit'] ?? 0);
    
    $trend = $prevProfit > 0 ? (($grossProfit - $prevProfit) / $prevProfit) * 100 : 0;
    
    return [
        'total' => $grossProfit,
        'gross' => $grossProfit,
        'net' => $netProfit,
        'trend' => $trend
    ];
}

function getSalesData($con, $startDate, $endDate, $prevStartDate, $prevEndDate) {
    // Current period sales (cash only - advance payments)
    $sql = "SELECT SUM(advance) as total_cash, COUNT(*) as invoice_count 
            FROM invoice 
            WHERE invoice_date BETWEEN '$startDate' AND '$endDate' 
            AND is_deleted = 0";
    $result = mysqli_query($con, $sql);
    $current = mysqli_fetch_assoc($result);
    
    $totalCash = floatval($current['total_cash'] ?? 0);
    $invoiceCount = intval($current['invoice_count'] ?? 0);
    $average = $invoiceCount > 0 ? $totalCash / $invoiceCount : 0;
    
    // Previous period
    $prevSql = "SELECT SUM(advance) as total_cash FROM invoice 
                WHERE invoice_date BETWEEN '$prevStartDate' AND '$prevEndDate' 
                AND is_deleted = 0";
    $prevResult = mysqli_query($con, $prevSql);
    $prev = mysqli_fetch_assoc($prevResult);
    $prevTotal = floatval($prev['total_cash'] ?? 0);
    
    $trend = $prevTotal > 0 ? (($totalCash - $prevTotal) / $prevTotal) * 100 : 0;
    
    return [
        'total' => $totalCash,
        'invoice_count' => $invoiceCount,
        'average' => $average,
        'trend' => $trend
    ];
}

function getExpensesData($con, $startDate, $endDate, $prevStartDate, $prevEndDate) {
    // Current period expenses
    $sql = "SELECT SUM(amount) as total, SUM(amount_paid) as paid 
            FROM expenses 
            WHERE DATE(expense_date) BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($con, $sql);
    $current = mysqli_fetch_assoc($result);
    
    $total = floatval($current['total'] ?? 0);
    $paid = floatval($current['paid'] ?? 0);
    $pending = $total - $paid;
    
    // Previous period
    $prevSql = "SELECT SUM(amount) as total FROM expenses 
                WHERE DATE(expense_date) BETWEEN '$prevStartDate' AND '$prevEndDate'";
    $prevResult = mysqli_query($con, $prevSql);
    $prev = mysqli_fetch_assoc($prevResult);
    $prevTotal = floatval($prev['total'] ?? 0);
    
    $trend = $prevTotal > 0 ? (($total - $prevTotal) / $prevTotal) * 100 : 0;
    
    return [
        'total' => $total,
        'paid' => $paid,
        'pending' => $pending,
        'trend' => $trend
    ];
}

function getSalaryData($con, $startDate, $endDate) {
    $sql = "SELECT SUM(amount) as total, COUNT(DISTINCT emp_id) as employees, COUNT(*) as payments 
            FROM salary 
            WHERE date BETWEEN '$startDate' AND '$endDate'";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    
    // Use absolute value to ensure positive numbers (salary amounts might be stored as negative)
    $totalAmount = floatval($data['total'] ?? 0);
    
    return [
        'total' => abs($totalAmount),  // Convert to positive
        'employees' => intval($data['employees'] ?? 0),
        'payments' => intval($data['payments'] ?? 0)
    ];
}

function getStockData($con, $startDate, $endDate) {
    // Get stock movement from sales
    $sql = "SELECT COUNT(DISTINCT s.product) as products, SUM(CAST(s.qty AS DECIMAL(10,3))) as qty_moved
            FROM sales s
            INNER JOIN invoice i ON s.invoice_number = i.invoice_number
            WHERE i.invoice_date BETWEEN '$startDate' AND '$endDate'
            AND i.is_deleted = 0";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_assoc($result);
    
    return [
        'items_moved' => intval($data['products'] ?? 0),
        'products' => intval($data['products'] ?? 0),
        'qty' => floatval($data['qty_moved'] ?? 0)
    ];
}

function getPrinterData($con, $startDate, $endDate) {
    // Connect to printer counter database - wrapped in try-catch to handle missing database/tables
    try {
        global $server;
        
        // Check if printer database credentials are configured
        if (!defined('PRINTER_DB_NAME')) {
            // Printer DB not configured, return zeros
            return [
                'total_prints' => 0,
                'cost' => 0,
                'revenue' => 0
            ];
        }
        
        $printerDB = @mysqli_connect($server, "srijayalk_shopprintercounter", "srijayalk_shopprintercounter", "srijayalk_shopprintercounter");
        
        if (!$printerDB) {
            // Printer DB connection failed, return zeros
            return [
                'total_prints' => 0,
                'cost' => 0,
                'revenue' => 0
            ];
        }
        
        $sql = "SELECT SUM(count) as total_prints, SUM(cost) as total_cost 
                FROM printer_counter_count 
                WHERE date BETWEEN '$startDate' AND '$endDate'";
        $result = @mysqli_query($printerDB, $sql);
        
        if (!$result) {
            // Table doesn't exist, try alternative table name
            $sql = "SELECT SUM(count) as total_prints, SUM(cost) as total_cost 
                    FROM `count` 
                    WHERE date BETWEEN '$startDate' AND '$endDate'";
            $result = @mysqli_query($printerDB, $sql);
        }
        
        if (!$result) {
            // Both queries failed, return zeros
            mysqli_close($printerDB);
            return [
                'total_prints' => 0,
                'cost' => 0,
                'revenue' => 0
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        mysqli_close($printerDB);
        
        return [
            'total_prints' => intval($data['total_prints'] ?? 0),
            'cost' => floatval($data['total_cost'] ?? 0),
            'revenue' => floatval($data['total_cost'] ?? 0)
        ];
        
    } catch (Exception $e) {
        // Any error in printer data fetch should not crash the entire API
        error_log("Printer data fetch error: " . $e->getMessage());
        return [
            'total_prints' => 0,
            'cost' => 0,
            'revenue' => 0
        ];
    }
}

function getSuppliersData($con) {
    try {
        // Get total suppliers and those with credit balance
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN credit_balance > 0 THEN 1 ELSE 0 END) as with_credit,
                    SUM(credit_balance) as total_credit
                FROM suppliers";
        $result = @mysqli_query($con, $sql);
        
        if (!$result) {
            // Table doesn't exist or query failed
            return [
                'total' => 0,
                'with_credit' => 0,
                'credit_balance' => 0
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        return [
            'total' => intval($data['total'] ?? 0),
            'with_credit' => intval($data['with_credit'] ?? 0),
            'credit_balance' => floatval($data['total_credit'] ?? 0)
        ];
    } catch (Exception $e) {
        error_log("Suppliers data fetch error: " . $e->getMessage());
        return [
            'total' => 0,
            'with_credit' => 0,
            'credit_balance' => 0
        ];
    }
}

function getBankData($con) {
    try {
        // Get total bank balance
        $sql = "SELECT SUM(amount) as total_balance, COUNT(*) as accounts 
                FROM accounts 
                WHERE account_type = 'bank'";
        $result = @mysqli_query($con, $sql);
        
        if (!$result) {
            // Table doesn't exist or query failed
            return [
                'total_balance' => 0,
                'accounts' => 0,
                'cash_in_hand' => 0,
                'trend' => 0
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        // Get cash in hand
        $cashSql = "SELECT amount as cash_in_hand 
                    FROM accounts 
                    WHERE account_name = 'cash_in_hand' 
                    LIMIT 1";
        $cashResult = @mysqli_query($con, $cashSql);
        $cashData = $cashResult ? mysqli_fetch_assoc($cashResult) : [];
        
        return [
            'total_balance' => floatval($data['total_balance'] ?? 0),
            'accounts' => intval($data['accounts'] ?? 0),
            'cash_in_hand' => floatval($cashData['cash_in_hand'] ?? 0),
            'trend' => 0
        ];
    } catch (Exception $e) {
        error_log("Bank data fetch error: " . $e->getMessage());
        return [
            'total_balance' => 0,
            'accounts' => 0,
            'cash_in_hand' => 0,
            'trend' => 0
        ];
    }
}

function getCustomersData($con) {
    try {
        // Get total customers and credit information
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN customer_extra_fund > 0 THEN 1 ELSE 0 END) as with_credit,
                    SUM(customer_extra_fund) as total_credit
                FROM customers";
        $result = @mysqli_query($con, $sql);
        
        if (!$result) {
            // Table doesn't exist, return zeros
            return [
                'total' => 0,
                'with_credit' => 0,
                'credit_amount' => 0
            ];
        }
        
        $data = mysqli_fetch_assoc($result);
        
        // Get customers with pending invoices (credit sales)
        $creditSql = "SELECT COUNT(DISTINCT customer_id) as credit_customers
                      FROM invoice 
                      WHERE balance > 0 AND customer_id > 0 AND is_deleted = 0";
        $creditResult = @mysqli_query($con, $creditSql);
        $creditData = $creditResult ? mysqli_fetch_assoc($creditResult) : [];
        
        // Get total credit amount from pending invoices
        $creditAmountSql = "SELECT SUM(balance) as credit_amount
                            FROM invoice 
                            WHERE balance > 0 AND customer_id > 0 AND is_deleted = 0";
        $creditAmountResult = @mysqli_query($con, $creditAmountSql);
        $creditAmountData = $creditAmountResult ? mysqli_fetch_assoc($creditAmountResult) : [];
        
        return [
            'total' => intval($data['total'] ?? 0),
            'with_credit' => intval($creditData['credit_customers'] ?? 0),
            'credit_amount' => floatval($creditAmountData['credit_amount'] ?? 0)
        ];
    } catch (Exception $e) {
        error_log("Customers data fetch error: " . $e->getMessage());
        return [
            'total' => 0,
            'with_credit' => 0,
            'credit_amount' => 0
        ];
    }
}
?>
