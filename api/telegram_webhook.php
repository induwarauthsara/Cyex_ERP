<?php
/**
 * telegram_webhook.php
 * Receives incoming updates from Telegram (Commands & Messages)
 */

// Include Config & Service
require_once(__DIR__ . '/../inc/config.php');
require_once(__DIR__ . '/../inc/TelegramService.php');

// Set Timezone (Override if not Set in Config)
date_default_timezone_set("Asia/Colombo");

// 1. Get incoming update
$content = file_get_contents("php://input");
// DEBUG LOGGING
file_put_contents(__DIR__ . '/webhook_debug_log.txt', date('Y-m-d H:i:s') . " - " . $content . "\n", FILE_APPEND);

$update = json_decode($content, true);

if (!$update) {
    echo "$GLOBALS['ERP_COMPANY_NAME'] ERP Telegram Webhook is Active.";
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

// 4. Logic Router
$response = "";

// A. COMMANDS (Start with /)
if (strpos($text, '/') === 0) {
    
    // Clean command (handle /command@BotName)
    $parts = explode(' ', $text);
    $command = strtolower($parts[0]);
    if (strpos($command, '@') !== false) {
        $command = substr($command, 0, strpos($command, '@'));
    }

    switch ($command) {
        case '/start':
            $response = "🤖 <b>$GLOBALS['ERP_COMPANY_NAME'] ERP Assistant</b>\n";
            $response .= "<i>Your real-time business companion.</i>\n\n";
            
            $response .= "<b>📊 Analysis & Finance</b>\n";
            $response .= "/today - Daily Sales, Profit & Expenses\n";
            $response .= "/month - Monthly Overview\n";
            $response .= "/cash - Current Cash in Hand\n";
            $response .= "/pending - Outstanding Customer Credit\n\n";

            $response .= "<b>📦 Inventory & Stock</b>\n";
            $response .= "/stock - Inventory Statistics\n";
            $response .= "/lowstock - ⚠️ Low Stock Alert List\n\n";

            $response .= "<b>👥 Team & Ops</b>\n";
            $response .= "/staff - Today's Staff Attendance\n\n";

            $response .= "<i>Tap any command above to get instant insights.</i>";
            break;

        case '/topic':
        case '/id':
            $response = "🆔 <b>Topic Info</b>\n\n";
            $response .= "Chat ID: <code>$chat_id</code>\n";
            if($thread_id) {
                $response .= "Topic Thread ID: <code>$thread_id</code>\n";
                $response .= "<i>Use this ID in Admin Panel Settings.</i>";
            } else {
                $response .= "Topic Thread ID: <i>(General / No Topic)</i>\n";
                $response .= "<i>Enable Topics in Group Settings to use this feature.</i>";
            }
            break;

        case '/sales': // Alias for /today
        case '/today':
            $today = date('Y-m-d');
            
            // 1. Sales & Profit
            $sql = "SELECT SUM(total - discount) as revenue, SUM(profit) as gross_profit, COUNT(*) as count FROM invoice WHERE DATE(invoice_date) = '$today'";
            $res = mysqli_fetch_assoc(mysqli_query($con, $sql));
            $revenue = $res['revenue'] ?? 0;
            $gross_profit = $res['gross_profit'] ?? 0;
            $inv_count = $res['count'] ?? 0;

            // 2. Expenses
            $sql_exp = "SELECT SUM(amount) as total_exp FROM expenses WHERE DATE(expense_date) = '$today'";
            $res_exp = mysqli_fetch_assoc(mysqli_query($con, $sql_exp));
            $expenses = $res_exp['total_exp'] ?? 0;

            // 3. Net Profit
            $net_profit = $gross_profit - $expenses;

            // 4. Top Item
            $sql_top = "SELECT product, SUM(qty) as q FROM sales WHERE DATE(datetime) = '$today' GROUP BY product ORDER BY q DESC LIMIT 1";
            $res_top = mysqli_fetch_assoc(mysqli_query($con, $sql_top));
            $top_item = $res_top ? $res_top['product'] . " (" . (float)$res_top['q'] . ")" : "None";

            $response = "📅 <b>Daily Report ($today)</b>\n\n";
            $response .= "💰 Revenue: <b>Rs. " . number_format($revenue, 2) . "</b>\n";
            $response .= "💸 Expenses: <b>Rs. " . number_format($expenses, 2) . "</b>\n";
            $response .= "📉 Net Profit: <b>Rs. " . number_format($net_profit, 2) . "</b>\n";
            $response .= "🧾 Invoices: <b>$inv_count</b>\n";
            $response .= "🏆 Top Item: <b>$top_item</b>";
            break;

        case '/month':
            $m = date('m'); $y = date('Y');
            $month_name = date('F Y');
            
            // 1. Sales
            $sql = "SELECT SUM(total - discount) as revenue, SUM(profit) as gross_profit FROM invoice WHERE MONTH(invoice_date) = '$m' AND YEAR(invoice_date) = '$y'";
            $res = mysqli_fetch_assoc(mysqli_query($con, $sql));
            $revenue = $res['revenue'] ?? 0;
            $gross_profit = $res['gross_profit'] ?? 0;

            // 2. Expenses
            $sql_exp = "SELECT SUM(amount) as total_exp FROM expenses WHERE MONTH(expense_date) = '$m' AND YEAR(expense_date) = '$y'";
            $res_exp = mysqli_fetch_assoc(mysqli_query($con, $sql_exp));
            $expenses = $res_exp['total_exp'] ?? 0;

            $net_profit = $gross_profit - $expenses;

            $response = "📅 <b>Monthly Overview ($month_name)</b>\n\n";
            $response .= "💰 Revenue: <b>Rs. " . number_format($revenue, 2) . "</b>\n";
            $response .= "💸 Expenses: <b>Rs. " . number_format($expenses, 2) . "</b>\n";
            $response .= "📉 Net Profit: <b>Rs. " . number_format($net_profit, 2) . "</b>";
            break;

        case '/pending':
            // Outstanding Customer Credit
            $sql = "SELECT SUM(balance) as total_due, COUNT(*) as count FROM invoice WHERE balance > 0";
            $query = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($query);
            
            $response = "⏳ <b>Outstanding Credit</b>\n\n";
            $response .= "💵 Total Due: <b>Rs. " . number_format($row['total_due'] ?? 0, 2) . "</b>\n";
            $response .= "👥 Invoices with Dues: <b>" . ($row['count'] ?? 0) . "</b>\n\n";
            $response .= "<i>Use Admin Panel > Reports for details.</i>";
            break;

        case '/stock': // Summary
            $low_stock_limit = 5; 
            $sql = "SELECT COUNT(*) as low_count FROM product_batch WHERE quantity <= $low_stock_limit AND quantity > 0";
            $row = mysqli_fetch_assoc(mysqli_query($con, $sql));
            
            $sql2 = "SELECT COUNT(*) as total_count FROM products WHERE active_status = 1";
            $row2 = mysqli_fetch_assoc(mysqli_query($con, $sql2));

            $response = "📦 <b>Inventory Snapshot</b>\n\n";
            $response .= "🔢 Unique Products: <b>" . ($row2['total_count'] ?? 0) . "</b>\n";
            $response .= "⚠️ Low Stock Batches: <b>" . ($row['low_count'] ?? 0) . "</b>\n";
            $response .= "\nUse /lowstock for item details.";
            break;

        case '/lowstock': // Detailed
            $low_stock_limit = 5;
            $sql = "SELECT p.product_name, pb.quantity, pb.batch_id 
                    FROM product_batch pb 
                    JOIN products p ON pb.product_id = p.product_id 
                    WHERE pb.quantity <= $low_stock_limit AND pb.quantity > 0 
                    ORDER BY pb.quantity ASC LIMIT 10";
            $query = mysqli_query($con, $sql);
            
            $response = "⚠️ <b>Low Stock Alert (Top 10)</b>\n\n";
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
                    $response .= "🔸 " . $row['product_name'] . " (" . (float)$row['quantity'] . ")\n";
                }
            } else {
                $response .= "✅ All stock levels are healthy.";
            }
            break;

        case '/staff':
            $today = date('Y-m-d');
            // Assuming 'employees' table has 'emp_name' and 'employ_id'
            // Assuming 'attendance' has 'employee_id' and 'action'='Clock In'
            $sql = "SELECT DISTINCT e.emp_name, a.time 
                    FROM attendance a 
                    JOIN employees e ON a.employee_id = e.employ_id 
                    WHERE a.date = '$today' AND a.action = 'Clock In'
                    ORDER BY a.time ASC";
            $query = mysqli_query($con, $sql);
            
            $response = "👥 <b>Staff Attendance ($today)</b>\n\n";
            if(mysqli_num_rows($query) > 0) {
                while($row = mysqli_fetch_assoc($query)) {
                    $time = date('g:i A', strtotime($row['time']));
                    $response .= "👤 " . $row['emp_name'] . " - $time\n";
                }
            } else {
                $response .= "Current Staff: None";
            }
            break;

        case '/cash':
            $sql = "SELECT amount FROM accounts WHERE account_name = 'cash_in_hand'";
            $row = mysqli_fetch_assoc(mysqli_query($con, $sql));
            
            $response = "💵 <b>Cash Status</b>\n\n";
            $response .= "Cash in Hand: <b>Rs. " . number_format($row['amount'] ?? 0, 2) . "</b>";
            break;
    }

} else {
    // B. NATURAL LANGUAGE (Hi/Hello)
    $clean_text = strtolower(trim($text));
    $greetings = ['hi', 'hello', 'hey', 'start', 'ping', 'test'];
    
    if (in_array($clean_text, $greetings)) {
         $response = "👋 <b>Hello! I am $GLOBALS['ERP_COMPANY_NAME'] Bot.</b>\n\n";
         $response .= "Apps are boring, Text me to manage your business! 🚀\n\n";
         $response .= "<b>Try typing:</b>\n";
         $response .= "/start - Open Main Menu";
    }
}

// 5. Send Reply
if ($response) {
    $tg->sendToChat($chat_id, $response, $message_id, $thread_id);
}
?>
