<?php
// Handle AJAX requests for updating limits BEFORE including any files that output HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Include only the config file for database connection, not conn.php which includes nav.php
    include '../inc/config.php';
    
    header('Content-Type: application/json');
    
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 0); // Don't display errors in output
    
    try {
        if ($_POST['action'] === 'update_limit') {
            $printerID = intval($_POST['printer_id']);
            $newLimit = floatval($_POST['new_limit']);
            
            // Debug logging
            error_log("Update limit request - PrinterID: $printerID, NewLimit: $newLimit");
            
            // Validate input
            if ($printerID <= 0) {
                throw new Exception('Invalid printer ID: ' . $printerID);
            }
            
            if ($newLimit < 0) {
                throw new Exception('Limit cannot be negative: ' . $newLimit);
            }
            
            // Check if printer exists first
            $checkSql = "SELECT printerID, printerName FROM printer_counter_printers WHERE printerID = ?";
            $checkStmt = $con->prepare($checkSql);
            
            if (!$checkStmt) {
                throw new Exception('Failed to prepare check statement: ' . $con->error);
            }
            
            $checkStmt->bind_param("i", $printerID);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows == 0) {
                $checkStmt->close();
                throw new Exception('Printer not found with ID: ' . $printerID);
            }
            
            $printerData = $checkResult->fetch_assoc();
            $checkStmt->close();
            
            // Update the rent_limit in the database
            $sql = "UPDATE printer_counter_printers SET rent_limit = ? WHERE printerID = ?";
            $stmt = $con->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Failed to prepare update statement: ' . $con->error);
            }
            
            $stmt->bind_param("di", $newLimit, $printerID);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to execute update: ' . $stmt->error);
            }
            
            $affectedRows = $stmt->affected_rows;
            $stmt->close();
            
            error_log("Update completed - Affected rows: $affectedRows");
            
            echo json_encode([
                'success' => true, 
                'message' => 'Limit updated successfully for ' . $printerData['printerName'],
                'affected_rows' => $affectedRows,
                'printer_name' => $printerData['printerName']
            ]);
            
        } else {
            throw new Exception('Invalid action: ' . $_POST['action']);
        }
        
    } catch (Exception $e) {
        error_log("Error updating limit: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
    $con->close();
    exit;
}

// For normal page requests, include conn.php which has navigation
include 'conn.php';

// Function to get current month total cost for each printer
function getCurrentMonthCost($con, $printerID, $year = null, $month = null) {
    if ($year === null || $month === null) {
        // Default to current month
        $sql = "SELECT 
                    SUM(c.count * t.cost) as total_cost
                FROM printer_counter_count c
                JOIN printer_counter_types t ON c.typeID = t.typeID
                JOIN printer_counter_printers p ON t.printerID = p.printerID
                WHERE p.printerID = ? 
                    AND YEAR(c.date) = YEAR(CURRENT_DATE) 
                    AND MONTH(c.date) = MONTH(CURRENT_DATE)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $printerID);
    } else {
        // Specific month and year
        $sql = "SELECT 
                    SUM(c.count * t.cost) as total_cost
                FROM printer_counter_count c
                JOIN printer_counter_types t ON c.typeID = t.typeID
                JOIN printer_counter_printers p ON t.printerID = p.printerID
                WHERE p.printerID = ? 
                    AND YEAR(c.date) = ? 
                    AND MONTH(c.date) = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("iii", $printerID, $year, $month);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_cost'] ? $row['total_cost'] : 0;
}

// Function to get available months with data
function getAvailableMonths($con) {
    $sql = "SELECT DISTINCT 
                YEAR(date) as year, 
                MONTH(date) as month,
                DATE_FORMAT(date, '%M') as month_name
            FROM printer_counter_count 
            ORDER BY year DESC, month DESC";
    
    $result = $con->query($sql);
    $months = [];
    
    while ($row = $result->fetch_assoc()) {
        $months[] = [
            'year' => $row['year'],
            'month' => $row['month'],
            'year_month' => $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT),
            'month_name' => $row['month_name']
        ];
    }
    
    return $months;
}

