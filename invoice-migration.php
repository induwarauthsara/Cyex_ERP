<?php
/**
 * Invoice Table Migration Script
 * 
 * This script will:
 * 1. Rename 'advance' column to 'amount_received'
 * 2. Rename 'balance' column to 'cash_change'
 * 3. Add new 'advance' and 'balance' columns
 * 4. Update existing records with calculated values
 */

// Include database configuration
require_once 'inc/config.php';

// Start output buffer to capture all output
ob_start();
echo "<h1>Invoice Table Migration</h1>";

// Check if migration is already done
$check_query = "SHOW COLUMNS FROM invoice LIKE 'amount_received'";
$result = mysqli_query($con, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Migration already done
    echo "<p style='color: orange;'>Migration already appears to be done. The 'amount_received' column already exists.</p>";
    
    // Ask for confirmation to continue
    echo "<p>Do you want to continue and try to run the migration again? This could potentially cause data loss.</p>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='force_migration' value='1'>";
    echo "<input type='submit' value='Yes, Continue Anyway' style='background-color: red; color: white; padding: 10px;'>";
    echo "</form>";
    
    // Check if user confirmed to continue
    if (!isset($_POST['force_migration'])) {
        echo "<p><a href='/'>Go back to homepage</a></p>";
        ob_end_flush();
        exit;
    }
    
    echo "<p style='color: red;'>Forcing migration as requested...</p>";
}

try {
    // 1. Backup the invoice table
    $backup_query = "CREATE TABLE invoice_backup LIKE invoice";
    if (mysqli_query($con, $backup_query)) {
        echo "<p>Created backup table structure.</p>";
        
        $backup_data_query = "INSERT INTO invoice_backup SELECT * FROM invoice";
        if (mysqli_query($con, $backup_data_query)) {
            echo "<p>Copied invoice data to backup table.</p>";
        } else {
            throw new Exception("Failed to copy data to backup table: " . mysqli_error($con));
        }
    } else {
        throw new Exception("Failed to create backup table: " . mysqli_error($con));
    }
    
    // 2. Rename existing columns
    $rename_columns_query = "ALTER TABLE invoice 
        CHANGE advance amount_received DECIMAL(15,2) NOT NULL,
        CHANGE balance cash_change DECIMAL(15,2) NOT NULL";
    
    if (mysqli_query($con, $rename_columns_query)) {
        echo "<p>Successfully renamed columns:</p>";
        echo "<ul>";
        echo "<li>'advance' to 'amount_received'</li>";
        echo "<li>'balance' to 'cash_change'</li>";
        echo "</ul>";
    } else {
        throw new Exception("Failed to rename columns: " . mysqli_error($con));
    }
    
    // 3. Add new columns
    $add_columns_query = "ALTER TABLE invoice 
        ADD COLUMN advance DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER cash_change,
        ADD COLUMN balance DECIMAL(15,2) NOT NULL DEFAULT 0.00 AFTER advance";
    
    if (mysqli_query($con, $add_columns_query)) {
        echo "<p>Successfully added new columns:</p>";
        echo "<ul>";
        echo "<li>'advance' - Amount applied to the invoice</li>";
        echo "<li>'balance' - Remaining amount due</li>";
        echo "</ul>";
    } else {
        throw new Exception("Failed to add new columns: " . mysqli_error($con));
    }
    
    // 4. Update existing records
    $update_records_query = "UPDATE invoice 
        SET 
            advance = CASE 
                WHEN amount_received >= (total - discount) THEN (total - discount) 
                ELSE amount_received 
            END,
            balance = CASE 
                WHEN amount_received >= (total - discount) THEN 0.00
                ELSE (total - discount - amount_received)
            END";
    
    if (mysqli_query($con, $update_records_query)) {
        echo "<p>Successfully updated existing records with calculated values.</p>";
        
        // Optional additional processing of records with customer extra fund
        // This is more of a placeholder since existing records won't have this flag recorded
        // In a real implementation, you might need to check transaction logs or other records
        echo "<p>Note: Records where customer extra fund was used will need additional review.</p>";
        echo "<p>The migration script cannot automatically determine which transactions used customer extra funds.</p>";
    } else {
        throw new Exception("Failed to update records: " . mysqli_error($con));
    }
    
    // 5. Verify structure
    $describe_query = "DESCRIBE invoice";
    $result = mysqli_query($con, $describe_query);
    
    if ($result) {
        echo "<h2>New Table Structure:</h2>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>Failed to get table structure: " . mysqli_error($con) . "</p>";
    }
    
    echo "<h2>Migration Complete!</h2>";
    echo "<p>The invoice table structure has been successfully updated with the new columns.</p>";
    echo "<p><a href='/'>Go back to homepage</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>Migration Failed!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    // Try to restore from backup if it exists
    $check_backup = "SHOW TABLES LIKE 'invoice_backup'";
    $backup_exists = mysqli_query($con, $check_backup);
    
    if (mysqli_num_rows($backup_exists) > 0) {
        echo "<p>Attempting to restore from backup...</p>";
        
        // Drop the current invoice table
        if (mysqli_query($con, "DROP TABLE invoice")) {
            // Rename backup to invoice
            if (mysqli_query($con, "RENAME TABLE invoice_backup TO invoice")) {
                echo "<p style='color: green;'>Successfully restored from backup!</p>";
            } else {
                echo "<p style='color: red;'>Failed to rename backup table: " . mysqli_error($con) . "</p>";
                echo "<p style='color: red;'>IMPORTANT: Your invoice table has been dropped but could not be restored! Please restore it manually from the invoice_backup table.</p>";
            }
        } else {
            echo "<p style='color: red;'>Failed to drop damaged invoice table: " . mysqli_error($con) . "</p>";
            echo "<p style='color: red;'>Backup is available in the invoice_backup table. Please manually restore it.</p>";
        }
    } else {
        echo "<p style='color: red;'>No backup table found to restore from.</p>";
    }
}

// End output buffer and display
ob_end_flush(); 