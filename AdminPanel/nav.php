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
        "/AdminPanel/quotation.php": {
            name: "Quotation",
            icon: "fas fa-file-invoice"
        },
        "/AdminPanel/charts.php": {
            name: "Charts",
            icon: "fas fa-chart-pie"
        },
        "/AdminPanel/reports.php": {
            name: "Reports",
            icon: "fas fa-table-list"
        },
        "/AdminPanel/suppliers.php": {
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
        "/AdminPanel/one_time_products.php": {
            name: "One Time Products",
            icon: "fas fa-box-open"
        },
        "/AdminPanel/hrm": {
            name: "HRM",
            icon: "fas fa-user-friends"
        },
        "/AdminPanel/customers.php": {
            name: "Customers",
            icon: "fas fa-users"
        },
        "/AdminPanel//pettycash.php": {
            name: "Pettycash",
            icon: "fas fa-money-bill-wave"
        },
        "/AdminPanel//transactionLog": { // After Developing "Report" page move this to "Report" page
            name: "Transaction Log",
            icon: "fas fa-file-invoice-dollar"
        },
        "/AdminPanel/client_website.php": {
            name: "Web",
            icon: "fas fa-globe"
        },
        "/AdminPanel/printers.php": {
            name: "Printers",
            icon: "fas fa-print"
        },
        "/AdminPanel/bank.php": {
            name: "Bank",
            icon: "fas fa-university"
        },
        "/AdminPanel/ai_chat.php": {
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