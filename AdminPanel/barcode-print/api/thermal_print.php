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
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

// Handle request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$action = $data['action'] ?? null;

if ($action === 'get_printers') {
    // Get list of available printers on Windows
    try {
        $printers = array();
        
        // For Windows systems
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Execute the WMI query to get printer information
            $output = array();
            exec('wmic printer get name', $output);
            
            // Process the output (skip first line which is the header)
            if (count($output) > 1) {
                for ($i = 1; $i < count($output); $i++) {
                    $printer = trim($output[$i]);
                    if (!empty($printer)) {
                        $printers[] = array(
                            'name' => $printer,
                            'type' => 'windows'
                        );
                    }
                }
            }
            
            // Also add USB ports as options
            for ($i = 1; $i <= 9; $i++) {
                $printers[] = array(
                    'name' => 'USB00' . $i,
                    'type' => 'usb'
                );
            }
            
            // Add COM ports for legacy receipt printers
            for ($i = 1; $i <= 9; $i++) {
                $printers[] = array(
                    'name' => 'COM' . $i,
                    'type' => 'com'
                );
            }
            
            // Add LPT ports for parallel port printers
            for ($i = 1; $i <= 3; $i++) {
                $printers[] = array(
                    'name' => 'LPT' . $i,
                    'type' => 'lpt'
                );
            }
        } else {
            // For Linux/Unix systems
            $output = array();
            exec('lpstat -p | awk \'{print $2}\'', $output);
            
            foreach ($output as $printer) {
                $printer = trim($printer);
                if (!empty($printer)) {
                    $printers[] = array(
                        'name' => $printer,
                        'type' => 'cups'
                    );
                }
            }
            
            // Also add USB devices
            if (file_exists('/dev/usb')) {
                $usbDevices = scandir('/dev/usb');
                foreach ($usbDevices as $device) {
                    if ($device != '.' && $device != '..' && strpos($device, 'lp') === 0) {
                        $printers[] = array(
                            'name' => '/dev/usb/' . $device,
                            'type' => 'file'
                        );
                    }
                }
            }
        }
        
        // Return the list of printers
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'printers' => $printers]);
        exit;
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error retrieving printer list: ' . $e->getMessage()]);
        exit;
    }
}

if ($action !== 'thermal_print') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}

// Get required parameters
$printerType = $data['printer_type'] ?? 'network';
$printerName = $data['printer_name'] ?? null;
$printerIp = $data['printer_ip'] ?? null;
$printerPort = intval($data['printer_port'] ?? 9100);
$settings = $data['settings'] ?? [];
$items = $data['items'] ?? [];

// Validate input
if ($printerType === 'network' && !$printerIp) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Printer IP address is required for network printing']);
    exit;
}

if ($printerType === 'windows' && !$printerName) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Printer name is required for Windows printing']);
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
    // Create appropriate connector based on printer type
    switch ($printerType) {
        case 'network':
            $connector = new NetworkPrintConnector($printerIp, $printerPort);
            break;
        case 'windows':
            $connector = new WindowsPrintConnector($printerName);
            break;
        case 'usb':
        case 'com':
        case 'lpt':
            // For USB, COM, and LPT ports on Windows
            $connector = new WindowsPrintConnector($printerName);
            break;
        case 'file':
            // For direct file printing on Unix/Linux
            $connector = new FilePrintConnector($printerName);
            break;
        default:
            throw new Exception('Unsupported printer type: ' . $printerType);
    }
    
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
            
            // Check both discount_price and promotional_price
            if (isset($settings['show_promo_price']) && $settings['show_promo_price']) {
                if (isset($item['discount_price']) && !empty($item['discount_price'])) {
                    $promoPrice = 'Promo: ' . number_format($item['discount_price'], 0, '.', ',');
                    $printer->text($promoPrice . "\n");
                } elseif (isset($item['promotional_price']) && !empty($item['promotional_price'])) {
                    $promoPrice = 'Promo: ' . number_format($item['promotional_price'], 0, '.', ',');
                    $printer->text($promoPrice . "\n");
                }
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