<?php
// File: /AdminPanel/barcode-print/api/barcode.php
ob_start();
require_once('../../nav.php');

// Try to load vendor autoload from different possible locations
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../../vendor/autoload.php';
}

use Picqer\Barcode\BarcodeGeneratorPNG;

ob_clean();
// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';

        switch ($action) {
            case 'get_template':
                handleGetTemplate();
                break;

            default:
                throw new Exception('Invalid action specified');
        }
    } else {
        $jsonData = json_decode(file_get_contents('php://input'), true);
        $action = $jsonData['action'] ?? '';

        switch ($action) {
            case 'preview':
                handleBarcodeGeneration($jsonData, true);
                break;

            case 'print':
                // Check if thermal printing is requested
                if (isset($jsonData['print_format']) && in_array($jsonData['print_format'], ['tspl', 'epl', 'dpl', 'zpl'])) {
                    handleThermalPrinting($jsonData);
                } else {
                    handleBarcodeGeneration($jsonData, false);
                }
                break;

            case 'save_template':
                handleSaveTemplate($jsonData);
                break;

            default:
                throw new Exception('Invalid action specified');
        }
    }
} catch (Exception $e) {
    if (!headers_sent()) {
        header('Content-Type: application/json');
        http_response_code(400);
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleThermalPrinting($data)
{
    $settings = $data['settings'] ?? [];
    $items = $data['items'] ?? [];
    $printFormat = strtolower($data['print_format'] ?? 'tspl');
    
    if (empty($items)) {
        throw new Exception('No items to print');
    }

    // Generate appropriate thermal printer commands
    switch ($printFormat) {
        case 'tspl':
            $commands = generateTSPLCommands($items, $settings);
            break;
        case 'epl':
            $commands = generateEPLCommands($items, $settings);
            break;
        case 'zpl':
            $commands = generateZPLCommands($items, $settings);
            break;
        case 'dpl':
            $commands = generateDPLCommands($items, $settings);
            break;
        default:
            $commands = generateTSPLCommands($items, $settings);
    }
    
    // Output commands
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="labels.' . $printFormat . '"');
    echo $commands;
}

function generateTSPLCommands($items, $settings)
{
    // Set dimensions based on settings
    $labelWidth = $settings['paper_size'] ?? 30;
    $labelHeight = ($labelWidth == 30) ? 15 : $labelWidth;
    $gap = $settings['gap_between'] ?? 3;
    $density = 8; // Default density
    $commands = "";
    
    // Expand items based on quantity
    $expandedItems = [];
    foreach ($items as $item) {
        $qty = $item['quantity'] ?? 1;
        for ($i = 0; $i < $qty; $i++) {
            $expandedItems[] = $item;
        }
    }
    
    // Add initialization command
    $commands .= "SIZE {$labelWidth} mm, {$labelHeight} mm\n";
    $commands .= "GAP {$gap} mm, 0 mm\n";
    $commands .= "DIRECTION 1\n";
    $commands .= "REFERENCE 0,0\n";
    $commands .= "OFFSET 0 mm\n";
    $commands .= "SET TEAR ON\n";
    $commands .= "SET PEEL OFF\n";
    $commands .= "SET CUTTER OFF\n";
    $commands .= "SET PARTIAL_CUTTER OFF\n";
    $commands .= "CODEPAGE UTF-8\n";
    $commands .= "COUNTRY 044\n";
    
    // Process each expanded item
    foreach ($expandedItems as $index => $item) {
        // Clear buffer for each label
        $commands .= "CLS\n";
        
        // Add shop name if enabled
        if ($settings['show_shop_name']) {
            $shopName = $settings['shop_name'] ?? 'Shop Name';
            $commands .= "TEXT 15,10,\"2\",0,1,1,\"{$shopName}\"\n";
        }
        
        // Add product name if enabled
        if ($settings['show_product_name']) {
            $productName = $item['product_name'];
            $commands .= "TEXT 15,30,\"2\",0,1,1,\"{$productName}\"\n";
        }
        
        // Add barcode
        $yPos = $settings['show_product_name'] ? 50 : 20;
        if (!empty($item['barcode'])) {
            $barcode = $item['barcode'];
            $barcodeHeight = $settings['barcode_height'] ?? 10;
            
            if (strlen($barcode) <= 13) {
                // Linear barcode
                $barcodeType = strlen($barcode) == 13 ? "EAN13" : "128";
                $commands .= "BARCODE 15,{$yPos},\"{$barcodeType}\",{$barcodeHeight},{$density},0,2,2,\"{$barcode}\"\n";
            } else {
                // QR code
                $commands .= "QRCODE 60,{$yPos},L,5,A,0,\"{$barcode}\"\n";
            }
        }
        
        // Add price if enabled
        if ($settings['show_price']) {
            $price = "Rs. " . number_format($item['price'], 2);
            $yPos += 35;
            $commands .= "TEXT 15,{$yPos},\"2\",0,1,1,\"{$price}\"\n";
        }
        
        // Add promotional price if enabled
        if ($settings['show_promo_price'] && !empty($item['discount_price'])) {
            $promoPrice = "Rs. " . number_format($item['discount_price'], 2);
            $yPos += 15;
            $commands .= "TEXT 15,{$yPos},\"2\",0,1,1,\"{$promoPrice}\"\n";
        }
        
        // Print the label
        $commands .= "PRINT 1,1\n";
    }
    
    return $commands;
}

function generateEPLCommands($items, $settings)
{
    // Similar structure as TSPL but with EPL syntax
    $labelWidth = $settings['paper_size'] ?? 30;
    $labelHeight = ($labelWidth == 30) ? 15 : $labelWidth;
    $commands = "";
    
    // Expand items based on quantity
    $expandedItems = [];
    foreach ($items as $item) {
        $qty = $item['quantity'] ?? 1;
        for ($i = 0; $i < $qty; $i++) {
            $expandedItems[] = $item;
        }
    }
    
    // Process each expanded item
    foreach ($expandedItems as $index => $item) {
        // Clear previous image and set up label
        $commands .= "N\n";
        $commands .= "q{$labelWidth}0\n"; // Set width in dots (203 dpi = 8 dots/mm)
        
        // Add shop name if enabled
        $yPos = 10;
        if ($settings['show_shop_name']) {
            $shopName = $settings['shop_name'] ?? 'Shop Name';
            $commands .= "A20,{$yPos},0,2,1,1,N,\"{$shopName}\"\n";
            $yPos += 20;
        }
        
        // Add product name if enabled
        if ($settings['show_product_name']) {
            $productName = $item['product_name'];
            $commands .= "A20,{$yPos},0,2,1,1,N,\"{$productName}\"\n";
            $yPos += 30;
        }
        
        // Add barcode
        if (!empty($item['barcode'])) {
            $barcode = $item['barcode'];
            $barcodeHeight = ($settings['barcode_height'] ?? 10) * 8; // Convert mm to dots
            
            if (strlen($barcode) <= 13) {
                // Linear barcode
                $barcodeType = strlen($barcode) == 13 ? "3" : "1"; // 3 for EAN13, 1 for Code 128
                $commands .= "B20,{$yPos},0,{$barcodeType},2,2,{$barcodeHeight},B,\"{$barcode}\"\n";
                $yPos += $barcodeHeight + 15;
            } else {
                // QR code (EPL doesn't directly support QR, use a workaround)
                $commands .= "B20,{$yPos},0,Q,2,2,\"{$barcode}\"\n";
                $yPos += 100;
            }
        }
        
        // Add price if enabled
        if ($settings['show_price']) {
            $price = "Rs. " . number_format($item['price'], 2);
            $commands .= "A20,{$yPos},0,2,1,1,N,\"{$price}\"\n";
            $yPos += 20;
        }
        
        // Print the label
        $commands .= "P1\n";
    }
    
    return $commands;
}

function generateZPLCommands($items, $settings)
{
    // ZPL commands for Zebra printers
    $labelWidth = $settings['paper_size'] ?? 30;
    $labelHeight = ($labelWidth == 30) ? 15 : $labelWidth;
    $commands = "";
    
    // Expand items based on quantity
    $expandedItems = [];
    foreach ($items as $item) {
        $qty = $item['quantity'] ?? 1;
        for ($i = 0; $i < $qty; $i++) {
            $expandedItems[] = $item;
        }
    }
    
    // Process each expanded item
    foreach ($expandedItems as $index => $item) {
        // Initialize label format
        $commands .= "^XA\n";
        
        // Set label dimensions (convert mm to dots at 203 dpi)
        $widthDots = round($labelWidth * 8);
        $heightDots = round($labelHeight * 8);
        $commands .= "^PW{$widthDots}\n";
        $commands .= "^LL{$heightDots}\n";
        
        // Add shop name if enabled
        $yPos = 10;
        if ($settings['show_shop_name']) {
            $shopName = $settings['shop_name'] ?? 'Shop Name';
            $commands .= "^FO20,{$yPos}^A0N,20,20^FD{$shopName}^FS\n";
            $yPos += 25;
        }
        
        // Add product name if enabled
        if ($settings['show_product_name']) {
            $productName = $item['product_name'];
            $commands .= "^FO20,{$yPos}^A0N,20,20^FD{$productName}^FS\n";
            $yPos += 30;
        }
        
        // Add barcode
        if (!empty($item['barcode'])) {
            $barcode = $item['barcode'];
            $barcodeHeight = ($settings['barcode_height'] ?? 10) * 8; // Convert mm to dots
            
            if (strlen($barcode) <= 13) {
                // Linear barcode
                if (strlen($barcode) == 13) {
                    // EAN-13
                    $commands .= "^FO20,{$yPos}^BY2^BEN,{$barcodeHeight},Y,N^FD{$barcode}^FS\n";
                } else {
                    // Code 128
                    $commands .= "^FO20,{$yPos}^BY2^BCN,{$barcodeHeight},Y,N,N^FD{$barcode}^FS\n";
                }
                $yPos += $barcodeHeight + 15;
            } else {
                // QR code
                $commands .= "^FO60,{$yPos}^BQN,2,5^FD{$barcode}^FS\n";
                $yPos += 120;
            }
        }
        
        // Add price if enabled
        if ($settings['show_price']) {
            $price = "Rs. " . number_format($item['price'], 2);
            $commands .= "^FO20,{$yPos}^A0N,20,20^FD{$price}^FS\n";
            $yPos += 25;
        }
        
        // End label format
        $commands .= "^XZ\n";
    }
    
    return $commands;
}

function generateDPLCommands($items, $settings)
{
    // DPL commands for Datamax printers
    $labelWidth = $settings['paper_size'] ?? 30;
    $labelHeight = ($labelWidth == 30) ? 15 : $labelWidth;
    $commands = "";
    
    // Expand items based on quantity
    $expandedItems = [];
    foreach ($items as $item) {
        $qty = $item['quantity'] ?? 1;
        for ($i = 0; $i < $qty; $i++) {
            $expandedItems[] = $item;
        }
    }
    
    // Process each expanded item
    foreach ($expandedItems as $index => $item) {
        // Initialize label
        $commands .= "<STX><ESC>D<ETX>\n"; // Clear image buffer
        $commands .= "<STX><ESC>P<ETX>\n"; // Print mode
        
        // Set label dimensions
        $widthDots = round($labelWidth * 8);
        $heightDots = round($labelHeight * 8);
        $commands .= "<STX><ESC>S{$widthDots},{$heightDots}<ETX>\n";
        
        // Add shop name if enabled
        $yPos = 10;
        if ($settings['show_shop_name']) {
            $shopName = $settings['shop_name'] ?? 'Shop Name';
            $commands .= "<STX><ESC>T20,{$yPos},2,0,0,0,0,\"$shopName\"<ETX>\n";
            $yPos += 25;
        }
        
        // Add product name if enabled
        if ($settings['show_product_name']) {
            $productName = $item['product_name'];
            $commands .= "<STX><ESC>T20,{$yPos},2,0,0,0,0,\"$productName\"<ETX>\n";
            $yPos += 30;
        }
        
        // Add barcode
        if (!empty($item['barcode'])) {
            $barcode = $item['barcode'];
            $barcodeHeight = ($settings['barcode_height'] ?? 10) * 8; // Convert mm to dots
            
            if (strlen($barcode) <= 13) {
                // Linear barcode
                $barcodeType = strlen($barcode) == 13 ? "E30" : "128"; // E30 for EAN13, 128 for Code 128
                $commands .= "<STX><ESC>B20,{$yPos},{$barcodeType},2,2,2,{$barcodeHeight},\"$barcode\"<ETX>\n";
                $yPos += $barcodeHeight + 15;
            } else {
                // QR code
                $commands .= "<STX><ESC>B60,{$yPos},QR,200,\"$barcode\"<ETX>\n";
                $yPos += 120;
            }
        }
        
        // Add price if enabled
        if ($settings['show_price']) {
            $price = "Rs. " . number_format($item['price'], 2);
            $commands .= "<STX><ESC>T20,{$yPos},2,0,0,0,0,\"$price\"<ETX>\n";
            $yPos += 25;
        }
        
        // Print the label
        $commands .= "<STX><ESC>E1<ETX>\n"; // Print one copy
    }
    
    return $commands;
}

function handleBarcodeGeneration($data, $isPreview = false)
{
    global $ERP_COMPANY_NAME;

    $settings = $data['settings'] ?? [];
    $items = $data['items'] ?? [];

    if (empty($items)) {
        throw new Exception('No items to print');
    }

    // Get paper dimensions based on selected size
    $paperSize = isset($settings['paper_size']) ? (float)$settings['paper_size'] : 30;
    $paperHeight = ($paperSize == 30) ? 15 : $paperSize;
    
    // Create PDF with custom page size
    $pdf = new TCPDF(
        'L',  // Landscape orientation for better barcode display
        'mm', // Unit in millimeters
        [$paperSize, $paperHeight],
        true,
        'UTF-8'
    );

    // Set document properties
    $pdf->SetCreator($ERP_COMPANY_NAME);
    $pdf->SetAuthor($ERP_COMPANY_NAME);
    $pdf->SetTitle('Product Barcodes');
    
    // Set full page display mode
    $pdf->SetDisplayMode('fullpage');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins
    $margin = $settings['margin'] ?? 1;
    $pdf->SetMargins($margin, $margin, $margin);
    
    // Disable automatic page breaks to have full control of label placement
    $pdf->SetAutoPageBreak(false);

    // Set font
    $pdf->SetFont('helvetica', '', $settings['font_size'] ?? 8);

    // Calculate dimensions based on paper size
    $dimensions = calculateDimensions($settings);

    // Generate barcodes
    foreach ($items as $item) {
        for ($i = 0; $i < ($item['quantity'] ?? 1); $i++) {
            // Add a new page for each label
            $pdf->AddPage();
            
            // Generate barcode label on this page
            generateBarcodeLabel($pdf, $item, $settings, $dimensions);
        }
    }

    // Output PDF
    header('Content-Type: application/pdf');
    echo $pdf->Output('barcodes.pdf', 'S');
}

function handleSaveTemplate($data)
{
    global $con;

    $templateName = $data['template_name'] ?? '';
    $settings = $data['settings'] ?? [];

    if (empty($templateName)) {
        throw new Exception('Template name is required');
    }

    mysqli_begin_transaction($con);

    try {
        // Check if template name exists
        $stmt = mysqli_prepare(
            $con,
            "SELECT template_id FROM barcode_templates WHERE template_name = ?"
        );
        mysqli_stmt_bind_param($stmt, 's', $templateName);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
            throw new Exception('Template name already exists');
        }

        // Insert template without margin
        $query = "INSERT INTO barcode_templates (
            template_name, paper_width, paper_height,
            show_shop_name, show_product_name, show_price, show_unit, show_category, show_promo_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);

        $paperSize = $settings['paper_size'];
        $paperHeight = $paperSize === '30' ? 15 : $paperSize;

        mysqli_stmt_bind_param(
            $stmt,
            'sddddddii',
            $templateName,
            $paperSize,
            $paperHeight,
            $settings['show_shop_name'],
            $settings['show_product_name'],
            $settings['show_price'],
            $settings['show_unit'],
            $settings['show_category'],
            $settings['show_promo_price']
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to save template: ' . mysqli_error($con));
        }

        $templateId = mysqli_insert_id($con);

        mysqli_commit($con);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'template_id' => $templateId,
            'message' => 'Template saved successfully'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function handleGetTemplate()
{
    global $con;

    $templateId = $_GET['id'] ?? null;

    if (!$templateId) {
        throw new Exception('Template ID is required');
    }

    $stmt = mysqli_prepare(
        $con,
        "SELECT * FROM barcode_templates WHERE template_id = ?"
    );
    mysqli_stmt_bind_param($stmt, 'i', $templateId);
    mysqli_stmt_execute($stmt);

    $template = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$template) {
        throw new Exception('Template not found');
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'settings' => [
            'paper_size' => $template['paper_width'],
            'margin' => $template['margin'] ?? 2,
            'gap_between' => 3, // Default value
            'font_size' => 8, // Default value
            'barcode_height' => 10, // Default value
            'show_price' => (bool)$template['show_price'],
            'show_unit' => (bool)$template['show_unit'],
            'show_category' => (bool)$template['show_category'],
            'show_promo_price' => (bool)$template['show_promo_price'],
            'show_shop_name' => (bool)$template['show_shop_name'],
            'show_product_name' => (bool)$template['show_product_name']
        ]
    ]);
}

