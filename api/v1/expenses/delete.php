<?php
/**
 * Delete Expense API
 * DELETE /api/v1/expenses/delete.php
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../ApiResponse.php';
require_once __DIR__ . '/../ApiAuth.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    ApiResponse::error('Method not allowed', 405);
}

$auth = ApiAuth::authenticate();
if (!$auth['success']) {
    ApiResponse::error($auth['message'], 401);
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['expense_id'])) {
        ApiResponse::error('Missing expense_id', 400);
    }
    
    $expenseId = intval($input['expense_id']);
    
    $sql = "DELETE FROM expenses WHERE expense_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $expenseId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $stmt->close();
            ApiResponse::success(
                ['expense_id' => $expenseId],
                "Expense deleted successfully"
            );
        } else {
            $stmt->close();
            ApiResponse::error('Expense not found', 404);
        }
    } else {
        throw new Exception("Failed to delete expense: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Delete Expense API Error: " . $e->getMessage());
    ApiResponse::error(
        "Failed to delete expense",
        500,
        [],
        API_DEBUG ? $e->getMessage() : null
    );
}
