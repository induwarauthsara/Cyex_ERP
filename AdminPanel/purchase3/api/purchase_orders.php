<?php
// File: /AdminPanel/purchase/api/purchase_orders.php
require_once '../../../inc/config.php';

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Set headers for JSON response
header('Content-Type: application/json');

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'list':
            handleList();
            break;

        case 'delete':
            handleDelete();
            break;

        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

function handleList()
{
    global $con;

    // DataTables server-side parameters
    $draw = $_POST['draw'] ?? 1;
    $start = $_POST['start'] ?? 0;
    $length = $_POST['length'] ?? 10;
    $search = $_POST['search']['value'] ?? '';
    $orderColumn = $_POST['order'][0]['column'] ?? 1;
    $orderDir = $_POST['order'][0]['dir'] ?? 'desc';

    // Filters
    $status = $_POST['status'] ?? '';
    $supplier_id = $_POST['supplier_id'] ?? '';
    $date_from = $_POST['date_from'] ?? '';
    $date_to = $_POST['date_to'] ?? '';

    // Base query
    $baseQuery = "FROM purchase_orders po
                  LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                  LEFT JOIN employees e ON po.created_by = e.employ_id
                  WHERE 1=1";

    // Apply filters
    $params = [];

    if ($search) {
        $baseQuery .= " AND (po.po_number LIKE ? OR s.supplier_name LIKE ? OR e.emp_name LIKE ?)";
        array_push($params, "%$search%", "%$search%", "%$search%");
    }

    if ($status) {
        $baseQuery .= " AND po.status = ?";
        array_push($params, $status);
    }

    if ($supplier_id) {
        $baseQuery .= " AND po.supplier_id = ?";
        array_push($params, $supplier_id);
    }

    if ($date_from) {
        $baseQuery .= " AND po.order_date >= ?";
        array_push($params, $date_from);
    }

    if ($date_to) {
        $baseQuery .= " AND po.order_date <= ?";
        array_push($params, $date_to);
    }

    // Count total records
    $stmt = mysqli_prepare($con, "SELECT COUNT(*) " . $baseQuery);
    if ($params) {
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
    }
    mysqli_stmt_execute($stmt);
    $totalRecords = mysqli_stmt_get_result($stmt)->fetch_row()[0];

    // Columns for ordering
    $columns = [
        'po_number',
        'order_date',
        'supplier_name',
        'total_amount',
        'status',
        'emp_name'
    ];

    // Add sorting
    $orderColumn = $columns[$orderColumn] ?? 'order_date';
    $baseQuery .= " ORDER BY $orderColumn $orderDir";

    // Add pagination
    $baseQuery .= " LIMIT ?, ?";
    array_push($params, (int)$start, (int)$length);

    // Get filtered data
    $query = "SELECT po.*, 
              s.supplier_name,
              e.emp_name as created_by_name
              $baseQuery";

    $stmt = mysqli_prepare($con, $query);
    if ($params) {
        $types = str_repeat('s', count($params) - 2) . 'ii';
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    echo json_encode([
        'draw' => (int)$draw,
        'recordsTotal' => (int)$totalRecords,
        'recordsFiltered' => (int)$totalRecords,
        'data' => $data,
        'error' => null
    ]);
}

function handleDelete()
{
    global $con;

    // Decode JSON body for DELETE requests
    $jsonBody = file_get_contents('php://input');
    $data = json_decode($jsonBody, true);

    $poId = $data['po_id'] ?? null;

    if (!$poId) {
        throw new Exception('Purchase order ID is required');
    }

    // Start transaction
    mysqli_begin_transaction($con);

    try {
        // Check if PO exists and is in draft status
        $stmt = mysqli_prepare(
            $con,
            "SELECT status FROM purchase_orders WHERE po_id = ? FOR UPDATE"
        );
        mysqli_stmt_bind_param($stmt, 'i', $poId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] !== 'draft') {
                throw new Exception('Only draft purchase orders can be deleted');
            }
        } else {
            throw new Exception('Purchase order not found');
        }

        // Delete PO items first
        $deleteItemsQuery = "DELETE FROM purchase_order_items WHERE po_id = ?";
        $stmt = mysqli_prepare($con, $deleteItemsQuery);
        mysqli_stmt_bind_param($stmt, 'i', $poId);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to delete purchase order items');
        }

        // Delete PO
        $deletePoQuery = "DELETE FROM purchase_orders WHERE po_id = ?";
        $stmt = mysqli_prepare($con, $deletePoQuery);
        mysqli_stmt_bind_param($stmt, 'i', $poId);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to delete purchase order');
        }

        // Log the transaction
        transaction_log(
            'DELETE_PO',
            "Deleted purchase order #$poId",
            0
        );

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'message' => 'Purchase order deleted successfully'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}
