<?php
// File: /AdminPanel/barcode-print/includes/utils.php

/**
 * Utility functions for barcode printing
 */

/**
 * Paper size configurations
 */
function getPaperSizes()
{
    return [
        // Standard sizes
        '36' => [
            'name' => '36mm (1.4 Inch)',
            'width' => 36,
            'height' => 36,
            'type' => 'standard',
            'margins' => ['top' => 2, 'right' => 2, 'bottom' => 2, 'left' => 2],
            'dpi' => 203 // Standard for thermal printers
        ],
        '24' => [
            'name' => '24mm (0.94 Inch)',
            'width' => 24,
            'height' => 24,
            'type' => 'standard',
            'margins' => ['top' => 1.5, 'right' => 1.5, 'bottom' => 1.5, 'left' => 1.5],
            'dpi' => 203
        ],
        '18' => [
            'name' => '18mm (0.7 Inch)',
            'width' => 18,
            'height' => 18,
            'type' => 'standard',
            'margins' => ['top' => 1, 'right' => 1, 'bottom' => 1, 'left' => 1],
            'dpi' => 203
        ],
        // Multi-label formats
        '30x15-3up' => [
            'name' => '30x15mm 3-up',
            'width' => 90,
            'height' => 15,
            'type' => 'multi',
            'labels_per_row' => 3,
            'gap' => 3,
            'margins' => ['top' => 1.5, 'right' => 1.5, 'bottom' => 1.5, 'left' => 1.5],
            'dpi' => 203
        ],
        '50x25-2up' => [
            'name' => '50x25mm 2-up',
            'width' => 100,
            'height' => 25,
            'type' => 'multi',
            'labels_per_row' => 2,
            'gap' => 2,
            'margins' => ['top' => 2, 'right' => 2, 'bottom' => 2, 'left' => 2],
            'dpi' => 203
        ],
        // A4 sheet formats
        'a4-65' => [
            'name' => 'A4 - 65 Labels (38.1x21.2mm)',
            'width' => 210,
            'height' => 297,
            'type' => 'sheet',
            'labels_per_row' => 5,
            'rows_per_sheet' => 13,
            'label_width' => 38.1,
            'label_height' => 21.2,
            'gap_horizontal' => 2.5,
            'gap_vertical' => 0,
            'margins' => ['top' => 13.1, 'right' => 5, 'bottom' => 13.1, 'left' => 5],
            'dpi' => 300
        ],
        'a4-40' => [
            'name' => 'A4 - 40 Labels (52.5x29.7mm)',
            'width' => 210,
            'height' => 297,
            'type' => 'sheet',
            'labels_per_row' => 4,
            'rows_per_sheet' => 10,
            'label_width' => 52.5,
            'label_height' => 29.7,
            'gap_horizontal' => 0,
            'gap_vertical' => 0,
            'margins' => ['top' => 13.1, 'right' => 0, 'bottom' => 13.1, 'left' => 0],
            'dpi' => 300
        ]
    ];
}

/**
 * Get paper size configuration
 */
function getPaperSizeConfig($size)
{
    $sizes = getPaperSizes();
    return $sizes[$size] ?? null;
}

/**
 * Calculate label positions for multi-label formats
 */
function calculateLabelPositions($paperConfig, $pageWidth, $pageHeight)
{
    $positions = [];

    switch ($paperConfig['type']) {
        case 'standard':
            $positions[] = [
                'x' => $paperConfig['margins']['left'],
                'y' => $paperConfig['margins']['top'],
                'width' => $pageWidth - ($paperConfig['margins']['left'] + $paperConfig['margins']['right']),
                'height' => $pageHeight - ($paperConfig['margins']['top'] + $paperConfig['margins']['bottom'])
            ];
            break;

        case 'multi':
            $labelWidth = ($pageWidth - ($paperConfig['margins']['left'] + $paperConfig['margins']['right'] +
                ($paperConfig['labels_per_row'] - 1) * $paperConfig['gap'])) / $paperConfig['labels_per_row'];

            for ($i = 0; $i < $paperConfig['labels_per_row']; $i++) {
                $x = $paperConfig['margins']['left'] + ($labelWidth + $paperConfig['gap']) * $i;
                $positions[] = [
                    'x' => $x,
                    'y' => $paperConfig['margins']['top'],
                    'width' => $labelWidth,
                    'height' => $pageHeight - ($paperConfig['margins']['top'] + $paperConfig['margins']['bottom'])
                ];
            }
            break;

        case 'sheet':
            for ($row = 0; $row < $paperConfig['rows_per_sheet']; $row++) {
                for ($col = 0; $col < $paperConfig['labels_per_row']; $col++) {
                    $x = $paperConfig['margins']['left'] +
                        ($paperConfig['label_width'] + $paperConfig['gap_horizontal']) * $col;
                    $y = $paperConfig['margins']['top'] +
                        ($paperConfig['label_height'] + $paperConfig['gap_vertical']) * $row;

                    $positions[] = [
                        'x' => $x,
                        'y' => $y,
                        'width' => $paperConfig['label_width'],
                        'height' => $paperConfig['label_height']
                    ];
                }
            }
            break;
    }

    return $positions;
}

