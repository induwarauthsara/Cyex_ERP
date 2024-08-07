  <?php
    include 'UCPnav.php';

//   $employee_id = 13; // for debugging

    // Get the employee's salary
    $sql = "SELECT day_salary FROM employees WHERE employ_id = '$employee_id'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
    $DaySalary = $row['day_salary'];
    // Employee Should be Working for 8 Hours
    $WorkingDayHours = 8;
    $WorkingDayMaxHours = 17;
    $HourSalary = $DaySalary / $WorkingDayHours;

    // Today
    $today = date('Y-m-d'); //for production
    // $today = '2024-08-06'; // for debugging

    // Get the employee's clock in time
    $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock In' ORDER BY id DESC LIMIT 1;";
    // echo $sql;
    $result = mysqli_query($con, $sql);    
    if ($result) {
        $row_count = mysqli_num_rows($result);
        if ($row_count == 0) {
            die('Error: No Clock In Record Found');
        }
        $row = mysqli_fetch_assoc($result);
        $last_clock_in_time = $row['time'];
        $last_clock_in_date = $row['date'];
        // echo "<br>Last Clock In Time: $last_clock_in_time <br>"; // for debugging
    }else{
        die('Error: ' . mysqli_error($con));
    }

    // Get the employee's clock out time
    $sql = "SELECT date, time FROM attendance WHERE employee_id = '$employee_id' AND date = '$today' AND action = 'Clock Out' ORDER BY id DESC LIMIT 1";
    // echo $sql ."<br>"; // for debugging
    $result = mysqli_query($con, $sql);
    if ($result) {
        $row_count = mysqli_num_rows($result);
        if ($row_count == 0) {
            die('Error: No Clock Out Record Found');
        }
        $row = mysqli_fetch_assoc($result);
        $last_clock_out_time = $row['time'];
        $last_clock_out_date = $row['date'];
        // echo "<br>Last Clock Out Time: $last_clock_out_time <br>"; // for debugging
    } else {
        die('Error: ' . mysqli_error($con));
    }

    // Calculate the working hours
    $clock_in = strtotime($last_clock_in_time);
    $clock_out = strtotime($last_clock_out_time);
    $emp_worked_hours = ($clock_out - $clock_in) / 3600;
    // echo "<br>Working Hours: $emp_worked_hours <br>"; // for debugging

    // Working Time > $WorkingDayHours && Working Time < 15 Hours 
    if ($emp_worked_hours > $WorkingDayHours && $emp_worked_hours < $WorkingDayMaxHours) {
        $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$DaySalary', 'Day Salary - $today')";
        // echo $sql . "<br>"; // for debugging
        // echo "pay_amount Rs. $DaySalary"; // for debugging
        // echo $sql; // for debugging
        insert_query($sql, "Employee Name : $employee_name , Salary : $DaySalary , Day : $today", "Add Day Salary"); // for production
    } elseif ($emp_worked_hours < $WorkingDayHours) {
        // echo 'Error: Working Hours Less Than 8 Hours'; // for debugging
        $pay_amount = $emp_worked_hours * $HourSalary;
        $pay_amount = number_format($pay_amount, 2);
        
        // convert $emp_worked_hours to 2 decimal places
        $emp_worked_hours = number_format($emp_worked_hours, 2);

        $sql = "INSERT INTO salary (emp_id, date, amount, description) VALUES ('$employee_id', '$today', '$pay_amount', 'Day Salary - $today ($emp_worked_hours Hours)')";
        // echo $sql . "<br>"; // for debugging
        insert_query($sql, "Employee Name : $employee_name , Salary : $pay_amount , Day : $today, Worked Hours : $emp_worked_hours", "Add Day Salary"); // for production
        // echo "Pay Amount: $pay_amount <br>"; // for debugging
    } elseif ($emp_worked_hours > $WorkingDayMaxHours) {
        echo "Error: Working Hours More Than $WorkingDayMaxHours Hours";  // for debugging
    } else {
        echo 'Error: Unknown Error'; // for debugging
    }