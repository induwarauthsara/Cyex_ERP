-- Telegram Bot Configuration Tables

-- 1. General Configuration (Bot Token, Chat ID, etc.)
CREATE TABLE IF NOT EXISTS `telegram_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL UNIQUE,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default keys
INSERT IGNORE INTO `telegram_config` (`setting_key`, `setting_value`) VALUES
('bot_token', ''),
('master_chat_id', ''),
('bot_enabled', '0');

-- 2. Telegram Topics (Mapping Functional Areas to Topic IDs)
CREATE TABLE IF NOT EXISTS `telegram_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_key` varchar(50) NOT NULL UNIQUE COMMENT 'internal key like inventory_alerts',
  `topic_name` varchar(100) NOT NULL COMMENT 'User facing name',
  `topic_id` int(11) DEFAULT NULL COMMENT 'Telegram Message Thread ID',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default topics based on the plan
INSERT IGNORE INTO `telegram_topics` (`topic_key`, `topic_name`, `description`, `is_active`) VALUES
('daily_summary', '📊 Business Overview', 'Daily Sales, Expenses, and Profit Reports', 1),
('finance', '💰 Finance & Banking', 'Payment Reminders, Expenses, Bank Deposits', 1),
('inventory', '📦 Inventory & Purchase', 'Low Stock, Expiry, GRN, PO Alerts', 1),
('sales_ops', '📝 Sales & Operations', 'High Value Invoices, Returns, Quotations', 1),
('hr_staff', '👥 HR & Staff', 'Attendance, Payroll updates', 1),
('system_health', '🚨 System Health', 'Errors, Backups, Security Alerts', 1);

-- 3. Schedules (Hourly Trigger Management)
CREATE TABLE IF NOT EXISTS `telegram_schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_type` varchar(50) NOT NULL,
  `schedule_hour` int(2) NOT NULL COMMENT '0-23 Hour',
  `target_topic_key` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default schedules
INSERT IGNORE INTO `telegram_schedules` (`report_type`, `schedule_hour`, `target_topic_key`) VALUES
('daily_summary_report', 21, 'daily_summary'), -- 9 PM
('payment_reminders', 8, 'finance'),           -- 8 AM
('low_stock_check', 9, 'inventory');             -- 9 AM

-- 4. Logs (Audit Trail)
CREATE TABLE IF NOT EXISTS `telegram_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_type` varchar(50) DEFAULT NULL,
  `recipient_topic` varchar(50) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('success', 'failed') NOT NULL DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
