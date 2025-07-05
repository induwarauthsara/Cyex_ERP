<?php
include 'conn.php';


// Get the selected view from GET parameter, default to 'bank'
$view = isset($_GET['view']) ? $_GET['view'] : 'counts';

// Function to get bank deposit data
function getBankDeposits($con, $period)
{
    if ($period === 'daily') {
        $sql = "SELECT DATE(date) as date, SUM(amount) as total 
                FROM printer_counter_bank_deposit 
                WHERE date >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)
                GROUP BY DATE(date)
                ORDER BY date";
    } else {
        $sql = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total 
                FROM printer_counter_bank_deposit 
                WHERE date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(date, '%Y-%m')
                ORDER BY month";
    }
    $result = $con->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get printer data with counts and totals
function getPrinterData($con, $period)
{
    if ($period === 'daily') {
        $sql = "SELECT 
                    p.printerName,
                    t.typeName,
                    DATE(c.date) as date,
                    SUM(c.count) as count,
                    SUM(c.count * t.cost) as cost
                FROM printer_counter_count c
                JOIN printer_counter_types t ON c.typeID = t.typeID
                JOIN printer_counter_printers p ON t.printerID = p.printerID
                WHERE c.date >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)
                GROUP BY p.printerID, t.typeID, DATE(c.date)
                ORDER BY date, p.printerName, t.typeName";
    } else {
        $sql = "SELECT 
                    p.printerName,
                    t.typeName,
                    DATE_FORMAT(c.date, '%Y-%m') as date,
                    SUM(c.count) as count,
                    SUM(c.count * t.cost) as cost
                FROM printer_counter_count c
                JOIN printer_counter_types t ON c.typeID = t.typeID
                JOIN printer_counter_printers p ON t.printerID = p.printerID
                WHERE c.date >= DATE_SUB(CURRENT_DATE, INTERVAL 12 MONTH)
                GROUP BY p.printerID, t.typeID, DATE_FORMAT(c.date, '%Y-%m')
                ORDER BY date, p.printerName, t.typeName";
    }
    $result = $con->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch data based on selected view