/**
 * Calculate font sizes based on label dimensions
 */
function calculateFontSizes($labelWidth, $labelHeight)
{
    $baseSize = min($labelWidth, $labelHeight) / 10;

    return [
        'company' => $baseSize * 0.8,
        'product' => $baseSize * 0.7,
        'price' => $baseSize * 0.9,
        'category' => $baseSize * 0.6,
        'batch' => $baseSize * 0.5
    ];
}

/**
 * Calculate barcode dimensions
 */
function calculateBarcodeDimensions($labelWidth, $labelHeight)
{
    return [
        'width' => $labelWidth * 0.9,
        'height' => $labelHeight * 0.3,
        'x_offset' => $labelWidth * 0.05,
        'y_offset' => $labelHeight * 0.4
    ];
}

/**
 * Format batch print data
 */
function formatBatchPrintData($items, $paperConfig)
{
    $batchGroups = [];

    foreach ($items as $item) {
        $key = $item['batch_number'] ?? 'no_batch';
        if (!isset($batchGroups[$key])) {
            $batchGroups[$key] = [
                'items' => [],
                'total_quantity' => 0
            ];
        }
        $batchGroups[$key]['items'][] = $item;
        $batchGroups[$key]['total_quantity'] += $item['quantity'];
    }

    // Calculate sheets needed
    $labelsPerSheet = $paperConfig['type'] === 'sheet' ?
        ($paperConfig['labels_per_row'] * $paperConfig['rows_per_sheet']) : 1;

    foreach ($batchGroups as &$group) {
        $group['sheets_needed'] = ceil($group['total_quantity'] / $labelsPerSheet);
    }

    return $batchGroups;
}

/**
 * Generate optimized print sequence
 */
function generatePrintSequence($batchGroups, $paperConfig)
{
    $sequence = [];
    $labelsPerSheet = $paperConfig['type'] === 'sheet' ?
        ($paperConfig['labels_per_row'] * $paperConfig['rows_per_sheet']) : 1;

    // Sort batches by quantity to optimize printing
    uasort($batchGroups, function ($a, $b) {
        return $b['total_quantity'] - $a['total_quantity'];
    });

    foreach ($batchGroups as $batchNumber => $group) {
        $itemsInBatch = $group['items'];
        $remainingQuantities = array_column($itemsInBatch, 'quantity');

        while (array_sum($remainingQuantities) > 0) {
            $sheetItems = [];
            $labelsUsed = 0;

            while ($labelsUsed < $labelsPerSheet) {
                // Find item with highest remaining quantity
                $maxIndex = array_search(max($remainingQuantities), $remainingQuantities);

                if ($remainingQuantities[$maxIndex] <= 0) {
                    break;
                }

                $item = $itemsInBatch[$maxIndex];
                $quantityForSheet = min(
                    $remainingQuantities[$maxIndex],
                    $labelsPerSheet - $labelsUsed
                );

                $sheetItems[] = [
                    'item' => $item,
                    'quantity' => $quantityForSheet
                ];

                $remainingQuantities[$maxIndex] -= $quantityForSheet;
                $labelsUsed += $quantityForSheet;
            }

            if (!empty($sheetItems)) {
                $sequence[] = [
                    'batch_number' => $batchNumber,
                    'items' => $sheetItems,
                    'total_labels' => $labelsUsed
                ];
            }
        }
    }

    return $sequence;
}

/**
 * Calculate print job statistics
 */
function calculatePrintStats($sequence, $paperConfig)
{
    $stats = [
        'total_labels' => 0,
        'total_sheets' => 0,
        'total_batches' => count(array_unique(array_column($sequence, 'batch_number'))),
        'labels_per_sheet' => 1,
        'estimated_time' => 0
    ];

    if ($paperConfig['type'] === 'sheet') {
        $stats['labels_per_sheet'] = $paperConfig['labels_per_row'] * $paperConfig['rows_per_sheet'];
    }

    foreach ($sequence as $sheet) {
        $stats['total_labels'] += $sheet['total_labels'];
        $stats['total_sheets']++;
    }

    // Estimate print time (rough estimate: 1 second per label + 3 seconds per sheet)
    $stats['estimated_time'] = $stats['total_labels'] + ($stats['total_sheets'] * 3);

    return $stats;
}
