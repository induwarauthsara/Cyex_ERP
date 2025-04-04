<?php
// File: /AdminPanel/barcode-print/api/thermal_print.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include Composer autoloader
require '../../../vendor/autoload.php';

// Use Mike42\Escpos library
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;

// Handle request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$action = $data['action'] ?? null;

if ($action !== 'thermal_print') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Get required parameters
$printerIp = $data['printer_ip'] ?? null;
$printerPort = intval($data['printer_port'] ?? 9100);
$settings = $data['settings'] ?? [];
$items = $data['items'] ?? [];

// Validate input
if (!$printerIp) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Printer IP address is required']);
    exit;
}

if (empty($items)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No items to print']);
    exit;
}

// Function to truncate text based on paper size
function truncateText($text, $maxLength = 20) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    return $text;
}

try {
    // Connect to printer
    $connector = new NetworkPrintConnector($printerIp, $printerPort);
    $printer = new Printer($connector);
    
    // Process each item
    foreach ($items as $item) {
        $quantity = intval($item['quantity']);
        
        // Print the requested number of copies
        for ($i = 0; $i < $quantity; $i++) {
            // Reset to default
            $printer->initialize();
            
            // Set text size - normal
            $printer->setTextSize(1, 1);
            
            // Add shop name if enabled
            if (isset($settings['show_shop_name']) && $settings['show_shop_name'] && isset($settings['shop_name']) && !empty($settings['shop_name'])) {
                $shopName = $settings['shop_name'];
                $printer->text(truncateText($shopName, 30) . "\n");
            }
            
            // Add product name if enabled
            if (isset($settings['show_product_name']) && $settings['show_product_name'] && isset($item['product_name']) && !empty($item['product_name'])) {
                $productName = $item['product_name'];
                $printer->text(truncateText($productName, 30) . "\n");
            }
            
            // Get barcode
            $barcode = $item['barcode'] ?? $item['product_id'];
            if (empty($barcode)) $barcode = $item['product_id'];
            
            // Determine barcode type based on content
            if (strlen($barcode) === 13 && is_numeric($barcode)) {
                // Set barcode height
                $printer->setBarcodeHeight(50);
                $printer->setBarcodeWidth(2);
                $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
                
                // Print barcode - BARCODE_JAN13 is equivalent to EAN13 in this library
                $printer->barcode($barcode, Printer::BARCODE_JAN13);
            } else {
                // Set barcode height
                $printer->setBarcodeHeight(50);
                $printer->setBarcodeWidth(2);
                $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
                
                // Print barcode as CODE128
                $printer->barcode($barcode, Printer::BARCODE_CODE128);
            }
            
            $printer->feed();
            
            // Add price if enabled
            if (isset($settings['show_price']) && $settings['show_price'] && isset($item['price']) && !empty($item['price'])) {
                $price = 'Price: ' . number_format($item['price'], 0, '.', ',');
                $printer->text($price . "\n");
            }
            
            // Add promotional price if enabled
            if (isset($settings['show_promo_price']) && $settings['show_promo_price'] && isset($item['promotional_price']) && !empty($item['promotional_price'])) {
                $promoPrice = 'Promo: ' . number_format($item['promotional_price'], 0, '.', ',');
                $printer->text($promoPrice . "\n");
            }
            
            // Add category if enabled
            if (isset($settings['show_category']) && $settings['show_category'] && isset($item['category_name']) && !empty($item['category_name'])) {
                $category = 'Cat: ' . $item['category_name'];
                $printer->text(truncateText($category, 30) . "\n");
            }
            
            // Add unit if enabled
            if (isset($settings['show_unit']) && $settings['show_unit'] && isset($item['unit']) && !empty($item['unit'])) {
                $unit = 'Unit: ' . $item['unit'];
                $printer->text($unit . "\n");
            }
            
            // Add batch number if available
            if (isset($item['batch_number']) && !empty($item['batch_number'])) {
                $batch = 'Batch: ' . $item['batch_number'];
                $printer->text($batch . "\n");
            }
            
            // Cut the paper
            $printer->cut();
        }
    }
    
    // Close connection
    $printer->close();
    
    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Labels sent to printer successfully']);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Printer error: ' . $e->getMessage()]);
} 