function calculateDimensions($settings)
{
    $paperSize = $settings['paper_size'];
    $margin = $settings['margin'] ?? 2;
    $gap = $settings['gap_between'] ?? 3;
    $barcodeHeight = $settings['barcode_height'] ?? 10;

    switch ($paperSize) {
        case '36':
            return [
                'barcode' => [
                    'width' => 32,
                    'height' => $barcodeHeight
                ],
                'text' => [
                    'company_y' => 2,
                    'product_y' => 5,
                    'barcode_y' => 8,
                    'price_y' => 20,
                    'category_y' => 23,
                    'batch_y' => 26
                ]
            ];

        case '24':
            return [
                'barcode' => [
                    'width' => 20,
                    'height' => $barcodeHeight
                ],
                'text' => [
                    'company_y' => 1.5,
                    'product_y' => 4,
                    'barcode_y' => 6,
                    'price_y' => 15,
                    'category_y' => 17,
                    'batch_y' => 19
                ]
            ];

        case '18':
            return [
                'barcode' => [
                    'width' => 14,
                    'height' => $barcodeHeight
                ],
                'text' => [
                    'company_y' => 1,
                    'product_y' => 3,
                    'barcode_y' => 5,
                    'price_y' => 12,
                    'category_y' => 14,
                    'batch_y' => 16
                ]
            ];

        case '30':
        case '30x15':
            return [
                'barcode' => [
                    'width' => 26,
                    'height' => $barcodeHeight
                ],
                'text' => [
                    'company_y' => 1.5,
                    'product_y' => 4,
                    'barcode_y' => 6,
                    'price_y' => 11,
                    'category_y' => 13,
                    'batch_y' => 13.5
                ]
            ];

        default:
            // Default to 30mm if invalid paper size
            return [
                'barcode' => [
                    'width' => 26,
                    'height' => $barcodeHeight
                ],
                'text' => [
                    'company_y' => 1.5,
                    'product_y' => 4,
                    'barcode_y' => 6,
                    'price_y' => 11,
                    'category_y' => 13,
                    'batch_y' => 13.5
                ]
            ];
    }
}

