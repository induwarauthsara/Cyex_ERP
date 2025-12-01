<?php
/**
 * Quotation Management API
 * Handles all quotation-related operations
 */

require_once('../../inc/config.php');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$user_id = $_SESSION['employee_id'] ?? 'admin';

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($method) {
        case 'GET':
            handleGetRequests($action);
            break;
        case 'POST':
            handlePostRequests();
            break;
        default:
            throw new Exception('Method not allowed', 405);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function handleGetRequests($action) {
    global $con;
    
    switch ($action) {
        case 'list':
            listQuotations();
            break;
        case 'get':
            getQuotation();
            break;
        case 'generate_number':
            generateQuotationNumber();
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function handlePostRequests() {
    global $con, $user_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        $input = $_POST;
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createQuotation($input);
            break;
        case 'update':
            updateQuotation($input);
            break;
        case 'delete':
            deleteQuotation($input);
            break;
        default:
            throw new Exception('Invalid action', 400);
    }
}

function listQuotations() {
    global $con;
    
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? min(100, max(1, (int)$_GET['per_page'])) : 25;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    
    $offset = ($page - 1) * $perPage;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if (!empty($search)) {
        $whereConditions[] = "(quotation_number LIKE ? OR customer_name LIKE ?)";
        $searchParam = "%{$search}%";
        array_push($params, $searchParam, $searchParam);
    }
    
    if (!empty($status) && $status !== 'all') {
        $whereConditions[] = "status = ?";
        $params[] = $status;
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM quotations {$whereClause}";
    $countStmt = mysqli_prepare($con, $countQuery);
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($countStmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($countStmt);
    $countResult = mysqli_stmt_get_result($countStmt);
    $totalRecords = mysqli_fetch_assoc($countResult)['total'];
    
    // Get quotations
    $query = "SELECT * FROM quotations {$whereClause} ORDER BY quotation_date DESC, id DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $query);
    
    $allParams = array_merge($params, [$perPage, $offset]);
    $types = str_repeat('s', count($params)) . 'ii';
    
    mysqli_stmt_bind_param($stmt, $types, ...$allParams);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $quotations = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $quotations[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'data' => $quotations,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => (int)$totalRecords,
            'total_pages' => ceil($totalRecords / $perPage)
        ]
    ]);
}

function getQuotation() {
    global $con;
    
    $id = $_GET['id'] ?? null;
    if (!$id) {
        throw new Exception('Quotation ID is required', 400);
    }
    
    // Get quotation data
    $query = "SELECT * FROM quotations WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($quotation = mysqli_fetch_assoc($result)) {
        // Get quotation items
        $itemsQuery = "SELECT * FROM quotation_items WHERE quotation_id = ? ORDER BY id";
        $itemsStmt = mysqli_prepare($con, $itemsQuery);
        mysqli_stmt_bind_param($itemsStmt, 'i', $id);
        mysqli_stmt_execute($itemsStmt);
        $itemsResult = mysqli_stmt_get_result($itemsStmt);
        
        $items = [];
        while ($item = mysqli_fetch_assoc($itemsResult)) {
            $items[] = $item;
        }
        
        $quotation['items'] = $items;
        
        echo json_encode([
            'status' => 'success',
            'data' => $quotation
        ]);
    } else {
        throw new Exception('Quotation not found', 404);
    }
}

function generateQuotationNumber() {
    global $con;
    
    // Get quotation settings
    $prefix = 'QT'; // Default
    $auto_generate = true;
    
    $settings_query = "SELECT setting_name, setting_value FROM settings WHERE setting_name IN ('quotation_prefix', 'quotation_auto_generate')";
    $settings_result = mysqli_query($con, $settings_query);
    
    if ($settings_result) {
        while ($setting = mysqli_fetch_assoc($settings_result)) {
            if ($setting['setting_name'] == 'quotation_prefix') {
                $prefix = $setting['setting_value'];
            } elseif ($setting['setting_name'] == 'quotation_auto_generate') {
                $auto_generate = ($setting['setting_value'] == '1');
            }
        }
    }
    
    if (!$auto_generate) {
        echo json_encode([
            'status' => 'success',
            'quotation_number' => '',
            'auto_generate' => false,
            'message' => 'Auto-generation is disabled. Please enter quotation number manually.'
        ]);
        return;
    }
    
    // Get the next quotation number
    $query = "SELECT IFNULL(MAX(CAST(SUBSTRING(quotation_number, " . (strlen($prefix) + 1) . ") AS UNSIGNED)), 0) + 1 as next_id 
              FROM quotations 
              WHERE quotation_number REGEXP ?";
    
    $pattern = '^' . preg_quote($prefix) . '[0-9]+$';
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $pattern);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $nextId = $row['next_id'];
    
    $quotationNumber = $prefix . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    
    echo json_encode([
        'status' => 'success',
        'quotation_number' => $quotationNumber,
        'auto_generate' => true
    ]);
}

