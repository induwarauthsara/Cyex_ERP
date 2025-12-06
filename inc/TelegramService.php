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
