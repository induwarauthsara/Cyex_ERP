-- =============================================================
-- EMPLOYEE COMMISSION FEATURE - DATABASE MIGRATION
-- =============================================================
-- Created: January 13, 2026
-- Description: Adds employee commission functionality to the ERP
-- =============================================================

-- 1. Add employee_commission_percentage column to products table
-- This column stores the commission percentage for each product
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `employee_commission_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00 
COMMENT 'Employee commission percentage from product profit (0-100)';

-- 2. Add employee_commission_enabled setting to settings table
-- This controls whether commission feature is active globally
INSERT IGNORE INTO `settings` (`setting_name`, `setting_description`, `setting_value`) 
VALUES ('employee_commission_enabled', 'Enable employee commission from invoice profit', '0');

-- 3. Create employee_commission_history table
-- This tracks all commission transactions for auditing and reporting
CREATE TABLE IF NOT EXISTS `employee_commission_history` (
  `commission_id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` INT(10) NOT NULL,
  `sales_id` INT(5) DEFAULT NULL,
  `employee_id` INT(4) NOT NULL,
  `product_id` INT(10) DEFAULT NULL,
  `product_name` VARCHAR(100) NOT NULL,
  `product_profit` DECIMAL(15,2) NOT NULL,
  `commission_percentage` DECIMAL(5,2) NOT NULL,
  `commission_amount` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commission_id`),
  KEY `idx_commission_invoice` (`invoice_number`),
  KEY `idx_commission_employee` (`employee_id`),
  KEY `idx_commission_product` (`product_id`),
  KEY `idx_commission_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =============================================================
-- VERIFICATION QUERIES (Run after migration to verify)
-- =============================================================

-- Verify products column was added:
-- SHOW COLUMNS FROM products LIKE 'employee_commission_percentage';

-- Verify setting was added:
-- SELECT * FROM settings WHERE setting_name = 'employee_commission_enabled';

-- Verify commission history table was created:
-- SHOW TABLES LIKE 'employee_commission_history';
-- DESCRIBE employee_commission_history;

-- =============================================================
-- ROLLBACK SCRIPT (If needed to undo changes)
-- =============================================================
-- ALTER TABLE `products` DROP COLUMN `employee_commission_percentage`;
-- DELETE FROM `settings` WHERE `setting_name` = 'employee_commission_enabled';
-- DROP TABLE IF EXISTS `employee_commission_history`;