function generateBarcodeLabel($pdf, $item, $settings, $dimensions)
{
    global $ERP_COMPANY_NAME;
    
    $pdf->AddPage();

    // Set font size with default fallback
    $fontSize = $settings['font_size'] ?? 8;
    $pdf->SetFont('helvetica', '', $fontSize);
    
    // Get margin with default fallback
    $margin = $settings['margin'] ?? 2;
    
    // Add shop name if enabled
    $showShopName = isset($settings['show_shop_name']) ? $settings['show_shop_name'] : true;
    if ($showShopName) {
        $shopName = $settings['shop_name'] ?? $ERP_COMPANY_NAME;
        $pdf->Text($margin, $dimensions['text']['company_y'], $shopName);
    }

    // Add product name if enabled
    $showProductName = isset($settings['show_product_name']) ? $settings['show_product_name'] : true;
    if ($showProductName && !empty($item['product_name'])) {
        $productName = truncateText($item['product_name'], $settings['paper_size']);
        $pdf->Text($margin, $dimensions['text']['product_y'], $productName);
    }

    // Generate barcode - ensure barcode is not empty
    $barcode = !empty($item['barcode']) ? $item['barcode'] : (!empty($item['product_id']) ? $item['product_id'] : '0000000000000');
    
    if ($barcode) {
        // Determine barcode format based on length
        $format = 'C128';
        if (strlen($barcode) == 13 && is_numeric($barcode)) {
            $format = 'EAN13';
        } elseif (strlen($barcode) == 8 && is_numeric($barcode)) {
            $format = 'EAN8';
        }
        
        // Make sure barcode height is set
        $barcodeHeight = $settings['barcode_height'] ?? 10;
        
        $pdf->write1DBarcode(
            $barcode,
            $format,
            $margin,
            $dimensions['text']['barcode_y'],
            $dimensions['barcode']['width'],
            $barcodeHeight,
            0.4,
            [
                'position' => 'S',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0,
                'hpadding' => 0,
                'vpadding' => 0,
                'fgcolor' => [0, 0, 0],
                'bgcolor' => false,
                'text' => true,
                'font' => 'helvetica',
                'fontsize' => 15,  // Setting barcode text font size to 15
                'stretchtext' => 0
            ]
        );
    }

    // Add price if enabled
    $showPrice = isset($settings['show_price']) ? $settings['show_price'] : true;
    if ($showPrice && isset($item['price'])) {
        $price = 'Rs. ' . number_format((float)$item['price'], 2);
        $pdf->Text($margin, $dimensions['text']['price_y'], $price);
    }

    // Add promotional price if enabled
    $showPromoPrice = isset($settings['show_promo_price']) ? $settings['show_promo_price'] : false;
    if ($showPromoPrice && !empty($item['discount_price']) && $item['discount_price'] > 0) {
        $promoPrice = 'Promo: Rs. ' . number_format((float)$item['discount_price'], 2);
        $pdf->Text($margin, $dimensions['text']['price_y'] + 3, $promoPrice);
    }

    // Add category if enabled
    $showCategory = isset($settings['show_category']) ? $settings['show_category'] : false;
    if ($showCategory && !empty($item['category'])) {
        $categoryName = truncateText($item['category'], $settings['paper_size']);
        $pdf->Text($margin, $dimensions['text']['category_y'], $categoryName);
    }

    // Add unit if enabled
    $showUnit = isset($settings['show_unit']) ? $settings['show_unit'] : false;
    if ($showUnit && !empty($item['unit'])) {
        $pdf->Text($margin + 20, $dimensions['text']['category_y'], $item['unit']);
    }

    // Add batch number
    if (!empty($item['batch_number'])) {
        $pdf->SetFont('helvetica', '', $fontSize * 0.8);
        $pdf->Text($margin, $dimensions['text']['batch_y'], $item['batch_number']);
        $pdf->SetFont('helvetica', '', $fontSize);
    }
}

