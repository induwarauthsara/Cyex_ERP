<?php
/**
 * TelegramService.php
 * Handles all interactions with Telegram Bot API
 */

class TelegramService {
    private $con;
    private $bot_token;
    private $master_chat_id;
    private $topics = [];

    public function __construct($db_connection) {
        $this->con = $db_connection;
        $this->loadConfig();
    }

    /**
     * Load configuration from database
     */
    private function loadConfig() {
        // Load settings
        $res = mysqli_query($this->con, "SELECT setting_key, setting_value FROM telegram_config");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                if ($row['setting_key'] == 'bot_token') $this->bot_token = $row['setting_value'];
                if ($row['setting_key'] == 'master_chat_id') $this->master_chat_id = $row['setting_value'];
            }
        }

        // Load topics map
        $res = mysqli_query($this->con, "SELECT topic_key, topic_id FROM telegram_topics WHERE is_active = 1");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $this->topics[$row['topic_key']] = $row['topic_id'];
            }
        }
    }

    /**
     * Send a message to a specific topic
     */
    public function sendMessage($message, $topic_key = null, $reply_to_msg_id = null) {
        if (empty($this->bot_token) || empty($this->master_chat_id)) {
            return false;
        }

        $topic_id = null;
        if ($topic_key && isset($this->topics[$topic_key])) {
            $topic_id = $this->topics[$topic_key];
        }

        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendMessage";
        
        $data = [
            'chat_id' => $this->master_chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];

        // Route to specific topic (Thread ID)
        if ($topic_id) {
            $data['message_thread_id'] = $topic_id;
        }
        
        if ($reply_to_msg_id) {
            $data['reply_to_message_id'] = $reply_to_msg_id;
        }

        return $this->curlPost($url, $data);
    }

    /**
     * Send message to a specific chat ID (for Webhook replies)
     */
    public function sendToChat($chat_id, $message, $reply_to_msg_id = null, $thread_id = null) {
        if (empty($this->bot_token)) return false;

        $url = "https://api.telegram.org/bot" . $this->bot_token . "/sendMessage";
        
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];

        if ($reply_to_msg_id) $data['reply_to_message_id'] = $reply_to_msg_id;
        if ($thread_id) $data['message_thread_id'] = $thread_id;

        return $this->curlPost($url, $data);
    }

    /**
     * Test Connectivity
     * Returns ['status' => bool, 'bot_name' => str, 'message' => str]
     */
    public function testConnection($token, $chat_id) {
        // 1. Check Bot validity
        $url = "https://api.telegram.org/bot" . $token . "/getMe";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        
        $json = json_decode($res, true);
        if (!$json || !$json['ok']) {
            return ['status' => false, 'message' => 'Invalid Bot Token'];
        }
        
        $bot_name = $json['result']['first_name'] . " (@" . $json['result']['username'] . ")";
        
        // 2. Try to send a message
        $msgUrl = "https://api.telegram.org/bot" . $token . "/sendMessage";
        $msgData = [
            'chat_id' => $chat_id,
            'text' => "✅ <b>Integration Successful!</b>\n\nHello from Srijaya ERP. Example topic mapping: General.",
            'parse_mode' => 'HTML'
        ];
        
        // Use raw curl since we are using a custom token, not the stored one
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $msgUrl);
        curl_setopt($ch2, CURLOPT_POST, 1);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $msgData);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        $msgRes = curl_exec($ch2);
        curl_close($ch2);
        
        $msgJson = json_decode($msgRes, true);
        
        if ($msgJson && $msgJson['ok']) {
            return ['status' => true, 'bot_name' => $bot_name];
        } else {
            return ['status' => false, 'message' => 'Bot valid, but cannot message Chat ID. Ensure Bot is Admin in Group. Error: ' . ($msgJson['description'] ?? 'Unknown')];
        }
    }

    /**
     * Helper to make POST requests
     */
    private function curlPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        if ($result === false) {
            // Log error if needed: curl_error($ch)
            curl_close($ch);
            return false;
        }
        
        curl_close($ch);
        return json_decode($result, true);
    }
}
?>
