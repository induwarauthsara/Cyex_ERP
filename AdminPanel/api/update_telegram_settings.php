<?php
header('Content-Type: application/json');
session_start();

// Only Admin can access this API
if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== "Admin") {
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

require_once(__DIR__ . '/../../inc/config.php');

$bot_token = $_POST['bot_token'] ?? '';
$master_chat_id = $_POST['master_chat_id'] ?? '';
$bot_enabled = isset($_POST['bot_enabled']) ? '1' : '0';

// Config Updates
$updates = [
    'bot_token' => $bot_token,
    'master_chat_id' => $master_chat_id,
    'bot_enabled' => $bot_enabled
];

foreach($updates as $key => $val) {
    // Upsert logic (Insert on duplicate update)
    $stmt = $con->prepare("INSERT INTO telegram_config (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->bind_param("sss", $key, $val, $val);
    $stmt->execute();
    $stmt->close();
}

// Topic Updates
if(isset($_POST['topic_id']) && is_array($_POST['topic_id'])) {
    foreach($_POST['topic_id'] as $id => $topicId) {
        $isActive = isset($_POST['topic_active'][$id]) ? 1 : 0;
        $id = intval($id);
        
        $stmt = $con->prepare("UPDATE telegram_topics SET topic_id = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("sii", $topicId, $isActive, $id);
        $stmt->execute();
        $stmt->close();
    }
}

// Schedule Updates
if(isset($_POST['sched_hour']) && is_array($_POST['sched_hour'])) {
    foreach($_POST['sched_hour'] as $id => $hour) {
        $isActive = isset($_POST['sched_active'][$id]) ? 1 : 0;
        $id = intval($id);
        $hour = intval($hour);
        
        $stmt = $con->prepare("UPDATE telegram_schedules SET schedule_hour = ?, is_active = ? WHERE id = ?");
        $stmt->bind_param("iii", $hour, $isActive, $id);
        $stmt->execute();
        $stmt->close();
    }
}

echo json_encode(['success' => true]);
?>
