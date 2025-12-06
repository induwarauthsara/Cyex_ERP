<?php
/**
 * Expenses Summary/Dashboard API
 * GET /api/v1/expenses/summary.php
 * 
 * Returns comprehensive dashboard data including:
 * - Total expenses (current month, last month, year-to-date)
 * - Breakdown by category
 * - Top spending categories
 * - Upcoming recurring payments
 * - Payment status overview
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed', 405);
}

// Verify authentication
$auth = ApiAuth::authenticate();
if (!$auth['success']) {
    ApiResponse::error($auth['message'], 401);
}

// Ensure database connection is available
if (!isset($conn)) {
    if (isset($con)) {
        $conn = $con;
    } else {
        // Attempt to load main config if not available
        require_once __DIR__ . '/../../../inc/config.php';
        if (isset($con)) {
            $conn = $con;
        }
    }
}

if (!isset($conn)) {
    ApiResponse::serverError("Database connection failed - conn not set");
}

try {
    $currentDate = date('Y-m-d');
    $currentMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));
    $currentYear = date('Y');
    
    // 1. Total Expenses Overview
    $totalsSql = "SELECT 
                    COALESCE(SUM(CASE 
                        WHEN DATE_FORMAT(expense_date, '%Y-%m') = ? 
                        THEN amount ELSE 0 END), 0) as current_month_total,
                    COALESCE(SUM(CASE 
                        WHEN DATE_FORMAT(expense_date, '%Y-%m') = ? 
                        THEN amount_paid ELSE 0 END), 0) as current_month_paid,
                    COALESCE(SUM(CASE 
                        WHEN DATE_FORMAT(expense_date, '%Y-%m') = ? 
                        THEN amount ELSE 0 END), 0) as last_month_total,
                    COALESCE(SUM(CASE 
                        WHEN YEAR(expense_date) = ? 
                        THEN amount ELSE 0 END), 0) as year_to_date_total,
                    COALESCE(SUM(CASE 
                        WHEN YEAR(expense_date) = ? 
                        THEN amount_paid ELSE 0 END), 0) as year_to_date_paid,
                    COALESCE(SUM(amount), 0) as all_time_total,
                    COUNT(CASE 
                        WHEN DATE_FORMAT(expense_date, '%Y-%m') = ? 
                        THEN 1 END) as current_month_count
                  FROM expenses";
    
    $totalsStmt = $conn->prepare($totalsSql);
    $totalsStmt->bind_param("ssssss", $currentMonth, $currentMonth, $lastMonth, $currentYear, $currentYear, $currentMonth);
    $totalsStmt->execute();
    $totalsResult = $totalsStmt->get_result()->fetch_assoc();
    $totalsStmt->close();
    
    // Calculate month-over-month change
    $monthChange = 0;
    if ($totalsResult['last_month_total'] > 0) {
        $monthChange = (($totalsResult['current_month_total'] - $totalsResult['last_month_total']) 
                       / $totalsResult['last_month_total']) * 100;
    }
    
    // 2. Category Breakdown
    $filterStart = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : null;
    $filterEnd = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : null;

    if ($filterStart && $filterEnd) {
        // Filtered Query
        $categorySql = "SELECT 
                            ec.category_id,
                            ec.category_name,
                            ec.color_code,
                            ec.icon,
                            COALESCE(SUM(e.amount), 0) as total_amount,
                            COUNT(e.expense_id) as transaction_count,
                            0 as percentage -- Calculated below due to dynamic total
                        FROM expense_categories ec
                        INNER JOIN expenses e ON ec.category_id = e.category_id 
                            AND DATE(e.expense_date) BETWEEN ? AND ?
                        WHERE ec.status = 1
                        GROUP BY ec.category_id, ec.category_name, ec.color_code, ec.icon
                        HAVING total_amount > 0
                        ORDER BY total_amount DESC";
        
        $categoryStmt = $conn->prepare($categorySql);
        $categoryStmt->bind_param("ss", $filterStart, $filterEnd);
    } else {
        // Default (Current Month)
        $categorySql = "SELECT 
                            ec.category_id,
                            ec.category_name,
                            ec.color_code,
                            ec.icon,
                            COALESCE(SUM(e.amount), 0) as total_amount,
                            COUNT(e.expense_id) as transaction_count,
                            0 as percentage
                        FROM expense_categories ec
                        INNER JOIN expenses e ON ec.category_id = e.category_id 
                            AND DATE_FORMAT(e.expense_date, '%Y-%m') = ?
                        WHERE ec.status = 1
                        GROUP BY ec.category_id, ec.category_name, ec.color_code, ec.icon
                        HAVING total_amount > 0
                        ORDER BY total_amount DESC";
        
        $categoryStmt = $conn->prepare($categorySql);
        $categoryStmt->bind_param("s", $currentMonth);
    }
    
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();
    
    $categoryBreakdown = [];
    $rangeTotal = 0;
    
    while ($row = $categoryResult->fetch_assoc()) {
        $categoryBreakdown[] = [
            'category_id' => (int)$row['category_id'],
            'category_name' => $row['category_name'],
            'color_code' => $row['color_code'],
            'icon' => $row['icon'],
            'total_amount' => (float)$row['total_amount'],
            'transaction_count' => (int)$row['transaction_count']
        ];
        $rangeTotal += (float)$row['total_amount'];
    }
    $categoryStmt->close();

    // Calculate percentages
    foreach ($categoryBreakdown as &$cat) {
        $cat['percentage'] = $rangeTotal > 0 ? round(($cat['total_amount'] / $rangeTotal) * 100, 2) : 0;
    }

    // 3. Top Spending Category
    $topCategory = !empty($categoryBreakdown) ? $categoryBreakdown[0] : null;
    
    // 4. Upcoming Recurring Payments (Using the view)
    $upcomingPayments = [];
    
    // Check if view exists first
    $viewCheck = $conn->query("SHOW TABLES LIKE 'v_upcoming_recurring_payments'");
    
    if ($viewCheck && $viewCheck->num_rows > 0) {
        $upcomingSql = "SELECT * FROM v_upcoming_recurring_payments 
                        WHERE days_until_due <= 30 
                        ORDER BY next_due_date ASC 
                        LIMIT 10";
        
        $upcomingStmt = $conn->query($upcomingSql);
        
        if ($upcomingStmt) {
            while ($row = $upcomingStmt->fetch_assoc()) {
                $upcomingPayments[] = [
                    'recurring_id' => (int)$row['recurring_id'],
                    'title' => $row['title'],
                    'amount' => (float)$row['amount'],
                    'next_due_date' => $row['next_due_date'],
                    'days_until_due' => (int)$row['days_until_due'],
                    'frequency' => $row['frequency'],
                    'payment_method' => $row['payment_method'],
                    'category' => [
                        'name' => $row['category_name'],
                        'color_code' => $row['color_code'],
                        'icon' => $row['icon']
                    ],
                    'status' => $row['payment_status']
                ];
            }
        }
    }
    
    // 5. Payment Status Overview (Current Month)
    $statusSql = "SELECT 
                    status,
                    COUNT(*) as count,
                    COALESCE(SUM(amount), 0) as total_amount,
                    COALESCE(SUM(amount_paid), 0) as total_paid,
                    COALESCE(SUM(amount - amount_paid), 0) as total_remaining
                  FROM expenses
                  WHERE DATE_FORMAT(expense_date, '%Y-%m') = ?
                  GROUP BY status";
    
    $statusStmt = $conn->prepare($statusSql);
    $statusStmt->bind_param("s", $currentMonth);
    $statusStmt->execute();
    $statusResult = $statusStmt->get_result();
    
    $statusOverview = [
        'paid' => ['count' => 0, 'amount' => 0, 'paid' => 0, 'remaining' => 0],
        'partial' => ['count' => 0, 'amount' => 0, 'paid' => 0, 'remaining' => 0],
        'unpaid' => ['count' => 0, 'amount' => 0, 'paid' => 0, 'remaining' => 0],
        'overdue' => ['count' => 0, 'amount' => 0, 'paid' => 0, 'remaining' => 0]
    ];
    
    while ($row = $statusResult->fetch_assoc()) {
        $statusOverview[$row['status']] = [
            'count' => (int)$row['count'],
            'amount' => (float)$row['total_amount'],
            'paid' => (float)$row['total_paid'],
            'remaining' => (float)$row['total_remaining']
        ];
    }
    $statusStmt->close();
    
    // 6. Recent Expenses (Last 5)
    $recentSql = "SELECT 
                    e.expense_id,
                    e.title,
                    e.amount,
                    e.expense_date,
                    ec.category_name,
                    ec.color_code,
                    ec.icon
                  FROM expenses e
                  INNER JOIN expense_categories ec ON e.category_id = ec.category_id
                  ORDER BY e.expense_date DESC
                  LIMIT 5";
    
    $recentResult = $conn->query($recentSql);
    $recentExpenses = [];
    
    while ($row = $recentResult->fetch_assoc()) {
        $recentExpenses[] = [
            'expense_id' => (int)$row['expense_id'],
            'title' => $row['title'],
            'amount' => (float)$row['amount'],
            'expense_date' => $row['expense_date'],
            'category' => [
                'name' => $row['category_name'],
                'color_code' => $row['color_code'],
                'icon' => $row['icon']
            ]
        ];
    }
    
    // 7. Monthly Trend
    $monthlyTrend = [];
    
    if ($filterStart && $filterEnd) {
        $trendSql = "SELECT 
                        DATE_FORMAT(expense_date, '%Y-%m') as month_key,
                        DATE_FORMAT(expense_date, '%b %Y') as month_name,
                        SUM(amount) as total_amount
                     FROM expenses
                     WHERE DATE(expense_date) BETWEEN ? AND ?
                     GROUP BY month_key, month_name
                     ORDER BY month_key ASC";
                     
        $trendStmt = $conn->prepare($trendSql);
        $trendStmt->bind_param("ss", $filterStart, $filterEnd);
        $trendStmt->execute();
        $trendResult = $trendStmt->get_result();
        
        while ($row = $trendResult->fetch_assoc()) {
            $monthlyTrend[] = [
                'month' => $row['month_name'],
                'total' => (float)$row['total_amount']
            ];
        }
        $trendStmt->close();
        
    } else {
        // Default: Current Year
        $trendSql = "SELECT 
                        DATE_FORMAT(expense_date, '%Y-%m') as month_key,
                        DATE_FORMAT(expense_date, '%M') as month_name,
                        SUM(amount) as total_amount
                     FROM expenses
                     WHERE YEAR(expense_date) = ?
                     GROUP BY month_key, month_name
                     ORDER BY month_key ASC";
        
        $trendStmt = $conn->prepare($trendSql);
        $trendStmt->bind_param("s", $currentYear);
        $trendStmt->execute();
        $trendResult = $trendStmt->get_result();
        
        // Initialize all months to 0
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];
        
        $trendData = [];
        foreach ($months as $m) {
            $trendData[$m] = 0;
        }
        
        while ($row = $trendResult->fetch_assoc()) {
            $trendData[$row['month_name']] = (float)$row['total_amount'];
        }
        
        foreach ($trendData as $month => $amount) {
            $monthlyTrend[] = [
                'month' => $month,
                'total' => $amount
            ];
        }
        $trendStmt->close();
    }
    
    // Prepare final response
    $summary = [
        'totals' => [
            'current_month' => (float)$totalsResult['current_month_total'],
            'current_month_paid' => (float)$totalsResult['current_month_paid'],
            'current_month_unpaid' => (float)($totalsResult['current_month_total'] - $totalsResult['current_month_paid']),
            'last_month' => (float)$totalsResult['last_month_total'],
            'year_to_date' => (float)$totalsResult['year_to_date_total'],
            'year_to_date_paid' => (float)$totalsResult['year_to_date_paid'],
            'year_to_date_unpaid' => (float)($totalsResult['year_to_date_total'] - $totalsResult['year_to_date_paid']),
            'all_time' => (float)$totalsResult['all_time_total'],
            'current_month_count' => (int)$totalsResult['current_month_count'],
            'month_over_month_change' => round($monthChange, 2),
            'month_over_month_trend' => $monthChange > 0 ? 'increase' : ($monthChange < 0 ? 'decrease' : 'stable')
        ],
        'category_breakdown' => $categoryBreakdown,
        'top_category' => $topCategory,
        'upcoming_payments' => $upcomingPayments,
        'status_overview' => $statusOverview,
        'recent_expenses' => $recentExpenses,
        'monthly_trend' => $monthlyTrend,
        'period' => [
            'current_month' => $currentMonth,
            'current_year' => $currentYear,
            'generated_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    ApiResponse::success(
        $summary,
        "Dashboard summary retrieved successfully",
        200
    );
    
} catch (Exception $e) {
    error_log("Summary API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to generate summary",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
