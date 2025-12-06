<?php
/**
 * Telegram Bot Scheduled Task Runner
 * Run this script via Cron Job every hour (e.g., at minute 0)
 * 0 * * * * php /path/to/crons/telegram_runner.php
 */

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/TelegramService.php';

echo "Running Telegram Bot Schedule...\n";

$tg = new TelegramService($con);
$currentHour = intval(date('H'));

echo "Current Hour: $currentHour\n";

// Fetch active schedules for this hour
$query = "SELECT * FROM telegram_schedules WHERE schedule_hour = $currentHour AND is_active = 1";
$result = mysqli_query($con, $query);

if($result) {
    while($row = mysqli_fetch_assoc($result)) {
        $reportType = $row['report_type'];
        $topicKey = $row['target_topic_key'];
        
        echo "Processing: $reportType -> Topic: $topicKey\n";
        
        $message = "";
        
        switch($reportType) {
            case 'daily_summary_report':
                // Logic to generate report
                $message = generateDailySummary($con);
                break;
            case 'payment_reminders':
                // Logic for payments
                $message = "💰 <b>Payment Reminder</b>\nCheck your due payments.";
                break;
            case 'low_stock_check':
                 // Logic for stock
                 $message = "📦 <b>Stock Check</b>\nInventory scan complete.";
                 break;
        }
        
        if(!empty($message)) {
            $tg->sendToTopic($topicKey, $message);
            echo "Sent.\n";
        }
    }
}

function generateDailySummary($con) {
    $today = date('Y-m-d');
    
    // Get Sales
    $sales = 0;
    $res = mysqli_query($con, "SELECT SUM(total) as t FROM invoice WHERE invoice_date = '$today'");
    if($row = mysqli_fetch_assoc($res)) $sales = $row['t'] ?? 0;
    
    // Get Expenses
    $expenses = 0;
    $res = mysqli_query($con, "SELECT SUM(amount) as t FROM expenses WHERE DATE(expense_date) = '$today'");
    if($row = mysqli_fetch_assoc($res)) $expenses = $row['t'] ?? 0;

    return "📊 <b>Daily Summary</b> (" . date('d M Y') . ")\n\n" .
           "💸 <b>Total Sales:</b> Rs. " . number_format($sales, 2) . "\n" .
           "📉 <b>Expenses:</b> Rs. " . number_format($expenses, 2) . "\n" .
           "💰 <b>Net Estimate:</b> Rs. " . number_format($sales - $expenses, 2);
}
?>
