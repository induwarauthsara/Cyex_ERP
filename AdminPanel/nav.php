<?php
ini_set('display_errors', 1); // for debugging - Display errors
ini_set('display_startup_errors', 1); // for debugging - Display errors
error_reporting(E_ALL); // for debugging - Display errors

// Only Admin can access this page
session_start();
$employee_role = $_SESSION['employee_role'];
if ($employee_role !== "Admin") {
    header("Location: /index.php");
}
require_once(__DIR__ . '/../inc/header.php');
require_once(__DIR__ . '/../inc/config.php');
include 'auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- For Swal Modal Box Popups -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!-- Data Table -->
<?php include __DIR__ . '/../inc/DataTable_cdn.php'; ?>


<link rel="stylesheet" href="<?php __DIR__ ?>/AdminPanel/acp.css">
<div class="nav">
    <h1>Admin Panel</h1>
</div>

<script>
    var NavLinkList = {
        "/AdminPanel/": {
            name: "Dashboard",
            icon: "fas fa-tachometer-alt"
        },
        "/AdminPanel/invoices/": {
            name: "Invoices",
            icon: "fas fa-file-invoice-dollar"
        },
        "/AdminPanel/purchase/": {
            name: "Purchase & GRN",
            icon: "fas fa-cart-plus"
        },
        "/AdminPanel/quotation.php": {
            name: "Quotation",
            icon: "fas fa-file-invoice"
        },
        "/AdminPanel/charts.php": {
            name: "Charts",
            icon: "fas fa-chart-pie"
        },
        "/AdminPanel/reports/index.php": {
            name: "Reports",
            icon: "fas fa-table-list"
        },
        "/suppliers/": {
            name: "Suppliers",
            icon: "fas fa-truck"
        },
        "/AdminPanel/due_payments.php": {
            name: "Due Payments",
            icon: "fas fa-credit-card"
        },
        "/AdminPanel/utility_payments.php": {
            name: "Utility Payments",
            icon: "fas fa-file-invoice"
        },
        "/AdminPanel/hrm/viewPayrolls.php": {
            name: "Salary Payrolls",
            icon: "fas fa-receipt"
        },
        "/AdminPanel/hrm/viewPayrolls.php": {
            name: "Salary Payrolls",
            icon: "fas fa-receipt"
        },
        "/AdminPanel/hrm": {
            name: "HRM",
            icon: "fas fa-user-friends"
        },
        "/AdminPanel/customers.php": {
            name: "Customers",
            icon: "fas fa-users"
        },
        "/pettycash.php": {
            name: "Pettycash",
            icon: "fas fa-money-bill-wave"
        },
        "/AdminPanel/logs/transactionLog.php": { // After Developing "Report" page move this to "Report" page
            name: "Transaction Log",
            icon: "fas fa-file-invoice-dollar"
        },
        "/AdminPanel/bank": {
            name: "Bank",
            icon: "fas fa-university"
        },
    };

    var nav = document.querySelector('.nav');
    const current_page = window.location.pathname;

    for (var key in NavLinkList) {
        var a = document.createElement('a');
        a.href = key;
        a.innerHTML = `<i class="${NavLinkList[key].icon}"></i> ${NavLinkList[key].name}`;
        a.className = key === current_page ? 'active' : '';
        nav.appendChild(a);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>