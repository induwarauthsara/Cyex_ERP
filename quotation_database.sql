-- Quotation Database Structure
-- Execute these queries in your cloud database

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `quotation_items`;
DROP TABLE IF EXISTS `quotations`;

-- Create quotations table
CREATE TABLE IF NOT EXISTS `quotations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_number` varchar(50) NOT NULL UNIQUE,
  `customer_name` varchar(255) NOT NULL,
  `customer_mobile` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `quotation_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('draft','sent','accepted','rejected','expired') DEFAULT 'draft',
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_quotation_number` (`quotation_number`),
  INDEX `idx_customer_name` (`customer_name`),
  INDEX `idx_quotation_date` (`quotation_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create quotation_items table
CREATE TABLE IF NOT EXISTS `quotation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_id` int(11) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(8,2) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_quotation_items_quotation` (`quotation_id`),
  INDEX `idx_product_id` (`product_id`),
  CONSTRAINT `fk_quotation_items_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create auto-increment function for quotation numbers
DELIMITER //
CREATE FUNCTION IF NOT EXISTS generate_quotation_number() RETURNS VARCHAR(50)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE next_id INT;
    DECLARE quotation_num VARCHAR(50);
    
    SELECT IFNULL(MAX(CAST(SUBSTRING(quotation_number, 3) AS UNSIGNED)), 0) + 1 
    INTO next_id 
    FROM quotations 
    WHERE quotation_number REGEXP '^QT[0-9]+$';
    
    SET quotation_num = CONCAT('QT', LPAD(next_id, 6, '0'));
    
    RETURN quotation_num;
END //
DELIMITER ;

-- Insert some sample quotation statuses in settings table if needed
INSERT IGNORE INTO `settings` (`setting_name`, `setting_value`, `setting_description`) VALUES
('quotation_validity_days', '30', 'Default validity period for quotations in days'),
('quotation_prefix', 'QT', 'Prefix for quotation numbers'),
('quotation_auto_generate', '1', 'Auto generate quotation numbers (1=yes, 0=no)');