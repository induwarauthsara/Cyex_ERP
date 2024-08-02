<?php
include 'nav.php';
?>
<title>Admin Panel</title>
<h1>Dashboard</h1>

<div id="errors">
    <!-- Database Erros -->
    <?php
    $sql = "SELECT COUNT(*) FROM `error_log` WHERE `status` = 'pending';";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $error_count = mysqli_fetch_array($result);
        $error_count = $error_count['COUNT(*)'];
        if ($error_count > 0) {
            echo "<p><b> You have <a href='erros.php'> $error_count  Critical Unsolved Errors.</a> Immediate action is required to prevent potential system failure. Solve them NOW! For any help, please contact the developer. </b></p>";
        }
    } else {
        echo "Database Query Failed";
    }
    ?>
    <p><b>Admin Panel is Currently Under Construction. Please Come Back Soon.!</b></p>

    <!-- One-Time-Products -->
    <?php
    $sql = "SELECT COUNT(*) FROM `oneTimeProducts_sales` WHERE `status` = 'uncleared';";
    $result = mysqli_query($con, $sql);
    if ($result) {
        $uncleared_oneTimeProducts_count = mysqli_fetch_array($result);
        $uncleared_oneTimeProducts_count = $uncleared_oneTimeProducts_count['COUNT(*)'];
        if ($uncleared_oneTimeProducts_count > 0) {
            echo "<p><b> You have  $uncleared_oneTimeProducts_count  Uncleared One-Time-Products. <a href='one_time_products.php'>Solve them NOW !</a>  </b></p>";
        }
    } else {
        echo "Database Query Failed";
    }
    ?>
</div>

<div id="cards">
    <div class="card">
        <i class="fas fa-file-invoice"></i>
        <div class="info">
            <h3>Today Invoice Count </h3>
            <h2>
                <?php
                $sql = "SELECT count(*) from invoice where invoice_date = CURDATE();";
                $invoice_count = mysqli_fetch_assoc(mysqli_query($con, $sql))['count(*)'] ?? 0;
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
                                $item_cost = $item_capital_sql['cost'] ?? 0;
                                $item_qty = $item_capital_sql['qty'] ?? 0;
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
                $cash_in_today = mysqli_fetch_assoc(mysqli_query($con, $sql))['SUM'] ?? 0;
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
                    $today_profit = mysqli_fetch_assoc(mysqli_query($con, $sql))['SUM'] ?? 0;
                    echo number_format($today_profit, 2);
                    ?></h2>
        </div>
    </div>
    <a href="/AdminPanel/hrm/index.php">
        <div class="card">
            <i class="fas fa-user-friends"></i>
            <div class="info">
                <h3>Currently Working Employees</h3>
                <h2> <?php
                        $sql = "SELECT COUNT(`is_clocked_in`) FROM `employees` WHERE `is_clocked_in` = '1';";
                        $currently_working_employees = mysqli_fetch_assoc(mysqli_query($con, $sql))['COUNT(`is_clocked_in`)'] ?? 0;
                        echo $currently_working_employees;
                        ?></h2>
            </div>
        </div>
    </a>
    <div class="card">
        <i class="fas fa-right-to-bracket"></i>
        <div class="info">
            <h3>Today Cash Out</h3>
            <h4>(Salary + Petty cash + Purchase)</h4>
            <h2> </h2>
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
                    $cash_in_hand_amountRS = $cash_in_hand_amount['amount'] ?? 0;
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
            <h4>(Credit Bill + Salary + Utility Payments)</h4>
            <h2></h2>
        </div>
    </div>
    <div class="card">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <div class="info">
            <h3>Pending Payments</h3>
            <h2><?php
                $sql = "SELECT sum(balance) from invoice where balance >= 0;";
                $payment_pending_total = mysqli_fetch_assoc(mysqli_query($con, $sql))['sum(balance)'] ?? 0;
                echo number_format($payment_pending_total, 2);
                ?></h2>
        </div>
    </div>
    <a href="bank/">
        <div class="card">
            <i class="fa-solid fa-piggy-bank"></i>
            <div class="info">
                <h3>Today Bank Deposit</h3>
                <h2><?php
                    $sql = "SELECT SUM(amount) FROM `bank_deposits` WHERE deposit_date = CURDATE();";
                    $today_bank_deposit = mysqli_fetch_assoc(mysqli_query($con, $sql))['SUM(amount)'] ?? 0;
                    echo number_format($today_bank_deposit, 2);
                    ?></h2>
            </div>
        </div>
    </a>
    <a href="bank/">
        <div class="card">
            <i class="fa-solid fa-university"></i>
            <div class="info">
                <h3>Total Bank Balance</h3>
                <h2><?php
                    $sql = "SELECT sum(amount) FROM `accounts` WHERE account_type = 'bank';";
                    $total_bank_balance = mysqli_fetch_assoc(mysqli_query($con, $sql))['sum(amount)'] ?? 0;
                    echo number_format($total_bank_balance, 2);
                    ?></h2>
            </div>
        </div>
    </a>
    <div class="card">
        <i class="fa-solid fa-chart-line"></i>
        <div class="info">
            <h3>Summery / Risk</h3>
            <h4>(Bank Balance - Due Payments)</h4>
            <h2></h2>
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