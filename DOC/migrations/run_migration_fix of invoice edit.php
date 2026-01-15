<?php
// Fix for Unknown Column 'is_deleted' error
require_once __DIR__ . '/inc/config.php';

echo "Checking database schema...\n";

// 1. Check if 'is_deleted' exists in 'invoice' table
$check = mysqli_query($con, "SHOW COLUMNS FROM invoice LIKE 'is_deleted'");
if (mysqli_num_rows($check) == 0) {
    echo "Column 'is_deleted' missing from 'invoice' table.\n";
    echo "Applying migration...\n";

    // SQL Commands to run
    $sql_commands = [
        // 1. Create Audit Log Table
        "CREATE TABLE IF NOT EXISTS `invoice_audit_logs` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `invoice_number` INT(10) NOT NULL COMMENT 'Linked Bill ID',
            `action_type` ENUM('EDIT', 'DELETE') NOT NULL,
            `old_payload` JSON DEFAULT NULL,
            `new_payload` JSON DEFAULT NULL,
            `stock_changes` JSON DEFAULT NULL,
            `commission_changes` JSON DEFAULT NULL,
            `reason` TEXT NOT NULL,
            `restock_items` TINYINT(1) NOT NULL DEFAULT 1,
            `user_id` INT(4) NOT NULL,
            `user_name` VARCHAR(50) DEFAULT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_invoice_number` (`invoice_number`),
            KEY `idx_action_type` (`action_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
        
        // 2. Add is_deleted to invoice
        "ALTER TABLE `invoice` ADD COLUMN `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Soft deleted, 0: Active' AFTER `cash_change`;",
        
        // 3. Add deleted_at to invoice
        "ALTER TABLE `invoice` ADD COLUMN `deleted_at` DATETIME DEFAULT NULL AFTER `is_deleted`;",
        
        // 4. Add deleted_by to invoice
        "ALTER TABLE `invoice` ADD COLUMN `deleted_by` INT(4) DEFAULT NULL AFTER `deleted_at`;"
    ];

    foreach ($sql_commands as $i => $sql) {
        if (mysqli_query($con, $sql)) {
            echo "Step " . ($i+1) . " Success.\n";
        } else {
            echo "Step " . ($i+1) . " Failed: " . mysqli_error($con) . "\n";
        }
    }
    echo "Migration applied successfully.\n";
} else {
    echo "Column 'is_deleted' already exists. No migration needed.\n";
}

echo "Done.";
?>