// Function to get printer rent limit data
function getPrinterLimitsData($con, $year = null, $month = null) {
    $sql = "SELECT 
                printerID,
                printerName,
                ipAddress,
                rent_limit
            FROM printer_counter_printers 
            WHERE active = '1'
            ORDER BY printerName";
    
    $result = $con->query($sql);
    $printers = [];
    
    while ($row = $result->fetch_assoc()) {
        $currentCost = getCurrentMonthCost($con, $row['printerID'], $year, $month);
        $limit = $row['rent_limit'];
        
        // Calculate usage percentage
        $usagePercentage = 0;
        if ($limit > 0) {
            $usagePercentage = ($currentCost / $limit) * 100;
        }
        
        $printers[] = [
            'printerID' => $row['printerID'],
            'printerName' => $row['printerName'],
            'ipAddress' => $row['ipAddress'],
            'rent_limit' => $limit,
            'current_cost' => $currentCost,
            'usage_percentage' => $usagePercentage,
            'is_unlimited' => ($limit == 0),
            'is_over_limit' => ($limit > 0 && $currentCost > $limit)
        ];
    }
    
    return $printers;
}

// Get selected month from URL parameters
$selectedYear = isset($_GET['year']) && !empty($_GET['year']) ? intval($_GET['year']) : null;
$selectedMonth = isset($_GET['month']) && !empty($_GET['month']) ? intval($_GET['month']) : null;

// Get available months
$availableMonths = getAvailableMonths($con);

// Get printer data for selected or current month
$printers = getPrinterLimitsData($con, $selectedYear, $selectedMonth);
$con->close();

