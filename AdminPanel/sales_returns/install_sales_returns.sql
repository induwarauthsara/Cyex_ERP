-- Sales Returns Tables

-- Create sales_returns table
CREATE TABLE IF NOT EXISTS `sales_returns` (
    `return_id` INT PRIMARY KEY AUTO_INCREMENT,
    `invoice_id` VARCHAR(20) NOT NULL,
    `customer_id` INT NULL,
    `return_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `return_amount` DECIMAL(10,2) NOT NULL,
    `refund_method` ENUM('Cash', 'Store Credit') NOT NULL,
    `return_reason` VARCHAR(100) NOT NULL,
    `return_note` TEXT NULL,
    `user_id` INT NOT NULL
);

-- Create sales_return_items table
CREATE TABLE IF NOT EXISTS `sales_return_items` (
    `return_item_id` INT PRIMARY KEY AUTO_INCREMENT,
    `return_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `batch_id` INT NOT NULL,
    `quantity_returned` DECIMAL(10,3) NOT NULL,
    `return_price` DECIMAL(10,2) NOT NULL,
    `add_to_stock` BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (`return_id`) REFERENCES `sales_returns`(`return_id`)
);

-- Create indexes for faster lookups
CREATE INDEX `idx_returns_invoice` ON `sales_returns` (`invoice_id`);
CREATE INDEX `idx_returns_customer` ON `sales_returns` (`customer_id`);
CREATE INDEX `idx_return_items_return` ON `sales_return_items` (`return_id`);
CREATE INDEX `idx_return_items_product` ON `sales_return_items` (`product_id`);