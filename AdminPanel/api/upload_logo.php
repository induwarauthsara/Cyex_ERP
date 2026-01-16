<?php
require_once '../connection.php';
require_once '../checkLogin.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['logo_file']) || $_FILES['logo_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['logo_file'];

// Validate file type
$allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
$fileType = mime_content_type($file['tmp_name']);

if (!in_array($fileType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PNG and JPEG images are allowed.']);
    exit;
}

// Validate file size (2MB max)
if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 2MB.']);
    exit;
}

// Determine target path - logo.png in the root directory
$targetPath = dirname(__DIR__) . '/logo.png';

// Backup existing logo if it exists
if (file_exists($targetPath)) {
    $backupPath = dirname(__DIR__) . '/logo_backup_' . date('Y-m-d_H-i-s') . '.png';
    if (!copy($targetPath, $backupPath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to backup existing logo']);
        exit;
    }
}

// Move uploaded file to replace logo.png
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    // Log the action
    $userId = $_SESSION['userid'];
    $userName = $_SESSION['username'] ?? 'Unknown';
    $logMessage = "Logo updated by user: $userName (ID: $userId)";
    error_log($logMessage);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Logo uploaded successfully',
        'path' => 'logo.png',
        'timestamp' => time()
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save logo file']);
}
?>
