<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON data');
    }

    // Validate required fields
    if (!isset($input['supplier_name']) || empty($input['supplier_name'])) {
        throw new Exception('Supplier name is required');
    }

    // Sanitize inputs
    $supplier_name = mysqli_real_escape_string($con, $input['supplier_name']);
    $supplier_tel = isset($input['supplier_tel']) ? mysqli_real_escape_string($con, $input['supplier_tel']) : '';
    $supplier_address = isset($input['supplier_address']) ? mysqli_real_escape_string($con, $input['supplier_address']) : '';
    $note = isset($input['note']) ? mysqli_real_escape_string($con, $input['note']) : '';

    // Insert new supplier
    $sql = "
        INSERT INTO suppliers (
            supplier_name, supplier_tel, supplier_address, 
            credit_balance, note, created_at
        ) VALUES (?, ?, ?, 0, ?, NOW())
    ";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssss', $supplier_name, $supplier_tel, $supplier_address, $note);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to add supplier: ' . mysqli_error($con));
    }

    $supplier_id = mysqli_insert_id($con);

    // Log action
    $action_description = "Added new supplier: $supplier_name";
    $action_sql = "
        INSERT INTO action_log (
            action_type, description, performed_by, performed_at
        ) VALUES ('ADD_SUPPLIER', ?, ?, NOW())
    ";
    
    $action_stmt = mysqli_prepare($con, $action_sql);
    mysqli_stmt_bind_param($action_stmt, 'si', $action_description, $_SESSION['employee_id']);
    mysqli_stmt_execute($action_stmt);

    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Supplier added successfully',
        'supplier' => [
            'id' => $supplier_id,
            'text' => $supplier_name
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush(); 