// Get current month name for display
if ($selectedYear && $selectedMonth) {
    $currentMonth = date('F Y', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear));
} else {
    $currentMonth = date('F Y');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printer Rent Limits - <?php echo $currentMonth; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .rent-limits-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 300;
        }

        .header .month {
            font-size: 0.9em;
            opacity: 0.9;
            margin-top: 3px;
        }

        .printers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .printer-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            border: 1px solid #e8e8e8;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .printer-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .printer-card.unlimited {
            border-left: 5px solid #28a745;
        }

        .printer-card.within-limit {
            border-left: 5px solid #17a2b8;
        }

        .printer-card.over-limit {
            border-left: 5px solid #dc3545;
            background: linear-gradient(135deg, #fff 0%, #ffe6e6 100%);
        }

        .printer-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }

        .printer-name {
            font-size: 1.4em;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .edit-limit-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.7em;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-left: 8px;
            vertical-align: middle;
        }

        .edit-limit-btn:hover {
            background: #0056b3;
        }

        .edit-limit-btn i {
            font-size: 0.8em;
        }

        .controls-section {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .month-selector-part {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }

        .analytics-part {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .month-selector-part select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.9em;
            min-width: 150px;
            cursor: pointer;
        }

        .month-selector-part select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }

        .month-selector-part button {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 0.9em;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .month-selector-part button:hover {
            background: #0056b3;
        }

        .analytics-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            font-size: 0.9em;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .analytics-btn.count {
            background: #17a2b8;
            color: white;
        }

        .analytics-btn.cost {
            background: #28a745;
            color: white;
        }

        .analytics-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .current-month-indicator {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 6px;
            padding: 8px 12px;
            margin-top: 10px;
            color: #0c5460;
            font-size: 0.9em;
            text-align: center;
        }

        @media (max-width: 768px) {
            .controls-section {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            
            .month-selector-part, 
            .analytics-part {
                justify-content: center;
            }
        }

        .edit-limit-form {
            display: none;
            margin-top: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }

        .edit-limit-form input {
            width: 100px;
            padding: 5px;
            margin-right: 5px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        .edit-limit-form button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin-right: 5px;
        }

        .save-btn {
            background: #28a745;
            color: white;
        }

        .cancel-btn {
            background: #6c757d;
            color: white;
        }

        .usage-info {
            margin: 20px 0;
        }

        .usage-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 12px 0;
            padding: 8px 0;
        }

        .usage-label {
            font-weight: 500;
            color: #555;
            display: flex;
            align-items: center;
        }

        .usage-label i {
            margin-right: 8px;
            width: 16px;
        }

        .usage-value {
            font-weight: 600;
            font-size: 1.1em;
        }

        .currency {
            color: #28a745;
        }

        .unlimited-badge {
            background: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
        }

        .over-limit-badge {
            background: #dc3545;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 500;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #17a2b8, #007bff);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-fill.over-limit {
            background: linear-gradient(90deg, #dc3545, #c82333);
        }

        .percentage {
            color: #666;
            font-size: 0.9em;
            text-align: center;
            margin-top: 5px;
        }

        .stats-summary {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .stat-item {
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .stat-number {
            font-size: 1.5em;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .stat-label {
            color: #666;
            font-size: 0.8em;
        }

        .no-printers {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }

        @media (max-width: 768px) {
            .printers-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="rent-limits-container">
        <div class="header">
            <h1><i class="fas fa-chart-pie"></i> Printer Rent Limits</h1>
            <div class="month">
                <?php if ($selectedYear && $selectedMonth): ?>
                    <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 1.1em;">
                        <?php echo $currentMonth; ?>
                    </span> Usage Report
                <?php else: ?>
                    <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 1.1em; box-shadow: 0 0 15px rgba(255,255,255,0.3);">
                        <?php echo $currentMonth; ?> (Current)
                    </span> Usage Report
                <?php endif; ?>
            </div>
        </div>

        <?php
        // Calculate summary statistics
        $totalPrinters = count($printers);
        $unlimitedCount = array_filter($printers, function($p) { return $p['is_unlimited']; });
        $rentPrinters = array_filter($printers, function($p) { return !$p['is_unlimited']; });
        $overLimitCount = array_filter($printers, function($p) { return $p['is_over_limit']; });
        $totalCurrentCost = array_sum(array_column($printers, 'current_cost'));
        ?>

        <div class="stats-summary">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" style="color: #007bff;"><?php echo $totalPrinters; ?></div>
                    <div class="stat-label">Total Printers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #28a745;">Rs. <?php echo number_format($totalCurrentCost, 2); ?></div>
                    <div class="stat-label">Total Month Cost</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #28a745;"><?php echo count($unlimitedCount); ?></div>
                    <div class="stat-label">Own Printers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #ff6b35;"><?php echo count($rentPrinters); ?></div>
                    <div class="stat-label">Rent Printers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" style="color: #dc3545;"><?php echo count($overLimitCount); ?></div>
                    <div class="stat-label">Over Limit</div>
                </div>
            </div>
        </div>

        <!-- Combined Controls Section -->
        <div class="controls-section">
            <div class="month-selector-part">
                <i class="fas fa-calendar-alt"></i>
                <span>Month:</span>
                <form method="GET" style="display: inline-flex; align-items: center; gap: 10px;" id="monthForm">
                    <select name="month" id="monthSelect" onchange="updateYear()">
                        <option value="">Current Month</option>
                        <?php foreach ($availableMonths as $monthData): ?>
                            <option value="<?php echo $monthData['month']; ?>" 
                                    data-year="<?php echo $monthData['year']; ?>"
                                    <?php echo ($selectedYear == $monthData['year'] && $selectedMonth == $monthData['month']) ? 'selected' : ''; ?>>
                                <?php echo $monthData['month_name'] . ' ' . $monthData['year']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="year" id="yearInput" value="<?php echo $selectedYear; ?>">
                    <button type="submit">View</button>
                </form>
            </div>
            
            <div class="analytics-part">
                <span style="color: #666; margin-right: 10px;">Analytics:</span>
                <a href="analytics.php?view=counts" target="_blank" class="analytics-btn count">
                    <i class="fas fa-chart-bar"></i> Count
                </a>
                <a href="analytics.php?view=cost" target="_blank" class="analytics-btn cost">
                    <i class="fas fa-dollar-sign"></i> Cost
                </a>
            </div>
        </div>

        <?php if ($selectedYear && $selectedMonth): ?>
            <div class="current-month-indicator">
                <strong>Viewing:</strong> <?php echo $currentMonth; ?> 
                | <a href="rent-limits.php" style="color: #007bff; text-decoration: none;">← Back to Current Month</a>
            </div>
        <?php endif; ?>

        <?php if (empty($printers)): ?>
            <div class="no-printers">
                <i class="fas fa-print" style="font-size: 3em; color: #ccc; margin-bottom: 20px;"></i>
                <h3>No Active Printers Found</h3>
                <p>There are no active printers configured in the system.</p>
            </div>
        <?php else: ?>
            <div class="printers-grid">
                <?php foreach ($printers as $printer): ?>
                    <div class="printer-card <?php 
                        if ($printer['is_unlimited']) {
                            echo 'unlimited';
                        } elseif ($printer['is_over_limit']) {
                            echo 'over-limit';
                        } else {
                            echo 'within-limit';
                        }
                    ?>">
                        <div class="printer-header">
                            <div>
                                <h3 class="printer-name">
                                    <i class="fas fa-print"></i> <?php echo htmlspecialchars($printer['printerName']); ?>
                                </h3>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if ($printer['is_unlimited']): ?>
                                    <span class="unlimited-badge">Unlimited</span>
                                <?php elseif ($printer['is_over_limit']): ?>
                                    <span class="over-limit-badge">Over Limit!</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Edit Limit Form -->
                        <div id="edit-form-<?php echo $printer['printerID']; ?>" class="edit-limit-form">
                            <form onsubmit="updateLimit(event, <?php echo $printer['printerID']; ?>)">
                                <label>New Limit: Rs.</label>
                                <input type="number" 
                                       id="limit-input-<?php echo $printer['printerID']; ?>" 
                                       value="<?php echo $printer['rent_limit']; ?>" 
                                       step="0.01" 
                                       min="0"
                                       placeholder="0 = Unlimited">
                                <button type="submit" class="save-btn">Save</button>
                                <button type="button" class="cancel-btn" onclick="toggleEditForm(<?php echo $printer['printerID']; ?>)">Cancel</button>
                            </form>
                        </div>

                        <div class="usage-info">
                            <div class="usage-item">
                                <span class="usage-label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Current Month Usage:
                                </span>
                                <span class="usage-value currency">
                                    Rs. <?php echo number_format($printer['current_cost'], 2); ?>
                                </span>
                            </div>

                            <div class="usage-item">
                                <span class="usage-label">
                                    <i class="fas fa-limit"></i>
                                    Monthly Limit:
                                </span>
                                <span class="usage-value">
                                    <?php if ($printer['is_unlimited']): ?>
                                        <span style="color: #28a745;">No Limit</span>
                                    <?php else: ?>
                                        <span class="currency">Rs. <?php echo number_format($printer['rent_limit'], 2); ?></span>
                                    <?php endif; ?>
                                    <button class="edit-limit-btn" onclick="toggleEditForm(<?php echo $printer['printerID']; ?>)" title="Edit Limit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </span>
                            </div>

                            <?php if (!$printer['is_unlimited']): ?>
                                <div class="progress-bar">
                                    <div class="progress-fill <?php echo $printer['is_over_limit'] ? 'over-limit' : ''; ?>" 
                                         style="width: <?php echo min($printer['usage_percentage'], 100); ?>%;">
                                    </div>
                                </div>
                                <div class="percentage">
                                    <?php echo number_format($printer['usage_percentage'], 1); ?>% used
                                    <?php if ($printer['is_over_limit']): ?>
                                        <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!$printer['is_unlimited']): ?>
                                <div class="usage-item" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                                    <span class="usage-label">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Remaining:
                                    </span>
                                    <span class="usage-value">
                                        <?php 
                                        $remaining = $printer['rent_limit'] - $printer['current_cost'];
                                        if ($remaining > 0): ?>
                                            <span class="currency">Rs. <?php echo number_format($remaining, 2); ?></span>
                                        <?php else: ?>
                                            <span style="color: #dc3545;">Rs. <?php echo number_format($remaining, 2); ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-refresh every 5 minutes
        setTimeout(function() {
            window.location.reload();
        }, 300000);

        // Add smooth loading animation
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.printer-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Toggle edit form visibility
        function toggleEditForm(printerID) {
            const form = document.getElementById('edit-form-' + printerID);
            if (form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        // Update printer limit
        function updateLimit(event, printerID) {
            event.preventDefault();
            
            const limitInput = document.getElementById('limit-input-' + printerID);
            const newLimit = parseFloat(limitInput.value);
            
            // Simple validation
            if (isNaN(newLimit) || newLimit < 0) {
                alert('Please enter a valid limit (0 or greater). 0 means unlimited.');
                return;
            }
            
            // Show loading state
            const submitBtn = event.target.querySelector('.save-btn');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            // Create FormData for AJAX request
            const formData = new FormData();
            formData.append('action', 'update_limit');
            formData.append('printer_id', printerID);
            formData.append('new_limit', newLimit);
            
            console.log('Sending data:', {
                action: 'update_limit',
                printer_id: printerID,
                new_limit: newLimit
            });
            
            // Send AJAX request
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed response:', data);
                    
                    if (data.success) {
                        alert('Limit updated successfully!');
                        window.location.reload(); // Refresh page to show updated data
                    } else {
                        alert('Error updating limit: ' + (data.error || 'Unknown error'));
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', text);
                    alert('Error: Invalid response from server. Check console for details.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message + '. Please check your connection and try again.');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        }

        // Function to update year input when month is selected
        function updateYear() {
            const monthSelect = document.getElementById('monthSelect');
            const yearInput = document.getElementById('yearInput');
            
            const selectedOption = monthSelect.options[monthSelect.selectedIndex];
            if (selectedOption.value !== '') {
                const year = selectedOption.getAttribute('data-year');
                if (year) {
                    yearInput.value = year;
                } else {
                    yearInput.value = '';
                }
            } else {
                yearInput.value = '';
            }
        }
    </script>
</body>

</html>
