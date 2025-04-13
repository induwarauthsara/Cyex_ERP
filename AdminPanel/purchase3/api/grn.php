<?php
// File: /AdminPanel/purchase/api/grn.php
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
            
        case 'print':
            handlePrint();
            break;
            
        case 'get_barcode_items':
            handleGetBarcodeItems();
            break;

        default:
            throw new Exception('Invalid action specified');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function handleList() {
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
    $baseQuery = "FROM goods_receipt_notes grn
                  LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
                  LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                  LEFT JOIN employees e ON grn.created_by = e.employ_id
                  WHERE 1=1";
    
    // Apply filters
    $params = [];
    
    if ($search) {
        $baseQuery .= " AND (grn.grn_number LIKE ? OR po.po_number LIKE ? OR 
                            s.supplier_name LIKE ? OR grn.invoice_number LIKE ?)";
        array_push($params, "%$search%", "%$search%", "%$search%", "%$search%");
    }
    
    if ($status) {
        $baseQuery .= " AND grn.status = ?";
        array_push($params, $status);
    }
    
    if ($supplier_id) {
        $baseQuery .= " AND po.supplier_id = ?";
        array_push($params, $supplier_id);
    }
    
    if ($date_from) {
        $baseQuery .= " AND grn.receipt_date >= ?";
        array_push($params, $date_from);
    }
    
    if ($date_to) {
        $baseQuery .= " AND grn.receipt_date <= ?";
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
        'grn.grn_id',
        'grn.grn_number',
        'po.po_number',
        'grn.receipt_date',
        's.supplier_name',
        'grn.invoice_number',
        'grn.status',
        'e.emp_name'
    ];
    
    // Add sorting
    $orderColumn = $columns[$orderColumn] ?? 'grn.receipt_date';
    $baseQuery .= " ORDER BY $orderColumn $orderDir";
    
    // Add pagination
    $baseQuery .= " LIMIT ?, ?";
    array_push($params, (int)$start, (int)$length);

    // Get filtered data
    $query = "SELECT grn.*, 
              po.po_number,
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
        'data' => $data
    ]);
}

function handleDelete() {
    global $con;
    
    $jsonBody = file_get_contents('php://input');
    $data = json_decode($jsonBody, true);
    
    $grnId = $data['grn_id'] ?? null;
    
    if (!$grnId) {
        throw new Exception('GRN ID is required');
    }
    
    mysqli_begin_transaction($con);

    try {
        // Check if GRN exists and is in draft status
        $stmt = mysqli_prepare(
            $con,
            "SELECT status FROM goods_receipt_notes WHERE grn_id = ? FOR UPDATE"
        );
        mysqli_stmt_bind_param($stmt, 'i', $grnId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['status'] !== 'draft') {
                throw new Exception('Only draft GRNs can be deleted');
            }
        } else {
            throw new Exception('GRN not found');
        }

        // Delete GRN items first
        $query = "DELETE FROM grn_items WHERE grn_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $grnId);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to delete GRN items');
        }

        // Delete GRN
        $query = "DELETE FROM goods_receipt_notes WHERE grn_id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'i', $grnId);

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception('Failed to delete GRN');
        }

        // Log the transaction
        transaction_log(
            'DELETE_GRN',
            "Deleted GRN #$grnId",
            0
        );

        mysqli_commit($con);

        echo json_encode([
            'success' => true,
            'message' => 'GRN deleted successfully'
        ]);
    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }
}

function handlePrint()
{
    global $con;

    $grnId = $_GET['id'] ?? null;

    if (!$grnId) {
        throw new Exception('GRN ID is required');
    }

    // Get GRN details
    $query = "SELECT grn.*, 
              po.po_number,
              s.supplier_name, s.supplier_tel, s.supplier_address,
              e.emp_name as created_by_name
              FROM goods_receipt_notes grn
              LEFT JOIN purchase_orders po ON grn.po_id = po.po_id
              LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
              LEFT JOIN employees e ON grn.created_by = e.employ_id
              WHERE grn.grn_id = ?";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $grnId);
    mysqli_stmt_execute($stmt);
    $grn = mysqli_stmt_get_result($stmt)->fetch_assoc();

    if (!$grn) {
        throw new Exception('GRN not found');
    }

    // Get GRN items
    $query = "SELECT gi.*, p.product_name, p.sku
              FROM grn_items gi
              JOIN products p ON gi.product_id = p.product_id
              WHERE gi.grn_id = ?";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, 'i', $grnId);
    mysqli_stmt_execute($stmt);
    $items = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    // Generate PDF
    require_once '../../../vendor/autoload.php'; // Assuming you have TCPDF installed

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
    $pdf->SetCreator('Your ERP System');
    $pdf->SetAuthor('Your Company');
    $pdf->SetTitle('GRN #' . $grn['grn_number']);

    // Add a page
    $pdf->AddPage();

    // Set font
    $pdf->SetFont('helvetica', '', 10);

    // Add content to PDF
    generateGRNPDF($pdf, $grn, $items);

    // Output PDF
    header('Content-Type: application/pdf');
    echo $pdf->Output('GRN_' . $grn['grn_number'] . '.pdf', 'S');
}

