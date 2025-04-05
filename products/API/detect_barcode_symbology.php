<?php
// File: products/API/detect_barcode_symbology.php
require_once '../../inc/config.php';

// Set content type to JSON if this is a direct request
if (count(debug_backtrace()) == 0) {
    header('Content-Type: application/json');
}

/**
 * Detect the barcode symbology based on the barcode string
 * 
 * @param string $barcode The barcode string to analyze
 * @return string The detected symbology (CODE128, EAN13, EAN8, UPC, CODE39)
 */
function detectBarcodeSymbology($barcode) {
    // Remove any whitespace
    $barcode = trim($barcode);
    
    // If empty, default to CODE128
    if (empty($barcode)) {
        return 'CODE128';
    }
    
    // Check if it's numeric only
    $isNumeric = ctype_digit($barcode);
    
    if ($isNumeric) {
        // Check length for standard symbologies
        $length = strlen($barcode);
        
        switch ($length) {
            case 8:
                return 'EAN8';
            case 12:
                return 'UPC';
            case 13:
                return 'EAN13';
        }
    }
    
    // Check for CODE39 patterns (alphanumeric and limited special chars)
    if (preg_match('/^[A-Z0-9\-\.\$\/\+\%\s]+$/', strtoupper($barcode))) {
        return 'CODE39';
    }
    
    // Default to CODE128 for anything else
    return 'CODE128';
}

// Check if this is a direct request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['barcode'])) {
    $barcode = $_GET['barcode'];
    $symbology = detectBarcodeSymbology($barcode);
    
    echo json_encode([
        'success' => true,
        'barcode' => $barcode,
        'symbology' => $symbology
    ]);
    exit;
}

// If it's a POST request (e.g., from AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = json_decode(file_get_contents('php://input'), true);
    
    if (isset($postData['barcode'])) {
        $barcode = $postData['barcode'];
        $symbology = detectBarcodeSymbology($barcode);
        
        echo json_encode([
            'success' => true,
            'barcode' => $barcode,
            'symbology' => $symbology
        ]);
        exit;
    }
}

// Only respond with JSON if this is a direct API call
if (count(debug_backtrace()) == 0) {
    // If we got here, it's an invalid request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request. Provide a barcode parameter.'
    ]);
} 