function truncateText($text, $paperSize)
{
    $maxLength = [
        '36' => 25,
        '24' => 20,
        '18' => 15,
        '30x15' => 22
    ];

    $length = $maxLength[$paperSize] ?? 20;
    return strlen($text) > $length ? substr($text, 0, $length - 3) . '...' : $text;
}

if (!function_exists('generateBarcodeLabel')) {
    function generateBarcodeLabel($item, $settings) {
        $html = '<div class="barcode-label">';
        
        // Add shop name if enabled
        if ($settings['show_shop_name']) {
            $html .= '<div class="shop-name">' . ($settings['shop_name'] ?? '') . '</div>';
        }

        // Add product name if enabled
        if ($settings['show_product_name']) {
            $html .= '<div class="product-name">' . htmlspecialchars($item['product_name']) . '</div>';
        }

        // Generate barcode
        $barcode = $item['barcode'] ?? 'PROD' . $item['product_id'];
        $html .= '<div class="barcode-container">';
        $html .= '<img src="data:image/png;base64,' . generateBarcode($barcode, $settings['barcode_height']) . '" class="barcode-image">';
        $html .= '</div>';

        // Add price information
        $html .= '<div class="price-info">';
        if ($settings['show_category'] && !empty($item['category'])) {
            $html .= '<span class="category">' . htmlspecialchars($item['category']) . '</span>';
        }
        
        if ($settings['show_price']) {
            $price_class = ($settings['show_promo_price'] && !empty($item['discount_price'])) ? 'price strike' : 'price';
            $html .= '<span class="' . $price_class . '">Rs. ' . number_format($item['price'], 2) . '</span>';
            
            if ($settings['show_promo_price'] && !empty($item['discount_price'])) {
                $html .= '<span class="promo-price">Rs. ' . number_format($item['discount_price'], 2) . '</span>';
            }
        }
        
        if ($settings['show_unit']) {
            $html .= '<span class="unit">' . htmlspecialchars($item['unit'] ?? 'pcs') . '</span>';
        }
        $html .= '</div>';

        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('generateBarcode')) {
    function generateBarcode($code, $height = 30) {
        try {
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($code, $generator::TYPE_CODE_128);
            return base64_encode($barcode);
        } catch (Exception $e) {
            error_log("Error generating barcode: " . $e->getMessage());
            return '';
        }
    }
}

ob_end_flush();
