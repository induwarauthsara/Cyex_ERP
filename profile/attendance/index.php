<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php';
include '../UCPnav.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Attendance System</title>
    <style>
        /* Basic CSS styles */
        body {
            font-family: Arial, sans-serif;
            /* background-color: #f4f4f4; */
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #202231;
            color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            padding: 20px;
            text-align: center;
        }

        .btn-container {
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            margin: 5px;
            cursor: pointer;
        }

        .btn-clock-in,
        .btn-clock-out {
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        .btn-clock-out {
            background-color: #f44336;
        }

        .current-time {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Employee Attendance System</h1>
        <div class="current-time" id="current-time"></div>
        <!-- <div class="btn-container"> -->
        <?php
        if (!isset($_SESSION['employee_id'])) {
            session_start();
            $employee_id = $_SESSION['employee_id'];
        } else {
            $employee_id = $_SESSION['employee_id'];
        }
        $sql = "SELECT is_clocked_in FROM employees WHERE employ_id = '$employee_id' AND `status` = '1'";
        $result = mysqli_query($con, $sql);
        if ($result) {
            // qury success
            if (mysqli_num_rows($result) == 1) {
                $employe = mysqli_fetch_assoc($result);
                if ($employe['is_clocked_in'] == 1) {
                    echo '<button class="btn btn-clock-out" onclick="markAttendance(\'Clock Out\')">Clock Out</button>';
                } else {
                    echo '<button class="btn btn-clock-in" onclick="markAttendance(\'Clock In\')">Clock In</button>';
                }
            } else {
                echo "No Result Found";
            }
        } else {
            echo "Database Query Failed";
        }
        ?>
        <!-- <button class="btn btn-clock-in" onclick="markAttendance('Clock In')">Clock In</button>
            <button class="btn btn-clock-out" onclick="markAttendance('Clock Out')">Clock Out</button> -->
        <!-- </div> -->
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


    <script>
        // Function to update the current time display
        function updateCurrentTime() {
            var now = new Date();
            var hours = now.getHours().toString().padStart(2, '0');
            var minutes = now.getMinutes().toString().padStart(2, '0');
            var seconds = now.getSeconds().toString().padStart(2, '0');
            document.getElementById('current-time').innerText = `${hours}:${minutes}:${seconds}`;
        }

        // Update the current time display every second
        setInterval(updateCurrentTime, 1000);

        // Function to mark attendance
        function markAttendance(action) {
            Swal.fire({
                title: 'Confirm',
                text: `Are you sure you want to ${action.toLowerCase()}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send data to server
                    return fetch("attendance-submit.php?action=" + action, {
                            method: 'GET',
                        })
                        .then(response => {
                            if (response.ok) {
                                return response.text();
                            }
                            throw new Error('Network response was not ok.');
                        })
                        .then(data => {
                            console.log(data);
                            Swal.fire({
                                title: 'Success!',
                                text: `You have successfully ${action.toLowerCase()}. Good ${getGreeting(action)}!`,
                                icon: 'success'
                            });
                            // Refresh Page
                            location.reload();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred. Please try again later.',
                                icon: 'error'
                            });
                        });
                }
            });
        }


        // Function to get greeting message based on the time of the day
        function getGreeting(action) {
            var now = new Date();
            var hour = now.getHours();
            if (action === 'Clock Out') {
                return 'bye';
            }
            if (hour < 12) {
                return 'morning';
            } else if (hour < 18) {
                return 'afternoon';
            } else {
                return 'evening';
            }
        }
    </script>
</body>

</html>

<table id="DataTable" class="display">
    <thead>
        <tr>
            <th>Employee Name</th>
            <th>Action</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT employees.emp_name, action, date, time 
        FROM attendance, employees
        WHERE attendance.employee_id = employees.employ_id
                AND employees.employ_id = '$employee_id'
        ORDER BY attendance.id DESC;";
        $result = mysqli_query($con, $sql);

        if ($result) {
            // Loop through the rows and generate HTML Table for each attendance record
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
            <td>" . $row['emp_name'] . "</td>
            <td>" . $row['action'] . "</td>
            <td>" . $row['date'] . "</td>
            <td>" . $row['time'] . "</td>";
            }
        }
        ?>
    </tbody>
</table>

<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#DataTable')) {
            $('#DataTable').DataTable();
        }
    });
</script>

<style>
    #DataTable_wrapper {
        width: 60%;
        margin: auto;
    }
</style>