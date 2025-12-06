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
            
            // Note: Adjust table/column names based on your actual schema if needed
            // Assuming 'invoices' table has 'invoice_date' and 'final_amount'
            $sql = "SELECT SUM(final_amount) as total, COUNT(*) as count FROM invoices WHERE invoice_date = '$today'";
            $query = mysqli_query($con, $sql);
            
            if($query) {
                $row = mysqli_fetch_assoc($query);
                $total = number_format($row['total'] ?? 0, 2);
                $count = $row['count'] ?? 0;
                
                $response = "💰 <b>Sales Update ($today)</b>\n\n";
                $response .= "📈 Total Revenue: <b>Rs. $total</b>\n";
                $response .= "🧾 Invoices Issued: <b>$count</b>";
            } else {
                $response = "⚠️ Error fetching sales data.";
            }
            break;

        case '/stock':
            // Low Stock Count (Assuming 'product_qty' column)
            $low_stock_limit = 5; 
            $sql = "SELECT COUNT(*) as low_count FROM products WHERE product_qty <= $low_stock_limit";
            $row = mysqli_fetch_assoc(mysqli_query($con, $sql));
            
            // Total Products
            $sql2 = "SELECT COUNT(*) as total_count FROM products";
            $row2 = mysqli_fetch_assoc(mysqli_query($con, $sql2));

            $response = "📦 <b>Inventory Snapshot</b>\n\n";
            $response .= "🔢 Total Products: <b>" . ($row2['total_count'] ?? 0) . "</b>\n";
            $response .= "⚠️ Low Stock Items: <b>" . ($row['low_count'] ?? 0) . "</b>\n";
            
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
