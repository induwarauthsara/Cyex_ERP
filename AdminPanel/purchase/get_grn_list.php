<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $supplier = isset($_GET['supplier']) ? (int)$_GET['supplier'] : null;
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $dateRange = isset($_GET['dateRange']) ? $_GET['dateRange'] : null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    // Initialize parameters array and types string
    $params = [];
    $types = '';
    $where_clauses = [];

    // Add date range filter
    if ($dateRange) {
        switch ($dateRange) {
            case 'today':
                $where_clauses[] = "DATE(grn.receipt_date) = CURDATE()";
                break;
            case 'week':
                $where_clauses[] = "YEARWEEK(grn.receipt_date) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $where_clauses[] = "YEAR(grn.receipt_date) = YEAR(CURDATE()) AND MONTH(grn.receipt_date) = MONTH(CURDATE())";
                break;
            case 'year':
                $where_clauses[] = "YEAR(grn.receipt_date) = YEAR(CURDATE())";
                break;
            case 'custom':
                if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
                    $where_clauses[] = "DATE(grn.receipt_date) BETWEEN ? AND ?";
                    $params[] = $_GET['startDate'];
                    $params[] = $_GET['endDate'];
                    $types .= 'ss';
                }
                break;
        }
    }

    // Add supplier filter
    if ($supplier) {
        $where_clauses[] = "s.supplier_id = ?";
        $params[] = $supplier;
        $types .= 'i';
    }

    // Add status filter
    if ($status) {
        $where_clauses[] = "grn.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    // Add search filter
    if ($search) {
        $where_clauses[] = "(grn.grn_number LIKE ? OR s.supplier_name LIKE ? OR po.po_number LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'sss';
    }

    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

    // Build query
    $sql = "
        SELECT 
            grn.grn_id,
            grn.grn_number,
            grn.receipt_date,
            grn.invoice_number,
            grn.status,
            po.po_number,
            s.supplier_name,
            (SELECT COUNT(*) FROM grn_items WHERE grn_id = grn.grn_id) as item_count,
            (SELECT SUM(cost * received_qty) FROM grn_items WHERE grn_id = grn.grn_id) as total_value
        FROM goods_receipt_notes grn
        LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
        LEFT JOIN suppliers s ON (
            CASE 
                WHEN grn.supplier_id IS NOT NULL THEN grn.supplier_id
                WHEN po.supplier_id IS NOT NULL THEN po.supplier_id
                ELSE (
                    SELECT pb.supplier_id 
                    FROM grn_items gi
                    JOIN product_batch pb ON gi.batch_id = pb.batch_id
                    WHERE gi.grn_id = grn.grn_id
                    LIMIT 1
                )
            END
        ) = s.supplier_id
        $where_sql
    ";

    // Add ordering
    if (isset($_GET['order'][0])) {
        $order_column = $_GET['order'][0]['column'];
        $order_dir = $_GET['order'][0]['dir'];

        $columns = [
            0 => 'grn.receipt_date',
            1 => 'grn.grn_number',
            2 => 'po.po_number',
            3 => 's.supplier_name',
            4 => 'grn.status',
            5 => 'item_count',
            6 => 'total_value'
        ];

        if (isset($columns[$order_column])) {
            $sql .= " ORDER BY " . $columns[$order_column] . " " .
                ($order_dir === 'asc' ? 'ASC' : 'DESC');
        }
    } else {
        $sql .= " ORDER BY grn.receipt_date DESC";
    }

    // Add limits for pagination
    if (isset($_GET['start']) && isset($_GET['length'])) {
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$_GET['length'];
        $params[] = (int)$_GET['start'];
        $types .= 'ii';
    }

    // Execute query
    $stmt = mysqli_prepare($con, $sql);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Handle NULL values
        $row['total_value'] = $row['total_value'] ?? 0;
        $row['item_count'] = $row['item_count'] ?? 0;
        $row['supplier_name'] = $row['supplier_name'] ?? 'Unknown Supplier';
        $data[] = $row;
    }

    // Count total records
    $count_sql = "
        SELECT COUNT(*) as total
        FROM goods_receipt_notes grn
        LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
        LEFT JOIN suppliers s ON (
            CASE 
                WHEN grn.supplier_id IS NOT NULL THEN grn.supplier_id
                WHEN po.supplier_id IS NOT NULL THEN po.supplier_id
                ELSE (
                    SELECT pb.supplier_id 
                    FROM grn_items gi
                    JOIN product_batch pb ON gi.batch_id = pb.batch_id
                    WHERE gi.grn_id = grn.grn_id
                    LIMIT 1
                )
            END
        ) = s.supplier_id
        $where_sql
    ";

    $count_stmt = mysqli_prepare($con, $count_sql);
    
    if (!empty($params)) {
        // Remove limit/offset parameters for count query
        array_splice($params, -2);
        $types = substr($types, 0, -2);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($count_stmt, $types, ...$params);
        }
    }
    
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total_count = mysqli_fetch_assoc($count_result)['total'];

    // Format response for DataTables
    echo json_encode([
        'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 0,
        'recordsTotal' => $total_count,
        'recordsFiltered' => $total_count,
        'data' => $data
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush(); 