-- Expenses Management Module - Database Initialization
-- Created: 2025-12-06
-- Description: Complete schema for expense tracking including categories, recurring expenses, transactions, and partial payments
-- This file consolidates previous migration files into a single source of truth.

-- --------------------------------------------------------
-- Table 1: Expense Categories
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color_code` varchar(7) DEFAULT '#808080',
  `icon` varchar(50) DEFAULT 'money-bill',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1: Active, 0: Inactive',
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `idx_category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories (using INSERT IGNORE to avoid duplicates if re-run)
INSERT IGNORE INTO `expense_categories` (`category_name`, `description`, `color_code`, `icon`) VALUES
('Rent', 'Monthly office/shop rent payments', '#FF6B6B', 'home'),
('Utilities', 'Electricity, water, internet bills', '#4ECDC4', 'bolt'),
('Salaries', 'Employee salary payments', '#45B7D1', 'users'),
('Transport', 'Vehicle fuel, maintenance, transport costs', '#FFA07A', 'car'),
('Office Supplies', 'Stationery, equipment, consumables', '#98D8C8', 'briefcase'),
('Telephone', 'Mobile and landline bills', '#F7B731', 'phone'),
('Marketing', 'Advertising and promotional expenses', '#6C5CE7', 'megaphone'),
('Maintenance', 'Repairs and maintenance costs', '#FD79A8', 'wrench'),
('Loans', 'Loan repayments and interest', '#E17055', 'credit-card'),
('Miscellaneous', 'Other expenses', '#95A5A6', 'ellipsis-h');

