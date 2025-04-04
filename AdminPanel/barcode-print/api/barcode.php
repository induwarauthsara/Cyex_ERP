<?php
// File: /AdminPanel/barcode-print/api/barcode.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Include Composer autoloader
require '../../../vendor/autoload.php';

// Use necessary libraries
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

// Handle request
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$action = $data['action'] ?? null;

if (!$action) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Missing action']);
    exit;
}

// Handle different actions
switch ($action) {
    case 'print':
        handleBarcodeGeneration($data);
        break;
    case 'preview':
        handleBarcodeGeneration($data);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

// Function to truncate text based on paper size
function truncateText($text, $paperWidth, $fontSize = 8) {
    // Define max characters based on paper size
    $maxLengths = [
        30 => 12, // 30mm width
        40 => 16, // 40mm width
        50 => 20, // 50mm width
        60 => 24, // 60mm width
        70 => 28  // 70mm width
    ];
    
    // Default max length
    $maxLength = 20;
    
    // Find the closest paper size
    foreach ($maxLengths as $size => $length) {
        if ($paperWidth <= $size) {
            $maxLength = $length;
            break;
        }
    }
    
    // Adjust max length based on font size
    $fontSizeAdjustment = 8 / $fontSize;
    $maxLength = round($maxLength * $fontSizeAdjustment);
    
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength) . '...';
    }
    
    return $text;
}

/**
 * Generate a barcode label for the product
 * @param TCPDF $pdf PDF object
 * @param array $settings Settings for the label
 * @param array $product Product data
 * @return void
 */
function generateBarcodeLabel($pdf, $settings, $product) {
    // Get font size and set a default if not provided
    $fontSize = isset($settings['font_size']) && !empty($settings['font_size']) ? $settings['font_size'] : 8;
    $margin = isset($settings['margin']) && !empty($settings['margin']) ? $settings['margin'] : 1;
    $paperWidth = isset($settings['paper_size']) && !empty($settings['paper_size']) ? $settings['paper_size'] : 30;
    
    // Line spacing based on font size
    $lineSpacing = $fontSize * 0.8; // 80% of font size for spacing
    
    // Set font
    $pdf->SetFont('helvetica', '', $fontSize);
    
    // Start Y position
    $yPos = $margin;
    
    // Add shop name if enabled
    if (isset($settings['show_shop_name']) && $settings['show_shop_name'] && isset($settings['shop_name']) && !empty($settings['shop_name'])) {
        $shopName = $settings['shop_name'];
        $pdf->Text($margin, $yPos, truncateText($shopName, $paperWidth, $fontSize));
        $yPos += $lineSpacing;
    }
    
    // Add product name if available
    if (isset($settings['show_product_name']) && $settings['show_product_name'] && isset($product['product_name']) && !empty($product['product_name'])) {
        $productName = $product['product_name'];
        $pdf->Text($margin, $yPos, truncateText($productName, $paperWidth, $fontSize));
        $yPos += $lineSpacing;
    }
    
    // Add barcode
    $barcode = $product['barcode'] ?? $product['product_id'];
    if (empty($barcode)) $barcode = $product['product_id'];
    
    // Determine barcode type based on content
    $barcodeType = 'EAN13';
    if (strlen($barcode) === 13 && is_numeric($barcode)) {
        $barcodeType = 'EAN13';
    } else {
        $barcodeType = 'CODE128';
    }
    
    // Set barcode height based on paper size
    $barcodeHeight = isset($settings['barcode_height']) && !empty($settings['barcode_height']) 
        ? $settings['barcode_height'] 
        : min(10, $paperWidth / 3);
    
    // Calculate barcode width - leave some margin
    $barcodeWidth = $paperWidth - (2 * $margin);
    
    // Generate and place barcode
    $pdf->write1DBarcode(
        $barcode,
        $barcodeType,
        $margin,
        $yPos,
        $barcodeWidth,
        $barcodeHeight,
        0.4,
        ['position' => 'N', 'stretch' => true]
    );
    
    // Move position down after barcode
    $yPos += $barcodeHeight + $lineSpacing;
    
    // Add price if available
    if (isset($settings['show_price']) && $settings['show_price'] && isset($product['price']) && !empty($product['price'])) {
        $price = 'Price: ' . number_format($product['price'], 0, '.', ',');
        $pdf->Text($margin, $yPos, $price);
        $yPos += $lineSpacing;
    }
    
    // Add promotional price if available
    if (isset($settings['show_promo_price']) && $settings['show_promo_price'] && isset($product['promotional_price']) && !empty($product['promotional_price'])) {
        $promoPrice = 'Promo: ' . number_format($product['promotional_price'], 0, '.', ',');
        $pdf->Text($margin, $yPos, $promoPrice);
        $yPos += $lineSpacing;
    }
    
    // Add category if available
    if (isset($settings['show_category']) && $settings['show_category'] && isset($product['category_name']) && !empty($product['category_name'])) {
        $category = 'Cat: ' . $product['category_name'];
        $pdf->Text($margin, $yPos, truncateText($category, $paperWidth, $fontSize));
        $yPos += $lineSpacing;
    }
    
    // Add unit if available
    if (isset($settings['show_unit']) && $settings['show_unit'] && isset($product['unit']) && !empty($product['unit'])) {
        $unit = 'Unit: ' . $product['unit'];
        $pdf->Text($margin, $yPos, $unit);
        $yPos += $lineSpacing;
    }
    
    // Add batch number if available
    if (isset($product['batch_number']) && !empty($product['batch_number'])) {
        $batch = 'Batch: ' . $product['batch_number'];
        $pdf->Text($margin, $yPos, $batch);
    }
}

/**
 * Handle barcode generation
 * @param array $data Request data
 * @return void
 */
function handleBarcodeGeneration($data) {
    // Get data
    $settings = $data['settings'] ?? [];
    $items = $data['items'] ?? [];
    
    // Validate data
    if (empty($items)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No items to print']);
        exit;
    }
    
    // Get paper size
    $paperWidth = isset($settings['paper_size']) && !empty($settings['paper_size']) ? floatval($settings['paper_size']) : 30;
    $paperHeight = $paperWidth === 30 ? 15 : $paperWidth / 2;
    
    // Create PDF
    $pdf = new TCPDF('L', 'mm', [$paperWidth, $paperHeight], true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Srijaya Barcode System');
    $pdf->SetAuthor('Srijaya');
    $pdf->SetTitle('Barcode Label');
    
    // Set margins
    $margin = isset($settings['margin']) && !empty($settings['margin']) ? floatval($settings['margin']) : 1;
    $pdf->SetMargins($margin, $margin, $margin);
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Disable auto page break
    $pdf->SetAutoPageBreak(false, 0);
    
    // Set display mode
    $pdf->SetDisplayMode('fullpage', 'SinglePage', 'UseNone');
    
    // Generate barcodes
    foreach ($items as $item) {
        $quantity = intval($item['quantity']);
        
        // Print the requested number of copies
        for ($i = 0; $i < $quantity; $i++) {
            // Add a page for each label
            $pdf->AddPage();
            
            // Generate barcode label
            generateBarcodeLabel($pdf, $settings, $item);
        }
    }
    
    // Output PDF
    $pdf->Output('barcodes.pdf', 'I');
    exit;
}
