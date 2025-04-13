<?php
require_once '../../../config/init.php';
session_start();

// Check if user is an admin
if (!isset($_SESSION['employee_id']) || $_SESSION['employee_role'] !== 'Admin') {
    echo "Access denied. Admin privileges required.";
    exit;
}

// Create the barcode_templates table if it doesn't exist
$createTableQuery = "
CREATE TABLE IF NOT EXISTS barcode_templates (
    template_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    settings TEXT NOT NULL,
    items TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (user_id),
    UNIQUE KEY user_template (user_id, template_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

try {
    mysqli_query($con, $createTableQuery);
    echo "Barcode templates table created successfully!";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
} 