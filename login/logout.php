<?php
session_start();
unset($_SESSION["employee_id"]);
unset($_SESSION["employee_name"]);
unset($_SESSION["employee_role"]);
header("Location:/login");
