<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ERP Dashboard</title>
    <!-- Include Moment.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- include ChartJS.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Date range selection interface -->
    <label for="startDate">Start Date:</label>
    <input type="date" id="startDate">
    <label for="endDate">End Date:</label>
    <input type="date" id="endDate">
    <button id="applyButton">Apply</button>

    <!-- Display dashboard content here -->
    <div id="dashboardContent">
        <!-- Dashboard content will be updated dynamically -->
    </div>

    <script>
        // Function to handle date range selection
        function handleDateRangeSelection() {
            // Get selected start and end dates
            var startDate = moment(document.getElementById('startDate').value, 'YYYY-MM-DD');
            var endDate = moment(document.getElementById('endDate').value, 'YYYY-MM-DD');

            // Output the selected date range (for demonstration)
            console.log("Selected date range:", startDate.format('YYYY-MM-DD'), "to", endDate.format('YYYY-MM-DD'));

            // Now, you can perform data querying/filtering and update the dashboard content accordingly
            // For simplicity, let's just update the dashboard content with the selected date range
            document.getElementById('dashboardContent').innerText = "Selected date range: " + startDate.format('YYYY-MM-DD') + " to " + endDate.format('YYYY-MM-DD');
        }

        // Event listener for custom date range selection
        document.getElementById('applyButton').addEventListener('click', handleDateRangeSelection);
    </script>

    <!-- Chart Script -->
    <canvas id="comboChart" width="400" height="200"></canvas>
    <script>
        var ctx = document.getElementById('comboChart').getContext('2d');
        var comboChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Bar Dataset',
                    data: [10, 20, 30, 40, 50, 60, 70],
                    backgroundColor: 'rgba(255, 99, 132, 0.5)'
                }, {
                    label: 'Line Dataset',
                    data: [5, 15, 25, 35, 45, 55, 65],
                    type: 'line',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>