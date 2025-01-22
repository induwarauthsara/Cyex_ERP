<?php
// File: /AdminPanel/barcode-print/api/barcode.php
ob_start();
require_once('../../nav.php');
require_once '../../../vendor/autoload.php';
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
            case 'print':
                handleBarcodeGeneration($jsonData, $action === 'preview');
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

function handleBarcodeGeneration($data, $isPreview = false)
{
    global $ERP_COMPANY_NAME;

    $settings = $data['settings'] ?? [];
    $items = $data['items'] ?? [];

    if (empty($items)) {
        throw new Exception('No items to print');
    }

    // Create PDF
    $pdf = new TCPDF(
        'P',
        'mm',
        [$settings['paper_size'], $settings['paper_size'] === '30x15' ? 15 : $settings['paper_size']],
        true,
        'UTF-8'
    );

    // Set document properties
    $pdf->SetCreator($ERP_COMPANY_NAME);
    $pdf->SetAuthor($ERP_COMPANY_NAME);
    $pdf->SetTitle('Product Barcodes');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins
    $margin = $settings['margin'];
    $pdf->SetMargins($margin, $margin, $margin);
    $pdf->SetAutoPageBreak(true, $margin);

    // Set font
    $pdf->SetFont('helvetica', '', $settings['font_size']);

    // Calculate dimensions based on paper size
    $dimensions = calculateDimensions($settings);

    // Generate barcodes
    foreach ($items as $item) {
        for ($i = 0; $i < $item['quantity']; $i++) {
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

        // Insert template
        $query = "INSERT INTO barcode_templates (
            template_name, paper_width, paper_height, margin,
            gap_between, font_size, barcode_height,
            show_price, show_unit, show_category, show_promo_price
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $query);

        $paperSize = $settings['paper_size'];
        $paperHeight = $paperSize === '30x15' ? 15 : $paperSize;

        mysqli_stmt_bind_param(
            $stmt,
            'sddddddiiii',
            $templateName,
            $paperSize,
            $paperHeight,
            $settings['margin'],
            $settings['gap_between'],
            $settings['font_size'],
            $settings['barcode_height'],
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
            'margin' => $template['margin'],
            'gap_between' => $template['gap_between'],
            'font_size' => $template['font_size'],
            'barcode_height' => $template['barcode_height'],
            'show_price' => (bool)$template['show_price'],
            'show_unit' => (bool)$template['show_unit'],
            'show_category' => (bool)$template['show_category'],
            'show_promo_price' => (bool)$template['show_promo_price']
        ]
    ]);
}

function calculateDimensions($settings)
{
    $paperSize = $settings['paper_size'];
    $margin = $settings['margin'];
    $gap = $settings['gap_between'];

    switch ($paperSize) {
        case '36':
            return [
                'barcode' => [
                    'width' => 32,
                    'height' => $settings['barcode_height']
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
                    'height' => $settings['barcode_height']
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
                    'height' => $settings['barcode_height']
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

        case '30x15':
            return [
                'barcode' => [
                    'width' => 26,
                    'height' => $settings['barcode_height']
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
            throw new Exception('Invalid paper size');
    }
}

function generateBarcodeLabel($pdf, $item, $settings, $dimensions)
{
    global $ERP_COMPANY_NAME;

    $pdf->AddPage();

    // Add company name
    $pdf->Text($settings['margin'], $dimensions['text']['company_y'], $ERP_COMPANY_NAME);

    // Add product name (truncated if necessary)
    $productName = truncateText($item['product_name'], $settings['paper_size']);
    $pdf->Text($settings['margin'], $dimensions['text']['product_y'], $productName);

    // Generate barcode
    $barcode = $item['barcode'] ?? $item['product_id'];
    $pdf->write1DBarcode(
        $barcode,
        'C128',
        $settings['margin'],
        $dimensions['text']['barcode_y'],
        $dimensions['barcode']['width'],
        $dimensions['barcode']['height'],
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
            'text' => true
        ]
    );

    // Add price if enabled
    if ($settings['show_price']) {
        $price = 'Rs. ' . number_format($item['price'], 2);
        $pdf->Text($settings['margin'], $dimensions['text']['price_y'], $price);
    }

    // Add category if enabled
    if ($settings['show_category'] && !empty($item['category_name'])) {
        $categoryName = truncateText($item['category_name'], $settings['paper_size']);
        $pdf->Text($settings['margin'], $dimensions['text']['category_y'], $categoryName);
    }

    // Add batch number
    if (!empty($item['batch_number'])) {
        $pdf->SetFont('helvetica', '', $settings['font_size'] * 0.8);
        $pdf->Text($settings['margin'], $dimensions['text']['batch_y'], $item['batch_number']);
        $pdf->SetFont('helvetica', '', $settings['font_size']);
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
ob_end_flush();
