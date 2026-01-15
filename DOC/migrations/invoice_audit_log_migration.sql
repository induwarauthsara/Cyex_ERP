-- =====================================================
-- Invoice Audit Log Migration Script
-- Bill Edit & Delete Feature
-- Generated: 2026-01-15
-- =====================================================

-- Create invoice_audit_logs table
CREATE TABLE IF NOT EXISTS `invoice_audit_logs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `invoice_number` INT(10) NOT NULL COMMENT 'Linked Bill ID (invoice_number from invoice table)',
    `action_type` ENUM('EDIT', 'DELETE') NOT NULL COMMENT 'Type of action performed',
    `old_payload` JSON DEFAULT NULL COMMENT 'Previous data snapshot before action',
    `new_payload` JSON DEFAULT NULL COMMENT 'New data snapshot after action (NULL for DELETE)',
    `stock_changes` JSON DEFAULT NULL COMMENT 'Stock adjustments made { product: qty_change }',
    `commission_changes` JSON DEFAULT NULL COMMENT 'Commission adjustments { biller: { old, new }, worker: { old, new } }',
    `reason` TEXT NOT NULL COMMENT 'User-provided reason for edit/delete',
    `restock_items` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1: Items restocked, 0: Items written off',
    `user_id` INT(4) NOT NULL COMMENT 'Employee ID who performed action',
    `user_name` VARCHAR(50) DEFAULT NULL COMMENT 'Employee name for quick reference',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_invoice_number` (`invoice_number`),
    KEY `idx_action_type` (`action_type`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Audit trail for invoice edits and deletes';

-- Add is_deleted column to invoice table for soft delete
ALTER TABLE `invoice` 
ADD COLUMN IF NOT EXISTS `is_deleted` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1: Soft deleted, 0: Active' AFTER `cash_change`,
ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME DEFAULT NULL COMMENT 'Timestamp of deletion' AFTER `is_deleted`,
ADD COLUMN IF NOT EXISTS `deleted_by` INT(4) DEFAULT NULL COMMENT 'User who deleted the invoice' AFTER `deleted_at`;

-- Add index for soft delete queries
ALTER TABLE `invoice` ADD KEY IF NOT EXISTS `idx_is_deleted` (`is_deleted`);

-- =====================================================
-- ROLLBACK SCRIPT (Run only if you need to undo migration)
-- =====================================================
-- DROP TABLE IF EXISTS `invoice_audit_logs`;
-- ALTER TABLE `invoice` DROP COLUMN IF EXISTS `is_deleted`;
-- ALTER TABLE `invoice` DROP COLUMN IF EXISTS `deleted_at`;
-- ALTER TABLE `invoice` DROP COLUMN IF EXISTS `deleted_by`;
