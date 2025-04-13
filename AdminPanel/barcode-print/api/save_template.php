<?php
require_once '../../../inc/config.php';

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get the raw input data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input data
if (!$data || !isset($data['template_name']) || !isset($data['settings'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid template data']);
    exit;
}

// Sanitize template name
$templateName = mysqli_real_escape_string($con, $data['template_name']);
$userId = $_SESSION['employee_id'];

// Extract settings
$settings = $data['settings'];
$paperSize = floatval($settings['paper_size'] ?? 30);
$paperHeight = ($paperSize === 30) ? 15 : $paperSize / 2; // Default height for 30mm width is 15mm
$showShopName = isset($settings['show_shop_name']) ? (int)$settings['show_shop_name'] : 1;
$showProductName = isset($settings['show_product_name']) ? (int)$settings['show_product_name'] : 1;
$showPrice = isset($settings['show_price']) ? (int)$settings['show_price'] : 1;
$showUnit = isset($settings['show_unit']) ? (int)$settings['show_unit'] : 0;
$showCategory = isset($settings['show_category']) ? (int)$settings['show_category'] : 0;
$showPromoPrice = isset($settings['show_promo_price']) ? (int)$settings['show_promo_price'] : 0;

// Check if template with the same name already exists
$checkQuery = "SELECT template_id FROM barcode_templates WHERE template_name = ?";
$stmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($stmt, 's', $templateName);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$existingTemplate = mysqli_fetch_assoc($result);

if ($existingTemplate) {
    // Update existing template
    $templateId = $existingTemplate['template_id'];
    $updateQuery = "UPDATE barcode_templates SET 
                    paper_width = ?, 
                    paper_height = ?,
                    show_shop_name = ?, 
                    show_product_name = ?, 
                    show_price = ?, 
                    show_unit = ?, 
                    show_category = ?, 
                    show_promo_price = ?,
                    updated_at = NOW() 
                    WHERE template_id = ?";
    
    $stmt = mysqli_prepare($con, $updateQuery);
    mysqli_stmt_bind_param($stmt, 'ddiiiiiii', 
        $paperSize, 
        $paperHeight,
        $showShopName,
        $showProductName, 
        $showPrice, 
        $showUnit, 
        $showCategory, 
        $showPromoPrice,
        $templateId
    );
    $success = mysqli_stmt_execute($stmt);
    
    $message = 'Template updated successfully';
} else {
    // Insert new template
    $insertQuery = "INSERT INTO barcode_templates 
                    (template_name, paper_width, paper_height, 
                     show_shop_name, show_product_name, show_price, 
                     show_unit, show_category, show_promo_price, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = mysqli_prepare($con, $insertQuery);
    mysqli_stmt_bind_param($stmt, 'sddiiiiii', 
        $templateName, 
        $paperSize, 
        $paperHeight,
        $showShopName,
        $showProductName, 
        $showPrice, 
        $showUnit, 
        $showCategory, 
        $showPromoPrice
    );
    $success = mysqli_stmt_execute($stmt);
    
    $templateId = mysqli_insert_id($con);
    $message = 'Template saved successfully';
}

// Create a print job for the template items if items are provided
if ($success && isset($data['items']) && !empty($data['items'])) {
    // Insert a print job
    $jobQuery = "INSERT INTO barcode_print_jobs 
                (template_id, created_by, status, created_at) 
                VALUES (?, ?, 'saved', NOW())";
    $stmt = mysqli_prepare($con, $jobQuery);
    mysqli_stmt_bind_param($stmt, 'ii', $templateId, $userId);
    $jobSuccess = mysqli_stmt_execute($stmt);
    
    if ($jobSuccess) {
        $jobId = mysqli_insert_id($con);
        
        // Add items to the print job
        $itemSuccess = true;
        foreach ($data['items'] as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'] ?? 1;
            $batchId = $item['batch_id'] ?? null;
            
            $itemQuery = "INSERT INTO barcode_print_items 
                        (job_id, product_id, quantity, batch_id) 
                        VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $itemQuery);
            mysqli_stmt_bind_param($stmt, 'iiis', $jobId, $productId, $quantity, $batchId);
            $result = mysqli_stmt_execute($stmt);
            
            if (!$result) {
                $itemSuccess = false;
                break;
            }
        }
    }
}

// Return response
header('Content-Type: application/json');
if ($success) {
    echo json_encode([
        'success' => true, 
        'message' => $message,
        'template_id' => $templateId
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to save template',
        'error' => mysqli_error($con)
    ]);
} 