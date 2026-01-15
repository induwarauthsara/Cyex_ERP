<?php
require_once '../inc/config.php';

// DataTables server-side processing
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

// Order
$orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
$orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'DESC';

// Column mapping for ordering
$columns = array(
    0 => 'i.invoice_number',
    1 => 'i.invoice_date',
    2 => 'i.customer_name',
    3 => 'i.customer_mobile',
    4 => 'i.time',
    5 => 'i.total',
    6 => 'i.discount',
    7 => 'i.advance',
    8 => 'i.balance',
    9 => 'i.cost',
    10 => 'i.profit',
    11 => 'e.emp_name',
    12 => 'i.full_paid',
    13 => 'i.paymentMethod'
);

$orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'i.invoice_number';

// Base query
$baseQuery = "FROM invoice i
              INNER JOIN employees e ON i.biller = e.employ_id
              WHERE i.is_deleted = 0";

// Search condition
$whereClause = "";
if (!empty($searchValue)) {
    $searchValue = mysqli_real_escape_string($con, $searchValue);
    $whereClause = " AND (i.invoice_number LIKE '%$searchValue%' 
                     OR i.customer_name LIKE '%$searchValue%' 
                     OR i.customer_mobile LIKE '%$searchValue%'
                     OR e.emp_name LIKE '%$searchValue%'
                     OR i.invoice_date LIKE '%$searchValue%')";
}

// Total records without filtering
$totalQuery = "SELECT COUNT(*) as total FROM invoice i INNER JOIN employees e ON i.biller = e.employ_id WHERE i.is_deleted = 0";
$totalResult = mysqli_query($con, $totalQuery);
$totalRecords = mysqli_fetch_assoc($totalResult)['total'];

// Total records with filtering
$filteredQuery = "SELECT COUNT(*) as total " . $baseQuery . $whereClause;
$filteredResult = mysqli_query($con, $filteredQuery);
$filteredRecords = mysqli_fetch_assoc($filteredResult)['total'];

// Fetch data
$dataQuery = "SELECT i.*, e.emp_name " . $baseQuery . $whereClause . " ORDER BY $orderColumn $orderDir LIMIT $start, $length";
$result = mysqli_query($con, $dataQuery);

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $paided = $row['full_paid'];
    
    // Full Paid column
    if ($paided == 0) {
        $fullPaidHtml = "No <br> 
            <button onclick='addFund(\"" . $row['invoice_number'] . "\", " . $row['balance'] . ", \"Add Fund\")'>Add Fund</button> <br>  
            <button onclick='addFund(\"" . $row['invoice_number'] . "\", " . $row['balance'] . ", \"Full Payment\")'>Full Payment</button>";
    } else {
        $fullPaidHtml = 'Yes';
    }
    
    // Actions column
    $actionsHtml = "<a href='/invoice/edit-bill.php?id=" . $row['invoice_number'] . "'>Edit</a> | 
                    <a href='javascript:void(0)' onclick='confirmDeleteInvoice(" . $row['invoice_number'] . ")' style='color:red;'>Delete</a>";
    
    // Print column
    $printHtml = "<a href='/invoice/print.php?id=" . $row['invoice_number'] . "' target='_blank'>Print</a>";
    
    $data[] = array(
        $row['invoice_number'],
        $row['invoice_date'],
        $row['customer_name'],
        $row['customer_mobile'],
        $row['time'],
        $row['total'],
        $row['discount'],
        $row['advance'],
        $row['balance'],
        $row['cost'],
        $row['profit'],
        $row['emp_name'],
        $fullPaidHtml,
        $row['paymentMethod'],
        $actionsHtml,
        $printHtml
    );
}

// Prepare response
$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $filteredRecords,
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);

end_db_con();
?>
