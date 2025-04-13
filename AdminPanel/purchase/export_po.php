<?php
require_once('../../inc/config.php');
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$po_id = intval($_GET['id']);

// Get PO details
$query = "SELECT po.*, s.supplier_name, s.supplier_tel, s.supplier_address, e.emp_name
          FROM purchase_orders po
          JOIN suppliers s ON po.supplier_id = s.supplier_id
          JOIN employees e ON po.created_by = e.employ_id
          WHERE po.po_id = ?";

$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $po_id);
mysqli_stmt_execute($stmt);
$po = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$po) {
    header("Location: index.php");
    exit();
}

// Get PO items
$query = "SELECT poi.*, p.product_name, p.sku
          FROM purchase_order_items poi
          JOIN products p ON poi.product_id = p.product_id
          WHERE po_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $po_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$items = [];
while ($item = mysqli_fetch_assoc($result)) {
    $items[] = $item;
}
mysqli_stmt_close($stmt);
mysqli_free_result($result);
}

// Create PDF
class MYPDF extends TCPDF
{
    public function Header()
    {
        global $ERP_COMPANY_NAME, $ERP_COMPANY_ADDRESS, $ERP_COMPANY_PHONE;

        // Logo
        if (file_exists('../../images/logo.png')) {
            $this->Image('../../images/logo.png', 10, 10, 30);
        }

        // Company info
        $this->SetFont('helvetica', 'B', 15);
        $this->Cell(0, 10, $ERP_COMPANY_NAME, 0, false, 'R');
        $this->Ln(5);

        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 10, $ERP_COMPANY_ADDRESS, 0, false, 'R');
        $this->Ln(5);

        $this->Cell(0, 10, "Tel: " . $ERP_COMPANY_PHONE, 0, false, 'R');
        $this->Ln(15);

        // Title
        $this->SetFont('helvetica', 'B', 18);
        $this->Cell(0, 15, 'Purchase Order', 0, false, 'C');
        $this->Ln(20);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($ERP_COMPANY_NAME);
$pdf->SetTitle("Purchase Order #" . $po['po_number']);

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// PO Details
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Purchase Order Details', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$pdf->Cell(40, 7, 'PO Number:', 0);
$pdf->Cell(60, 7, $po['po_number'], 0);
$pdf->Cell(40, 7, 'Date:', 0);
$pdf->Cell(50, 7, date('Y-m-d', strtotime($po['order_date'])), 0, 1);

$pdf->Cell(40, 7, 'Status:', 0);
$pdf->Cell(60, 7, ucfirst($po['status']), 0);
$pdf->Cell(40, 7, 'Created By:', 0);
$pdf->Cell(50, 7, $po['emp_name'], 0, 1);

// Supplier Details
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Supplier Details', 0, 1);
$pdf->SetFont('helvetica', '', 10);

$pdf->Cell(40, 7, 'Name:', 0);
$pdf->Cell(150, 7, $po['supplier_name'], 0, 1);

$pdf->Cell(40, 7, 'Address:', 0);
$pdf->Cell(150, 7, $po['supplier_address'], 0, 1);

$pdf->Cell(40, 7, 'Phone:', 0);
$pdf->Cell(150, 7, $po['supplier_tel'], 0, 1);

// Items Table
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Order Items', 0, 1);
$pdf->SetFont('helvetica', '', 10);

// Table Header
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(70, 7, 'Product', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'SKU', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Unit Cost', 1, 0, 'C', true);
$pdf->Cell(35, 7, 'Total', 1, 1, 'C', true);

// Table Content
foreach ($items as $item) {
    $total = $item['quantity'] * $item['unit_cost'];

    // Use MultiCell for product name to handle long text
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(70, 7, $item['product_name'], 1, 'L');
    $new_y = $pdf->GetY();
    $pdf->SetXY($x + 70, $y);

    $pdf->Cell(30, $new_y - $y, $item['sku'] ?: '-', 1);
    $pdf->Cell(25, $new_y - $y, $item['quantity'], 1, 0, 'R');
    $pdf->Cell(30, $new_y - $y, number_format($item['unit_cost'], 2), 1, 0, 'R');
    $pdf->Cell(35, $new_y - $y, number_format($total, 2), 1, 1, 'R');
}

// Totals
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(155, 7, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(35, 7, number_format($po['subtotal'], 2), 1, 1, 'R');

if ($po['total_discount'] > 0) {
    $pdf->Cell(155, 7, 'Discount:', 0, 0, 'R');
    $pdf->Cell(35, 7, number_format($po['total_discount'], 2), 1, 1, 'R');
}

if ($po['total_tax'] > 0) {
    $pdf->Cell(155, 7, 'Tax:', 0, 0, 'R');
    $pdf->Cell(35, 7, number_format($po['total_tax'], 2), 1, 1, 'R');
}

if ($po['shipping_fee'] > 0) {
    $pdf->Cell(155, 7, 'Shipping:', 0, 0, 'R');
    $pdf->Cell(35, 7, number_format($po['shipping_fee'], 2), 1, 1, 'R');
}

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(155, 8, 'Total Amount:', 0, 0, 'R');
$pdf->Cell(35, 8, number_format($po['total_amount'], 2), 1, 1, 'R');

// Notes
if (!empty($po['notes'])) {
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Notes:', 0, 1);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->MultiCell(0, 7, $po['notes'], 0, 'L');
}

// Terms & Conditions
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Terms & Conditions:', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->MultiCell(
    0,
    7,
    "1. Please mention our PO number in all correspondence.\n" .
        "2. Goods should match the specifications provided.\n" .
        "3. Delivery should be made within the agreed timeline.\n" .
        "4. Any changes must be approved in writing.\n" .
        "5. Payment will be processed as per agreed terms.",
    0,
    'L'
);

// Signatures
$pdf->Ln(15);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(90, 7, 'Prepared By: _______________________', 0, 0, 'L');
$pdf->Cell(90, 7, 'Approved By: _______________________', 0, 1, 'L');
$pdf->Cell(90, 7, 'Date: ____________________________', 0, 0, 'L');
$pdf->Cell(90, 7, 'Date: ____________________________', 0, 1, 'L');

// Output PDF
$filename = "PO_{$po['po_number']}.pdf";
$pdf->Output($filename, 'D');
