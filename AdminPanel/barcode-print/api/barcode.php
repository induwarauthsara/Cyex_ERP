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
    case 'save_template':
        // Redirect to save_template.php instead of including it
        header('Location: save_template.php');
        exit;
            case 'get_template':
        // This action should be handled via direct GET request to get_template.php
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Template retrieval must be done via GET request to get_template.php']);
        exit;
    default:
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
}

// Function to truncate text based on paper size
function truncateText($text, $paperWidth, $fontSize = 6) {
    // Define max characters based on paper size
    $maxLengths = [
        30 => 15, // 30mm width
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
    
    // More compact line spacing based on font size (70% of font size)
    $lineSpacing = $fontSize * 0.7;
    
    // Set font
    $pdf->SetFont('helvetica', '', $fontSize);
    
    // Start Y position
    $yPos = $margin;
    
    // Calculate total available height
    $paperHeight = $paperWidth === 30 ? 15 : $paperWidth / 2;
    $availableHeight = $paperHeight - (2 * $margin);
    
    // Add shop name if enabled
    if (isset($settings['show_shop_name']) && $settings['show_shop_name'] && isset($settings['shop_name']) && !empty($settings['shop_name'])) {
        $shopName = $settings['shop_name'];
        // Use a smaller font for shop name
        $pdf->SetFont('helvetica', 'B', $fontSize * 0.9);
        $pdf->Text($margin, $yPos, truncateText($shopName, $paperWidth, $fontSize));
        $yPos += $lineSpacing;
        // Reset font
        $pdf->SetFont('helvetica', '', $fontSize);
        // Adjust Space for shop name
        $space_adjustment_for_shop_name = 2;
    }else{
        $space_adjustment_for_shop_name = 0;
    }
    
    // Add product name if available
    if (isset($settings['show_product_name']) && $settings['show_product_name'] && isset($product['product_name']) && !empty($product['product_name'])) {
        $productName = $product['product_name'];
        $pdf->Text($margin, $yPos, truncateText($productName, $paperWidth, $fontSize));
        $yPos += $lineSpacing;
    }
    
    // Add barcode - calculate appropriate height based on remaining space
    $barcode = $product['barcode'] ?? $product['product_id'];
    if (empty($barcode)) $barcode = $product['product_id'];
    
    // Determine barcode type based on content
    $barcodeType = 'CODE128';
    if (strlen($barcode) === 13 && is_numeric($barcode)) {
        $barcodeType = 'EAN13';
    }
    
    // Calculate how much space we've used and what's left for the barcode
    $usedSpace = $yPos - $margin;
    $textSpaceNeeded = $lineSpacing * 3; // Space for price, unit, etc.
    
    // Set barcode height - use user's setting but ensure it fits
    $barcodeHeight = isset($settings['barcode_height']) && !empty($settings['barcode_height']) 
        ? min($settings['barcode_height'], $availableHeight - $usedSpace - $textSpaceNeeded)
        : min(10, $availableHeight - $usedSpace - $textSpaceNeeded);
    
    // Calculate barcode width - leave some margin
    $barcodeWidth = $paperWidth - (2 * $margin);
    
    // Generate and place barcode
    $style = [
        'position' => 'N', 
        'stretch' => true,
        'fitwidth' => true,
        'cellfitalign' => '',
        'hpadding' => 0,
        'vpadding' => 0,
        'fgcolor' => [0, 0, 0],
        'bgcolor' => false,
        'text' => true,
        'font' => 'helvetica',
        'fontsize' => $fontSize * 1.2, // Larger text for barcode
        'stretchtext' => 0,
        'textposition' => 'bottom'
    ];
    
    $pdf->write1DBarcode(
        $barcode,
        $barcodeType,
        $margin,
        $yPos - $space_adjustment_for_shop_name,
        $barcodeWidth,
        $barcodeHeight,
        0.4,
        $style
    );
    
    // Move position down after barcode - account for barcode text
    $yPos += $barcodeHeight + $fontSize - $space_adjustment_for_shop_name;
    
    // Use smaller font for remaining text
    $pdf->SetFont('helvetica', '', $fontSize * 0.9);
    
    // Create a row for price with proper spacing - centered
    $xPosCenter = $margin + ($paperWidth - 2 * $margin) / 2;
    
    // Show only regular price - centered on the label for better visibility
    if (isset($settings['show_price']) && $settings['show_price'] && isset($product['price']) && !empty($product['price'])) {
        $price = 'Rs. ' . number_format($product['price'], 0, '.', ',');
        
        // Calculate text width to center it properly
        $textWidth = $pdf->GetStringWidth($price);
        $centerX = ($paperWidth - $textWidth) / 2;
        
        // Display the price centered
        $pdf->Text($centerX, $yPos + $space_adjustment_for_shop_name, $price);
        
        $yPos += $lineSpacing;
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
    $pdf->SetCreator('CyexTech Solutions Barcode System');
    $pdf->SetAuthor('CyexTech Solutions');
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
