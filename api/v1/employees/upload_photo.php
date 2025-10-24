<?php
/**
 * Upload Employee Photo API Endpoint
 * Upload and update employee profile photo (resized to 200x200px)
 * 
 * @route POST /api/v1/employees/upload_photo.php
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ApiResponse::error('Method not allowed. Use POST', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get employee ID from POST or authenticated user
$employeeId = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;

// If no employee_id provided, use authenticated user's ID
if (!$employeeId) {
    $employeeId = intval($userData['id']);
}

// Validate employee ID
if (!$employeeId) {
    ApiResponse::error('Employee ID is required', 400);
}

// Check if user is authorized to update this photo
// Admin can update any photo, Employee can only update their own
if ($userData['role'] !== 'Admin' && intval($userData['id']) !== $employeeId) {
    ApiResponse::error('You are not authorized to update this employee photo', 403);
}

// Verify employee exists
$checkQuery = "SELECT employ_id, emp_name FROM employees WHERE employ_id = ?";
$checkStmt = mysqli_prepare($con, $checkQuery);
mysqli_stmt_bind_param($checkStmt, 'i', $employeeId);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    mysqli_stmt_close($checkStmt);
    ApiResponse::error('Employee not found', 404);
}

$employee = mysqli_fetch_assoc($checkResult);
mysqli_stmt_close($checkStmt);

// Check if file was uploaded
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    ApiResponse::error('No photo file uploaded', 400);
}

// Check for upload errors
if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
    ];
    $message = $errorMessages[$_FILES['photo']['error']] ?? 'Unknown upload error';
    ApiResponse::error($message, 400);
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
$fileType = $_FILES['photo']['type'];
$fileTmpPath = $_FILES['photo']['tmp_name'];

// Detect actual MIME type using file signature (magic bytes)
$actualMimeType = null;
if (file_exists($fileTmpPath)) {
    $handle = fopen($fileTmpPath, 'rb');
    $bytes = fread($handle, 8);
    fclose($handle);
    
    $signature = bin2hex($bytes);
    
    // PNG signature: 89 50 4E 47 0D 0A 1A 0A
    if (substr($signature, 0, 16) === '89504e470d0a1a0a') {
        $actualMimeType = 'image/png';
    }
    // JPEG signature: FF D8 FF
    elseif (substr($signature, 0, 6) === 'ffd8ff') {
        $actualMimeType = 'image/jpeg';
    }
}

if (!$actualMimeType || !in_array($actualMimeType, $allowedTypes)) {
    ApiResponse::error('Invalid file type. Only JPG and PNG are allowed', 400);
}

// Validate file size (max 5MB before resize)
$maxFileSize = 5 * 1024 * 1024; // 5MB
if ($_FILES['photo']['size'] > $maxFileSize) {
    ApiResponse::error('File size exceeds 5MB limit', 400);
}

// Load image based on type
$sourceImage = null;
switch ($actualMimeType) {
    case 'image/jpeg':
    case 'image/jpg':
        $sourceImage = @imagecreatefromjpeg($fileTmpPath);
        break;
    case 'image/png':
        $sourceImage = @imagecreatefrompng($fileTmpPath);
        break;
}

if (!$sourceImage) {
    ApiResponse::error('Failed to process image file', 400);
}

// Get original dimensions
$originalWidth = imagesx($sourceImage);
$originalHeight = imagesy($sourceImage);

// Target dimensions
$targetWidth = 200;
$targetHeight = 200;

// Create new image with target dimensions
$resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

// Preserve transparency for PNG
if ($actualMimeType === 'image/png') {
    imagealphablending($resizedImage, false);
    imagesavealpha($resizedImage, true);
    $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
    imagefilledrectangle($resizedImage, 0, 0, $targetWidth, $targetHeight, $transparent);
}

// Resize image (crop to square first, then resize)
$size = min($originalWidth, $originalHeight);
$x = ($originalWidth - $size) / 2;
$y = ($originalHeight - $size) / 2;

imagecopyresampled(
    $resizedImage,
    $sourceImage,
    0, 0,
    $x, $y,
    $targetWidth, $targetHeight,
    $size, $size
);

// Save resized image to buffer
ob_start();
if ($actualMimeType === 'image/png') {
    imagepng($resizedImage, null, 9); // Max compression for PNG
} else {
    imagejpeg($resizedImage, null, 85); // 85% quality for JPEG
}
$imageData = ob_get_clean();

// Free memory
imagedestroy($sourceImage);
imagedestroy($resizedImage);

// Update database
$updateQuery = "UPDATE employees SET dp = ? WHERE employ_id = ?";
$updateStmt = mysqli_prepare($con, $updateQuery);

if (!$updateStmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

$null = null;
mysqli_stmt_bind_param($updateStmt, 'bi', $null, $employeeId);
mysqli_stmt_send_long_data($updateStmt, 0, $imageData);

if (!mysqli_stmt_execute($updateStmt)) {
    mysqli_stmt_close($updateStmt);
    ApiResponse::serverError('Failed to update photo', mysqli_error($con));
}

mysqli_stmt_close($updateStmt);

// Prepare response
$response = [
    'employee_id' => $employeeId,
    'employee_name' => $employee['emp_name'],
    'photo_size_bytes' => strlen($imageData),
    'dimensions' => [
        'width' => $targetWidth,
        'height' => $targetHeight
    ],
    'mime_type' => $actualMimeType,
    'message' => 'Photo uploaded and resized successfully'
];

ApiResponse::success($response, 'Employee photo updated successfully');
