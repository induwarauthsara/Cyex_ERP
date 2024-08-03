<?php
include '../nav.php';
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

    <button onclick="addNewEmployee()">Add New Employee</button>

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
                    $emoloyee_id = $row['employ_id'];
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
                        $status = "Active";
                        $action .= "<button>Pay Salary</button><br>"
                        . "<button>Terminate</button><br>"
                        . "<button>Edit</button><br>";

                        if ($is_clocked_in == 1) {
                            $is_clocked_in = "Working";
                            $action .= "<button>Clock Out</button><br>";
                        } else {
                            $is_clocked_in = "Off";
                            $action .= "<button>Clock In</button><br>";
                        }
                    } else {
                        $status = "Terminated";
                        $action .= "<button>Hire</button><br>";
                    }
                   

                    echo "<tr>";
                    echo "<td>$emoloyee_id</td>";
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
</script>

</html>