<?php
include '../nav.php';

// Function to calculate daily report data
function getDailyReportData($con, $date) {
    $data = [];
    
    // Invoice count and revenue
    $invoiceQuery = "SELECT COUNT(*) as count, SUM(advance) as cash_in, SUM(profit) as profit 
                     FROM invoice WHERE invoice_date = '$date'";
    $invoiceResult = mysqli_query($con, $invoiceQuery);
    $invoiceData = mysqli_fetch_assoc($invoiceResult);
    
    // Expenses
    $expenseQuery = "SELECT SUM(amount) as expenses 
                     FROM transaction_log 
                     WHERE transaction_date = '$date' 
                     AND (transaction_type = 'Petty Cash' OR transaction_type = 'Raw Item Purchase' 
                          OR transaction_type LIKE 'Fall OneTimeProduct Cost from%' OR transaction_type = 'Salary Payment')";
    $expenseResult = mysqli_query($con, $expenseQuery);
    $expenseData = mysqli_fetch_assoc($expenseResult);
    
    // Bank deposits
    $bankQuery = "SELECT SUM(amount) as bank_deposit FROM bank_deposits WHERE deposit_date = '$date'";
    $bankResult = mysqli_query($con, $bankQuery);
    $bankData = mysqli_fetch_assoc($bankResult);
    
    return [
        'date' => $date,
        'invoice_count' => $invoiceData['count'] ?? 0,
        'cash_in' => $invoiceData['cash_in'] ?? 0,
        'profit' => $invoiceData['profit'] ?? 0,
        'expenses' => $expenseData['expenses'] ?? 0,
        'bank_deposit' => $bankData['bank_deposit'] ?? 0,
        'net_profit' => ($invoiceData['profit'] ?? 0) - ($expenseData['expenses'] ?? 0)
    ];
}

// Function to get weekly report data
function getWeeklyReportData($con, $startDate, $endDate) {
    $data = [];
    
    // Invoice data
    $invoiceQuery = "SELECT COUNT(*) as count, SUM(advance) as cash_in, SUM(profit) as profit 
                     FROM invoice WHERE invoice_date BETWEEN '$startDate' AND '$endDate'";
    $invoiceResult = mysqli_query($con, $invoiceQuery);
    $invoiceData = mysqli_fetch_assoc($invoiceResult);
    
    // Expenses
    $expenseQuery = "SELECT SUM(amount) as expenses 
                     FROM transaction_log 
                     WHERE transaction_date BETWEEN '$startDate' AND '$endDate' 
                     AND (transaction_type = 'Petty Cash' OR transaction_type = 'Raw Item Purchase' 
                          OR transaction_type LIKE 'Fall OneTimeProduct Cost from%' OR transaction_type = 'Salary Payment')";
    $expenseResult = mysqli_query($con, $expenseQuery);
    $expenseData = mysqli_fetch_assoc($expenseResult);
    
    // Bank deposits
    $bankQuery = "SELECT SUM(amount) as bank_deposit FROM bank_deposits WHERE deposit_date BETWEEN '$startDate' AND '$endDate'";
    $bankResult = mysqli_query($con, $bankQuery);
    $bankData = mysqli_fetch_assoc($bankResult);
    
    return [
        'start_date' => $startDate,
        'end_date' => $endDate,
        'invoice_count' => $invoiceData['count'] ?? 0,
        'cash_in' => $invoiceData['cash_in'] ?? 0,
        'profit' => $invoiceData['profit'] ?? 0,
        'expenses' => $expenseData['expenses'] ?? 0,
        'bank_deposit' => $bankData['bank_deposit'] ?? 0,
        'net_profit' => ($invoiceData['profit'] ?? 0) - ($expenseData['expenses'] ?? 0)
    ];
}

// Function to format currency
function formatCurrency($amount) {
    return number_format($amount, 2);
}

?>

<title>Reports Dashboard</title>

