<?php
require_once '../../../inc/config.php';

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate template ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid template ID']);
    exit;
}

$templateId = (int)$_GET['id'];
$userId = $_SESSION['employee_id'];

// Retrieve template data
$query = "SELECT * FROM barcode_templates WHERE template_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 'i', $templateId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$template = mysqli_fetch_assoc($result);

if (!$template) {
    echo json_encode(['success' => false, 'message' => 'Template not found']);
    exit;
}

// Set default values for missing columns
$settings = [
    'paper_size' => $template['paper_width'] ?? '30',
    'margin' => $template['margin'] ?? '2',
    'gap_between' => '3',
    'font_size' => '8',
    'barcode_height' => '10',
    'show_price' => (bool)($template['show_price'] ?? true),
    'show_unit' => (bool)($template['show_unit'] ?? false),
    'show_category' => (bool)($template['show_category'] ?? false),
    'show_promo_price' => (bool)($template['show_promo_price'] ?? false),
    'show_shop_name' => (bool)($template['show_shop_name'] ?? true),
    'show_product_name' => (bool)($template['show_product_name'] ?? true),
];

// Return template data
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'template' => [
        'template_id' => $template['template_id'],
        'template_name' => $template['template_name'],
        'settings' => $settings,
        'items' => [] // No items in the response as they're stored in a different table
    ]
]); 