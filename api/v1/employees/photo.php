<?php
/**
 * Employee Photo API Endpoint
 * Get employee profile photo
 * 
 * @route GET /api/v1/employees/photo.php?id=5
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    ApiResponse::error('Method not allowed. Use GET', 405);
}

// Require authentication
$userData = ApiAuth::requireAuth();

// Get employee ID
$employeeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$employeeId) {
    ApiResponse::error('Employee ID is required', 400);
}

// Get employee photo
$query = "SELECT dp, emp_name FROM employees WHERE employ_id = ?";
$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    ApiResponse::serverError('Database error', mysqli_error($con));
}

mysqli_stmt_bind_param($stmt, 'i', $employeeId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    ApiResponse::error('Employee not found', 404);
}

$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// Check if photo exists
if (empty($row['dp'])) {
    ApiResponse::error('No photo found for this employee', 404);
}

// Get image data
$imageData = $row['dp'];
$empName = $row['emp_name'];

// Detect image type using image signature (magic bytes)
$mimeType = 'image/jpeg'; // Default
if (strlen($imageData) >= 4) {
    $signature = bin2hex(substr($imageData, 0, 4));
    
    // PNG signature: 89 50 4E 47
    if (substr($signature, 0, 8) === '89504e47') {
        $mimeType = 'image/png';
    }
    // JPEG signature: FF D8 FF
    elseif (substr($signature, 0, 6) === 'ffd8ff') {
        $mimeType = 'image/jpeg';
    }
}

// Encode to base64
$base64Image = base64_encode($imageData);
$dataUri = "data:$mimeType;base64,$base64Image";

// Prepare response
$response = [
    'employee_id' => $employeeId,
    'employee_name' => $empName,
    'image' => $dataUri,
    'mime_type' => $mimeType,
    'size_bytes' => strlen($imageData)
];

ApiResponse::success($response, 'Employee photo retrieved successfully');
