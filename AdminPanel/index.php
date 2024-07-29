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
            <h2>
                <?php
                $sql = "SELECT count(*) from invoice where invoice_date = CURDATE();";
                $invoice_count = mysqli_fetch_assoc(mysqli_query($con, $sql))['count(*)'];
                echo $invoice_count;
                ?>
            </h2>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-solid fa-cubes"></i>
        <div class="info">
            <h3>Stock Remain</h3>
            <h2> <?php
                    // Get each Item Full Cost (Cost x qty)
                    $item_capital = array();
                    $sql = "SELECT * FROM items";
                    $result = mysqli_query($con, $sql);
                    if ($result) {
                        // qury success
                        if (mysqli_num_rows($result) > 0) {
                            while ($item_capital_sql = mysqli_fetch_array($result)) {
                                $item_cost = $item_capital_sql['cost'];
                                $item_qty = $item_capital_sql['qty'];
                                $item_fullcost = $item_cost * $item_qty;
                                // echo $item_fullcost;
                                array_push($item_capital, $item_fullcost);
                            }
                        } else {
                            echo "No any Item";
                        }
                    } else {
                        echo "Database Query Failed";
                    }

                    $capital_currency = array_sum($item_capital);
                    echo number_format($capital_currency, 2);  ?></h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-cash-register"></i>
        <div class="info">
            <h3>Today Cash in</h3>
            <h2>
                <?php
                $sql = "SELECT sum(advance) as SUM from invoice where invoice_date = CURDATE();";
                $cash_in_today = mysqli_fetch_assoc(mysqli_query($con, $sql))['SUM'];
                echo number_format($cash_in_today, 2);
                ?>
            </h2>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-wallet"></i>
        <div class="info">
            <h3>Today Profit</h3>
            <h2> <?php
                    $sql = "SELECT sum(profit) as SUM from invoice where invoice_date = CURDATE();";
                    $cash_in_today = mysqli_fetch_assoc(mysqli_query($con, $sql))['SUM'];
                    echo number_format($cash_in_today, 2);
                    ?></h2>
        </div>
    </div>
    <div class="card">
        <i class="fas fa-right-to-bracket"></i>
        <div class="info">
            <h3>Today Cash Out</h3>
            <h2 style="font-size:1.5rem;">Bank Deposit + Salary + Petty cash + Purchase </h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-hand-holding-dollar"></i>
        <div class="info">
            <h3>Cash In Hand</h3>
            <h2><?php
                $sql = "SELECT amount FROM accounts WHERE account_name = 'cash_in_hand'";
                $result = mysqli_query($con, $sql);
                if ($result) {
                    $cash_in_hand_amount = mysqli_fetch_array($result);
                    $cash_in_hand_amountRS = $cash_in_hand_amount['amount'];
                    echo number_format($cash_in_hand_amountRS, 2);
                } else {
                    echo "ERROR";
                }
                ?></h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-credit-card"></i>
        <br>
        <i class="fa-solid fa-lightbulb"></i>
        <div class="info">
            <h3>Due Payments</h3>
            <h2 style="font-size:1.5rem;">Credit Bill + Salary + Utility Payments</h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <div class="info">
            <h3>Pending Payments</h3>
            <h2><?php
                $sql = "SELECT sum(balance) from invoice where balance >= 0;";
                $payment_pending_total = mysqli_fetch_assoc(mysqli_query($con, $sql))['sum(balance)'];
                echo number_format($payment_pending_total, 2);
                ?></h2>
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