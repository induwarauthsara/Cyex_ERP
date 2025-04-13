<?php
require_once '../inc/config.php';

// Create cash_register table
$cash_register_sql = "CREATE TABLE IF NOT EXISTS `cash_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `opening_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `actual_cash` decimal(10,2) DEFAULT NULL,
  `bank_deposit` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `closing_notes` text DEFAULT NULL,
  `opened_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Create payment_details table
$payment_details_sql = "CREATE TABLE IF NOT EXISTS `payment_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `payment_date` date NOT NULL,
  `payment_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Create bank_deposits table
$bank_deposits_sql = "CREATE TABLE IF NOT EXISTS `bank_deposits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(10,2) NOT NULL,
  `deposit_date` date NOT NULL,
  `deposit_time` time NOT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Execute all schema statements
$sql_statements = [
    'cash_register' => $cash_register_sql,
    'payment_details' => $payment_details_sql, 
    'bank_deposits' => $bank_deposits_sql
];

// Execute each statement
$success = true;
$error_messages = [];
$tables_created = [];

foreach ($sql_statements as $table => $statement) {
    if (!mysqli_query($con, $statement)) {
        $success = false;
        $error_messages[] = "Error creating {$table} table: " . mysqli_error($con);
    } else {
        $tables_created[] = $table;
    }
}

// Check if register_id column exists in pettycash table
$column_exists = false;
$check_column_sql = "SHOW COLUMNS FROM `pettycash` LIKE 'register_id'";
$column_result = mysqli_query($con, $check_column_sql);

if ($column_result) {
    $column_exists = mysqli_num_rows($column_result) > 0;
    
    // Add the column if it doesn't exist
    if (!$column_exists) {
        $alter_sql = "ALTER TABLE `pettycash` ADD COLUMN `register_id` int(11) DEFAULT NULL";
        if (mysqli_query($con, $alter_sql)) {
            // Try to add the foreign key
            $fk_sql = "ALTER TABLE `pettycash` ADD CONSTRAINT `fk_pettycash_register` FOREIGN KEY (`register_id`) REFERENCES `cash_register`(`id`) ON DELETE SET NULL";
            
            if (mysqli_query($con, $fk_sql)) {
                $column_exists = true;
            } else {
                $error_messages[] = "Added register_id column but failed to add foreign key: " . mysqli_error($con);
            }
        } else {
            $error_messages[] = "Failed to add register_id column to pettycash table: " . mysqli_error($con);
        }
    }
} else {
    $error_messages[] = "Failed to check for register_id column: " . mysqli_error($con);
}

// Output the result
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Register Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .success {
            color: green;
            font-weight: bold;
        }
        
        .error {
            color: red;
            font-weight: bold;
        }
        
        .info {
            color: blue;
        }
        
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .status-table th, .status-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        .status-table th {
            background-color: #f2f2f2;
        }
        
        .success-row {
            background-color: #d4edda;
        }
        
        .error-row {
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <h1>Cash Register Installation</h1>
    
    <h2>Installation Status</h2>
    <table class="status-table">
        <tr>
            <th>Component</th>
            <th>Status</th>
        </tr>
        <?php foreach ($sql_statements as $table => $statement): ?>
            <tr class="<?php echo in_array($table, $tables_created) ? 'success-row' : 'error-row'; ?>">
                <td><?php echo ucfirst(str_replace('_', ' ', $table)); ?> Table</td>
                <td>
                    <?php if (in_array($table, $tables_created)): ?>
                        <span class="success">✅ Created successfully</span>
                    <?php else: ?>
                        <span class="error">❌ Failed to create</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr class="<?php echo $column_exists ? 'success-row' : 'error-row'; ?>">
            <td>Register ID column in Pettycash table</td>
            <td>
                <?php if ($column_exists): ?>
                    <span class="success">✅ Added successfully</span>
                <?php else: ?>
                    <span class="error">❌ Failed to add</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
    <?php if (!empty($error_messages)): ?>
        <h2>Error Messages:</h2>
        <pre><?php echo implode("\n", $error_messages); ?></pre>
    <?php endif; ?>
    
    <?php if (count($tables_created) === count($sql_statements) && $column_exists): ?>
        <div class="success">
            <p>✅ Cash Register installation completed successfully!</p>
            <p>All tables and columns were created successfully.</p>
        </div>
    <?php else: ?>
        <div class="error">
            <p>❌ Some components failed to install properly. Please check the error messages above.</p>
        </div>
    <?php endif; ?>
    
    <h2>Next Steps:</h2>
    <ol>
        <li>Go to the <a href="/index.php">POS Homepage</a></li>
        <li>Click on the "Cash Register" button to start using the feature</li>
        <li>Open a register session at the start of the day</li>
        <li>Use the "Cash Out" feature to record petty cash expenses</li>
        <li>Close the register at the end of the day to generate a report</li>
    </ol>
    
    <p class="info">If you encounter any issues, please check browser console for detailed error messages and contact support at support@cyextech.com</p>
    
    <div>
        <a href="/index.php">Back to POS Homepage</a>
    </div>
</body>
</html> 