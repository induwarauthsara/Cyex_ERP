<?php
session_start();
unset($_SESSION["employee_id"]);
unset($_SESSION["employee_name"]);
unset($_SESSION["employee_role"]);
session_destroy();
?>
<script>
// Clear localStorage when logging out
localStorage.removeItem('employee_id');
localStorage.removeItem('employee_name');
localStorage.removeItem('employee_role');
window.location.href = '/login';
</script>
