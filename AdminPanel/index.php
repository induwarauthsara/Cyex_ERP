<?php
include 'nav.php';
?>
<title>Admin Panel</title>
<h1>Dashboard</h1>

<div id="errors">
    <p><b>Admin Panel is Currently Under Construction. Please Come Back Soon.!</b></p>
    <p>You have Uncleared 03 One-Time-Products. <a href="index.php">Solve them here</a></p>
</div>

<div id="cards">
    <div class="card">
        <i class="fas fa-file-invoice"></i>
        <div class="info">
            <h3>Today Invoice Count </h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-solid fa-cubes"></i>
        <div class="info">
            <h3>Stock Remain</h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-cash-register"></i>
        <div class="info">
            <h3>Today Cash in</h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-wallet"></i>
        <div class="info">
            <h3>Today Profit</h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-right-to-bracket"></i>
        <div class="info">
            <h3>Today Cash Out</h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-hand-holding-dollar"></i>
        <div class="info">
            <h3>Cash In Hand</h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-credit-card"></i>
        <br>
        <i class="fa-solid fa-lightbulb"></i>
        <div class="info">
            <h3>Due Payments</h3>
            <h2>100</h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <div class="info">
            <h3>Pending Payments</h3>
            <h2>100</h2>
        </div>
    </div>

</div>

<!-- 
I want to Create Charts for my Admin Panel Dashboard (Developed with HTML, CSS, JS, PHP, MySQL) It should get data from MySQL Database and display it in the form of Charts. 
Want following Charts:
 - Goods Sales Chart,
 - Stock Chart (Goods Purchased, Sold, Remaining), User Registration Chart, etc.
 - Profit Chart (Cash Inflow, Outflow, Net Profit), etc.
 - Employee Performance Chart (Sales, Attendance, etc.)
 - Printer Counter Chart
 - Utility Bills Chart
It should be responsive and should be able to display data for a specific time period (like Daily, Weekly, Monthly, 3 Months, 6 Moths, Yearly and Custom date range select).
It should be able to Customize Design, Colors, Labels, etc. (I love to Dark Mode)
What is the best way to create these Charts? Should I use Chart.js, apexcharts, Google Charts, Highcharts, D3.js, or any other library? I need less learning curve and easy to implement solution.

Answer: apexcharts
-->

<!-- Include ApexCharts -->

<!-- Create a Div for Chart -->
<!-- <div id="chart"></div> -->

<!-- Create a Script for Chart -->
<script>
    var options = {
        series: [{
            name: 'Goods Sold',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        title: {
            text: 'Goods Sales Chart',
            align: 'left'
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>