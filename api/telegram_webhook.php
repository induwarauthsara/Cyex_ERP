<?php
/**
 * telegram_webhook.php
 * Receives incoming updates from Telegram (Commands & Messages)
 */

// Include Config & Service
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/TelegramService.php');

// 1. Get incoming update
$content = file_get_contents("php://input");
// DEBUG LOGGING
file_put_contents(__DIR__ . '/webhook_debug_log.txt', date('Y-m-d H:i:s') . " - " . $content . "\n", FILE_APPEND);

$update = json_decode($content, true);

if (!$update) {
    echo "Srijaya ERP Telegram Webhook is Active.";
    exit;
}

// 2. Initialize Service
$tg = new TelegramService($con);

// 3. Extract Message Details
$message = $update['message'] ?? null;
if (!$message) exit;

$chat_id = $message['chat']['id'];
$text = $message['text'] ?? '';
$message_id = $message['message_id'];
$thread_id = $message['message_thread_id'] ?? null;

// Check DM Permission (Private Chat Block)
$is_private = (isset($message['chat']['type']) && $message['chat']['type'] === 'private');
if ($is_private) {
    $res = mysqli_query($con, "SELECT setting_value FROM telegram_config WHERE setting_key = 'allow_dm'");
    $setting = mysqli_fetch_assoc($res);
    // Exit if allow_dm is not '1'
    if (!$setting || $setting['setting_value'] !== '1') {
         exit; 
    }
}

// Only process commands starting with /
if (strpos($text, '/') === 0) {
    
    // Clean command (handle /command@BotName)
    $parts = explode(' ', $text);
    $command = strtolower($parts[0]);
    if (strpos($command, '@') !== false) {
        $command = substr($command, 0, strpos($command, '@'));
    }

    $response = "";

    switch ($command) {
        case '/start':
            $response = "👋 <b>Hello! I am Srijaya ERP Bot.</b>\n\n";
            $response .= "I am ready to serve you. Try these commands:\n\n";
            $response .= "🔹 /sales - Today's Sales Report\n";
            $response .= "🔹 /stock - Stock Overview\n";
            break;

        case '/sales':
            // Calculate Today's Sales
            $today = date('Y-m-d');
            
            // Correct Table: 'invoice' (singular)
            // Revenue = total - discount
            // Date Check: DATE(invoice_date) because it's datetime
            $sql = "SELECT SUM(total - discount) as revenue, COUNT(*) as count FROM invoice WHERE DATE(invoice_date) = '$today'";
            $query = mysqli_query($con, $sql);
            
            if($query) {
                $row = mysqli_fetch_assoc($query);
                $revenue = number_format($row['revenue'] ?? 0, 2);
                $count = $row['count'] ?? 0;
                
                $response = "💰 <b>Sales Update ($today)</b>\n\n";
                $response .= "📈 Total Revenue: <b>Rs. $revenue</b>\n";
                $response .= "🧾 Invoices Issued: <b>$count</b>";
            } else {
                $response = "⚠️ Error fetching sales data: " . mysqli_error($con);
            }
            break;

        case '/stock':
            // Low Stock Count (Using product_batch table)
            $low_stock_limit = 5; 
            $sql = "SELECT COUNT(*) as low_count FROM product_batch WHERE quantity <= $low_stock_limit AND quantity > 0";
            $row = mysqli_fetch_assoc(mysqli_query($con, $sql));
            
            // Total Unique Products
            $sql2 = "SELECT COUNT(*) as total_count FROM products WHERE active_status = 1";
            $row2 = mysqli_fetch_assoc(mysqli_query($con, $sql2));

            $response = "📦 <b>Inventory Snapshot</b>\n\n";
            $response .= "🔢 unique Products: <b>" . ($row2['total_count'] ?? 0) . "</b>\n";
            $response .= "⚠️ Low Stock Batches: <b>" . ($row['low_count'] ?? 0) . "</b>\n";
            
            if(($row['low_count'] ?? 0) > 0) {
                $response .= "\n<i>View 'Inventory Alerts' topic for details.</i>";
            }
            break;
    }

    // Send Reply
    if ($response) {
        $tg->sendToChat($chat_id, $response, $message_id, $thread_id);
    }
}
?>
