<?php
/**
 * Monthly Report API
 * 
 * Get monthly business report
 * GET /api/v1/reports/monthly.php?month=2025-10
 * 
 * Admin only
 */

require_once('../config.php');
require_once('../ApiResponse.php');
require_once('../ApiAuth.php');
require_once('../../../inc/config.php');

// Authenticate and require admin role
$user = ApiAuth::requireAuth();
if ($user['employee_role'] !== 'Admin') {
    ApiResponse::forbidden('Admin access required');
}

// Only allow GET method
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

try {
    // Get month parameter (default to current month)
    $month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
    
    // Validate month format
    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        ApiResponse::error('Invalid month format. Use Y-m format (e.g., 2025-10)', 422);
    }
    
    $startDate = $month . '-01';
    $endDate = date('Y-m-t', strtotime($startDate));
    
    // Get monthly invoice data
    $invoiceQuery = "SELECT 
                        COUNT(*) as invoice_count,
                        COALESCE(SUM(advance), 0) as cash_in,
                        COALESCE(SUM(profit), 0) as profit,
                        COALESCE(AVG(advance), 0) as avg_invoice_value,
                        COALESCE(SUM(CASE WHEN full_paid = 1 THEN 1 ELSE 0 END), 0) as paid_count,
                        COALESCE(SUM(CASE WHEN full_paid = 0 THEN 1 ELSE 0 END), 0) as pending_count
                     FROM invoice 
                     WHERE invoice_date BETWEEN ? AND ?";
    
    $stmt = mysqli_prepare($con, $invoiceQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $invoiceResult = mysqli_stmt_get_result($stmt);
    $invoiceData = mysqli_fetch_assoc($invoiceResult);
    
    // Get expense breakdown
    $expenseQuery = "SELECT 
                        transaction_type,
                        COALESCE(SUM(amount), 0) as amount
                     FROM transaction_log 
                     WHERE transaction_date BETWEEN ? AND ?
                     AND (transaction_type = 'Petty Cash' 
                          OR transaction_type = 'Raw Item Purchase' 
                          OR transaction_type LIKE 'Fall OneTimeProduct Cost from%' 
                          OR transaction_type = 'Salary Payment')
                     GROUP BY transaction_type";
    
    $stmt = mysqli_prepare($con, $expenseQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $expenseResult = mysqli_stmt_get_result($stmt);
    
    $expenseBreakdown = [];
    $totalExpenses = 0;
    while ($row = mysqli_fetch_assoc($expenseResult)) {
        $expenseBreakdown[$row['transaction_type']] = abs((float)$row['amount']);
        $totalExpenses += abs((float)$row['amount']);
    }
    
    // Get daily breakdown
    $dailyQuery = "SELECT 
                      invoice_date,
                      COUNT(*) as count,
                      COALESCE(SUM(advance), 0) as revenue,
                      COALESCE(SUM(profit), 0) as profit
                   FROM invoice 
                   WHERE invoice_date BETWEEN ? AND ?
                   GROUP BY invoice_date
                   ORDER BY invoice_date ASC";
    
    $stmt = mysqli_prepare($con, $dailyQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $dailyResult = mysqli_stmt_get_result($stmt);
    
    $dailyBreakdown = [];
    while ($row = mysqli_fetch_assoc($dailyResult)) {
        $dailyBreakdown[] = [
            'date' => $row['invoice_date'],
            'invoice_count' => (int)$row['count'],
            'revenue' => (float)$row['revenue'],
            'profit' => (float)$row['profit']
        ];
    }
    
    // Get bank deposits
    $bankQuery = "SELECT COALESCE(SUM(amount), 0) as bank_deposit FROM bank_deposits WHERE deposit_date BETWEEN ? AND ?";
    $stmt = mysqli_prepare($con, $bankQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $bankResult = mysqli_stmt_get_result($stmt);
    $bankData = mysqli_fetch_assoc($bankResult);
    
    // Calculate net profit
    $netProfit = (float)$invoiceData['profit'] - $totalExpenses;
    
    // Return monthly report
    ApiResponse::success('Monthly report retrieved successfully', [
        'month' => $month,
        'period' => [
            'start_date' => $startDate,
            'end_date' => $endDate
        ],
        'sales' => [
            'invoice_count' => (int)$invoiceData['invoice_count'],
            'cash_in' => (float)$invoiceData['cash_in'],
            'gross_profit' => (float)$invoiceData['profit'],
            'avg_invoice_value' => (float)$invoiceData['avg_invoice_value'],
            'paid_invoices' => (int)$invoiceData['paid_count'],
            'pending_invoices' => (int)$invoiceData['pending_count']
        ],
        'expenses' => [
            'total' => $totalExpenses,
            'breakdown' => $expenseBreakdown
        ],
        'banking' => [
            'deposits' => (float)$bankData['bank_deposit']
        ],
        'summary' => [
            'net_profit' => $netProfit,
            'profitability_ratio' => $invoiceData['cash_in'] > 0 
                ? round(($netProfit / $invoiceData['cash_in']) * 100, 2) 
                : 0
        ],
        'daily_breakdown' => $dailyBreakdown
    ]);
    
} catch (Exception $e) {
    if (API_DEBUG) {
        ApiResponse::error($e->getMessage(), 500);
    } else {
        ApiResponse::error('Failed to generate monthly report', 500);
    }
}
