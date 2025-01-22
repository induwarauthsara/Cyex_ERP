<?php
// File: /AdminPanel/barcode-print/api/barcode.php
require_once '../../../inc/config.php';

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set headers for JSON response
header('Content-Type: application/json');

try {
    $jsonBody = file_get_contents('php://input');
    $data = json_decode($jsonBody, true);
    
    $action = $data['action'] ?? '';

    if ($action !== 'print') {
        throw new Exception('Invalid action specified');
    }

    // Get print settings
    $paperSize = $data['data']['paper_size'] ?? '36';
    $showPrice = $data['data']['show_price'] ?? true;
    $showUnit = $data['data']['show_unit'] ?? false;
    $showCategory = $data['data']['show_category'] ?? false;
    $showPromoPrice = $data['data']['show_promo_price'] ?? false;
    
    // Get items
    $items = $data['data']['items'] ?? [];
    
    if (empty($items)) {
        throw new Exception('No items to print');
    }

    // Create barcode print job
    mysqli_begin_transaction($con);
    
    try {
        // Get default template or create if not exists
        $query = "SELECT template_id FROM barcode_templates 
                  WHERE paper_width = ? LIMIT 1";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'd', $paperSize);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($template = mysqli_fetch_assoc($result)) {
            $templateId = $template['template_id'];
            
            // Update template settings
            $query = "UPDATE barcode_templates SET 
                     show_price = ?,
                     show_unit = ?,
                     show_category = ?,
                     show_promo_price = ?
                     WHERE template_id = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, 'iiiii', 
                $showPrice, $showUnit, $showCategory, $showPromoPrice, $templateId
            );
            mysqli_stmt_execute($stmt);
        } else {
            // Create new template
            $query = "INSERT INTO barcode_templates (
                        paper_width,
                        paper_height,
                        show_price,
                        show_unit,
                        show_category,
                        show_promo_price
                    ) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $query);
            
            // Set paper height based on width
            $paperHeight = $paperSize === '30x15' ? 15 : $paperSize;
            
            mysqli_stmt_bind_param($stmt, 'ddiiii',
                $paperSize,
                $paperHeight,
                $showPrice,
                $showUnit,
                $showCategory,
                $showPromoPrice
            );
            mysqli_stmt_execute($stmt);
            
            $templateId = mysqli_insert_id($con);
        }

        // Create print job
        $query = "INSERT INTO barcode_print_jobs (
                    template_id,
                    created_by,
                    status
                ) VALUES (?, ?, 'pending')";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $templateId, $_SESSION['employee_id']);
        mysqli_stmt_execute($stmt);
        
        $jobId = mysqli_insert_id($con);

        // Add items to print job
        $query = "INSERT INTO barcode_print_items (
                    job_id,
                    product_id,
                    batch_id,
                    quantity
                ) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $query);
        
        foreach ($items as $item) {
            mysqli_stmt_bind_param($stmt, 'iiii',
                $jobId,
                $item['product_id'],
                $item['batch_id'],
                $item['quantity']
            );
            mysqli_stmt_execute($stmt);
        }

        // Get items with all required data
        $query = "SELECT p.product_name, p.sku, p.barcode,
                        pb.selling_price, pb.batch_number,
                        c.category_name,
                        bpi.quantity
                 FROM barcode_print_items bpi
                 JOIN products p ON bpi.product_id = p.product_id
                 JOIN product_batch pb ON bpi.batch_id = pb.batch_id
                 LEFT JOIN categories c ON p.category_id = c.category_id
                 WHERE bpi.job_id = ?";

        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $jobId);
        mysqli_stmt_execute($stmt);
        $printItems = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

        // Update job status
        $query = "UPDATE barcode_print_jobs SET 
                  status = 'completed',
                  completed_at = CURRENT_TIMESTAMP
                  WHERE job_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $jobId);
        mysqli_stmt_execute($stmt);

        mysqli_commit($con);

        // Generate PDF with barcodes
        require_once '../../../vendor/autoload.php'; // For TCPDF and Barcode Generator

        $pdf = new TCPDF($paperSize === '30x15' ? 'L' : 'P', 'mm', [$paperSize, $paperHeight], true, 'UTF-8');
        $pdf->SetCreator($ERP_COMPANY_NAME);
        $pdf->SetAuthor($ERP_COMPANY_NAME);
        $pdf->SetTitle('Product Barcodes');

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins based on paper size
        $margins = getPaperMargins($paperSize);
        $pdf->SetMargins($margins['left'], $margins['top'], $margins['right']);
        $pdf->SetAutoPageBreak(true, $margins['bottom']);

        // Generate barcodes
        foreach ($printItems as $item) {
            for ($i = 0; $i < $item['quantity']; $i++) {
                generateBarcodeLabel($pdf, $item, [
                    'showPrice' => $showPrice,
                    'showUnit' => $showUnit,
                    'showCategory' => $showCategory,
                    'showPromoPrice' => $showPromoPrice
                ], $paperSize);
            }
        }

        // Output PDF
        header('Content-Type: application/pdf');
        echo $pdf->Output('barcodes.pdf', 'S');
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function getPaperMargins($paperSize)
{
    switch ($paperSize) {
        case '36':
            return [
                'left' => 2,
                'top' => 2,
                'right' => 2,
                'bottom' => 2
            ];
        case '24':
            return [
                'left' => 1.5,
                'top' => 1.5,
                'right' => 1.5,
                'bottom' => 1.5
            ];
        case '18':
            return [
                'left' => 1,
                'top' => 1,
                'right' => 1,
                'bottom' => 1
            ];
        case '30x15':
            return [
                'left' => 1.5,
                'top' => 1.5,
                'right' => 1.5,
                'bottom' => 1.5
            ];
        default:
            throw new Exception('Invalid paper size');
    }
}

