<?php
ob_start();
require_once('../nav.php');
ob_clean();
header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Build query
    $where_clause = $search ? "WHERE supplier_name LIKE ?" : "";

    $sql = "
        SELECT supplier_id as id, supplier_name as text, credit_balance
        FROM suppliers
        $where_clause
        ORDER BY supplier_name
        LIMIT ? OFFSET ?
    ";

    // Prepare and execute query
    $stmt = mysqli_prepare($con, $sql);
    if ($search) {
        $search_param = "%$search%";
        mysqli_stmt_bind_param($stmt, 'sii', $search_param, $limit, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Format the text to show credit balance if any
        $text = $row['text'];
        if ($row['credit_balance'] > 0) {
            $text .= " (Credit: " . number_format($row['credit_balance'], 2) . ")";
        }
        $items[] = [
            'id' => $row['id'],
            'text' => $text
        ];
    }

    // Count total for pagination
    $count_sql = "SELECT COUNT(*) as total FROM suppliers " . $where_clause;
    if ($search) {
        $count_stmt = mysqli_prepare($con, $count_sql);
        mysqli_stmt_bind_param($count_stmt, 's', $search_param);
        mysqli_stmt_execute($count_stmt);
    } else {
        $count_stmt = mysqli_query($con, $count_sql);
    }
    $total = mysqli_fetch_assoc($count_stmt)['total'];

    echo json_encode([
        'items' => $items,
        'hasMore' => ($page * $limit) < $total
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
ob_end_flush();
