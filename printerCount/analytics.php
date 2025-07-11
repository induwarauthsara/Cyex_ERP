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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .analytics-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
            font-weight: 400;
        }

        .navigation {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .nav-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav-button {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            border: none;
            padding: 15px 25px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .nav-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .nav-button.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .nav-button i {
            font-size: 18px;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            margin: 30px 0;
            padding: 30px;
            transition: all 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f3f4;
        }

        .chart-title {
            font-size: 1.8em;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-title i {
            color: #667eea;
            font-size: 0.9em;
        }

        .chart-controls {
            display: flex;
            gap: 10px;
        }

        .control-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .control-btn:hover {
            background: #e9ecef;
            border-color: #adb5bd;
        }

        .control-btn.active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .chart-wrapper {
            position: relative;
            height: 450px;
            margin: 25px 0;
            background: #fafbfc;
            border-radius: 15px;
            padding: 20px;
            border: 1px solid #f1f3f4;
        }

        .section {
            margin-bottom: 50px;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card.primary {
            border-left-color: #667eea;
        }

        .stat-card.success {
            border-left-color: #28a745;
        }

        .stat-card.warning {
            border-left-color: #ffc107;
        }

        .stat-card.danger {
            border-left-color: #dc3545;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #666;
            font-size: 1em;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2em;
            opacity: 0.2;
        }

        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            font-size: 1.2em;
            color: #666;
        }

        .loading-spinner i {
            animation: spin 1s linear infinite;
            margin-right: 10px;
            font-size: 1.5em;
            color: #667eea;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .no-data i {
            font-size: 4em;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .zoom-hint {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 10px;
            margin-top: 15px;
            font-size: 0.9em;
            color: #1565c0;
            text-align: center;
        }

        .zoom-hint i {
            margin-right: 5px;
        }

        @media (max-width: 768px) {
            .analytics-container {
                padding: 15px;
                max-width: 100%;
            }

            .header h1 {
                font-size: 2em;
            }

            .nav-tabs {
                flex-direction: column;
            }

            .nav-button {
                justify-content: center;
            }

            .chart-wrapper {
                height: 300px;
            }

            .chart-header {
                flex-direction: column;
                gap: 15px;
                align-items: stretch;
            }

            .chart-controls {
                justify-content: center;
            }

            .stats-overview {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .analytics-container {
                padding: 10px;
            }

            .header {
                padding: 20px;
                margin-bottom: 20px;
            }

            .header h1 {
                font-size: 1.8em;
            }

            .header p {
                font-size: 1em;
            }

            .chart-container {
                padding: 20px;
                margin: 20px 0;
            }

            .chart-wrapper {
                height: 250px;
                padding: 15px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-number {
                font-size: 2em;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }

        /* Animations */
        .chart-container {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .nav-button {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
    <div class="analytics-container">
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> Printer Analytics Dashboard</h1>
            <p>Comprehensive insights into printer usage, costs, and performance metrics</p>
        </div>

        <div class="navigation">
            <form method="get" class="nav-tabs">
                <button type="submit" name="view" value="bank"
                    class="nav-button <?php echo $view === 'bank' ? 'active' : ''; ?>">
                    <i class="fas fa-university"></i>
                    Bank Deposits
                </button>
                <button type="submit" name="view" value="counts"
                    class="nav-button <?php echo $view === 'counts' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    Printer Usage
                </button>
                <button type="submit" name="view" value="cost"
                    class="nav-button <?php echo $view === 'cost' ? 'active' : ''; ?>">
                    <i class="fas fa-dollar-sign"></i>
                    Cost Analysis
                </button>
            </form>
        </div>

        <?php if ($view === 'bank'): ?>
            <section class="section">
                <div class="stats-overview">
                    <div class="stat-card primary">
                        <div class="stat-icon"><i class="fas fa-university"></i></div>
                        <div class="stat-number"><?php echo count($data['dailyDeposits']); ?></div>
                        <div class="stat-label">Daily Records</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="stat-number"><?php echo count($data['monthlyDeposits']); ?></div>
                        <div class="stat-label">Monthly Records</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="stat-number">Rs. <?php echo number_format(array_sum(array_column($data['dailyDeposits'], 'total')), 0); ?></div>
                        <div class="stat-label">Total Deposits</div>
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-chart-area"></i>
                            Daily Bank Deposits
                        </div>
                        <div class="chart-controls">
                            <button class="control-btn active" onclick="resetZoom('dailyDepositsChart')">
                                <i class="fas fa-search-minus"></i> Reset Zoom
                            </button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="dailyDepositsChart"></canvas>
                    </div>
                    <div class="zoom-hint">
                        <i class="fas fa-info-circle"></i>
                        Use mouse wheel to zoom, hold Ctrl + drag to pan. Click reset to return to original view.
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-calendar-alt"></i>
                            Monthly Bank Deposits
                        </div>
                        <div class="chart-controls">
                            <button class="control-btn active" onclick="resetZoom('monthlyDepositsChart')">
                                <i class="fas fa-search-minus"></i> Reset Zoom
                            </button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="monthlyDepositsChart"></canvas>
                    </div>
                    <div class="zoom-hint">
                        <i class="fas fa-info-circle"></i>
                        Hover over bars to see detailed values. Use zoom controls for better analysis.
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($view === 'counts'): ?>
            <section class="section">
                <div class="stats-overview">
                    <div class="stat-card primary">
                        <div class="stat-icon"><i class="fas fa-print"></i></div>
                        <div class="stat-number"><?php echo count(array_unique(array_column($data['dailyPrinterData'], 'printerName'))); ?></div>
                        <div class="stat-label">Active Printers</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-calculator"></i></div>
                        <div class="stat-number"><?php echo number_format(array_sum(array_column($data['dailyPrinterData'], 'count'))); ?></div>
                        <div class="stat-label">Total Print Count</div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                        <div class="stat-number"><?php echo count(array_unique(array_column($data['dailyPrinterData'], 'date'))); ?></div>
                        <div class="stat-label">Days Tracked</div>
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-chart-bar"></i>
                            Daily Printer Usage
                        </div>
                        <div class="chart-controls">
                            <button class="control-btn active" onclick="resetZoom('dailyCountsChart')">
                                <i class="fas fa-search-minus"></i> Reset Zoom
                            </button>
                            <button class="control-btn" onclick="downloadChart('dailyCountsChart')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="dailyCountsChart"></canvas>
                    </div>
                    <div class="zoom-hint">
                        <i class="fas fa-info-circle"></i>
                        Stacked bars show individual printer types, lines show total usage per printer.
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-chart-line"></i>
                            Monthly Printer Trends
                        </div>
                        <div class="chart-controls">
                            <button class="control-btn active" onclick="resetZoom('monthlyCountsChart')">
                                <i class="fas fa-search-minus"></i> Reset Zoom
                            </button>
                            <button class="control-btn" onclick="downloadChart('monthlyCountsChart')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="monthlyCountsChart"></canvas>
                    </div>
                    <div class="zoom-hint">
                        <i class="fas fa-info-circle"></i>
                        Long-term usage patterns help identify printer efficiency and maintenance needs.
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($view === 'cost'): ?>
            <section class="section">
                <div class="stats-overview">
                    <div class="stat-card primary">
                        <div class="stat-icon"><i class="fas fa-money-bill"></i></div>
                        <div class="stat-number">Rs. <?php echo number_format(array_sum(array_column($data['dailyPrinterData'], 'cost')), 0); ?></div>
                        <div class="stat-label">Total Costs</div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
                        <div class="stat-number">Rs. <?php echo !empty($data['dailyPrinterData']) ? number_format(array_sum(array_column($data['dailyPrinterData'], 'cost')) / count(array_unique(array_column($data['dailyPrinterData'], 'date'))), 0) : '0'; ?></div>
                        <div class="stat-label">Avg Daily Cost</div>
                    </div>
                    <div class="stat-card danger">
                        <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                        <div class="stat-number">Rs. <?php 
                            if (!empty($data['dailyPrinterData'])) {
                                // Group costs by date and sum them
                                $dailyCosts = [];
                                foreach ($data['dailyPrinterData'] as $record) {
                                    $date = $record['date'];
                                    if (!isset($dailyCosts[$date])) {
                                        $dailyCosts[$date] = 0;
                                    }
                                    $dailyCosts[$date] += floatval($record['cost']);
                                }
                                echo number_format(max($dailyCosts), 0);
                            } else {
                                echo '0';
                            }
                        ?></div>
                        <div class="stat-label">Highest Daily Cost</div>
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-money-check-alt"></i>
                            Daily Cost Analysis
                        </div>
                        <div class="chart-controls">
                            <button class="control-btn active" onclick="resetZoom('dailyCostsChart')">
                                <i class="fas fa-search-minus"></i> Reset Zoom
                            </button>
                            <button class="control-btn" onclick="downloadChart('dailyCostsChart')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="dailyCostsChart"></canvas>
                    </div>
                    <div class="zoom-hint">
                        <i class="fas fa-info-circle"></i>
                        Track rental costs and identify cost-saving opportunities across different printers.
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <div class="chart-title">
                            <i class="fas fa-trending-up"></i>
                            Monthly Cost Trends
                        </div>
                        <div class="chart-controls">
                            <button class="control-btn active" onclick="resetZoom('monthlyCostsChart')">
                                <i class="fas fa-search-minus"></i> Reset Zoom
                            </button>
                            <button class="control-btn" onclick="downloadChart('monthlyCostsChart')">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="monthlyCostsChart"></canvas>
                    </div>
                    <div class="zoom-hint">
                        <i class="fas fa-info-circle"></i>
                        Monthly overview helps budget planning and cost optimization strategies.
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <script>
        // Register Chart.js zoom plugin
        Chart.register(ChartZoom);
        
        // Initialize chart data
        const chartData = <?php echo json_encode($data); ?>;
        const currentView = '<?php echo $view; ?>';
        let chartInstances = {};

        // Helper function to generate vibrant colors with better contrast
        function generateColors(count) {
            const colors = [
                '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD',
                '#98D8C8', '#6C5CE7', '#A29BFE', '#FD79A8', '#FDCB6E', '#6C5CE7',
                '#00B894', '#E17055', '#0984E3', '#B2BEC3', '#74B9FF', '#00CEC9',
                '#FF7675', '#55A3FF', '#FDCB6E', '#E84393', '#A29BFE', '#81ECEC'
            ];
            const result = [];
            for (let i = 0; i < count; i++) {
                if (i < colors.length) {
                    result.push(colors[i]);
                } else {
                    // Generate high contrast colors for additional items
                    const hue = (i * 137.5) % 360; // Golden angle for better distribution
                    result.push(`hsl(${hue}, 70%, 55%)`);
                }
            }
            return result;
        }

        // Enhanced chart options
        function getChartOptions(title, isZoomable = true) {
            return {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#667eea',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                if (currentView === 'bank' || currentView === 'cost') {
                                    return label + ': Rs. ' + new Intl.NumberFormat().format(context.parsed.y);
                                }
                                return label + ': ' + new Intl.NumberFormat().format(context.parsed.y);
                            }
                        }
                    },
                    zoom: isZoomable ? {
                        limits: {
                            x: {min: 'original', max: 'original'},
                            y: {min: 'original', max: 'original'}
                        },
                        pan: {
                            enabled: true,
                            mode: 'x',
                            modifierKey: 'ctrl'
                        },
                        zoom: {
                            wheel: {
                                enabled: true
                            },
                            pinch: {
                                enabled: true
                            },
                            mode: 'x',
                            onZoomComplete: function({chart}) {
                                // Optional: callback after zoom completes
                            }
                        }
                    } : undefined
                },
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            maxRotation: 45
                        }
                    },
                    y: {
                        stacked: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                if (currentView === 'bank' || currentView === 'cost') {
                                    return 'Rs. ' + new Intl.NumberFormat().format(value);
                                }
                                return new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                elements: {
                    bar: {
                        borderWidth: 1,
                        borderSkipped: false,
                        borderRadius: 2
                    },
                    line: {
                        borderWidth: 3,
                        tension: 0.4
                    },
                    point: {
                        radius: 4,
                        hoverRadius: 6,
                        borderWidth: 2
                    }
                }
            };
        }

        // Reset zoom function
        function resetZoom(chartId) {
            if (chartInstances[chartId]) {
                chartInstances[chartId].resetZoom('default');
            }
        }

        // Download chart function
        function downloadChart(chartId) {
            if (chartInstances[chartId]) {
                const link = document.createElement('a');
                link.download = chartId + '_' + new Date().toISOString().split('T')[0] + '.png';
                link.href = chartInstances[chartId].toBase64Image();
                link.click();
            }
        }

        // Process printer data for charts
        function processPrinterData(data) {
            const dates = [...new Set(data.map(d => d.date))];
            const printers = [...new Set(data.map(d => d.printerName))];
            const processedData = {};

            printers.forEach(printer => {
                // Initialize totals array for this printer
                const totals = dates.map(date => ({ date, count: 0, cost: 0 }));
                
                processedData[printer] = {
                    types: {},
                    total: totals
                };

                const printerData = data.filter(d => d.printerName === printer);
                const types = [...new Set(printerData.map(d => d.typeName))];

                types.forEach(type => {
                    processedData[printer].types[type] = dates.map((date, dateIndex) => {
                        const match = printerData.find(d => d.date === date && d.typeName === type);
                        const count = match ? parseInt(match.count) : 0;
                        const cost = match ? parseFloat(match.cost) : 0;
                        
                        // Add to totals for this date
                        processedData[printer].total[dateIndex].count += count;
                        processedData[printer].total[dateIndex].cost += cost;
                        
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

        // Create enhanced charts based on current view
        if (currentView === 'bank') {
            // Enhanced Bank Deposit Charts with better colors and tooltips
            const dailyCtx = document.getElementById('dailyDepositsChart');
            chartInstances['dailyDepositsChart'] = new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: chartData.dailyDeposits.map(d => {
                        const date = new Date(d.date);
                        return date.toLocaleDateString('en-US', { 
                            month: 'short', 
                            day: 'numeric'
                        });
                    }),
                    datasets: [{
                        label: 'Daily Deposits',
                        data: chartData.dailyDeposits.map(d => d.total),
                        backgroundColor: 'rgba(102, 126, 234, 0.8)',
                        borderColor: '#667eea',
                        borderWidth: 2,
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.8,
                        categoryPercentage: 0.9
                    }]
                },                    options: {
                        ...getChartOptions('Daily Bank Deposits (Last 90 Days)'),
                        plugins: {
                            ...getChartOptions('Daily Bank Deposits (Last 90 Days)').plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Daily Deposit: Rs. ' + new Intl.NumberFormat().format(context.parsed.y);
                                    }
                                }
                            }
                        }
                    }
            });

            const monthlyCtx = document.getElementById('monthlyDepositsChart');
            chartInstances['monthlyDepositsChart'] = new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: chartData.monthlyDeposits.map(d => d.month),
                    datasets: [{
                        label: 'Monthly Deposits',
                        data: chartData.monthlyDeposits.map(d => d.total),
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        borderColor: '#4CAF50',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4CAF50',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 7,
                        pointHoverRadius: 10,
                        pointHoverBackgroundColor: '#4CAF50',
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 4
                    }]
                },                    options: {
                        ...getChartOptions('Monthly Bank Deposits (Last 12 Months)'),
                        plugins: {
                            ...getChartOptions('Monthly Bank Deposits (Last 12 Months)').plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Monthly Deposit: Rs. ' + new Intl.NumberFormat().format(context.parsed.y);
                                    }
                                }
                            }
                        }
                    }
            });
        } else {
            // Process printer data
            const dailyPrinterStats = processPrinterData(chartData.dailyPrinterData);
            const monthlyPrinterStats = processPrinterData(chartData.monthlyPrinterData);

            function createEnhancedComboChart(canvasId, data, title, valueType) {
                const datasets = [];
                const colors = generateColors(data.printers.length * 4);
                let colorIndex = 0;

                data.printers.forEach((printer, printerIndex) => {
                    // Add stacked bars for each type
                    Object.entries(data.data[printer].types).forEach(([type, typeData]) => {
                        const color = colors[colorIndex++];
                        datasets.push({
                            type: 'bar',
                            label: `${printer} - ${type}`,
                            data: typeData.map(d => d[valueType]),
                            backgroundColor: color + '80',
                            borderColor: color,
                            borderWidth: 1,
                            stack: `stack${printerIndex}`,
                            borderRadius: 3,
                            borderSkipped: false,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        });
                    });

                    // Add line for total
                    const lineColor = colors[colorIndex++];
                    datasets.push({
                        type: 'line',
                        label: `${printer} - Total`,
                        data: data.data[printer].total.map(d => d[valueType]),
                        borderColor: lineColor,
                        backgroundColor: lineColor + '20',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: lineColor,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        pointHoverBackgroundColor: lineColor,
                        pointHoverBorderColor: '#ffffff',
                        pointHoverBorderWidth: 3
                    });
                });

                const ctx = document.getElementById(canvasId);
                chartInstances[canvasId] = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.dates.map(d => {
                            const date = new Date(d);
                            return date.toLocaleDateString('en-US', { 
                                month: 'short', 
                                day: 'numeric'
                            });
                        }),
                        datasets: datasets
                    },
                    options: getChartOptions(title)
                });
            }

            if (currentView === 'counts') {
                // Create enhanced count charts
                createEnhancedComboChart(
                    'dailyCountsChart',
                    dailyPrinterStats,
                    'Daily Printer Counts (Last 90 Days)',
                    'count'
                );

                createEnhancedComboChart(
                    'monthlyCountsChart',
                    monthlyPrinterStats,
                    'Monthly Printer Counts (Last 12 Months)',
                    'count'
                );
            } else if (currentView === 'cost') {
                // Create enhanced cost charts
                createEnhancedComboChart(
                    'dailyCostsChart',
                    dailyPrinterStats,
                    'Daily Printer Costs (Last 90 Days)',
                    'cost'
                );

                createEnhancedComboChart(
                    'monthlyCostsChart',
                    monthlyPrinterStats,
                    'Monthly Printer Costs (Last 12 Months)',
                    'cost'
                );
            }
        }

        // Add loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const containers = document.querySelectorAll('.chart-container');
            containers.forEach((container, index) => {
                container.style.opacity = '0';
                container.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    container.style.transition = 'all 0.6s ease';
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>

</html>