<?php
require_once '../../../config/init.php';
session_start();

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

$userId = $_SESSION['employee_id'];

// Retrieve all templates for this user
$query = "SELECT template_id, template_name, created_at, updated_at FROM barcode_templates WHERE user_id = '$userId' ORDER BY template_name ASC";
$result = mysqli_query($con, $query);

$templates = [];
while($row = mysqli_fetch_assoc($result)) {
    $templates[] = $row;
}

// Return templates list
echo json_encode([
    'success' => true,
    'templates' => $templates
]); 