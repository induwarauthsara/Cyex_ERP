<?php
header('Content-Type: application/json');
session_start();

// Only Admin can access this API
if (!isset($_SESSION['employee_role']) || $_SESSION['employee_role'] !== "Admin") {
    echo json_encode([
        'success' => false,
        'message' => 'Access denied. Admin privileges required.'
    ]);
    exit;
}

require_once(__DIR__ . '/../../inc/config.php');

// Handle GET request for fetching settings
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get_quotation_settings') {
        $settings_query = "SELECT setting_name, setting_value FROM settings 
                          WHERE setting_name IN ('quotation_validity_days', 'quotation_prefix', 'quotation_auto_generate')";
        $result = mysqli_query($con, $settings_query);
        
        $settings = [
            'quotation_validity_days' => '30',
            'quotation_prefix' => 'QT',
            'quotation_auto_generate' => '1'
        ];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $settings[$row['setting_name']] = $row['setting_value'];
            }
        }
        
        echo json_encode([
            'success' => true,
            'settings' => $settings
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Invalid GET action.'
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. POST required.'
    ]);
    exit;
}

// Get the posted data
$sell_Insufficient_stock_item = isset($_POST['sell_Insufficient_stock_item']) ? intval($_POST['sell_Insufficient_stock_item']) : 0;
$sell_Inactive_batch_products = isset($_POST['sell_Inactive_batch_products']) ? intval($_POST['sell_Inactive_batch_products']) : 0;
$invoice_print_type = isset($_POST['invoice_print_type']) ? $_POST['invoice_print_type'] : 'both';

// Get quotation settings
$quotation_validity_days = isset($_POST['quotation_validity_days']) ? intval($_POST['quotation_validity_days']) : 30;
$quotation_prefix = isset($_POST['quotation_prefix']) ? trim($_POST['quotation_prefix']) : 'QT';
$quotation_auto_generate = isset($_POST['quotation_auto_generate']) ? intval($_POST['quotation_auto_generate']) : 1;

// Get employee commission setting
$employee_commission_enabled = isset($_POST['employee_commission_enabled']) ? intval($_POST['employee_commission_enabled']) : 0;

// Get Company Settings
$company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : '';
$company_address = isset($_POST['company_address']) ? trim($_POST['company_address']) : '';
$company_phone = isset($_POST['company_phone']) ? trim($_POST['company_phone']) : '';
$company_logo = isset($_POST['company_logo']) ? trim($_POST['company_logo']) : 'logo.png';
$company_base_url = isset($_POST['company_base_url']) ? trim($_POST['company_base_url']) : '';
$company_website = isset($_POST['company_website']) ? trim($_POST['company_website']) : '';

// Validate values
if (!in_array($sell_Insufficient_stock_item, [0, 1]) || !in_array($sell_Inactive_batch_products, [0, 1])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid setting values. Must be 0 or 1.'
    ]);
    exit;
}

if (!in_array($invoice_print_type, ['receipt', 'standard', 'both'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid invoice print type. Must be receipt, standard, or both.'
    ]);
    exit;
}

if ($quotation_validity_days < 1 || $quotation_validity_days > 365) {
    echo json_encode([
        'success' => false,
        'message' => 'Quotation validity days must be between 1 and 365.'
    ]);
    exit;
}

if (empty($quotation_prefix) || strlen($quotation_prefix) > 10) {
    echo json_encode([
        'success' => false,
        'message' => 'Quotation prefix must be between 1 and 10 characters.'
    ]);
    exit;
}

// Start transaction
mysqli_begin_transaction($con);

