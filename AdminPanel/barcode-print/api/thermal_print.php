<?php
// File: /AdminPanel/barcode-print/api/thermal_print.php
require_once('../../auth.php');
require_once '../../../vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    // Get JSON data
    $jsonData = json_decode(file_get_contents('php://input'), true);
    
    if (!$jsonData) {
        throw new Exception('Invalid JSON data');
    }
    
    $action = $jsonData['action'] ?? '';
    $printerIp = $jsonData['printer_ip'] ?? '';
    $printerPort = $jsonData['printer_port'] ?? 9100;
    $items = $jsonData['items'] ?? [];
    $settings = $jsonData['settings'] ?? [];
    
    if (empty($items)) {
        throw new Exception('No items to print');
    }
    
    if (empty($printerIp)) {
        throw new Exception('Printer IP address is required');
    }
    
    if ($action !== 'thermal_print') {
        throw new Exception('Invalid action specified');
    }
    
    // Generate and send thermal print commands
    printThermalLabels($printerIp, $printerPort, $items, $settings);
    
    echo json_encode([
        'success' => true,
        'message' => 'Labels sent to printer successfully'
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

/**
 * Print labels to a thermal printer using ESC/POS commands
 * 
 * @param string $printerIp IP address of the printer
 * @param int $printerPort Port number of the printer
 * @param array $items Items to print
 * @param array $settings Print settings
 */
function printThermalLabels($printerIp, $printerPort, $items, $settings)
{
    try {
        // Create printer connector
        $connector = new NetworkPrintConnector($printerIp, $printerPort);
        $printer = new Printer($connector);
        
        // Get settings with defaults
        $fontSize = $settings['font_size'] ?? 1;
        $barcodeHeight = $settings['barcode_height'] ?? 30;
        $showShopName = $settings['show_shop_name'] ?? true;
        $shopName = $settings['shop_name'] ?? '';
        $showProductName = $settings['show_product_name'] ?? true;
        $showPrice = $settings['show_price'] ?? true;
        $showUnit = $settings['show_unit'] ?? false;
        $showCategory = $settings['show_category'] ?? false;
        
        // Process each item
        foreach ($items as $item) {
            // Expand based on quantity
            $quantity = $item['quantity'] ?? 1;
            
            for ($i = 0; $i < $quantity; $i++) {
                // Start with a clean label
                $printer->initialize();
                
                // Shop name
                if ($showShopName && !empty($shopName)) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->setEmphasis(true);
                    $printer->text($shopName . "\n");
                    $printer->setEmphasis(false);
                    $printer->feed(1);
                }
                
                // Product name
                if ($showProductName && !empty($item['product_name'])) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text($item['product_name'] . "\n");
                    $printer->feed(1);
                }
                
                // Barcode
                $barcode = !empty($item['barcode']) ? $item['barcode'] : (!empty($item['product_id']) ? $item['product_id'] : '');
                
                if (!empty($barcode)) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    
                    // Determine barcode type based on content
                    if (is_numeric($barcode) && strlen($barcode) == 13) {
                        $printer->setBarcodeHeight($barcodeHeight);
                        $printer->setBarcodeWidth(2); // Medium width
                        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
                        $printer->barcode($barcode, Printer::BARCODE_JAN13); // EAN13 = JAN13 in this library
                    } else {
                        $printer->setBarcodeHeight($barcodeHeight);
                        $printer->setBarcodeWidth(2); // Medium width
                        $printer->setBarcodeTextPosition(Printer::BARCODE_TEXT_BELOW);
                        $printer->barcode($barcode, Printer::BARCODE_CODE128);
                    }
                    
                    $printer->feed(1);
                }
                
                // Price
                if ($showPrice && isset($item['price'])) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    $printer->text("Rs. " . number_format((float)$item['price'], 2) . "\n");
                    
                    // Promotional price
                    if (!empty($item['discount_price']) && $item['discount_price'] > 0) {
                        $printer->text("Promo: Rs. " . number_format((float)$item['discount_price'], 2) . "\n");
                    }
                    
                    $printer->feed(1);
                }
                
                // Category and Unit
                if (($showCategory && !empty($item['category'])) || ($showUnit && !empty($item['unit']))) {
                    $printer->setJustification(Printer::JUSTIFY_CENTER);
                    
                    $line = "";
                    if ($showCategory && !empty($item['category'])) {
                        $line .= $item['category'];
                    }
                    
                    if ($showUnit && !empty($item['unit'])) {
                        if (!empty($line)) {
                            $line .= " | ";
                        }
                        $line .= $item['unit'];
                    }
                    
                    $printer->text($line . "\n");
                    $printer->feed(1);
                }
                
                // Cut the paper
                $printer->cut();
            }
        }
        
        // Close the printer connection
        $printer->close();
        
    } catch (Exception $e) {
        throw new Exception('Printer error: ' . $e->getMessage());
    }
} 