<?php
include 'UCPnav.php';

$sql = "SELECT salary FROM employees WHERE employ_id = '$employee_id'";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$salary = $row['salary'];
$salary = number_format($salary, 2);
?>

<h1>Payroll</h1>
<h3>Rs. <?php echo $salary ?> </h3>

<table id="DataTable" class="display">
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT employees.emp_name, amount, description, date, time
                FROM salary, employees
                WHERE salary.emp_id = employees.employ_id
                    AND employees.employ_id = '$employee_id'
                ORDER BY date DESC, time DESC";
        $result = mysqli_query($con, $sql);

        if ($result) {
            // Loop through the rows and generate HTML Table for each attendance record
            while ($row = mysqli_fetch_assoc($result)) {
                $amount = number_format($row['amount'], 2);
                echo "<tr>
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

<style>
    h3 {
        color: white;
    }
</style>