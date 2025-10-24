<?php
include '../nav.php';

// total salary to pay
$total_salary_have_pay = number_format(mysqli_fetch_assoc(mysqli_query($con, "SELECT SUM(salary) as total_salary FROM employees WHERE status = 1"))['total_salary'], 2);
?>
<!DOCTYPE html>
<html class="dark" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM - Admin Panel - Srijaya Printers</title>

    <style>
        input {
            margin-top: 0 !important;
        }
    </style>
</head>

<body>
    <h1>Human Resource Management</h1>
    <h2 style="color:white">Total Salary to Pay : Rs. <?= $total_salary_have_pay ?></h2>


    <button onclick="addNewEmployee()" id="refreshPage">Add New Employee</button>
    <button onclick="clockOutAllEmployees()" style="background-color: #ff6b6b; margin-left: 10px;">Clock Out All Employees</button>

    <table id="DataTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Mobile</th>
                <th>Address</th>
                <th>Bank Account</th>
                <th>Role</th>
                <th>NIC</th>
                <th>Now Working?</th>
                <th>to Pay Salary</th>
                <th>Day Salary</th>
                <!-- <th>Password</th> -->
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Retrieve all the employees from the database and display them in a DataTable

            // employees(employ_id,emp_name,mobile,address,bank_account,role,nic,salary,password,status)
            $sql = "SELECT * FROM employees";
            $result = mysqli_query($con, $sql);
            $resultCheck = mysqli_num_rows($result);

            if ($resultCheck > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $employee_id = $row['employ_id'];
                    $emp_name = $row['emp_name'];
                    $mobile = $row['mobile'];
                    $address = $row['address'];
                    $bank_account = $row['bank_account'];
                    $role = $row['role'];
                    $nic = $row['nic'];
                    $salary = $row['salary'];
                    $day_salary = $row['day_salary'];
                    $is_clocked_in = $row['is_clocked_in'];
                    // $password = $row['password'];
                    $status = $row['status'];

                    $action = "";

                    if ($status == 1) {
                        if ($is_clocked_in == 1) {
                            $is_clocked_in = "Working";
                            $action .= "<button onclick='updateAttendance($employee_id, `$emp_name`, `Clock Out`)'>Clock Out</button><br>";
                        } else {
                            $is_clocked_in = "Off";
                            $action .= "<button onclick='updateAttendance($employee_id, `$emp_name`, `Clock In`)'>Clock In</button><br>";
                        }
                        $status = "Active";
                        $action .= "<button onclick='paySalary($employee_id, `$emp_name`, `$bank_account`, `$salary`)'>Pay Salary</button><br>"
                            . "<button onclick='editEmployee($employee_id, `$emp_name`, `$mobile`, `$address`, `$bank_account`, `$role`, `$nic`, `$salary`, `$day_salary`)'>Edit</button><br>"
                            . "<a href='/AdminPanel/hrm/viewPayrolls.php?employee_id=$employee_id'> <button>View Payroll</button> </a><br>"
                            . "<button onclick='terminate($employee_id, `$emp_name`)'>Terminate</button><br>";
                    } else {
                        $status = "Terminated";
                        $action .= "<button onclick='hire($employee_id, `$emp_name`)'>Hire Again</button><br>";
                    }


                    echo "<tr>";
                    echo "<td>$employee_id</td>";
                    echo "<td>$emp_name</td>";
                    echo "<td>$mobile</td>";
                    echo "<td>$address</td>";
                    echo "<td>$bank_account</td>";
                    echo "<td>$role</td>";
                    echo "<td>$nic</td>";
                    echo "<td>$is_clocked_in</td>";
                    echo "<td>$salary</td>";
                    echo "<td>$day_salary</td>";
                    // echo "<td>$password</td>";
                    echo "<td>$status</td>";
                    echo "<td> $action </td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</body>
<script>
    function addNewEmployee() {
        Swal.fire({
            title: 'Add New Employee',
            html: `<label for='emp_name'>Name :</label> 
                    <input type='text' id='emp_name' placeholder='Name' class='swal2-input'>
                    <label for='mobile'>Mobile :</label> 
                    <input type='text' id='mobile' placeholder='Mobile' class='swal2-input'>
                    <label for='address'>Address :</label> 
                    <input type='text' id='address' placeholder='Address' class='swal2-input'>
                    <label for='bank_account'>Bank Account Number :</label> 
                    <input type='text' id='bank_account' placeholder='Bank Account Number' class='swal2-input'>
                    <label for='role'>Role :</label> 
                    <input type='text' id='role' placeholder='Role' class='swal2-input' value='Employee'>
                    <!-- <label for='salary'>Salary :</label> -->
                    <input type='text' id='salary' placeholder='Salary' class='swal2-input' value='0' hidden>
                    <label for='nic'>NIC :</label> 
                    <input type='text' id='nic' placeholder='NIC' class='swal2-input'>
                    <label for='password'>Password :</label>
                    <input type='password' id='password' placeholder='Password' class='swal2-input'>
                    <label for='day_salary'>Day Salary Amount :</label> 
                    <input type='text' id='day_salary' placeholder='Day Salary' class='swal2-input'>`,

            showCancelButton: false,
            confirmButtonText: 'Add',
            showLoaderOnConfirm: false,
            focusConfirm: false,
            preConfirm: (login) => {
                // Add the new Employee to the database
                if (isNaN($('#mobile').val())) {
                    Swal.showValidationMessage("Mobile number should be numbers");
                } else if (isNaN($('#day_salary').val())) {
                    Swal.showValidationMessage("Day Salary should be numbers");
                } else if ($('#emp_name').val() == "" || $('#mobile').val() == "" || $('#address').val() == "" || $('#bank_account').val() == "" || $('#role').val() == "" || $('#nic').val() == "" || $('#password').val() == "" || $('#day_salary').val() == "") {
                    Swal.showValidationMessage("Please fill all the fields");
                } else {
                    $.ajax({
                        type: "POST",
                        url: '/AdminPanel/hrm/addNewEmployee.php',
                        data: {
                            emp_name: $('#emp_name').val(),
                            mobile: $('#mobile').val(),
                            address: $('#address').val(),
                            bank_account: $('#bank_account').val(),
                            role: $('#role').val(),
                            nic: $('#nic').val(),
                            password: $('#password').val(),
                            salary: $('#salary').val(),
                            day_salary: $('#day_salary').val()
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Employee Added successfully',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 2000); // 3-second delay before reloading
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error Adding Employee',
                                icon: 'error'
                            });
                            console.log(data);
                        }
                    });
                }
            }
        });
    }

    // Edit Employee
    function editEmployee(emp_id, emp_name, mobile, address, bank_account, role, nic, salary, day_salary) {
        Swal.fire({
            title: 'Edit Employee',
            html: `<label for='emp_name'>Name :</label> 
                    <input type='text' id='emp_name' placeholder='Name' class='swal2-input' value='${emp_name}'>
                    <label for='mobile'>Mobile :</label> 
                    <input type='text' id='mobile' placeholder='Mobile' class='swal2-input' value='${mobile}'>
                    <label for='address'>Address :</label> 
                    <input type='text' id='address' placeholder='Address' class='swal2-input' value='${address}'>
                    <label for='bank_account'>Bank Account Number :</label> 
                    <input type='text' id='bank_account' placeholder='Bank Account Number' class='swal2-input' value='${bank_account}'>
                    <label for='role'>Role :</label> 
                    <input type='text' id='role' placeholder='Role' class='swal2-input' value='${role}'>
                    <label for='salary'>To pay Salary :</label>
                    <input type='text' id='salary' placeholder='Salary' class='swal2-input' value='${salary}'>
                    <label for='nic'>NIC :</label> 
                    <input type='text' id='nic' placeholder='NIC' class='swal2-input' value='${nic}'>
                    <label for='day_salary'>Day Salary Amount :</label> 
                    <input type='text' id='day_salary' placeholder='Day Salary' class='swal2-input' value='${day_salary}'>`,

            showCancelButton: false,
            confirmButtonText: 'Update',
            showLoaderOnConfirm: false,
            focusConfirm: false,
            preConfirm: (login) => {
                // Update the Employee in the database
                if (isNaN($('#mobile').val())) {
                    Swal.showValidationMessage("Mobile number should be numbers");
                } else if (isNaN($('#day_salary').val())) {
                    Swal.showValidationMessage("Day Salary should be numbers");
                } else if ($('#emp_name').val() == "" || $('#mobile').val() == "" || $('#address').val() == "" || $('#bank_account').val() == "" || $('#role').val() == "" || $('#nic').val() == "" || $('#day_salary').val() == "") {
                    Swal.showValidationMessage("Please fill all the fields");
                } else {
                    $.ajax({
                        type: "POST",
                        url: '/AdminPanel/hrm/editEmployee.php',
                        data: {
                            emp_id: emp_id,
                            emp_name: $('#emp_name').val(),
                            mobile: $('#mobile').val(),
                            address: $('#address').val(),
                            bank_account: $('#bank_account').val(),
                            role: $('#role').val(),
                            nic: $('#nic').val(),
                            salary: $('#salary').val(),
                            day_salary: $('#day_salary').val()
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Employee Updated successfully',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 2000); // 3-second delay before reloading
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error Updating Employee',
                                icon: 'error'
                            });
                            console.log(data);
                        }
                    });
                }
            }
        });
    }


    // Update Attendance
    function updateAttendance(emp_id, emp_name, action) {
        Swal.fire({
            title: 'Update Attendance',
            text: `Are you sure you want to ${action} ${emp_name}?`,
            showCancelButton: true,
            confirmButtonText: 'Yes',
            showLoaderOnConfirm: true,
            focusConfirm: false,
            preConfirm: (login) => {
                // Update the Attendance in the database
                $.ajax({
                    type: "GET",
                    url: '/AdminPanel/hrm/updateClockInOut.php',
                    data: {
                        employee_id: emp_id,
                        employee_name: emp_name,
                        action: action
                    },
                    success: function(data) {
                        Swal.fire({
                            title: 'Attendance Updated successfully',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 2000); // 3-second delay before reloading
                        console.log(data);
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error Updating Attendance',
                            icon: 'error'
                        });
                        console.log(data);
                    }
                });
            }
        });
    }

    // Terminate Employee
    function terminate(emp_id, emp_name) {
        Swal.fire({
            title: 'Terminate Employee',
            text: `Are you sure you want to Terminate ${emp_name}?`,
            showCancelButton: true,
            confirmButtonText: 'Yes',
            showLoaderOnConfirm: true,
            focusConfirm: false,
            preConfirm: (login) => {
                // Update the Attendance in the database
                $.ajax({
                    type: "GET",
                    url: '/AdminPanel/hrm/terminateEmployee.php',
                    data: {
                        employee_id: emp_id,
                    },
                    success: function(data) {
                        Swal.fire({
                            title: 'Employee Terminated successfully',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 2000); // 3-second delay before reloading
                        console.log(data);
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error Terminating Employee',
                            icon: 'error'
                        });
                        console.log(data);
                    }
                });
            }
        });
    }

    // Hire Employee
    function hire(emp_id, emp_name) {
        Swal.fire({
            title: 'Hire Employee',
            text: `Are you sure you want to Hire ${emp_name} again?`,
            showCancelButton: true,
            confirmButtonText: 'Yes',
            showLoaderOnConfirm: true,
            focusConfirm: false,
            preConfirm: (login) => {
                // Update the Attendance in the database
                $.ajax({
                    type: "GET",
                    url: '/AdminPanel/hrm/hireEmployee.php',
                    data: {
                        employee_id: emp_id,
                    },
                    success: function(data) {
                        Swal.fire({
                            title: 'Employee Hired successfully',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => {
                            location.reload();
                        }, 10); // 3-second delay before reloading
                        console.log(data);
                    },
                    error: function(data) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error Hiring Employee',
                            icon: 'error'
                        });
                        console.log(data);
                    }
                });
            }
        });
    }

    // Pay Salary
    function paySalary(employee_id, emp_name, bank_account, salary) {
        Swal.fire({
            title: 'Pay Salary',
            html: `
            <label>Employee Name : <b>${emp_name} </b></label> <br> <br>
            <label>Bank Account : <b>${bank_account} </b></label> <br> <br>
            <label>To pay Salary : <b>${salary} </b></label> <br> <br>
            <label for='amount'> Pay Amount :</label> 
                    <input type='text' id='amount' placeholder='Amount' class='swal2-input'>`,

            showCancelButton: false,
            confirmButtonText: 'Pay',
            showLoaderOnConfirm: true,
            focusConfirm: false,
            preConfirm: (login) => {
                // Pay the Salary to the Employee
                if (isNaN($('#amount').val())) {
                    Swal.showValidationMessage("Amount should be numbers");
                } else if ($('#amount').val() == "") {
                    Swal.showValidationMessage("Please fill the amount field");
                } else {
                    $.ajax({
                        type: "POST",
                        url: '/AdminPanel/hrm/paySalary.php',
                        data: {
                            employee_id: employee_id,
                            amount: $('#amount').val()
                        },
                        success: function(data) {
                            Swal.fire({
                                title: 'Salary Paid successfully',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1000); // 3-second delay before reloading
                            console.log(data);
                        },
                        error: function(data) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error Paying Salary',
                                icon: 'error'
                            });
                            console.log(data);
                        }
                    });
                }
            }
        });

    }

    // Clock Out All Employees
    function clockOutAllEmployees() {
        Swal.fire({
            title: 'Clock Out All Employees',
            text: 'Are you sure you want to clock out all currently working employees? This will calculate and pay their salaries.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Clock Out All',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#ff6b6b',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    type: "POST",
                    url: '/api/v1/attendance/clockout_all.php',
                    headers: {
                        'Authorization': 'Bearer <?php echo $_SESSION['api_token'] ?? ''; ?>',
                        'Content-Type': 'application/json'
                    },
                    dataType: 'json'
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.error?.message || 'Failed to clock out employees');
                    }
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(
                        `Request failed: ${error.message || error.responseJSON?.error?.message || 'Unknown error'}`
                    );
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                const data = result.value.data;
                const employeesCount = data.employees_clocked_out || 0;
                
                let detailsHtml = `<p>Successfully clocked out <b>${employeesCount}</b> employee(s)</p>`;
                
                if (data.processed_employees && data.processed_employees.length > 0) {
                    detailsHtml += '<div style="max-height: 300px; overflow-y: auto; text-align: left; margin-top: 15px;">';
                    detailsHtml += '<table style="width: 100%; border-collapse: collapse;">';
                    detailsHtml += '<thead><tr style="background: #f0f0f0;"><th style="padding: 8px; border: 1px solid #ddd;">Employee</th><th style="padding: 8px; border: 1px solid #ddd;">Hours</th><th style="padding: 8px; border: 1px solid #ddd;">Salary Paid</th></tr></thead>';
                    detailsHtml += '<tbody>';
                    
                    data.processed_employees.forEach(emp => {
                        detailsHtml += `<tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">${emp.employee_name}</td>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">${emp.worked_hours}</td>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: right;">Rs. ${emp.salary_paid}</td>
                        </tr>`;
                    });
                    
                    detailsHtml += '</tbody></table></div>';
                }
                
                if (data.errors && data.errors.length > 0) {
                    detailsHtml += '<div style="margin-top: 15px; color: #ff6b6b;"><p><b>Errors occurred for some employees:</b></p><ul style="text-align: left;">';
                    data.errors.forEach(err => {
                        detailsHtml += `<li>${err.employee_name}: ${err.error}</li>`;
                    });
                    detailsHtml += '</ul></div>';
                }

                Swal.fire({
                    title: 'Success!',
                    html: detailsHtml,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
</script>

</html>