$data = [];
switch ($view) {
    case 'bank':
        $data['dailyDeposits'] = getBankDeposits($con, 'daily');
        $data['monthlyDeposits'] = getBankDeposits($con, 'monthly');
        break;
    case 'counts':
        $data['dailyPrinterData'] = getPrinterData($con, 'daily');
        $data['monthlyPrinterData'] = getPrinterData($con, 'monthly');
        break;
    case 'cost':
        $data['dailyPrinterData'] = getPrinterData($con, 'daily');
        $data['monthlyPrinterData'] = getPrinterData($con, 'monthly');
        break;
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printer Analytics Dashboard</title>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            max-width: 1200px;
            padding: 20px;
            margin-left: 150px;
        }

        .chart-wrapper {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 40px;
        }

        .navigation {
            text-align: center;
            margin-bottom: 30px;
        }

        .nav-button {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 10px;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .nav-button:hover {
            background-color: #1976D2;
        }

        .nav-button.active {
            background-color: #1565C0;
        }
    </style>
</head>

<body>
    <div class="chart-container">
        <div class="navigation">
            <form method="get" style="display: inline-block;">
                <button type="submit" name="view" value="bank"
                    class="nav-button <?php echo $view === 'bank' ? 'active' : ''; ?>">
                    Bank Deposits
                </button>
                <button type="submit" name="view" value="counts"
                    class="nav-button <?php echo $view === 'counts' ? 'active' : ''; ?>">
                    Printer Counts
                </button>
                <button type="submit" name="view" value="cost"
                    class="nav-button <?php echo $view === 'cost' ? 'active' : ''; ?>">
                    Cost (Rent)
                </button>
            </form>
        </div>

        <?php if ($view === 'bank'): ?>
            <section class="section">
                <h2>Bank Deposits</h2>
                <div class="chart-wrapper">
                    <canvas id="dailyDepositsChart"></canvas>
                </div>
                <div class="chart-wrapper">
                    <canvas id="monthlyDepositsChart"></canvas>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($view === 'counts'): ?>
            <section class="section">
                <h2>Printer Counts</h2>
                <div class="chart-wrapper">
                    <canvas id="dailyCountsChart"></canvas>
                </div>
                <div class="chart-wrapper">
                    <canvas id="monthlyCountsChart"></canvas>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($view === 'cost'): ?>
            <section class="section">
                <h2>Printer Costs</h2>
                <div class="chart-wrapper">
                    <canvas id="dailyCostsChart"></canvas>
                </div>
                <div class="chart-wrapper">
                    <canvas id="monthlyCostsChart"></canvas>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script>
        // Initialize chart data
        const chartData = <?php echo json_encode($data); ?>;
        const currentView = '<?php echo $view; ?>';

        // Helper function to generate colors
        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                colors.push(`hsl(${(i * 360) / count}, 70%, 50%)`);
            }
            return colors;
        }

        // Process printer data for charts
        function processPrinterData(data) {
            const dates = [...new Set(data.map(d => d.date))];
            const printers = [...new Set(data.map(d => d.printerName))];
            const processedData = {};

            printers.forEach(printer => {
                processedData[printer] = {
                    types: {},
                    total: dates.map(date => ({
                        date,
                        count: 0,
                        cost: 0
                    }))
                };

                const printerData = data.filter(d => d.printerName === printer);
                const types = [...new Set(printerData.map(d => d.typeName))];

                types.forEach(type => {
                    processedData[printer].types[type] = dates.map(date => {
                        const match = printerData.find(d => d.date === date && d.typeName === type);
                        const count = match ? parseInt(match.count) : 0;
                        const cost = match ? parseInt(match.cost) : 0;
                        processedData[printer].total[dates.indexOf(date)].count += count;
                        processedData[printer].total[dates.indexOf(date)].cost += cost;
                        return {
                            date,
                            count,
                            cost
                        };
                    });
                });
            });

            return {
                dates,
                printers,
                data: processedData
            };
        }

        // Create charts based on current view
        if (currentView === 'bank') {
            // Create Bank Deposit Charts
            new Chart(document.getElementById('dailyDepositsChart'), {
                type: 'bar',
                data: {
                    labels: chartData.dailyDeposits.map(d => d.date),
                    datasets: [{
                        label: 'Daily Deposits',
                        data: chartData.dailyDeposits.map(d => d.total),
                        backgroundColor: '#2196F3'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Daily Bank Deposits (Last 90 Days)'
                        },
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'xy',
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: true
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
            });

            new Chart(document.getElementById('monthlyDepositsChart'), {
                type: 'bar',
                data: {
                    labels: chartData.monthlyDeposits.map(d => d.month),
                    datasets: [{
                        label: 'Monthly Deposits',
                        data: chartData.monthlyDeposits.map(d => d.total),
                        backgroundColor: '#4CAF50'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Bank Deposits (Last 12 Months)'
                        },
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'xy',
                            }
                        }
                    }
                }
            });
        } else {
            // Process printer data
            const dailyPrinterStats = processPrinterData(chartData.dailyPrinterData);
            const monthlyPrinterStats = processPrinterData(chartData.monthlyPrinterData);

            function createComboChart(canvasId, data, title, valueType) {
                const datasets = [];
                const colors = generateColors(data.printers.length * 3);
                let colorIndex = 0;

                data.printers.forEach((printer, index) => {
                    // Add stacked bars for each type
                    Object.entries(data.data[printer].types).forEach(([type, typeData]) => {
                        datasets.push({
                            type: 'bar',
                            label: `${printer} - ${type}`,
                            data: typeData.map(d => d[valueType]),
                            backgroundColor: colors[colorIndex++],
                            stack: `stack${index}`
                        });
                    });

                    // Add line for total
                    datasets.push({
                        type: 'line',
                        label: `${printer} - Total`,
                        data: data.data[printer].total.map(d => d[valueType]),
                        borderColor: colors[colorIndex++],
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    });
                });

                return new Chart(document.getElementById(canvasId), {
                    type: 'bar',
                    data: {
                        labels: data.dates,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: title
                            },
                            zoom: {
                                pan: {
                                    enabled: true,
                                    mode: 'x'
                                },
                                zoom: {
                                    wheel: {
                                        enabled: true
                                    },
                                    pinch: {
                                        enabled: true
                                    },
                                    mode: 'x'
                                }
                            }
                        },
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true
                            }
                        },
                        transitions: {
                            zoom: {
                                animation: {
                                    duration: 1000,
                                    easing: 'easeOutCubic'
                                }
                            }
                        }
                    }
                });
            }

            if (currentView === 'counts') {
                // Create count charts
                createComboChart(
                    'dailyCountsChart',
                    dailyPrinterStats,
                    'Daily Printer Counts (Last 90 Days)',
                    'count'
                );

                createComboChart(
                    'monthlyCountsChart',
                    monthlyPrinterStats,
                    'Monthly Printer Counts (Last 12 Months)',
                    'count'
                );
            } else if (currentView === 'cost') {
                // Create cost charts
                createComboChart(
                    'dailyCostsChart',
                    dailyPrinterStats,
                    'Daily Printer Costs (Last 90 Days)',
                    'cost'
                );

                createComboChart(
                    'monthlyCostsChart',
                    monthlyPrinterStats,
                    'Monthly Printer Costs (Last 12 Months)',
                    'cost'
                );
            }
        }
    </script>
</body>

</html>