function handleGetBarcodeItems()
{
    global $con;

    $jsonBody = file_get_contents('php://input');
    $data = json_decode($jsonBody, true);

    $grnIds = $data['grn_ids'] ?? [];

    if (empty($grnIds)) {
        throw new Exception('No GRNs selected');
    }

    // Get items from selected GRNs
    $placeholders = str_repeat('?,', count($grnIds) - 1) . '?';
    $query = "SELECT gi.*, p.product_name, pb.batch_number
              FROM grn_items gi
              JOIN products p ON gi.product_id = p.product_id
              JOIN product_batch pb ON gi.batch_id = pb.batch_id
              WHERE gi.grn_id IN ($placeholders)
              ORDER BY p.product_name";

    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, str_repeat('i', count($grnIds)), ...$grnIds);
    mysqli_stmt_execute($stmt);
    $items = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'success' => true,
        'items' => $items
    ]);
}

function generateGRNPDF($pdf, $grn, $items)
{
    // Add company logo and header
    $pdf->Image('../../../logo.png', 10, 10, 30);
    $pdf->SetXY(45, 10);
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'GOODS RECEIPT NOTE', 0, 1, 'C');

    // Add GRN details
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 7, 'GRN Number:', 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(60, 7, $grn['grn_number'], 0);

    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 7, 'Date:', 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 7, date('d/m/Y', strtotime($grn['receipt_date'])), 0, 1);

    // Add supplier details
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(30, 7, 'Supplier:', 0);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 7, $grn['supplier_name'] . "\n" .
        $grn['supplier_address'] . "\n" .
        "Tel: " . $grn['supplier_tel'], 0);

    // Add items table
    $pdf->Ln(5);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(200, 200, 200);
    $pdf->Cell(10, 7, '#', 1, 0, 'C', true);
    $pdf->Cell(60, 7, 'Product', 1, 0, 'L', true);
    $pdf->Cell(30, 7, 'Batch', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Quantity', 1, 0, 'R', true);
    $pdf->Cell(30, 7, 'Unit Cost', 1, 0, 'R', true);
    $pdf->Cell(30, 7, 'Total', 1, 1, 'R', true);

    $pdf->SetFont('helvetica', '', 10);
    $total = 0;
    foreach ($items as $i => $item) {
        $lineTotal = $item['received_qty'] * $item['cost'];
        $total += $lineTotal;

        $pdf->Cell(10, 7, $i + 1, 1, 0, 'C');
        $pdf->Cell(60, 7, $item['product_name'], 1, 0, 'L');
        $pdf->Cell(30, 7, $item['batch_number'], 1, 0, 'C');
        $pdf->Cell(30, 7, number_format($item['received_qty']), 1, 0, 'R');
        $pdf->Cell(30, 7, number_format($item['cost'], 2), 1, 0, 'R');
        $pdf->Cell(30, 7, number_format($lineTotal, 2), 1, 1, 'R');
    }

    // Add total
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(130, 7, 'Total:', 1, 0, 'R', true);
    $pdf->Cell(60, 7, number_format($total, 2), 1, 1, 'R', true);

    // Add signatures
    $pdf->Ln(20);
    $pdf->Cell(90, 7, 'Received By: _________________', 0, 0, 'C');
    $pdf->Cell(90, 7, 'Checked By: _________________', 0, 1, 'C');

    $pdf->Cell(90, 7, 'Date: _________________', 0, 0, 'C');
    $pdf->Cell(90, 7, 'Date: _________________', 0, 1, 'C');
}