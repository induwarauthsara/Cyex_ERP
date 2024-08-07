<?php
ini_set('display_errors', 1); // for debugging - Display errors
ini_set('display_startup_errors', 1); // for debugging - Display errors
error_reporting(E_ALL); // for debugging - Display errors

require_once(__DIR__ . '/../inc/header.php');
require_once(__DIR__ . '/../inc/config.php');

$current_page = basename($_SERVER['PHP_SELF']);

if (session_status() === PHP_SESSION_ACTIVE) {
    if (isset($_SESSION['employee_id'])) {
        $employee_id = $_SESSION['employee_id'];
    }else{
        $employee_id = 0;
    }
} else {
    // redirect to login page
    header("Location: /login.php");
}
?>

<!-- For Swal Modal Box Popups -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!-- Data Table -->
<?php include __DIR__ . '/../inc/DataTable_cdn.php'; ?>


<link rel="stylesheet" href="<?php __DIR__ ?>/AdminPanel/acp.css">

<div class="nav">
    <h1>User Profile</h1>
</div>

<script>
    var NavLinkList = {
        "/profile": {
            name: "Edit Profile",
            icon: "fas fa-user-edit"
        },
        "/profile/attendance/": {
            name: "Attendance",
            icon: "fas fa-user-clock"
        },
        "/profile/payroll.php": {
            name: "Payroll",
            icon: "fas fa-file-invoice-dollar"
        },
    };

    var nav = document.querySelector('.nav');
    //    Get current Path
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

