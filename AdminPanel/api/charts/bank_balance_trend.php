<?php
/**
 * Bank Balance Trend API Endpoint
 * Returns bank balance changes over time
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once(__DIR__ . '/../../../inc/config.php');

$startDate = isset($_GET['start_date']) ? mysqli_real_escape_string($con, $_GET['start_date']) : date('Y-m-d');
$endDate = isset($_GET['end_date']) ? mysqli_real_escape_string($con, $_GET['end_date']) : date('Y-m-d');

// Get all bank accounts
$accountsSql = "SELECT account_id, account_name, amount 
                FROM accounts 
                WHERE account_type = 'bank' OR account_name = 'cash_in_hand'
                ORDER BY amount DESC";

$accountsResult = @mysqli_query($con, $accountsSql);

$accounts = [];
$balances = [];
$colors = [];

if ($accountsResult && mysqli_num_rows($accountsResult) > 0) {
    $colorPalette = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
    $index = 0;
    
    while ($row = mysqli_fetch_assoc($accountsResult)) {
        $accounts[] = $row['account_name'];
        $balances[] = round(floatval($row['amount']), 2);
        $colors[] = $colorPalette[$index % count($colorPalette)];
        $index++;
    }
}

// If no data
if (empty($accounts)) {
    $accounts = ['No Bank Accounts'];
    $balances = [0];
    $colors = ['#64748b'];
}

// Get transaction trend for the period
$labels = [];
$deposits = [];
$withdrawals = [];

$dateDiff = (strtotime($endDate) - strtotime($startDate)) / 86400;

if ($dateDiff > 90) {
    $dateFormat = '%Y-%m';
} else if ($dateDiff > 14) {
    $dateFormat = '%Y-%u';
} else {
    $dateFormat = '%Y-%m-%d';
}

// Get bank transactions grouped by date
$transactionSql = "SELECT 
                    DATE_FORMAT(transaction_date, '$dateFormat') as period,
                    SUM(CASE WHEN transaction_type = 'deposit' THEN amount ELSE 0 END) as total_deposits,
                    SUM(CASE WHEN transaction_type = 'withdrawal' THEN amount ELSE 0 END) as total_withdrawals
                FROM bank_transactions
                WHERE transaction_date BETWEEN '$startDate' AND '$endDate'
                GROUP BY period
                ORDER BY transaction_date";

$transactionResult = @mysqli_query($con, $transactionSql);

if ($transactionResult && mysqli_num_rows($transactionResult) > 0) {
    while ($row = mysqli_fetch_assoc($transactionResult)) {
        if ($dateDiff <= 14) {
            $labels[] = date('M d', strtotime($row['period']));
        } else if ($dateDiff <= 90) {
            $parts = explode('-', $row['period']);
            $labels[] = 'W' . $parts[1];
        } else {
            $labels[] = date('M Y', strtotime($row['period'] . '-01'));
        }
        $deposits[] = round(floatval($row['total_deposits']), 2);
        $withdrawals[] = round(floatval($row['total_withdrawals']), 2);
    }
}

echo json_encode([
    'accounts' => $accounts,
    'balances' => $balances,
    'colors' => $colors,
    'trend' => [
        'labels' => $labels,
        'deposits' => $deposits,
        'withdrawals' => $withdrawals
    ]
]);
?>
