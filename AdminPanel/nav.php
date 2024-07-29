<?php
// Only Admin can access this page
session_start();
$employee_role = $_SESSION['employee_role'];
if ($employee_role !== "Admin") {
    header("Location: /index.php");
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/header.php';
require_once $_SERVER['DOCUMENT_ROOT'] . './inc/config.php';
include 'auth.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="<?php $_SERVER['DOCUMENT_ROOT']?> /AdminPanel/acp.css">
<div class="nav">
    <h1>Admin Panel</h1>
</div>

<script>
    var NavLinkList = {
        "index.php": {
            name: "Dashboard",
            icon: "fas fa-tachometer-alt"
        },
        "invoices/": {
            name: "Invoices",
            icon: "fas fa-file-invoice-dollar"
        },
        "quotation.php": {
            name: "Quotation",
            icon: "fas fa-file-invoice"
        },
        "charts.php": {
            name: "Charts",
            icon: "fas fa-chart-pie"
        },
        "reports.php": {
            name: "Reports",
            icon: "fas fa-table-list"
        },
        "suppliers.php": {
            name: "Suppliers",
            icon: "fas fa-truck"
        },
        "due_payments.php": {
            name: "Due Payments",
            icon: "fas fa-credit-card"
        },
        "utility_payments.php": {
            name: "Utility Payments",
            icon: "fas fa-file-invoice"
        },
        "one_time_products.php": {
            name: "One Time Products",
            icon: "fas fa-box-open"
        },
        "hrm": {
            name: "HRM",
            icon: "fas fa-user-friends"
        },
        "customers.php": {
            name: "Customers",
            icon: "fas fa-users"
        },
        "/pettycash.php": {
            name: "Pettycash",
            icon: "fas fa-money-bill-wave"
        },
        "/transactionLog": { // After Developing "Report" page move this to "Report" page
            name: "Transaction Log",
            icon: "fas fa-file-invoice-dollar"
        },
        "client_website.php": {
            name: "Web",
            icon: "fas fa-globe"
        },
        "printers.php": {
            name: "Printers",
            icon: "fas fa-print"
        },
        "bank.php": {
            name: "Bank",
            icon: "fas fa-university"
        },
        "ai_chat.php": {
            name: "AI Chat",
            icon: "fas fa-comments"
        }
    };

    var nav = document.querySelector('.nav');
    var current_page = '<?= $current_page ?>'; // PHP variable for the current page

    for (var key in NavLinkList) {
        var a = document.createElement('a');
        a.href = key;
        a.innerHTML = `<i class="${NavLinkList[key].icon}"></i> ${NavLinkList[key].name}`;
        a.className = key === current_page ? 'active' : '';
        nav.appendChild(a);
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>