<?php
// Only Admin can access this page
session_start();
$employee_role = $_SESSION['employee_role'];
if ($employee_role !== "Admin") {
    header("Location: /index.php");
}
require '../inc/header.php';
require_once '../inc/config.php';
include '../dashboard/auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="acp.css">
<div class="nav">
    <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Add Count</a>
    <a href="index.php" class="<?= $current_page == 'dailycost-Total.php' ? 'active' : '' ?>"><b>Total</b> - Daily Cost</a>
    <a href="index
    .php" class="<?= $current_page == 'dailycost-Printer.php' ? 'active' : '' ?>"><b>Printers</b> - Daily Cost</a>
    <a href="bankdeposit.php" class="<?= $current_page == 'bankdeposit.php' ? 'active' : '' ?>">Deposit History</a>
    <a href="sum.php" class="<?= $current_page == 'sum.php' ? 'active' : '' ?>">Details Report</a>
</div>