function createQuotation($data) {
    global $con, $user_id;
    
    mysqli_begin_transaction($con);
    
    try {
        // Validate required fields
        $requiredFields = ['quotation_number', 'quotation_date', 'subtotal', 'total'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required", 400);
            }
        }
        
        // Extract customer data (could be from customer_name or customerName)
        $customerName = $data['customer_name'] ?? $data['customerName'] ?? null;
        $customerMobile = $data['customer_mobile'] ?? $data['customerMobile'] ?? null;
        $customerAddress = $data['customer_address'] ?? $data['customerAddress'] ?? null;
        
        if (empty($customerName)) {
            throw new Exception('Customer name is required', 400);
        }
        
        // Get validity days from settings if not provided
        if (empty($data['valid_until'])) {
            $validity_days = 30; // Default
            $validity_query = "SELECT setting_value FROM settings WHERE setting_name = 'quotation_validity_days' LIMIT 1";
            $validity_result = mysqli_query($con, $validity_query);
            if ($validity_result && $row = mysqli_fetch_assoc($validity_result)) {
                $validity_days = intval($row['setting_value']);
            }
            $validUntil = date('Y-m-d', strtotime($data['quotation_date'] . ' + ' . $validity_days . ' days'));
        } else {
            $validUntil = $data['valid_until'];
        }
        
        // Insert quotation
        $query = "INSERT INTO quotations (quotation_number, customer_name, customer_mobile, 
                  customer_address, quotation_date, valid_until, note, subtotal, discount, total, 
                  status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', ?)";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'sssssssddds', 
            $data['quotation_number'],
            $customerName,
            $customerMobile,
            $customerAddress,
            $data['quotation_date'],
            $validUntil,
            $data['note'] ?? null,
            $data['subtotal'],
            $data['discount'] ?? 0,
            $data['total'],
            $user_id
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to create quotation: ' . mysqli_error($con));
        }
        
        $quotationId = mysqli_insert_id($con);
        
        // Insert quotation items
        if (!empty($data['products'])) {
            $itemQuery = "INSERT INTO quotation_items (quotation_id, product_id, product_name, 
                          quantity, rate, amount) VALUES (?, ?, ?, ?, ?, ?)";
            $itemStmt = mysqli_prepare($con, $itemQuery);
            
            foreach ($data['products'] as $product) {
                if (empty($product['product_name']) || empty($product['quantity']) || empty($product['rate'])) {
                    continue;
                }
                
                $productId = $product['product_id'] ?? null;
                if (empty($productId) || strpos($productId, 'MANUAL') !== false) {
                    $productId = null; // Store as NULL for manual products
                }
                
                $productName = $product['product_name'];
                $quantity = floatval($product['quantity']);
                $rate = floatval($product['rate']);
                $amount = $quantity * $rate;
                
                mysqli_stmt_bind_param($itemStmt, 'issddd',
                    $quotationId,
                    $productId,
                    $productName,
                    $quantity,
                    $rate,
                    $amount
                );
                
                if (!mysqli_stmt_execute($itemStmt)) {
                    throw new Exception('Failed to create quotation item: ' . mysqli_error($con));
                }
            }
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Quotation created successfully',
            'quotation_id' => $quotationId
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function updateQuotation($data) {
    global $con, $user_id;
    
    if (empty($data['id'])) {
        throw new Exception('Quotation ID is required', 400);
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Validate required fields
        $requiredFields = ['customer_name', 'quotation_date', 'subtotal', 'total'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field '{$field}' is required", 400);
            }
        }
        
        // Get validity days from settings if not provided
        if (empty($data['valid_until'])) {
            $validity_days = 30; // Default
            $validity_query = "SELECT setting_value FROM settings WHERE setting_name = 'quotation_validity_days' LIMIT 1";
            $validity_result = mysqli_query($con, $validity_query);
            if ($validity_result && $row = mysqli_fetch_assoc($validity_result)) {
                $validity_days = intval($row['setting_value']);
            }
            $validUntil = date('Y-m-d', strtotime($data['quotation_date'] . ' + ' . $validity_days . ' days'));
        } else {
            $validUntil = $data['valid_until'];
        }
        
        // Update quotation
        $query = "UPDATE quotations SET customer_name = ?, customer_mobile = ?, 
                  customer_address = ?, quotation_date = ?, valid_until = ?, note = ?, 
                  subtotal = ?, discount = ?, total = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'ssssssdddi',
            $data['customer_name'],
            $data['customer_mobile'] ?? null,
            $data['customer_address'] ?? null,
            $data['quotation_date'],
            $validUntil,
            $data['note'] ?? null,
            $data['subtotal'],
            $data['discount'] ?? 0,
            $data['total'],
            $data['id']
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to update quotation: ' . mysqli_error($con));
        }
        
        // Delete existing items and re-insert
        $deleteQuery = "DELETE FROM quotation_items WHERE quotation_id = ?";
        $deleteStmt = mysqli_prepare($con, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, 'i', $data['id']);
        mysqli_stmt_execute($deleteStmt);
        
        // Insert updated items
        if (!empty($data['products'])) {
            $itemQuery = "INSERT INTO quotation_items (quotation_id, product_id, product_name, 
                          quantity, rate, amount) VALUES (?, ?, ?, ?, ?, ?)";
            $itemStmt = mysqli_prepare($con, $itemQuery);
            
            foreach ($data['products'] as $product) {
                if (empty($product['product_name']) || empty($product['quantity']) || empty($product['rate'])) {
                    continue;
                }
                
                $productId = $product['product_id'] ?? 'MANUAL';
                $productName = $product['product_name'];
                $amount = $product['quantity'] * $product['rate'];
                
                mysqli_stmt_bind_param($itemStmt, 'issddd',
                    $data['id'],
                    $productId,
                    $productName,
                    $product['quantity'],
                    $product['rate'],
                    $amount
                );
                
                if (!mysqli_stmt_execute($itemStmt)) {
                    throw new Exception('Failed to update quotation item: ' . mysqli_error($con));
                }
            }
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Quotation updated successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function deleteQuotation($data) {
    global $con;
    
    if (empty($data['id'])) {
        throw new Exception('Quotation ID is required', 400);
    }
    
    mysqli_begin_transaction($con);
    
    try {
        // Check if quotation exists
        $checkQuery = "SELECT id FROM quotations WHERE id = ?";
        $checkStmt = mysqli_prepare($con, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 'i', $data['id']);
        mysqli_stmt_execute($checkStmt);
        $checkResult = mysqli_stmt_get_result($checkStmt);
        
        if (mysqli_num_rows($checkResult) === 0) {
            throw new Exception('Quotation not found', 404);
        }
        
        // Delete quotation items first (foreign key constraint)
        $deleteItemsQuery = "DELETE FROM quotation_items WHERE quotation_id = ?";
        $deleteItemsStmt = mysqli_prepare($con, $deleteItemsQuery);
        mysqli_stmt_bind_param($deleteItemsStmt, 'i', $data['id']);
        
        if (!mysqli_stmt_execute($deleteItemsStmt)) {
            throw new Exception('Failed to delete quotation items: ' . mysqli_error($con));
        }
        
        // Delete quotation
        $deleteQuery = "DELETE FROM quotations WHERE id = ?";
        $deleteStmt = mysqli_prepare($con, $deleteQuery);
        mysqli_stmt_bind_param($deleteStmt, 'i', $data['id']);
        
        if (!mysqli_stmt_execute($deleteStmt)) {
            throw new Exception('Failed to delete quotation: ' . mysqli_error($con));
        }
        
        mysqli_commit($con);
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Quotation deleted successfully'
        ]);
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function getProductName($productId) {
    global $con;
    
    $query = "SELECT product_name FROM products WHERE product_id = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 's', $productId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['product_name'];
    }
    
    return $productId; // Fallback to product ID if name not found
}

?>