-- --------------------------------------------------------
-- Table 2: Recurring Expenses
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recurring_expenses` (
  `recurring_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `frequency` enum('daily', 'weekly', 'monthly', 'annually') NOT NULL,
  `start_date` date NOT NULL,
  `next_due_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'Null means indefinite',
  `payment_method` varchar(50) DEFAULT 'Cash',
  `remind_days_before` int(2) DEFAULT 3 COMMENT 'Days before due date to show reminder',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`recurring_id`),
  KEY `idx_next_due` (`next_due_date`),
  KEY `idx_category` (`category_id`),
  KEY `idx_active` (`is_active`),
  CONSTRAINT `fk_recurring_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`category_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table 3: Expenses (Actual Transaction Records)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expenses` (
  `expense_id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_no` varchar(50) DEFAULT NULL COMMENT 'Receipt/Invoice number',
  `title` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `amount_paid` decimal(15,2) NOT NULL DEFAULT 0 COMMENT 'Total amount paid so far',
  `category_id` int(11) NOT NULL,
  `expense_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `status` enum('paid', 'partial', 'unpaid', 'overdue') NOT NULL DEFAULT 'unpaid',
  `recurring_ref_id` int(11) DEFAULT NULL,
  `attachment_url` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`expense_id`),
  KEY `idx_date` (`expense_date`),
  KEY `idx_status` (`status`),
  KEY `idx_category` (`category_id`),
  KEY `idx_recurring_ref` (`recurring_ref_id`),
  KEY `idx_created_by` (`created_by`),
  CONSTRAINT `fk_expense_category` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`category_id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_expense_recurring` FOREIGN KEY (`recurring_ref_id`) REFERENCES `recurring_expenses` (`recurring_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table 4: Expense Payments (Track partial payments)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expense_payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `expense_id` int(11) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `reference_no` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `idx_expense` (`expense_id`),
  KEY `idx_date` (`payment_date`),
  CONSTRAINT `fk_payment_expense` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Views
-- --------------------------------------------------------

-- View for expense summary by category
CREATE OR REPLACE VIEW `v_expense_category_summary` AS
SELECT 
    ec.category_id,
    ec.category_name,
    ec.color_code,
    ec.icon,
    COUNT(e.expense_id) as total_transactions,
    COALESCE(SUM(e.amount), 0) as total_amount,
    COALESCE(SUM(e.amount_paid), 0) as total_paid,
    COALESCE(SUM(CASE WHEN MONTH(e.expense_date) = MONTH(CURRENT_DATE()) 
                       AND YEAR(e.expense_date) = YEAR(CURRENT_DATE()) 
                       THEN e.amount ELSE 0 END), 0) as current_month_amount,
    COALESCE(SUM(CASE WHEN MONTH(e.expense_date) = MONTH(CURRENT_DATE()) 
                       AND YEAR(e.expense_date) = YEAR(CURRENT_DATE()) 
                       THEN e.amount_paid ELSE 0 END), 0) as current_month_paid
FROM expense_categories ec
LEFT JOIN expenses e ON ec.category_id = e.category_id
WHERE ec.status = 1
GROUP BY ec.category_id, ec.category_name, ec.color_code, ec.icon;

-- View for upcoming recurring payments
CREATE OR REPLACE VIEW `v_upcoming_recurring_payments` AS
SELECT 
    re.recurring_id,
    re.title,
    re.amount,
    re.next_due_date,
    re.frequency,
    re.payment_method,
    re.remind_days_before,
    ec.category_name,
    ec.color_code,
    ec.icon,
    DATEDIFF(re.next_due_date, CURRENT_DATE()) as days_until_due,
    CASE 
        WHEN re.next_due_date < CURRENT_DATE() THEN 'overdue'
        WHEN DATEDIFF(re.next_due_date, CURRENT_DATE()) <= re.remind_days_before THEN 'due_soon'
        ELSE 'upcoming'
    END as payment_status
FROM recurring_expenses re
INNER JOIN expense_categories ec ON re.category_id = ec.category_id
WHERE re.is_active = 1
ORDER BY re.next_due_date ASC;

-- View for expense payment history
CREATE OR REPLACE VIEW `v_expense_payment_history` AS
SELECT 
    ep.payment_id,
    ep.expense_id,
    e.title as expense_title,
    e.amount as total_amount,
    e.amount_paid as total_paid,
    (e.amount - e.amount_paid) as remaining_amount,
    ep.payment_amount,
    ep.payment_date,
    ep.payment_method,
    ep.reference_no,
    ep.notes,
    -- using emp_name from employees table
    COALESCE(emp.emp_name, 'Unknown') as created_by_name,
    ep.created_at
FROM expense_payments ep
INNER JOIN expenses e ON ep.expense_id = e.expense_id
LEFT JOIN employees emp ON ep.created_by = emp.employ_id
ORDER BY ep.payment_date DESC;

-- --------------------------------------------------------
-- Indexes
-- --------------------------------------------------------

-- Add indexes for performance optimization
-- using IF NOT EXISTS which is supported in MariaDB 10.5+
CREATE INDEX IF NOT EXISTS idx_expense_date ON expenses(expense_date);
CREATE INDEX IF NOT EXISTS idx_expense_amount ON expenses(amount);



-- --------------------------------------------------------
-- Triggers
-- --------------------------------------------------------

DELIMITER $$

DROP TRIGGER IF EXISTS `update_expense_status_after_payment`$$

CREATE TRIGGER `update_expense_status_after_payment`
AFTER INSERT ON `expense_payments`
FOR EACH ROW
BEGIN
    DECLARE var_total_amount DECIMAL(15,2);
    DECLARE var_old_paid DECIMAL(15,2);
    DECLARE var_new_paid DECIMAL(15,2);

    -- Get expense details
    SELECT amount, amount_paid INTO var_total_amount, var_old_paid
    FROM expenses
    WHERE expense_id = NEW.expense_id;
    
    SET var_new_paid = var_old_paid + NEW.payment_amount;

    -- Update parent expense record
    UPDATE expenses
    SET 
        amount_paid = var_new_paid,
        status = CASE
            WHEN var_new_paid >= var_total_amount THEN 'paid'
            WHEN var_new_paid > 0 THEN 'partial'
            ELSE 'unpaid'
        END
    WHERE expense_id = NEW.expense_id;
END$$

DELIMITER ;

SELECT 'Expenses Management Module initialized successfully!' as status;