<div class="reports-container">
    <h1><i class="fas fa-chart-bar"></i> Reports Dashboard</h1>
    
    <!-- Navigation Tabs -->
    <div class="report-tabs">
        <button class="tab-button active" onclick="showTab('daily')">
            <i class="fas fa-calendar-day"></i> Daily Reports
        </button>
        <button class="tab-button" onclick="showTab('weekly')">
            <i class="fas fa-calendar-week"></i> Weekly Reports
        </button>
        <button class="tab-button" onclick="showTab('monthly')">
            <i class="fas fa-calendar-alt"></i> Monthly Reports
        </button>
        <button class="tab-button" onclick="showTab('annual')">
            <i class="fas fa-calendar"></i> Annual Reports
        </button>
    </div>

    <!-- Daily Reports Section -->
    <div id="daily-tab" class="tab-content active">
        <div class="report-header">
            <h2><i class="fas fa-calendar-day"></i> Daily Reports</h2>
            <div class="date-navigation">
                <button onclick="navigateDate('daily', 'prev')" class="nav-btn">
                    <i class="fas fa-chevron-left"></i> Previous Day
                </button>
                <input type="date" id="daily-date" value="<?php echo date('Y-m-d'); ?>" onchange="loadDailyReports()">
                <button onclick="navigateDate('daily', 'next')" class="nav-btn">
                    Next Day <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="report-summary">
            <h3>Last 10 Days Summary</h3>
            <div class="table-container">
                <table id="daily-summary-table" class="report-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Invoices</th>
                            <th>Cash In</th>
                            <th>Profit</th>
                            <th>Expenses</th>
                            <th>Bank Deposit</th>
                            <th>Net Profit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < 10; $i++) {
                            $date = date('Y-m-d', strtotime("-$i days"));
                            $dailyData = getDailyReportData($con, $date);
                            echo "<tr>";
                            echo "<td>" . date('M j, Y', strtotime($date)) . "</td>";
                            echo "<td>" . $dailyData['invoice_count'] . "</td>";
                            echo "<td>Rs. " . formatCurrency($dailyData['cash_in']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($dailyData['profit']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($dailyData['expenses']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($dailyData['bank_deposit']) . "</td>";
                            echo "<td class='" . ($dailyData['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative') . "'>Rs. " . formatCurrency($dailyData['net_profit']) . "</td>";
                            echo "<td><button class='view-btn' onclick='showDailyDetails(\"$date\")'>View Details</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Weekly Reports Section -->
    <div id="weekly-tab" class="tab-content">
        <div class="report-header">
            <h2><i class="fas fa-calendar-week"></i> Weekly Reports</h2>
            <div class="date-navigation">
                <button onclick="navigateWeek('prev')" class="nav-btn">
                    <i class="fas fa-chevron-left"></i> Previous Week
                </button>
                <span id="current-week-display"><?php echo date('M j') . ' - ' . date('M j, Y', strtotime('+6 days')); ?></span>
                <button onclick="navigateWeek('next')" class="nav-btn">
                    Next Week <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="report-summary">
            <h3>Last 8 Weeks Summary</h3>
            <div class="table-container">
                <table id="weekly-summary-table" class="report-table">
                    <thead>
                        <tr>
                            <th>Week</th>
                            <th>Period</th>
                            <th>Invoices</th>
                            <th>Cash In</th>
                            <th>Profit</th>
                            <th>Expenses</th>
                            <th>Bank Deposit</th>
                            <th>Net Profit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < 8; $i++) {
                            $weekStart = date('Y-m-d', strtotime("-" . ($i * 7) . " days Monday"));
                            $weekEnd = date('Y-m-d', strtotime($weekStart . " +6 days"));
                            $weeklyData = getWeeklyReportData($con, $weekStart, $weekEnd);
                            
                            echo "<tr>";
                            echo "<td>Week " . ($i + 1) . "</td>";
                            echo "<td>" . date('M j', strtotime($weekStart)) . " - " . date('M j, Y', strtotime($weekEnd)) . "</td>";
                            echo "<td>" . $weeklyData['invoice_count'] . "</td>";
                            echo "<td>Rs. " . formatCurrency($weeklyData['cash_in']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($weeklyData['profit']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($weeklyData['expenses']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($weeklyData['bank_deposit']) . "</td>";
                            echo "<td class='" . ($weeklyData['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative') . "'>Rs. " . formatCurrency($weeklyData['net_profit']) . "</td>";
                            echo "<td><button class='view-btn' onclick='showWeeklyDetails(\"$weekStart\", \"$weekEnd\")'>View Details</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Monthly Reports Section -->
    <div id="monthly-tab" class="tab-content">
        <div class="report-header">
            <h2><i class="fas fa-calendar-alt"></i> Monthly Reports</h2>
            <div class="date-navigation">
                <button onclick="navigateMonth('prev')" class="nav-btn">
                    <i class="fas fa-chevron-left"></i> Previous Month
                </button>
                <span id="current-month-display"><?php echo date('F Y'); ?></span>
                <button onclick="navigateMonth('next')" class="nav-btn">
                    Next Month <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="report-summary">
            <h3>Last 12 Months Summary</h3>
            <div class="table-container">
                <table id="monthly-summary-table" class="report-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Invoices</th>
                            <th>Cash In</th>
                            <th>Profit</th>
                            <th>Expenses</th>
                            <th>Bank Deposit</th>
                            <th>Net Profit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < 12; $i++) {
                            $monthStart = date('Y-m-01', strtotime("-$i months"));
                            $monthEnd = date('Y-m-t', strtotime($monthStart));
                            if ($i === 0) {
                                $monthEnd = date('Y-m-d'); // Current date for current month
                            }
                            $monthlyData = getWeeklyReportData($con, $monthStart, $monthEnd);
                            
                            echo "<tr>";
                            echo "<td>" . date('F Y', strtotime($monthStart)) . "</td>";
                            echo "<td>" . $monthlyData['invoice_count'] . "</td>";
                            echo "<td>Rs. " . formatCurrency($monthlyData['cash_in']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($monthlyData['profit']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($monthlyData['expenses']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($monthlyData['bank_deposit']) . "</td>";
                            echo "<td class='" . ($monthlyData['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative') . "'>Rs. " . formatCurrency($monthlyData['net_profit']) . "</td>";
                            echo "<td><button class='view-btn' onclick='showMonthlyDetails(\"$monthStart\", \"$monthEnd\")'>View Details</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Annual Reports Section -->
    <div id="annual-tab" class="tab-content">
        <div class="report-header">
            <h2><i class="fas fa-calendar"></i> Annual Reports</h2>
            <div class="date-navigation">
                <button onclick="navigateYear('prev')" class="nav-btn">
                    <i class="fas fa-chevron-left"></i> Previous Year
                </button>
                <span id="current-year-display"><?php echo date('Y'); ?></span>
                <button onclick="navigateYear('next')" class="nav-btn">
                    Next Year <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <div class="report-summary">
            <h3>Last 5 Years Summary</h3>
            <div class="table-container">
                <table id="annual-summary-table" class="report-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Invoices</th>
                            <th>Cash In</th>
                            <th>Profit</th>
                            <th>Expenses</th>
                            <th>Bank Deposit</th>
                            <th>Net Profit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 0; $i < 5; $i++) {
                            $yearStart = date('Y-01-01', strtotime("-$i years"));
                            $yearEnd = date('Y-12-31', strtotime("-$i years"));
                            if ($i === 0) {
                                $yearEnd = date('Y-m-d'); // Current date for current year
                            }
                            $annualData = getWeeklyReportData($con, $yearStart, $yearEnd);
                            
                            echo "<tr>";
                            echo "<td>" . date('Y', strtotime($yearStart)) . "</td>";
                            echo "<td>" . $annualData['invoice_count'] . "</td>";
                            echo "<td>Rs. " . formatCurrency($annualData['cash_in']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($annualData['profit']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($annualData['expenses']) . "</td>";
                            echo "<td>Rs. " . formatCurrency($annualData['bank_deposit']) . "</td>";
                            echo "<td class='" . ($annualData['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative') . "'>Rs. " . formatCurrency($annualData['net_profit']) . "</td>";
                            echo "<td><button class='view-btn' onclick='showAnnualDetails(\"$yearStart\", \"$yearEnd\")'>View Details</button></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for detailed reports -->
<div id="reportModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modal-title">Report Details</h2>
        <div id="modal-body">
            <!-- Report details will be loaded here -->
        </div>
    </div>
</div>

<style>
.reports-container {
    padding: 20px;
    color: white;
}

.report-tabs {
    display: flex;
    margin-bottom: 30px;
    border-bottom: 2px solid #333;
}

.tab-button {
    background: transparent;
    border: none;
    color: #ccc;
    padding: 15px 25px;
    cursor: pointer;
    font-size: 16px;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.tab-button:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
}

.tab-button.active {
    color: #007bff;
    border-bottom-color: #007bff;
    background: rgba(0, 123, 255, 0.1);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

.report-header h2 {
    margin: 0;
    color: #007bff;
}

.date-navigation {
    display: flex;
    align-items: center;
    gap: 15px;
}

.nav-btn {
    background: #007bff;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.nav-btn:hover {
    background: #0056b3;
}

#daily-date {
    background: #333;
    color: white;
    border: 1px solid #555;
    padding: 8px 12px;
    border-radius: 5px;
}

.report-summary h3 {
    color: #28a745;
    margin-bottom: 20px;
}

.table-container {
    overflow-x: auto;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    padding: 20px;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    color: white;
}

.report-table th {
    background: #007bff;
    color: white;
    padding: 15px 10px;
    text-align: left;
    border-bottom: 2px solid #0056b3;
}

.report-table td {
    padding: 12px 10px;
    border-bottom: 1px solid #444;
}

.report-table tr:nth-child(even) {
    background: rgba(255, 255, 255, 0.05);
}

.report-table tr:hover {
    background: rgba(0, 123, 255, 0.2);
}

.profit-positive {
    color: #28a745 !important;
    font-weight: bold;
}

.profit-negative {
    color: #dc3545 !important;
    font-weight: bold;
}

.view-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.3s ease;
}

.view-btn:hover {
    background: #1e7e34;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
}

.modal-content {
    background-color: #222;
    margin: 5% auto;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 1000px;
    color: white;
    position: relative;
    max-height: 80vh;
    overflow-y: auto;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    right: 20px;
    top: 15px;
    cursor: pointer;
}

.close:hover {
    color: white;
}

.detail-card {
    background: rgba(255, 255, 255, 0.1);
    padding: 20px;
    margin: 15px 0;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.metric-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 1px solid #444;
}

.metric-value {
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

.metric-label {
    color: #ccc;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .report-header {
        flex-direction: column;
        gap: 20px;
    }
    
    .date-navigation {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .tab-button {
        font-size: 14px;
        padding: 12px 15px;
    }
    
    .report-table {
        font-size: 12px;
    }
    
    .modal-content {
        width: 95%;
        margin: 2% auto;
        padding: 20px;
    }
}
</style>

<script>
// Tab navigation
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(tab => tab.classList.remove('active'));
    
    // Remove active class from all buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(btn => btn.classList.remove('active'));
    
    // Show selected tab and activate button
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

// Date navigation functions
function navigateDate(type, direction) {
    const dateInput = document.getElementById('daily-date');
    const currentDate = new Date(dateInput.value);
    
    if (direction === 'prev') {
        currentDate.setDate(currentDate.getDate() - 1);
    } else {
        currentDate.setDate(currentDate.getDate() + 1);
    }
    
    dateInput.value = currentDate.toISOString().split('T')[0];
    loadDailyReports();
}

function navigateWeek(direction) {
    // Implementation for week navigation
    console.log('Navigate week:', direction);
}

function navigateMonth(direction) {
    // Implementation for month navigation
    console.log('Navigate month:', direction);
}

function navigateYear(direction) {
    // Implementation for year navigation
    console.log('Navigate year:', direction);
}

// Load daily reports based on selected date
function loadDailyReports() {
    // Implementation for loading specific date reports
    console.log('Loading daily reports for:', document.getElementById('daily-date').value);
}

// Modal functions
function showDailyDetails(date) {
    document.getElementById('modal-title').textContent = 'Daily Report Details - ' + new Date(date).toLocaleDateString();
    
    // Show loading message
    document.getElementById('modal-body').innerHTML = '<div class="detail-card"><h3>Loading...</h3><p>Please wait while we fetch the report details.</p></div>';
    document.getElementById('reportModal').style.display = 'block';
    
    // Fetch detailed data via AJAX
    fetch('api/get_daily_details.php?date=' + date)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displayDailyDetails(data);
        })
        .catch(error => {
            console.error('Error fetching daily details:', error);
            document.getElementById('modal-body').innerHTML = `
                <div class="detail-card">
                    <h3>Error Loading Details</h3>
                    <p><strong>Error:</strong> ${error.message}</p>
                    <p><strong>Date:</strong> ${date}</p>
                    <p>Please check the browser console for more details or contact the system administrator.</p>
                    <button class="view-btn" onclick="showDailyDetails('${date}')">Retry</button>
                </div>
            `;
        });
}

function showWeeklyDetails(startDate, endDate) {
    document.getElementById('modal-title').textContent = 'Weekly Report Details - ' + 
        new Date(startDate).toLocaleDateString() + ' to ' + new Date(endDate).toLocaleDateString();
    
    // Show loading message
    document.getElementById('modal-body').innerHTML = '<div class="detail-card"><h3>Loading...</h3><p>Please wait while we fetch the report details.</p></div>';
    document.getElementById('reportModal').style.display = 'block';
    
    fetch('api/get_weekly_details.php?start=' + startDate + '&end=' + endDate)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displayWeeklyDetails(data);
        })
        .catch(error => {
            console.error('Error fetching weekly details:', error);
            showErrorModal('weekly', error.message, startDate, endDate);
        });
}

function showMonthlyDetails(startDate, endDate) {
    document.getElementById('modal-title').textContent = 'Monthly Report Details - ' + 
        new Date(startDate).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    
    // Show loading message
    document.getElementById('modal-body').innerHTML = '<div class="detail-card"><h3>Loading...</h3><p>Please wait while we fetch the report details.</p></div>';
    document.getElementById('reportModal').style.display = 'block';
    
    fetch('api/get_monthly_details.php?start=' + startDate + '&end=' + endDate)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displayMonthlyDetails(data);
        })
        .catch(error => {
            console.error('Error fetching monthly details:', error);
            showErrorModal('monthly', error.message, startDate, endDate);
        });
}

function showAnnualDetails(startDate, endDate) {
    document.getElementById('modal-title').textContent = 'Annual Report Details - ' + 
        new Date(startDate).getFullYear();
    
    // Show loading message
    document.getElementById('modal-body').innerHTML = '<div class="detail-card"><h3>Loading...</h3><p>Please wait while we fetch the report details.</p></div>';
    document.getElementById('reportModal').style.display = 'block';
    
    fetch('api/get_annual_details.php?start=' + startDate + '&end=' + endDate)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            displayAnnualDetails(data);
        })
        .catch(error => {
            console.error('Error fetching annual details:', error);
            showErrorModal('annual', error.message, startDate, endDate);
        });
}

function displayDailyDetails(data) {
    const modalBody = document.getElementById('modal-body');
    modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="metric-card">
                <div class="metric-value">${data.invoice_count || 0}</div>
                <div class="metric-label">Total Invoices</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.cash_in || 0)}</div>
                <div class="metric-label">Cash In</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.profit || 0)}</div>
                <div class="metric-label">Gross Profit</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.expenses || 0)}</div>
                <div class="metric-label">Total Expenses</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.bank_deposit || 0)}</div>
                <div class="metric-label">Bank Deposits</div>
            </div>
            <div class="metric-card">
                <div class="metric-value ${(data.net_profit || 0) >= 0 ? 'profit-positive' : 'profit-negative'}">
                    Rs. ${formatNumber(data.net_profit || 0)}
                </div>
                <div class="metric-label">Net Profit</div>
            </div>
        </div>
        
        <div class="detail-card">
            <h3><i class="fas fa-chart-breakdown"></i> Daily Performance Analysis</h3>
            <p><strong>Average Invoice Value:</strong> Rs. ${formatNumber(data.avg_invoice_value || 0)}</p>
            <p><strong>Profit Margin:</strong> ${data.cash_in > 0 ? ((data.profit / data.cash_in) * 100).toFixed(1) : 0}%</p>
            <p><strong>Expense Ratio:</strong> ${data.cash_in > 0 ? ((data.expenses / data.cash_in) * 100).toFixed(1) : 0}% of revenue</p>
        </div>
        
        ${data.top_products && data.top_products.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-star"></i> Top Selling Products</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.top_products.map(product => `
                        <tr>
                            <td>${product.product_name}</td>
                            <td>${product.qty_sold}</td>
                            <td>Rs. ${formatNumber(product.revenue)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
        
        ${data.expense_breakdown && Object.keys(data.expense_breakdown).length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-money-bill-wave"></i> Expense Breakdown</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Expense Type</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    ${Object.entries(data.expense_breakdown).map(([type, amount]) => `
                        <tr>
                            <td>${type}</td>
                            <td>Rs. ${formatNumber(amount)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
    `;
}

function displayWeeklyDetails(data) {
    const modalBody = document.getElementById('modal-body');
    modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="metric-card">
                <div class="metric-value">${data.invoice_count || 0}</div>
                <div class="metric-label">Total Invoices</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.cash_in || 0)}</div>
                <div class="metric-label">Weekly Cash In</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.profit || 0)}</div>
                <div class="metric-label">Weekly Gross Profit</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.expenses || 0)}</div>
                <div class="metric-label">Weekly Expenses</div>
            </div>
            <div class="metric-card">
                <div class="metric-value ${(data.net_profit || 0) >= 0 ? 'profit-positive' : 'profit-negative'}">
                    Rs. ${formatNumber(data.net_profit || 0)}
                </div>
                <div class="metric-label">Net Profit</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.avg_daily_revenue || 0)}</div>
                <div class="metric-label">Avg Daily Revenue</div>
            </div>
        </div>
        
        <div class="detail-card">
            <h3><i class="fas fa-calendar-week"></i> Weekly Performance Analysis</h3>
            <p><strong>Average Daily Revenue:</strong> Rs. ${formatNumber(data.avg_daily_revenue || 0)}</p>
            <p><strong>Average Daily Invoices:</strong> ${Math.round(data.avg_daily_invoices || 0)} invoices per day</p>
            <p><strong>Average Invoice Value:</strong> Rs. ${formatNumber(data.avg_invoice_value || 0)}</p>
        </div>
        
        ${data.daily_breakdown && data.daily_breakdown.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-calendar-day"></i> Daily Breakdown</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoices</th>
                        <th>Cash In</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.daily_breakdown.map(day => `
                        <tr>
                            <td>${new Date(day.invoice_date).toLocaleDateString()}</td>
                            <td>${day.daily_invoices}</td>
                            <td>Rs. ${formatNumber(day.daily_cash_in)}</td>
                            <td>Rs. ${formatNumber(day.daily_profit)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
        
        ${data.top_performing_days && data.top_performing_days.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-trophy"></i> Top Performing Days</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.top_performing_days.map(day => `
                        <tr>
                            <td>${new Date(day.invoice_date).toLocaleDateString()}</td>
                            <td>Rs. ${formatNumber(day.daily_revenue)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
    `;
}

function displayMonthlyDetails(data) {
    const modalBody = document.getElementById('modal-body');
    modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="metric-card">
                <div class="metric-value">${data.invoice_count || 0}</div>
                <div class="metric-label">Monthly Invoices</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.cash_in || 0)}</div>
                <div class="metric-label">Monthly Revenue</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.profit || 0)}</div>
                <div class="metric-label">Monthly Profit</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.expenses || 0)}</div>
                <div class="metric-label">Monthly Expenses</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">${data.unique_customers || 0}</div>
                <div class="metric-label">Unique Customers</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">${formatNumber(data.profit_margin || 0)}%</div>
                <div class="metric-label">Profit Margin</div>
            </div>
        </div>
        
        <div class="detail-card">
            <h3><i class="fas fa-calendar-alt"></i> Monthly Performance Summary</h3>
            <p><strong>Average Daily Revenue:</strong> Rs. ${formatNumber(data.avg_daily_revenue || 0)}</p>
            <p><strong>Average Orders per Customer:</strong> ${formatNumber(data.avg_orders_per_customer || 0)}</p>
            <p><strong>Expense Ratio:</strong> ${((data.expenses || 0) / Math.max(data.cash_in || 1, 1) * 100).toFixed(1)}% of revenue</p>
        </div>
        
        ${data.weekly_breakdown && data.weekly_breakdown.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-calendar-week"></i> Weekly Breakdown</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Week Period</th>
                        <th>Invoices</th>
                        <th>Cash In</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.weekly_breakdown.map(week => `
                        <tr>
                            <td>${new Date(week.week_start).toLocaleDateString()} - ${new Date(week.week_end).toLocaleDateString()}</td>
                            <td>${week.weekly_invoices}</td>
                            <td>Rs. ${formatNumber(week.weekly_cash_in)}</td>
                            <td>Rs. ${formatNumber(week.weekly_profit)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
        
        ${data.top_products && data.top_products.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-star"></i> Top Products (Monthly)</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.top_products.slice(0, 5).map(product => `
                        <tr>
                            <td>${product.product_name}</td>
                            <td>${product.qty_sold}</td>
                            <td>Rs. ${formatNumber(product.revenue)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
    `;
}

function displayAnnualDetails(data) {
    const modalBody = document.getElementById('modal-body');
    modalBody.innerHTML = `
        <div class="detail-grid">
            <div class="metric-card">
                <div class="metric-value">${data.invoice_count || 0}</div>
                <div class="metric-label">Annual Invoices</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.cash_in || 0)}</div>
                <div class="metric-label">Annual Revenue</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.profit || 0)}</div>
                <div class="metric-label">Annual Profit</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">Rs. ${formatNumber(data.expenses || 0)}</div>
                <div class="metric-label">Annual Expenses</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">${data.total_customers || 0}</div>
                <div class="metric-label">Total Customers</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">${formatNumber(data.profit_margin || 0)}%</div>
                <div class="metric-label">Profit Margin</div>
            </div>
        </div>
        
        <div class="detail-card">
            <h3><i class="fas fa-calendar"></i> Annual Business Overview (${data.year})</h3>
            <p><strong>Average Monthly Revenue:</strong> Rs. ${formatNumber((data.cash_in || 0) / 12)}</p>
            <p><strong>Average Orders per Customer:</strong> ${formatNumber(data.avg_orders_per_customer || 0)}</p>
            <p><strong>Customer Retention:</strong> Based on repeat order patterns</p>
        </div>
        
        ${data.quarterly_breakdown && data.quarterly_breakdown.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-chart-bar"></i> Quarterly Performance</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Quarter</th>
                        <th>Invoices</th>
                        <th>Cash In</th>
                        <th>Profit</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.quarterly_breakdown.map(quarter => `
                        <tr>
                            <td>Q${quarter.quarter} ${quarter.year}</td>
                            <td>${quarter.quarterly_invoices}</td>
                            <td>Rs. ${formatNumber(quarter.quarterly_cash_in)}</td>
                            <td>Rs. ${formatNumber(quarter.quarterly_profit)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
        
        ${data.top_performing_months && data.top_performing_months.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-trophy"></i> Top Performing Months</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.top_performing_months.map(month => `
                        <tr>
                            <td>${month.month_name}</td>
                            <td>Rs. ${formatNumber(month.monthly_revenue)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
        
        ${data.top_products && data.top_products.length > 0 ? `
        <div class="detail-card">
            <h3><i class="fas fa-star"></i> Top Products (Annual)</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    ${data.top_products.slice(0, 8).map(product => `
                        <tr>
                            <td>${product.product_name}</td>
                            <td>${product.qty_sold}</td>
                            <td>Rs. ${formatNumber(product.revenue)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
    `;
}

function showErrorModal(reportType, errorMessage, startDate, endDate) {
    document.getElementById('modal-body').innerHTML = `
        <div class="detail-card">
            <h3><i class="fas fa-exclamation-triangle"></i> Error Loading ${reportType} Report Details</h3>
            <p><strong>Error:</strong> ${errorMessage}</p>
            <p><strong>Report Type:</strong> ${reportType}</p>
            ${startDate ? `<p><strong>Date Range:</strong> ${startDate} ${endDate ? 'to ' + endDate : ''}</p>` : ''}
            <p>Please check the following:</p>
            <ul>
                <li>Make sure you are logged in as an admin</li>
                <li>Check if the database connection is working</li>
                <li>Verify the API endpoint is accessible</li>
                <li>Check browser console for detailed error messages</li>
            </ul>
            <div style="margin-top: 20px;">
                <button class="view-btn" onclick="closeModal()">Close</button>
                ${reportType === 'daily' ? 
                    `<button class="view-btn" onclick="showDailyDetails('${startDate}')" style="margin-left: 10px;">Retry</button>` :
                    reportType === 'weekly' ?
                    `<button class="view-btn" onclick="showWeeklyDetails('${startDate}', '${endDate}')" style="margin-left: 10px;">Retry</button>` :
                    reportType === 'monthly' ?
                    `<button class="view-btn" onclick="showMonthlyDetails('${startDate}', '${endDate}')" style="margin-left: 10px;">Retry</button>` :
                    `<button class="view-btn" onclick="showAnnualDetails('${startDate}', '${endDate}')" style="margin-left: 10px;">Retry</button>`
                }
            </div>
        </div>
    `;
    document.getElementById('reportModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('reportModal').style.display = 'none';
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('reportModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Set default tab
    document.querySelector('.tab-button').click();
});
</script>