try {
    // Define settings to update
    $settings = [
        'sell_Insufficient_stock_item' => [
            'value' => $sell_Insufficient_stock_item,
            'description' => 'Allow selling out-of-stock items (1=allow, 0=restrict)'
        ],
        'sell_Inactive_batch_products' => [
            'value' => $sell_Inactive_batch_products,
            'description' => 'Allow selling from inactive batches (1=allow, 0=restrict)'
        ],
        'invoice_print_type' => [
            'value' => $invoice_print_type,
            'description' => 'Default invoice print type (receipt, standard, or both)'
        ],
        'quotation_validity_days' => [
            'value' => $quotation_validity_days,
            'description' => 'Default validity period for quotations in days'
        ],
        'quotation_prefix' => [
            'value' => $quotation_prefix,
            'description' => 'Prefix for quotation numbers'
        ],
        'quotation_auto_generate' => [
            'value' => $quotation_auto_generate,
            'description' => 'Auto generate quotation numbers (1=yes, 0=no)'
        ],
        'employee_commission_enabled' => [
            'value' => $employee_commission_enabled,
            'description' => 'Enable employee commission from invoice profit (1=yes, 0=no)'
        ],
        // Company Settings
        'company_name' => [
            'value' => $company_name,
            'description' => 'Company Name displayed on invoices and header'
        ],
        'company_address' => [
            'value' => $company_address,
            'description' => 'Company Address displayed on invoices'
        ],
        'company_phone' => [
            'value' => $company_phone,
            'description' => 'Company Phone Number'
        ],
        'company_logo' => [
            'value' => $company_logo,
            'description' => 'URL/Path to Company Logo'
        ],
        'company_base_url' => [
            'value' => $company_base_url,
            'description' => 'Base URL for API calls'
        ],
        'company_website' => [
            'value' => $company_website,
            'description' => 'Company Website URL'
        ],

    ];

    $updatedCount = 0;
    $insertedCount = 0;

    foreach ($settings as $setting_name => $setting_data) {
        // Check if setting exists
        $check_query = "SELECT setting_name FROM settings WHERE setting_name = ?";
        $check_stmt = mysqli_prepare($con, $check_query);
        
        if (!$check_stmt) {
            throw new Exception("Error preparing check query: " . mysqli_error($con));
        }

        mysqli_stmt_bind_param($check_stmt, 's', $setting_name);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        $exists = mysqli_num_rows($check_result) > 0;
        mysqli_stmt_close($check_stmt);

        if ($exists) {
            // Update existing setting
            $update_query = "UPDATE settings SET setting_value = ?, setting_description = ? WHERE setting_name = ?";
            $update_stmt = mysqli_prepare($con, $update_query);
            
            if (!$update_stmt) {
                throw new Exception("Error preparing update query: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($update_stmt, 'sss', $setting_data['value'], $setting_data['description'], $setting_name);
            mysqli_stmt_execute($update_stmt);
            
            if (mysqli_affected_rows($con) > 0) {
                $updatedCount++;
            }
            mysqli_stmt_close($update_stmt);
        } else {
            // Insert new setting
            $insert_query = "INSERT INTO settings (setting_name, setting_value, setting_description) VALUES (?, ?, ?)";
            $insert_stmt = mysqli_prepare($con, $insert_query);
            
            if (!$insert_stmt) {
                throw new Exception("Error preparing insert query: " . mysqli_error($con));
            }

            mysqli_stmt_bind_param($insert_stmt, 'sss', $setting_name, $setting_data['value'], $setting_data['description']);
            mysqli_stmt_execute($insert_stmt);
            
            if (mysqli_affected_rows($con) > 0) {
                $insertedCount++;
            }
            mysqli_stmt_close($insert_stmt);
        }
    }

    // Commit transaction
    mysqli_commit($con);

    // Create response message
    $message = "Settings updated successfully!";
    if ($updatedCount > 0 && $insertedCount > 0) {
        $message = "Settings updated! ($updatedCount updated, $insertedCount created)";
    } elseif ($updatedCount > 0) {
        $message = "Settings updated! ($updatedCount settings updated)";
    } elseif ($insertedCount > 0) {
        $message = "Settings created! ($insertedCount new settings created)";
    }

    // Log the changes
    $employee_id = $_SESSION['employee_id'] ?? 0;
    $log_message = "Settings updated: Out-of-Stock Sales=" . ($sell_Insufficient_stock_item ? 'Enabled' : 'Disabled') . 
                   ", Inactive Batch Sales=" . ($sell_Inactive_batch_products ? 'Enabled' : 'Disabled') .
                   ", Company Name=" . $company_name;
    
    error_log("Settings updated by Employee ID $employee_id: $log_message");

    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'sell_Insufficient_stock_item' => $sell_Insufficient_stock_item,
            'sell_Inactive_batch_products' => $sell_Inactive_batch_products,
            'updated_count' => $updatedCount,
            'inserted_count' => $insertedCount
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($con);
    
    // Log error
    error_log("Settings update error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update settings: ' . $e->getMessage()
    ]);
}

// Close connection
mysqli_close($con);
?>
