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
    $paymentStatus = isset($_GET['paymentStatus']) ? $_GET['paymentStatus'] : null;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $is_select2 = isset($_GET['select2']);

    // Initialize parameters array and types string
    $params = [];
    $types = '';
    $where_clauses = [];
    $having_clauses = [];

    // Add date range filter
    if ($dateRange) {
        switch ($dateRange) {
            case 'today':
                $where_clauses[] = "DATE(po.order_date) = CURDATE()";
                break;
            case 'week':
                $where_clauses[] = "YEARWEEK(po.order_date) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $where_clauses[] = "YEAR(po.order_date) = YEAR(CURDATE()) AND MONTH(po.order_date) = MONTH(CURDATE())";
                break;
            case 'year':
                $where_clauses[] = "YEAR(po.order_date) = YEAR(CURDATE())";
                break;
            case 'custom':
                if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
                    $where_clauses[] = "DATE(po.order_date) BETWEEN ? AND ?";
                    $params[] = $_GET['startDate'];
                    $params[] = $_GET['endDate'];
                    $types .= 'ss';
                }
                break;
        }
    }

    // Add supplier filter
    if ($supplier) {
        $where_clauses[] = "po.supplier_id = ?";
        $params[] = $supplier;
        $types .= 'i';
    }

    // Add status filter
    if ($status) {
        $where_clauses[] = "po.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    // Add payment status filter
    if ($paymentStatus) {
        switch ($paymentStatus) {
            case 'paid':
                $having_clauses[] = "paid_amount >= total_amount";
                break;
            case 'partial':
                $having_clauses[] = "paid_amount > 0 AND paid_amount < total_amount";
                break;
            case 'unpaid':
                $having_clauses[] = "COALESCE(paid_amount, 0) = 0";
                break;
        }
    }

    // Add search filter
    if ($search) {
        if ($is_select2) {
            $where_clauses[] = "(po.po_number LIKE ? OR s.supplier_name LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'ss';
        } else {
            $where_clauses[] = "(po.po_number LIKE ? OR s.supplier_name LIKE ? OR po.status LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
            $params[] = $search_param;
            $types .= 'sss';
        }
    }

    $where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";
    $having_sql = !empty($having_clauses) ? "HAVING " . implode(" AND ", $having_clauses) : "";

    // Base query with all required columns for GROUP BY
    $sql = "
        SELECT 
            po.po_id,
            po.po_number,
            po.order_date,
            po.supplier_id,
            po.status,
            po.shipping_fee,
            po.discount_type,
            po.discount_value,
            po.tax_type,
            po.tax_value,
            po.subtotal,
            po.total_discount,
            po.total_tax,
            po.total_amount,
            po.notes,
            po.created_by,
            po.created_at,
            po.updated_at,
            s.supplier_name,
            COALESCE(SUM(sp.amount), 0) as paid_amount
        FROM purchase_orders po
        LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
        LEFT JOIN supplier_payments sp ON po.po_id = sp.po_id
        $where_sql
        GROUP BY 
            po.po_id, po.po_number, po.order_date, po.supplier_id, 
            po.status, po.shipping_fee, po.discount_type, po.discount_value,
            po.tax_type, po.tax_value, po.subtotal, po.total_discount,
            po.total_tax, po.total_amount, po.notes, po.created_by,
            po.created_at, po.updated_at, s.supplier_name
        $having_sql
    ";

    // Add ordering
    if ($is_select2) {
        $sql .= " ORDER BY po.order_date DESC";
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
    } else {
        if (isset($_GET['order'][0])) {
            $order_column = $_GET['order'][0]['column'];
            $order_dir = $_GET['order'][0]['dir'];

            $columns = [
                0 => 'po.order_date',
                1 => 'po.po_number',
                2 => 's.supplier_name',
                3 => 'po.status',
                4 => 'po.total_amount',
                5 => 'paid_amount'
            ];

            if (isset($columns[$order_column])) {
                $sql .= " ORDER BY " . $columns[$order_column] . " " .
                    ($order_dir === 'asc' ? 'ASC' : 'DESC');
            }
        } else {
            $sql .= " ORDER BY po.order_date DESC";
        }

        if (isset($_GET['start']) && isset($_GET['length'])) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = (int)$_GET['length'];
            $params[] = (int)$_GET['start'];
            $types .= 'ii';
        }
    }

    // Prepare and execute query with error checking
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($con));
    }

    if (!empty($params)) {
        if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
            throw new Exception('Failed to bind parameters: ' . mysqli_stmt_error($stmt));
        }
    }

    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        throw new Exception('Failed to get result: ' . mysqli_error($con));
    }

    // Modified count query to include HAVING clause
    $count_sql = "
        SELECT COUNT(*) as total FROM (
            SELECT po.po_id, COALESCE(SUM(sp.amount), 0) as paid_amount,
                   po.total_amount
            FROM purchase_orders po
            LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
            LEFT JOIN supplier_payments sp ON po.po_id = sp.po_id
            $where_sql
            GROUP BY po.po_id, po.total_amount
            $having_sql
        ) as counted
    ";

    $count_stmt = mysqli_prepare($con, $count_sql);
    if ($count_stmt === false) {
        throw new Exception('Failed to prepare count statement: ' . mysqli_error($con));
    }

    if (!empty($params)) {
        // Remove limit/offset parameters for count query
        if (!$is_select2) {
            array_splice($params, -2);
            $types = substr($types, 0, -2);
        }

        if (!mysqli_stmt_bind_param($count_stmt, $types, ...$params)) {
            throw new Exception('Failed to bind count parameters: ' . mysqli_stmt_error($count_stmt));
        }
    }

    if (!mysqli_stmt_execute($count_stmt)) {
        throw new Exception('Failed to execute count statement: ' . mysqli_stmt_error($count_stmt));
    }

    $count_result = mysqli_stmt_get_result($count_stmt);
    if ($count_result === false) {
        throw new Exception('Failed to get count result: ' . mysqli_error($con));
    }

    $total_count = mysqli_fetch_assoc($count_result)['total'];

    // Format and return results
    if ($is_select2) {
        $items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = [
                'id' => $row['po_id'],
                'text' => "PO #{$row['po_number']} - {$row['supplier_name']} (" .
                    date('Y-m-d', strtotime($row['order_date'])) . ")"
            ];
        }

        echo json_encode([
            'items' => $items,
            'hasMore' => ($page * $limit) < $total_count
        ]);
    } else {
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = [
                'DT_RowId' => 'po_' . $row['po_id'],
                'po_id' => $row['po_id'],
                'po_number' => $row['po_number'],
                'order_date' => date('Y-m-d', strtotime($row['order_date'])),
                'supplier_name' => $row['supplier_name'],
                'status' => $row['status'],
                'total_amount' => $row['total_amount'],
                'paid_amount' => $row['paid_amount'],
                'balance' => $row['total_amount'] - $row['paid_amount']
            ];
        }

        echo json_encode([
            'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 0,
            'recordsTotal' => $total_count,
            'recordsFiltered' => $total_count,
            'data' => $data
        ]);
    }
} catch (Exception $e) {
    error_log("Error in get_purchase_orders.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}

ob_end_flush();
