<?php
include '../nav.php';

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
} elseif (isset($_GET['emp']) && isset($_GET['month'])) {
    // Get employee name and month from the GET request
    $employee_name = $_GET['emp'];
    $month = $_GET['month'];

    // Determine the payroll cycle start and end dates (customizable)
    $start_date = date("Y-m-d", strtotime("$month-21 -1 month"));
    $end_date = date("Y-m-d", strtotime("$month-20"));

    // Fetch the employee's payroll data
    $PayrollTableSql = "SELECT salary_id, employees.emp_name, amount, description, date, time
                        FROM salary, employees
                        WHERE salary.emp_id = employees.employ_id
                        AND employees.emp_name = '$employee_name'
                        AND date BETWEEN '$start_date' AND '$end_date'
                        ORDER BY date DESC, time DESC";

    echo "<h1>Payroll for $employee_name, from $start_date to $end_date</h1>";

    $Employee_names_sql = "SELECT employ_id, emp_name FROM employees WHERE status = 1";

    // find employee id
    $employee_id = mysqli_fetch_assoc(mysqli_query($con, "SELECT employ_id FROM employees WHERE emp_name = '$employee_name'"))['employ_id'];

    // Fetch total salary to pay
    $total_salary_result = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(salary) as total_salary FROM employees WHERE employ_id = '$employee_id' AND status = 1"));
    $total_salary_have_pay = $total_salary_result['total_salary'] !== null ? floatval($total_salary_result['total_salary']) : 0.0;
    echo "<h3 style='color:white'>Total Salary to pay : Rs. " . number_format($total_salary_have_pay, 2) . "</h3><br>";
    echo "<hr>";

    // Fetch gross earnings
    $grossEarning_result = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(amount) as monthSum FROM salary, employees WHERE amount > 0 and salary.emp_id = employees.employ_id AND employees.emp_name = '$employee_name' AND date BETWEEN '$start_date' AND '$end_date'"));
    $grossEarning = $grossEarning_result['monthSum'] !== null ? floatval($grossEarning_result['monthSum']) : 0.0;
    echo "<h3 style='color:white'>($month Month) Total Earning : Rs. " . number_format($grossEarning, 2) . "</h3><br>";

    // Fetch total deductions
    $totalDeduct_result = mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(amount) as monthSum FROM salary, employees WHERE amount < 0 and salary.emp_id = employees.employ_id AND employees.emp_name = '$employee_name' AND date BETWEEN '$start_date' AND '$end_date'"));
    $totalDeduct = $totalDeduct_result['monthSum'] !== null ? floatval($totalDeduct_result['monthSum']) : 0.0;
    echo "<h3 style='color:white'>($month Month) Total Deduction : Rs. " . number_format($totalDeduct, 2) . "</h3><br>";

    // Calculate total salary due payment
    $monthSum = $grossEarning + $totalDeduct; // Adjusted for deductions being negative
    echo "<h3 style='color:white'>($month Month) Total Salary Due Payment : Rs. " . number_format($monthSum, 2) . "</h3><br>";
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
echo "<hr>";
?>

<title>Payroll</title>

<!-- Buttons for view all Employee Names and Monthly Payroll -->
<div style='display: flex; justify-content: space-between; align-items: center; margin: auto 20px;'>
    <div style='display: flex; gap: 5px;'>
        <h3>Others : </h3>
        <a href='viewPayrolls.php'><button>All</button></a>
        <?php
        $Employee_names_sql_result = mysqli_query($con, $Employee_names_sql);

        $employee_names_array = [];

        if ($Employee_names_sql_result) {
            while ($row = mysqli_fetch_assoc($Employee_names_sql_result)) {
                $employee_id_for_buttons = $row['employ_id'];
                $employee_name_for_buttons = $row['emp_name'];
                array_push($employee_names_array, $employee_name_for_buttons);
                echo "<a href='viewPayrolls.php?employee_id=$employee_id_for_buttons'><button>$employee_name_for_buttons</button></a><br>";
            }
        }
        ?>
    </div>

    <!-- Monthly Payroll Button -->
    <button id="monthlyPayrollBtn" style="height: 35px;">Monthly Payroll</button>
</div>

<!-- Table to display Payroll data -->
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
            // Loop through the rows and generate HTML Table for each payroll record
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

<!-- SweetAlert2 and DataTable JS -->
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#DataTable').DataTable();

        // Get URL parameters
        var empName = "<?php echo isset($_GET['emp']) ? $_GET['emp'] : ''; ?>";
        var startDate = "<?php echo isset($start_date) ? $start_date : ''; ?>";
        var endDate = "<?php echo isset($end_date) ? $end_date : ''; ?>";

        // Custom filtering function for employee name and date range
        if (empName && startDate && endDate) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var employeeName = data[1]; // Employee Name column index
                var date = data[4]; // Date column index

                // Check employee name and date range
                var isNameMatch = employeeName === empName;
                var isDateInRange = (date >= startDate && date <= endDate);

                return isNameMatch && isDateInRange;
            });

            table.draw();
        }

        // Monthly Payroll button click event
        $('#monthlyPayrollBtn').click(function() {
            // Trigger SweetAlert2 popup for employee and month input
            Swal.fire({
                title: 'Filter Payroll by Employee and Month',
                html: `
                    <form id="payrollFilterForm">
                        <label for="emp">Employee Name:</label>
                        <select id="emp" name="emp" class="swal2-select" required>
                            <?php
                            foreach ($employee_names_array as $employee_name) {
                                echo "<option value='$employee_name'>$employee_name</option>";
                            }
                            ?>

                        <label for="month">Month (format: YYYY-MM):</label>
                        <input type="month" id="month" name="month" class="swal2-input" required>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: function() {
                    var emp = document.getElementById('emp').value;
                    var month = document.getElementById('month').value;
                    if (!emp || !month) {
                        Swal.showValidationMessage('Please fill in both fields.');
                    }
                    return {
                        emp: emp,
                        month: month
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to viewPayrolls.php with query parameters for employee and month
                    var emp = result.value.emp;
                    var month = result.value.month;
                    window.location.href = `viewPayrolls.php?emp=${emp}&month=${month}`;
                }
            });
        });
    });
</script>