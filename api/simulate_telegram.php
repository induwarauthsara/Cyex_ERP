<?php
/**
 * SIMULATION TOOL
 * Use this to test your bot commands on Localhost without ngrok/https.
 */

// URL of your webhook (Update this to your live domain)
$prod_url = "$GLOBALS['ERP_COMPANY_BASE_URL']/api/telegram_webhook.php";
$webhook_url = "$GLOBALS['ERP_COMPANY_BASE_URL']/api/telegram_webhook.php"; // Local testing

// Helper: Show Registration Link
if(isset($_GET['action']) && $_GET['action'] == 'register') {
    // We need the token. For security, ask user to enter it or fetch from config if secure.
    // Since this is a dev tool, let's just ask user to click a link.
    echo "<h2>🚀 One-Time Setup: Register Webhook</h2>";
    echo "<p>You need to tell Telegram to send messages to: <b>$prod_url</b></p>";
    echo "<p>Replace <code>&lt;YOUR_BOT_TOKEN&gt;</code> and visit this link in your browser:</p>";
    echo "<textarea style='width:100%; padding:10px;'>https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook?url=$prod_url</textarea>";
    echo "<br><br><a href='?'>&larr; Back to Simulator</a>";
    exit;
}

// Helper: Set Commands Link
if(isset($_GET['action']) && $_GET['action'] == 'set_commands') {
    $cmds = [
        ['command' => 'start', 'description' => 'Open Main Menu'],
        ['command' => 'today', 'description' => 'Daily Sales Report'],
        ['command' => 'month', 'description' => 'Monthly Overview'],
        ['command' => 'stock', 'description' => 'Inventory Snapshot'],
        ['command' => 'lowstock', 'description' => 'Low Stock Alerts'],
        ['command' => 'pending', 'description' => 'Outstanding Credit'],
        ['command' => 'staff', 'description' => 'Staff Attendance'],
        ['command' => 'cash', 'description' => 'Cash in Hand'],
    ];
    $json = json_encode($cmds);
    
    echo "<h2>⚙️ Setup Bot Menu (Commands)</h2>";
    echo "<p>Run this to make the '/' command list appear in Telegram.</p>";
    echo "<p>Replace <code>&lt;YOUR_BOT_TOKEN&gt;</code> and visit this link:</p>";
    echo "<textarea style='width:100%; height:100px; padding:10px;'>https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setMyCommands?commands=$json</textarea>";
    echo "<br><br><a href='?'>&larr; Back to Simulator</a>";
    exit;
}

echo "<div style='background:#f4f4f4; padding:10px; margin-bottom:20px; border-bottom:2px solid #ccc;'>";
echo "<b>Tools:</b> <a href='?'>Simulate Command</a> | <a href='?action=register'>📋 Get Production Registration Link</a> | <a href='?action=set_commands'>⚙️ Set Bot Commands</a>";
echo "</div>"; 

// 1. Define a Mock Update (What Telegram would send)
// Change 'text' to test different commands: /start, /stock, /sales
$mock_update = [
    'update_id' => 123456,
    'message' => [
        'message_id' => 789,
        'from' => [
            'id' => 9999999,
            'first_name' => 'Induwara',
            'username' => 'induwara'
        ],
        'chat' => [
            'id' => -1001234567890, // Your Master Chat ID
            'type' => 'supergroup',
            'title' => '$GLOBALS['ERP_COMPANY_NAME'] ERP Alerts'
        ],
        'date' => time(),
        'text' => '/stock' // <--- CHANGE THIS TO TEST COMMANDS
    ]
];

// 2. Send Request
$ch = curl_init($webhook_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mock_update));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo "<h1>Telegram Webhook Simulator</h1>";
echo "<p>Sending command: <b>" . $mock_update['message']['text'] . "</b></p>";
echo "<hr>";

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo "<h3>❌ Error: " . curl_error($ch) . "</h3>";
    echo "<p>Ensure the URL is correct: $webhook_url</p>";
} else {
    echo "<h3>✅ Request Sent (HTTP $http_code)</h3>";
    echo "<pre>Response: " . htmlspecialchars($response) . "</pre>";
    echo "<p><i>Check your Telegram Group. If the bot is configured correctly, it should have replied!</i></p>";
}

curl_close($ch);
?>
