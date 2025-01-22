<?php
// File: /AdminPanel/barcode-print/api/batch_print.php

ob_start();
require_once('../../nav.php');
require_once '../includes/utils.php';
require_once '../../../vendor/autoload.php';
ob_clean();

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $jsonData = json_decode(file_get_contents('php://input'), true);
    $action = $jsonData['action'] ?? '';

    switch ($action) {
        case 'preview':
        case 'print':
            handleBarcodeGeneration($jsonData, $action === 'preview');
            break;

        case 'calculate_stats':
            handleCalculateStats($jsonData);
            break;

        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleBarcodeGeneration($data, $isPreview = false)
{
    global $ERP_COMPANY_NAME;

    validatePrintData($data);

    $settings = $data['settings'];
    $batchGroups = $data['batch_groups'];
    $paperConfig = getPaperSizeConfig($settings['paper_size']);

    if (!$paperConfig) {
        throw new Exception('Invalid paper size');
    }

    // Create print sequence based on settings
    $printSequence = generatePrintSequence($batchGroups, $settings, $paperConfig);

    // Create PDF
    $pdf = createPDF($paperConfig, $settings);

    // Generate labels based on print sequence
    generateLabels($pdf, $printSequence, $settings, $paperConfig);

    // Output PDF
    header('Content-Type: application/pdf');
    echo $pdf->Output('barcodes.pdf', 'S');
}

function handleCalculateStats($data)
{
    $settings = $data['settings'];
    $batchGroups = $data['batch_groups'];
    $paperConfig = getPaperSizeConfig($settings['paper_size']);

    if (!$paperConfig) {
        throw new Exception('Invalid paper size');
    }

    // Calculate print sequence
    $printSequence = generatePrintSequence($batchGroups, $settings, $paperConfig);

    // Calculate statistics
    $stats = [
        'total_labels' => 0,
        'total_sheets' => 0,
        'total_batches' => count($batchGroups),
        'estimated_time' => 0,
        'paper_usage' => '',
        'warnings' => []
    ];

    // Count labels and sheets
    foreach ($printSequence as $sheet) {
        $stats['total_labels'] += count($sheet['items']);
        $stats['total_sheets']++;
    }

    // Calculate paper usage
    switch ($paperConfig['type']) {
        case 'standard':
            $stats['paper_usage'] = sprintf(
                '%d labels at %dmm each = %.1fm total',
                $stats['total_labels'],
                $paperConfig['height'],
                ($stats['total_labels'] * $paperConfig['height']) / 1000
            );
            break;

        case 'multi':
            $stats['paper_usage'] = sprintf(
                '%d labels on %d sheets (%dmm each)',
                $stats['total_labels'],
                $stats['total_sheets'],
                $paperConfig['height']
            );
            break;

        case 'sheet':
            $stats['paper_usage'] = sprintf(
                '%d labels on %d A4 sheets (%d labels per sheet)',
                $stats['total_labels'],
                $stats['total_sheets'],
                $paperConfig['labels_per_row'] * $paperConfig['rows_per_sheet']
            );
            break;
    }

    // Add warnings
    if ($paperConfig['type'] === 'sheet' && $settings['skip_partial'] === false) {
        $labelsPerSheet = $paperConfig['labels_per_row'] * $paperConfig['rows_per_sheet'];
        $lastSheet = end($printSequence);
        if (count($lastSheet['items']) < $labelsPerSheet) {
            $stats['warnings'][] = 'Last sheet will be partially filled. Consider enabling "Skip Partial Sheets" option.';
        }
    }

    // Estimate print time based on printer capabilities
    // Base time: 1 second per label + 3 seconds per sheet for paper handling
    $stats['estimated_time'] = $stats['total_labels'] + ($stats['total_sheets'] * 3);

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

function generatePrintSequence($batchGroups, $settings, $paperConfig)
{
    $sequence = [];
    $currentSheet = ['items' => []];
    $labelsPerSheet = 1;

    // Calculate labels per sheet based on paper type
    if ($paperConfig['type'] === 'multi') {
        $labelsPerSheet = $paperConfig['labels_per_row'];
    } elseif ($paperConfig['type'] === 'sheet') {
        $labelsPerSheet = $paperConfig['labels_per_row'] * $paperConfig['rows_per_sheet'];
    }

    // Sort batch groups based on settings
    $sortedGroups = sortBatchGroups($batchGroups, $settings['sorting']);

    // Process each batch group
    foreach ($sortedGroups as $batchNumber => $group) {
        if ($settings['print_mode'] === 'batch') {
            // Print each batch separately
            $batchItems = [];
            foreach ($group['items'] as $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $batchItems[] = [
                        'batch_number' => $batchNumber,
                        'item' => $item
                    ];
                }
            }

            // Create sheets for batch items
            while (!empty($batchItems)) {
                if (count($currentSheet['items']) >= $labelsPerSheet) {
                    $sequence[] = $currentSheet;
                    $currentSheet = ['items' => []];
                }

                $remainingSpace = $labelsPerSheet - count($currentSheet['items']);
                $itemsToAdd = array_slice($batchItems, 0, $remainingSpace);
                $currentSheet['items'] = array_merge($currentSheet['items'], $itemsToAdd);
                $batchItems = array_slice($batchItems, $remainingSpace);
            }

            // Add final sheet if it has items and meets skip_partial criteria
            if (!empty($currentSheet['items'])) {
                if (
                    !$settings['skip_partial'] ||
                    count($currentSheet['items']) === $labelsPerSheet
                ) {
                    $sequence[] = $currentSheet;
                }
                $currentSheet = ['items' => []];
            }
        } else {
            // Mixed mode - optimize paper usage
            foreach ($group['items'] as $item) {
                for ($i = 0; $i < $item['quantity']; $i++) {
                    if (count($currentSheet['items']) >= $labelsPerSheet) {
                        $sequence[] = $currentSheet;
                        $currentSheet = ['items' => []];
                    }

                    $currentSheet['items'][] = [
                        'batch_number' => $batchNumber,
                        'item' => $item
                    ];
                }
            }
        }
    }

    // Add final sheet if it has items and meets skip_partial criteria
    if (!empty($currentSheet['items'])) {
        if (
            !$settings['skip_partial'] ||
            count($currentSheet['items']) === $labelsPerSheet
        ) {
            $sequence[] = $currentSheet;
        }
    }

    return $sequence;
}

function createPDF($paperConfig, $settings)
{
    // Create PDF with correct orientation and page size
    $orientation = $settings['orientation'] === 'landscape' ? 'L' : 'P';

    if ($paperConfig['type'] === 'sheet') {
        $pdf = new TCPDF($orientation, 'mm', 'A4', true, 'UTF-8');
    } else {
        $pdf = new TCPDF(
            $orientation,
            'mm',
            [$paperConfig['width'], $paperConfig['height']],
            true,
            'UTF-8'
        );
    }

    // Set document properties
    $pdf->SetCreator($GLOBALS['ERP_COMPANY_NAME']);
    $pdf->SetAuthor($GLOBALS['ERP_COMPANY_NAME']);
    $pdf->SetTitle('Batch Barcode Print');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Set margins based on paper config
    $pdf->SetMargins(
        $paperConfig['margins']['left'],
        $paperConfig['margins']['top'],
        $paperConfig['margins']['right']
    );
    $pdf->SetAutoPageBreak(true, $paperConfig['margins']['bottom']);

    return $pdf;
}

function generateLabels($pdf, $printSequence, $settings, $paperConfig)
{
    $startPosition = max(1, $settings['start_position']);
    $currentPosition = 1;

    foreach ($printSequence as $sheetIndex => $sheet) {
        // Skip positions until start position on first sheet
        if ($sheetIndex === 0 && $startPosition > 1) {
            $currentPosition = $startPosition;
        } else {
            $currentPosition = 1;
        }

        $pdf->AddPage();

        // Calculate label positions for this sheet
        $positions = calculateLabelPositions($paperConfig, $pdf->getPageWidth(), $pdf->getPageHeight());

        foreach ($sheet['items'] as $itemIndex => $item) {
            if ($currentPosition < $startPosition && $sheetIndex === 0) {
                $currentPosition++;
                continue;
            }

            $position = $positions[($currentPosition - 1) % count($positions)];
            generateLabel($pdf, $item, $settings, $position);
            $currentPosition++;
        }
    }
}

function generateLabel($pdf, $item, $settings, $position)
{
    $data = $item['item'];
    $batchNumber = $item['batch_number'];

    // Save current position
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Move to label position
    $pdf->SetXY($position['x'], $position['y']);

    // Add company name
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->Cell($position['width'], 3, $GLOBALS['ERP_COMPANY_NAME'], 0, 1, 'C');

    // Add product name
    $pdf->SetFont('helvetica', '', 7);
    $pdf->MultiCell($position['width'], 3, $data['product_name'], 0, 'C');

    // Generate barcode
    $barcodeX = $position['x'] + ($position['width'] * 0.1);
    $barcodeWidth = $position['width'] * 0.8;

    $pdf->write1DBarcode(
        $data['product_id'],
        'C128',
        $barcodeX,
        $pdf->GetY(),
        $barcodeWidth,
        8,
        0.4,
        [
            'position' => 'S',
            'border' => false,
            'padding' => 0,
            'text' => true
        ]
    );

    // Move below barcode
    $pdf->SetY($pdf->GetY() + 10);

    // Add price if enabled
    if ($settings['show_price']) {
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(
            $position['width'],
            3,
            'Rs. ' . number_format($data['price'], 2),
            0,
            1,
            'C'
        );
    }

    // Add unit if enabled
    if ($settings['show_unit'] && !empty($data['unit'])) {
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell($position['width'], 3, $data['unit'], 0, 1, 'C');
    }

    // Add category if enabled
    if ($settings['show_category'] && !empty($data['category'])) {
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell($position['width'], 3, $data['category'], 0, 1, 'C');
    }

    // Add batch number if enabled
    if ($settings['show_batch']) {
        $pdf->SetFont('helvetica', '', 6);
        $pdf->Cell($position['width'], 3, $batchNumber, 0, 1, 'C');
    }

    // Restore position
    $pdf->SetXY($x, $y);
}

function sortBatchGroups($batchGroups, $sortType)
{
    $sorted = $batchGroups;

    switch ($sortType) {
        case 'batch':
            ksort($sorted);
            break;

        case 'product':
            uasort($sorted, function ($a, $b) {
                return strcmp(
                    $a['items'][0]['product_name'],
                    $b['items'][0]['product_name']
                );
            });
            break;

        case 'quantity':
            uasort($sorted, function ($a, $b) {
                $totalA = array_sum(array_column($a['items'], 'quantity'));
                $totalB = array_sum(array_column($b['items'], 'quantity'));
                return $totalB <=> $totalA;
            });
            break;
    }

    return $sorted;
}

function validatePrintData($data)
{
    if (empty($data['settings'])) {
        throw new Exception('Print settings are required');
    }

    if (empty($data['batch_groups'])) {
        throw new Exception('No items to print');
    }

    $settings = $data['settings'];
    $requiredSettings = [
        'paper_size',
        'orientation',
        'print_mode',
        'sorting'
    ];

    foreach ($requiredSettings as $setting) {
        if (!isset($settings[$setting])) {
            throw new Exception("Missing required setting: $setting");
        }
    }

    foreach ($data['batch_groups'] as $batch) {
        if (empty($batch['items'])) {
            throw new Exception('Empty batch group found');
        }

        foreach ($batch['items'] as $item) {
            $requiredFields = ['product_id', 'product_name', 'quantity', 'price'];
            foreach ($requiredFields as $field) {
                if (!isset($item[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            if ($item['quantity'] < 1) {
                throw new Exception('Invalid quantity found');
            }
        }
    }
}
ob_end_flush();
