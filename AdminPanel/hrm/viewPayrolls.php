<?php
include '../nav.php';

// check GET request
if (isset($_GET['employee_id'])) {
    // for individual employee
    $employee_id = $_GET['employee_id'];
    $sql = "SELECT salary FROM employees WHERE employ_id = '$employee_id'";

    $total_salary_have_pay = number_format(mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(salary) as total_salary FROM employees WHERE employ_id = '$employee_id' AND status = 1"))['total_salary'], 2);

    $employee_name = mysqli_fetch_assoc(mysqli_query($con, "SELECT emp_name FROM employees WHERE employ_id = '$employee_id'"))['emp_name'];
    echo "<h1>Payroll for $employee_name</h1>";
    $PayrollTableSql = "SELECT salary_id, employees.emp_name, amount, description, date, time
                FROM salary, employees
                WHERE salary.emp_id = employees.employ_id
                    AND employees.employ_id = '$employee_id'
                ORDER BY date DESC, time DESC";

    echo "<h3 style='color:white'>Total Salary to pay : Rs. $total_salary_have_pay </h3><br>";

    $Employee_names_sql = "SELECT employ_id, emp_name FROM employees WHERE status = 1 AND employ_id != $employee_id";
} else {
    // for all employees
    $total_salary_have_pay = number_format(mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(salary) as total_salary FROM employees WHERE status = 1"))['total_salary'], 2);
    echo "<h1>Payroll of All Employees</h1>";
    $PayrollTableSql = "SELECT salary_id, employees.emp_name, amount, description, date, time
                FROM salary, employees
                WHERE salary.emp_id = employees.employ_id
                ORDER BY date DESC, time DESC";

    $Employee_names_sql = "SELECT employ_id, emp_name FROM employees WHERE status = 1";
}

?>

<title>Payroll</title>

<!-- Buttons for view all Employee Names -->
<div style='display: flex; gap: 5px;'>
    <h3>Others : </h3>
    <a href='viewPayrolls.php'><button>All</button></a>
    <?php
        $Employee_names_sql_result = mysqli_query($con, $Employee_names_sql);

    if ($Employee_names_sql_result) {
        while ($row = mysqli_fetch_assoc($Employee_names_sql_result)) {
            $employee_id_for_buttons = $row['employ_id'];
            $employee_name_for_buttons = $row['emp_name'];
            echo "<a href='viewPayrolls.php?employee_id=$employee_id_for_buttons'><button>$employee_name_for_buttons</button></a><br>";
        }
    } ?>
</div>
<table id="DataTable" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Employee Name</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        <?php

        $result = mysqli_query($con, $PayrollTableSql);

        if ($result) {
            // Loop through the rows and generate HTML Table for each attendance record
            while ($row = mysqli_fetch_assoc($result)) {
                $amount = number_format($row['amount'], 2);
                echo "<tr>
            <td>" . $row['salary_id'] . "</td>
            <td>" . $row['emp_name'] . "</td>
            <td>" . $row['description'] . "</td>
            <td>" . $amount . "</td>
            <td>" . $row['date'] . "</td>
            <td>" . $row['time'] . "</td>
        </tr>";
            }
        }
        ?>
    </tbody>
</table>