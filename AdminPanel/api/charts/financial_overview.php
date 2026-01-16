<?php
/**
 * Financial Overview API Endpoint
 * Returns income, expenses, and profit data for bar chart
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');
$view = isset($_GET['view']) ? $_GET['view'] : 'daily';

$labels = [];
$income = [];
$expenses = [];
$profit = [];

$dateDiff = (strtotime($endDate) - strtotime($startDate)) / 86400;

// Determine grouping based on date range
if ($view === 'monthly' || $dateDiff > 90) {
    $groupBy = 'month';
    $dateFormat = '%Y-%m';
    $displayFormat = 'M Y';
} else if ($view === 'weekly' || $dateDiff > 30) {
    $groupBy = 'week';
    $dateFormat = '%Y-%u';
    $displayFormat = 'Week %W, %Y';
} else {
    $groupBy = 'day';
    $dateFormat = '%Y-%m-%d';
    $displayFormat = 'M d';
}

// Get income data (from invoices)
$incomeQuery = "SELECT DATE_FORMAT(invoice_date, '$dateFormat') as period, 
                       SUM(advance) as income, 
                       SUM(profit) as profit
                FROM invoice 
                WHERE invoice_date BETWEEN '$startDate' AND '$endDate'
                AND is_deleted = 0
                GROUP BY period
                ORDER BY invoice_date";
$incomeResult = mysqli_query($con, $incomeQuery);

$incomeData = [];
while ($row = mysqli_fetch_assoc($incomeResult)) {
    $incomeData[$row['period']] = [
        'income' => floatval($row['income']),
        'profit' => floatval($row['profit'])
    ];
}

// Get expenses data
$expenseQuery = "SELECT DATE_FORMAT(expense_date, '$dateFormat') as period, 
                        SUM(amount) as expenses
                 FROM expenses 
                 WHERE DATE(expense_date) BETWEEN '$startDate' AND '$endDate'
                 GROUP BY period
                 ORDER BY expense_date";
$expenseResult = mysqli_query($con, $expenseQuery);

$expenseData = [];
while ($row = mysqli_fetch_assoc($expenseResult)) {
    $expenseData[$row['period']] = floatval($row['expenses']);
}

// Get salary data
$salaryQuery = "SELECT DATE_FORMAT(date, '$dateFormat') as period, 
                       SUM(amount) as salary
                FROM salary 
                WHERE date BETWEEN '$startDate' AND '$endDate'
                GROUP BY period
                ORDER BY date";
$salaryResult = mysqli_query($con, $salaryQuery);

while ($row = mysqli_fetch_assoc($salaryResult)) {
    if (!isset($expenseData[$row['period']])) {
        $expenseData[$row['period']] = 0;
    }
    $expenseData[$row['period']] += floatval($row['salary']);
}

// Get petty cash data
$pettyQuery = "SELECT DATE_FORMAT(date, '$dateFormat') as period, 
                      SUM(amount) as petty
               FROM pettycash 
               WHERE date BETWEEN '$startDate' AND '$endDate'
               GROUP BY period
               ORDER BY date";
$pettyResult = mysqli_query($con, $pettyQuery);

while ($row = mysqli_fetch_assoc($pettyResult)) {
    if (!isset($expenseData[$row['period']])) {
        $expenseData[$row['period']] = 0;
    }
    $expenseData[$row['period']] += floatval($row['petty']);
}

// Merge all periods and sort
$allPeriods = array_unique(array_merge(array_keys($incomeData), array_keys($expenseData)));
sort($allPeriods);

// Limit to last 12 periods if too many
if (count($allPeriods) > 12) {
    $allPeriods = array_slice($allPeriods, -12);
}

foreach ($allPeriods as $period) {
    // Format label
    if ($groupBy === 'day') {
        $labels[] = date('M d', strtotime($period));
    } else if ($groupBy === 'week') {
        $parts = explode('-', $period);
        $labels[] = 'Week ' . $parts[1];
    } else {
        $labels[] = date('M Y', strtotime($period . '-01'));
    }
    
    $periodIncome = $incomeData[$period]['income'] ?? 0;
    $periodExpense = $expenseData[$period] ?? 0;
    $periodProfit = ($incomeData[$period]['profit'] ?? 0) - $periodExpense;
    
    $income[] = round($periodIncome, 2);
    $expenses[] = round($periodExpense, 2);
    $profit[] = round($periodProfit, 2);
}

// If no data, return empty with sample structure
if (empty($labels)) {
    $labels = ['No Data'];
    $income = [0];
    $expenses = [0];
    $profit = [0];
}

echo json_encode([
    'labels' => $labels,
    'income' => $income,
    'expenses' => $expenses,
    'profit' => $profit
]);
?>
