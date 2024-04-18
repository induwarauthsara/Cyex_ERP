<?php require_once '../../inc/config.php'; ?>
<?php require '../../inc/header.php';
include '../../inc/DataTable_cdn.php'
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
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        <div class="btn-container">
            <button class="btn btn-clock-in" onclick="markAttendance('Clock In')">Clock In</button>
            <button class="btn btn-clock-out" onclick="markAttendance('Clock Out')">Clock Out</button>
        </div>
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
                            refreshEntranceTable();

                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred. Please try again later.',
                                icon: 'error'
                            });
                            refreshEntranceTable();
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
    </tbody>
</table>
<script>
    function refreshEntranceTable() {
        fetch("attendance-table.php") // Replace with your server-side script to fetch updated todo list
            .then(response => response.text())
            .then(data => {
                document.querySelector('tbody').innerHTML = data;
            })
            .catch(error => {
                console.error('Error:', error);
                // Handle error if necessary
            });
    }
    window.onload = function() {
        refreshEntranceTable();
    }
</script>

<style>
    #DataTable_wrapper {
        width: 60%;
        margin: auto;
    }
</style>