function generateBarcodeLabel($pdf, $item, $options, $paperSize)
{
    $pdf->AddPage();

    // Set font size based on paper size
    $fontSize = getFontSize($paperSize);
    $pdf->SetFont('helvetica', '', $fontSize);

    // Calculate positions based on paper size
    $positions = getLabelPositions($paperSize);

    // Add company name
    $pdf->SetFont('helvetica', 'B', $fontSize);
    $pdf->Text($positions['company']['x'], $positions['company']['y'], $GLOBALS['ERP_COMPANY_NAME']);

    // Add product name
    $pdf->SetFont('helvetica', '', $fontSize);
    $pdf->Text($positions['product']['x'], $positions['product']['y'], truncateText($item['product_name'], $paperSize));

    // Add barcode
    $barcodeStyle = getBarcodeStyle($paperSize);
    $pdf->write1DBarcode(
        $item['barcode'] ?? $item['sku'],
        'C128',
        $positions['barcode']['x'],
        $positions['barcode']['y'],
        $barcodeStyle['width'],
        $barcodeStyle['height'],
        0.4,
        $barcodeStyle
    );

    // Add price if enabled
    if ($options['showPrice']) {
        $price = 'Rs. ' . number_format($item['selling_price'], 2);
        $pdf->Text($positions['price']['x'], $positions['price']['y'], $price);
    }

    // Add category if enabled
    if ($options['showCategory'] && !empty($item['category_name'])) {
        $pdf->Text($positions['category']['x'], $positions['category']['y'], $item['category_name']);
    }

    // Add batch number
    $pdf->SetFont('helvetica', '', $fontSize * 0.8);
    $pdf->Text($positions['batch']['x'], $positions['batch']['y'], $item['batch_number']);
}

function getFontSize($paperSize)
{
    switch ($paperSize) {
        case '36':
            return 8;
        case '24':
            return 6;
        case '18':
            return 5;
        case '30x15':
            return 7;
        default:
            return 6;
    }
}

function getLabelPositions($paperSize)
{
    switch ($paperSize) {
        case '36':
            return [
                'company' => ['x' => 2, 'y' => 2],
                'product' => ['x' => 2, 'y' => 5],
                'barcode' => ['x' => 2, 'y' => 8],
                'price' => ['x' => 2, 'y' => 20],
                'category' => ['x' => 2, 'y' => 23],
                'batch' => ['x' => 2, 'y' => 26]
            ];
        case '24':
            return [
                'company' => ['x' => 1.5, 'y' => 1.5],
                'product' => ['x' => 1.5, 'y' => 4],
                'barcode' => ['x' => 1.5, 'y' => 7],
                'price' => ['x' => 1.5, 'y' => 15],
                'category' => ['x' => 1.5, 'y' => 18],
                'batch' => ['x' => 1.5, 'y' => 21]
            ];
            // Add more sizes as needed
        default:
            throw new Exception('Invalid paper size');
    }
}

function getBarcodeStyle($paperSize)
{
    switch ($paperSize) {
        case '36':
            return [
                'width' => 32,
                'height' => 10,
                'position' => 'S',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0
            ];
        case '24':
            return [
                'width' => 21,
                'height' => 8,
                'position' => 'S',
                'align' => 'C',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0
            ];
            // Add more sizes as needed
        default:
            throw new Exception('Invalid paper size');
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