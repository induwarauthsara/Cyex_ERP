<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== "Admin") {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

require_once(__DIR__ . '/../../inc/config.php');
require_once(__DIR__ . '/../../inc/TelegramService.php');

$token = $_POST['token'] ?? '';
$chat_id = $_POST['chat_id'] ?? '';

if(empty($token) || empty($chat_id)) {
    echo json_encode(['success' => false, 'message' => 'Token and Chat ID required']);
    exit;
}

$tg = new TelegramService($con);
$result = $tg->testConnection($token, $chat_id);

if($result['status']) {
    echo json_encode(['success' => true, 'bot_name' => $result['bot_name']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Telegram API Error: ' . ($result['message'] ?? 'Unknown error')]);
}
?>
