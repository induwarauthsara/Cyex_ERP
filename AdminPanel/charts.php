<?php
include 'nav.php';

// Function to format currency
function formatCurrency($amount) {
    return number_format($amount, 2);
}
?>

<title>Analytics Dashboard | <?php echo $GLOBALS['ERP_COMPANY_NAME']; ?> </title>

<!-- Chart.js (lightweight alternative) with plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/luxon@3.3.0/build/global/luxon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.3.1/dist/chartjs-adapter-luxon.umd.min.js"></script>

<!-- Date Range Picker -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<style>
/* ============================================
   ANALYTICS DASHBOARD STYLES
   Modern Dark Theme with Glassmorphism
   ============================================ */

:root {
    --primary: #6366f1;
    --primary-light: #818cf8;
    --primary-dark: #4f46e5;
    --secondary: #22d3ee;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    
    --bg-dark: #0f172a;
    --bg-card: rgba(30, 41, 59, 0.8);
    --bg-card-hover: rgba(51, 65, 85, 0.9);
    --border-color: rgba(148, 163, 184, 0.2);
    
    --text-primary: #f1f5f9;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;
    
    --gradient-primary: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
    --gradient-success: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    --gradient-danger: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
    --gradient-info: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
    --gradient-cyan: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
    
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    --shadow-glow: 0 0 40px rgba(99, 102, 241, 0.3);
}

.analytics-container {
    padding: 20px;
    max-width: 1800px;
    margin: 0 auto;
    color: var(--text-primary);
}

/* Header Section */
.analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid var(--border-color);
}

.analytics-header h1 {
    margin: 0;
    font-size: 28px;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: flex;
    align-items: center;
    gap: 12px;
}

.analytics-header h1 i {
    -webkit-text-fill-color: var(--primary);
}

.header-controls {
    display: flex;
    gap: 15px;
    align-items: center;
}

/* Date Range Picker Styling */
.date-range-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

#dateRangePicker {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    padding: 12px 20px;
    border-radius: 10px;
    color: var(--text-primary);
    cursor: pointer;
    font-size: 14px;
    min-width: 280px;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

#dateRangePicker:hover {
    border-color: var(--primary);
    box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
}

#dateRangePicker i {
    color: var(--primary);
}

.quick-filters {
    display: flex;
    gap: 8px;
}

.quick-filter-btn {
    padding: 10px 16px;
    border: 1px solid var(--border-color);
    background: var(--bg-card);
    color: var(--text-secondary);
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.3s ease;
}

.quick-filter-btn:hover,
.quick-filter-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
    transform: translateY(-2px);
}

.refresh-btn {
    padding: 12px;
    background: var(--gradient-primary);
    border: none;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.refresh-btn:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-glow);
}

/* KPI Cards Grid */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kpi-card {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.kpi-card:hover::before {
    opacity: 1;
}

.kpi-card.profit::before { background: var(--gradient-success); }
.kpi-card.sales::before { background: var(--gradient-info); }
.kpi-card.expense::before { background: var(--gradient-danger); }
.kpi-card.salary::before { background: var(--gradient-warning); }
.kpi-card.stock::before { background: var(--gradient-cyan); }
.kpi-card.printer::before { background: var(--gradient-primary); }

.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-light);
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}

.kpi-icon.profit { background: var(--gradient-success); color: white; }
.kpi-icon.sales { background: var(--gradient-info); color: white; }
.kpi-icon.expense { background: var(--gradient-danger); color: white; }
.kpi-icon.salary { background: var(--gradient-warning); color: white; }
.kpi-icon.stock { background: var(--gradient-cyan); color: white; }
.kpi-icon.printer { background: var(--gradient-primary); color: white; }

.kpi-trend {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.kpi-trend.up {
    background: rgba(16, 185, 129, 0.2);
    color: var(--success);
}

.kpi-trend.down {
    background: rgba(239, 68, 68, 0.2);
    color: var(--danger);
}

.kpi-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 5px;
}

.kpi-label {
    color: var(--text-secondary);
    font-size: 14px;
}

.kpi-footer {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: var(--text-muted);
}

/* Chart Grid Layout */
.charts-grid {
    display: grid;
    gap: 25px;
    margin-bottom: 30px;
}

.chart-row {
    display: grid;
    gap: 25px;
}

.chart-row.two-cols {
    grid-template-columns: repeat(2, 1fr);
}

.chart-row.three-cols {
    grid-template-columns: repeat(3, 1fr);
}

.chart-row.flex-cols {
    grid-template-columns: 2fr 1fr;
}

@media (max-width: 1200px) {
    .chart-row.two-cols,
    .chart-row.three-cols,
    .chart-row.flex-cols {
        grid-template-columns: 1fr;
    }
}

.chart-card {
    background: var(--bg-card);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: all 0.3s ease;
}

.chart-card:hover {
    border-color: var(--primary-light);
    box-shadow: var(--shadow-lg);
}

.chart-header {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chart-header h3 {
    margin: 0;
    font-size: 16px;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 10px;
}

.chart-header h3 i {
    color: var(--primary);
}

.chart-actions {
    display: flex;
    gap: 8px;
}

.chart-action-btn {
    padding: 6px 12px;
    background: transparent;
    border: 1px solid var(--border-color);
    color: var(--text-secondary);
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
}

.chart-action-btn:hover,
.chart-action-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.chart-body {
    padding: 20px;
    position: relative;
    min-height: 300px;
}

.chart-body.large {
    min-height: 400px;
}

/* Tables Styling */
.data-table-container {
    overflow-x: auto;
}

.analytics-table {
    width: 100%;
    border-collapse: collapse;
}

.analytics-table th {
    background: rgba(99, 102, 241, 0.1);
    padding: 14px 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--border-color);
}

.analytics-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-color);
    font-size: 14px;
    color: var(--text-primary);
}

.analytics-table tbody tr {
    transition: background 0.2s ease;
}

.analytics-table tbody tr:hover {
    background: rgba(99, 102, 241, 0.05);
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.product-avatar {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 14px;
}

.rank-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
}

.rank-badge.gold {
    background: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
    color: #333;
}

.rank-badge.silver {
    background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
    color: #333;
}

.rank-badge.bronze {
    background: linear-gradient(135deg, #cd7f32 0%, #a0522d 100%);
    color: white;
}

.category-tag {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 500;
}

.amount-positive {
    color: var(--success);
    font-weight: 600;
}

.amount-negative {
    color: var(--danger);
    font-weight: 600;
}

/* Progress Bars */
.progress-bar {
    width: 100%;
    height: 8px;
    background: rgba(148, 163, 184, 0.2);
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.5s ease;
}

.progress-fill.success { background: var(--gradient-success); }
.progress-fill.warning { background: var(--gradient-warning); }
.progress-fill.danger { background: var(--gradient-danger); }
.progress-fill.info { background: var(--gradient-info); }
.progress-fill.primary { background: var(--gradient-primary); }

/* Loading State */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

/* Full Page Loading Overlay */
.page-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    flex-direction: column;
    gap: 20px;
}

.page-loading-overlay.active {
    display: flex;
}

.loading-spinner {
    width: 64px;
    height: 64px;
    border: 5px solid var(--border-color);
    border-top-color: var(--primary);
    border-right-color: var(--primary-light);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

.loading-text {
    color: var(--text-primary);
    font-size: 16px;
    font-weight: 500;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

/* Responsive Adjustments */
@media (max-width: 1024px) {
    .analytics-header {
        flex-direction: column;
        gap: 20px;
        align-items: flex-start;
    }
    
    .header-controls {
        width: 100%;
        flex-wrap: wrap;
    }
    
    .quick-filters {
        flex-wrap: wrap;
    }
    
    .kpi-grid {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}

@media (max-width: 768px) {
    .analytics-container {
        padding: 10px;
    }
    
    .kpi-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .kpi-value {
        font-size: 22px;
    }
    
    #dateRangePicker {
        min-width: 100%;
    }
}

/* Stats Mini Cards */
.stats-mini-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.stat-mini-card {
    background: rgba(99, 102, 241, 0.1);
    padding: 15px;
    border-radius: 10px;
    text-align: center;
}

.stat-mini-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
}

.stat-mini-label {
    font-size: 11px;
    color: var(--text-muted);
    margin-top: 5px;
}

/* Legend Custom Styling */
.chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 15px;
    justify-content: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--text-secondary);
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
}

/* Date Range Picker Custom Theme */
.daterangepicker {
    background-color: var(--bg-card) !important;
    border: 1px solid var(--border-color) !important;
    border-radius: 12px !important;
    color: var(--text-primary) !important;
}

.daterangepicker::before,
.daterangepicker::after {
    display: none !important;
}

.daterangepicker .calendar-table {
    background: transparent !important;
    border: none !important;
}

.daterangepicker td.available:hover,
.daterangepicker th.available:hover {
    background-color: var(--primary) !important;
    color: white !important;
}

.daterangepicker td.active,
.daterangepicker td.active:hover {
    background-color: var(--primary) !important;
    color: white !important;
}

.daterangepicker .ranges li {
    color: var(--text-primary) !important;
}

.daterangepicker .ranges li:hover {
    background-color: var(--primary) !important;
}

.daterangepicker .ranges li.active {
    background-color: var(--primary) !important;
    color: white !important;
}

.daterangepicker .drp-buttons .btn {
    border-radius: 6px !important;
}

.daterangepicker .drp-buttons .btn-primary {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
}

.daterangepicker td,
.daterangepicker th {
    color: var(--text-primary) !important;
}

.daterangepicker td.off {
    color: var(--text-muted) !important;
}

.daterangepicker .calendar-table thead th {
    color: var(--text-secondary) !important;
}

.daterangepicker select.monthselect,
.daterangepicker select.yearselect {
    background: var(--bg-card) !important;
    color: var(--text-primary) !important;
    border: 1px solid var(--border-color) !important;
}
</style>

<!-- Full Page Loading Overlay -->
<div class="page-loading-overlay" id="pageLoadingOverlay">
    <div class="loading-spinner"></div>
    <div class="loading-text">Loading data...</div>
</div>

<div class="analytics-container">
    <!-- Header Section -->
    <div class="analytics-header">
        <h1><i class="fas fa-chart-pie"></i> Analytics Dashboard</h1>
        <div class="header-controls">
            <div class="quick-filters">
                <button class="quick-filter-btn" data-range="today">Today</button>
                <button class="quick-filter-btn" data-range="yesterday">Yesterday</button>
                <button class="quick-filter-btn" data-range="week">This Week</button>
                <button class="quick-filter-btn active" data-range="month">This Month</button>
                <button class="quick-filter-btn" data-range="last_month">Last Month</button>
                <button class="quick-filter-btn" data-range="quarter">Last 3 Months</button>
                <button class="quick-filter-btn" data-range="year">This Year</button>
                <button class="quick-filter-btn" data-range="last_year">Last Year</button>
            </div>
            <div id="dateRangePicker">
                <i class="fas fa-calendar-alt"></i>
                <span id="dateRangeText">Select Date Range</span>
            </div>
            <button class="refresh-btn" onclick="refreshAllCharts()" title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>
    
    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card profit">
            <div class="kpi-header">
                <div class="kpi-icon profit"><i class="fas fa-chart-line"></i></div>
                <span class="kpi-trend up" id="profitTrend">+0%</span>
            </div>
            <div class="kpi-value" id="totalProfit">Rs. 0.00</div>
            <div class="kpi-label">Total Profit</div>
            <div class="kpi-footer">
                <span>Gross: <strong id="grossProfit">Rs. 0</strong></span>
                <span>Net: <strong id="netProfit">Rs. 0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card sales">
            <div class="kpi-header">
                <div class="kpi-icon sales"><i class="fas fa-cash-register"></i></div>
                <span class="kpi-trend up" id="salesTrend">+0%</span>
            </div>
            <div class="kpi-value" id="totalCashSales">Rs. 0.00</div>
            <div class="kpi-label">Total Cash Sales</div>
            <div class="kpi-footer">
                <span>Invoices: <strong id="invoiceCount">0</strong></span>
                <span>Avg: <strong id="avgInvoice">Rs. 0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card expense">
            <div class="kpi-header">
                <div class="kpi-icon expense"><i class="fas fa-receipt"></i></div>
                <span class="kpi-trend down" id="expenseTrend">+0%</span>
            </div>
            <div class="kpi-value" id="totalExpenses">Rs. 0.00</div>
            <div class="kpi-label">Total Expenses</div>
            <div class="kpi-footer">
                <span>Paid: <strong id="paidExpenses">Rs. 0</strong></span>
                <span>Pending: <strong id="pendingExpenses">Rs. 0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card salary">
            <div class="kpi-header">
                <div class="kpi-icon salary"><i class="fas fa-wallet"></i></div>
                <span class="kpi-trend" id="salaryTrend">-</span>
            </div>
            <div class="kpi-value" id="totalSalary">Rs. 0.00</div>
            <div class="kpi-label">Salary Payments</div>
            <div class="kpi-footer">
                <span>Employees: <strong id="employeesPaid">0</strong></span>
                <span>Payments: <strong id="salaryPayments">0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card stock">
            <div class="kpi-header">
                <div class="kpi-icon stock"><i class="fas fa-boxes"></i></div>
                <span class="kpi-trend down" id="stockTrend">-0 items</span>
            </div>
            <div class="kpi-value" id="stockReduced">0 Items</div>
            <div class="kpi-label">Stock Reduced</div>
            <div class="kpi-footer">
                <span>Products: <strong id="productsMovement">0</strong></span>
                <span>Qty: <strong id="totalQtyMoved">0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card printer">
            <div class="kpi-header">
                <div class="kpi-icon printer"><i class="fas fa-print"></i></div>
                <span class="kpi-trend" id="printerTrend">-</span>
            </div>
            <div class="kpi-value" id="totalPrints">0 Prints</div>
            <div class="kpi-label">Printer Usage</div>
            <div class="kpi-footer">
                <span>Cost: <strong id="printCost">Rs. 0</strong></span>
                <span>Revenue: <strong id="printRevenue">Rs. 0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card expense">
            <div class="kpi-header">
                <div class="kpi-icon expense"><i class="fas fa-truck"></i></div>
                <span class="kpi-trend" id="suppliersTrend">-</span>
            </div>
            <div class="kpi-value" id="supplierCredit">Rs. 0.00</div>
            <div class="kpi-label">Suppliers Credit Balance</div>
            <div class="kpi-footer">
                <span>Total: <strong id="totalSuppliers">0</strong></span>
                <span>With Credit: <strong id="suppliersWithCredit">0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card sales">
            <div class="kpi-header">
                <div class="kpi-icon sales"><i class="fas fa-university"></i></div>
                <span class="kpi-trend up" id="bankTrend">+0%</span>
            </div>
            <div class="kpi-value" id="totalBankBalance">Rs. 0.00</div>
            <div class="kpi-label">Total Bank Balance</div>
            <div class="kpi-footer">
                <span>Accounts: <strong id="bankAccounts">0</strong></span>
                <span>Cash: <strong id="cashInHand">Rs. 0</strong></span>
            </div>
        </div>
        
        <div class="kpi-card profit">
            <div class="kpi-header">
                <div class="kpi-icon profit"><i class="fas fa-users"></i></div>
                <span class="kpi-trend" id="customersTrend">-</span>
            </div>
            <div class="kpi-value" id="totalCustomers">0</div>
            <div class="kpi-label">Total Customers</div>
            <div class="kpi-footer">
                <span>With Credit: <strong id="customersWithCredit">0</strong></span>
                <span>Credit: <strong id="customerCredit">Rs. 0</strong></span>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Income vs Expenses vs Profit (Stacked Cluster Bar Chart) -->
        <div class="chart-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-chart-bar"></i> Income, Expenses & Profit Overview</h3>
                </div>
                <div class="chart-body large">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Expenses by Category & Product Selling Trend -->
        <div class="chart-row two-cols">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-pie-chart"></i> Expenses by Category</h3>
                </div>
                <div class="chart-body">
                    <canvas id="expensesByCategoryChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-trending-up"></i> Product Sales Trend</h3>
                </div>
                <div class="chart-body">
                    <canvas id="productTrendChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Stock Reduction & Best Selling Products -->
        <div class="chart-row two-cols">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-cubes"></i> Stock Reduction</h3>
                </div>
                <div class="chart-body">
                    <canvas id="stockReductionChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-fire"></i> Best Selling Products</h3>
                </div>
                <div class="chart-body">
                    <div class="data-table-container">
                        <table class="analytics-table" id="bestSellingTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Rank</th>
                                    <th>Product</th>
                                    <th>Qty Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody id="bestSellingBody">
                                <!-- Data loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Most Profitable Products & Employee Performance -->
        <div class="chart-row two-cols">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-gem"></i> Most Profitable Products</h3>
                </div>
                <div class="chart-body">
                    <canvas id="profitableProductsChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-trophy"></i> Best Performance Employee</h3>
                </div>
                <div class="chart-body">
                    <div class="data-table-container">
                        <table class="analytics-table" id="employeePerformanceTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Rank</th>
                                    <th>Employee</th>
                                    <th>Sales</th>
                                    <th>Commission</th>
                                </tr>
                            </thead>
                            <tbody id="employeePerformanceBody">
                                <!-- Data loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Salary Payments & Printer Usage -->
        <div class="chart-row two-cols">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-money-bill-wave"></i> Salary Payments by Employee</h3>
                </div>
                <div class="chart-body">
                    <canvas id="salaryChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-print"></i> Printer Usage Statistics</h3>
                </div>
                <div class="chart-body">
                    <canvas id="printerUsageChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Fast Moving Products Table -->
        <div class="chart-row">
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-rocket"></i> Fast Moving Products</h3>
                </div>
                <div class="chart-body">
                    <div class="data-table-container">
                        <table class="analytics-table" id="fastMovingTable">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Initial Stock</th>
                                    <th>Sold</th>
                                    <th>Current Stock</th>
                                    <th>Velocity</th>
                                    <th>Days to Deplete</th>
                                </tr>
                            </thead>
                            <tbody id="fastMovingBody">
                                <!-- Data loaded via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- NEW BUSINESS ANALYTICS CHARTS -->
        <div class="chart-row">
            <!-- Suppliers Credit Balance -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-truck-loading"></i> Top Suppliers by Credit Balance</h3>
                </div>
                <div class="chart-body">
                    <canvas id="suppliersCreditChart"></canvas>
                </div>
            </div>
            <!-- GRN Trend -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-file-invoice"></i> GRN (Goods Received) Trend</h3>
                </div>
                <div class="chart-body">
                    <canvas id="grnTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-row">
            <!-- Bank Balance Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-university"></i> Bank Balance & Transactions</h3>
                </div>
                <div class="chart-body">
                    <canvas id="bankBalanceChart"></canvas>
                </div>
            </div>
            <!-- Customer Analytics -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3><i class="fas fa-user-friends"></i> Customer Credit & Volume</h3>
                </div>
                <div class="chart-body">
                    <canvas id="customersAnalysisChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================
// CHART CONFIGURATION & INITIALIZATION
// ============================================

// Global chart instances
let charts = {};

// Global date range state
let currentDateRange = {
    startDate: moment().startOf('month'),
    endDate: moment().endOf('month'),
    rangeType: 'month'
};

// Chart.js global defaults
Chart.defaults.color = '#94a3b8';
Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.1)';
Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";

// Color palette
const colors = {
    primary: '#6366f1',
    primaryLight: '#818cf8',
    secondary: '#22d3ee',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#3b82f6',
    purple: '#a855f7',
    pink: '#ec4899',
    cyan: '#06b6d4'
};

const gradients = {};

// Loading overlay functions
function showLoading() {
    document.getElementById('pageLoadingOverlay').classList.add('active');
}

function hideLoading() {
    document.getElementById('pageLoadingOverlay').classList.remove('active');
}

// ============================================
// DATE RANGE PICKER INITIALIZATION
// ============================================

$(function() {
    $('#dateRangePicker').daterangepicker({
        startDate: moment().startOf('month'),
        endDate: moment().endOf('month'),
        opens: 'left',
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
           'Last 3 Months': [moment().subtract(3, 'months').startOf('month'), moment()],
           'This Year': [moment().startOf('year'), moment()],
           'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
        }
    }, function(start, end, label) {
        updateDateRange(start, end, 'custom');
    });
    
    updateDateRangeDisplay();
});

// Quick filter buttons
document.querySelectorAll('.quick-filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.quick-filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const range = this.dataset.range;
        let start, end;
        
        switch(range) {
            case 'today':
                start = moment().startOf('day');
                end = moment().endOf('day');
                break;
            case 'yesterday':
                start = moment().subtract(1, 'days').startOf('day');
                end = moment().subtract(1, 'days').endOf('day');
                break;
            case 'week':
                start = moment().startOf('week');
                end = moment().endOf('week');
                break;
            case 'month':
                start = moment().startOf('month');
                end = moment().endOf('month');
                break;
            case 'last_month':
                start = moment().subtract(1, 'month').startOf('month');
                end = moment().subtract(1, 'month').endOf('month');
                break;
            case 'quarter':
                start = moment().subtract(3, 'months').startOf('month');
                end = moment();
                break;
            case 'year':
                start = moment().startOf('year');
                end = moment();
                break;
            case 'last_year':
                start = moment().subtract(1, 'year').startOf('year');
                end = moment().subtract(1, 'year').endOf('year');
                break;
        }
        
        updateDateRange(start, end, range);
    });
});

function updateDateRange(start, end, rangeType) {
    currentDateRange.startDate = start;
    currentDateRange.endDate = end;
    currentDateRange.rangeType = rangeType;
    
    // Update date picker display
    $('#dateRangePicker').data('daterangepicker').setStartDate(start);
    $('#dateRangePicker').data('daterangepicker').setEndDate(end);
    
    updateDateRangeDisplay();
    refreshAllCharts();
}

function updateDateRangeDisplay() {
    const start = currentDateRange.startDate;
    const end = currentDateRange.endDate;
    
    if (start.isSame(end, 'day')) {
        $('#dateRangeText').text(start.format('MMMM D, YYYY'));
    } else {
        $('#dateRangeText').text(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
    }
}

// ============================================
// API FETCH FUNCTIONS
// ============================================

async function fetchChartData(endpoint, params = {}) {
    const urlParams = new URLSearchParams({
        start_date: currentDateRange.startDate.format('YYYY-MM-DD'),
        end_date: currentDateRange.endDate.format('YYYY-MM-DD'),
        ...params
    });
    
    try {
        const response = await fetch(`/AdminPanel/api/charts/${endpoint}?${urlParams}`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();
        console.log(`Fetched ${endpoint}:`, data); // Debug logging
        return data;
    } catch (error) {
        console.error(`Error fetching ${endpoint}:`, error);
        return null;
    }
}

// ============================================
// KPI CARDS UPDATE
// ============================================

async function updateKPICards() {
    console.log('updateKPICards: Starting...');
    const data = await fetchChartData('kpi_summary.php');
    
    if (!data) {
        console.error('updateKPICards: Failed to fetch KPI data - data is null or undefined');
        return;
    }
    
    console.log('updateKPICards: Received data:', data);
    
    // Check if response has error
    if (data.success === false) {
        console.error('updateKPICards: API returned error:', data.error);
        alert('Error loading KPI data: ' + data.error);
        return;
    }
    
    // Check if all required data sections exist
    const requiredSections = ['profit', 'sales', 'expenses', 'salary', 'stock', 'printer', 'suppliers', 'bank', 'customers'];
    const missingSections = requiredSections.filter(section => !data[section]);
    
    if (missingSections.length > 0) {
        console.warn('updateKPICards: Missing data sections:', missingSections);
    }
    
    try {
        // Update Profit KPI
        if (data.profit) {
            console.log('Updating Profit KPI:', data.profit);
            document.getElementById('totalProfit').textContent = 'Rs. ' + formatNumber(data.profit.total || 0);
            document.getElementById('grossProfit').textContent = 'Rs. ' + formatNumber(data.profit.gross || 0);
            document.getElementById('netProfit').textContent = 'Rs. ' + formatNumber(data.profit.net || 0);
            updateTrend('profitTrend', data.profit.trend || 0);
        }
        
        // Update Sales KPI
        if (data.sales) {
            console.log('Updating Sales KPI:', data.sales);
            document.getElementById('totalCashSales').textContent = 'Rs. ' + formatNumber(data.sales.total || 0);
            document.getElementById('invoiceCount').textContent = data.sales.invoice_count || 0;
            document.getElementById('avgInvoice').textContent = 'Rs. ' + formatNumber(data.sales.average || 0);
            updateTrend('salesTrend', data.sales.trend || 0);
        }
        
        // Update Expenses KPI
        if (data.expenses) {
            console.log('Updating Expenses KPI:', data.expenses);
            document.getElementById('totalExpenses').textContent = 'Rs. ' + formatNumber(data.expenses.total || 0);
            document.getElementById('paidExpenses').textContent = 'Rs. ' + formatNumber(data.expenses.paid || 0);
            document.getElementById('pendingExpenses').textContent = 'Rs. ' + formatNumber(data.expenses.pending || 0);
            updateTrend('expenseTrend', data.expenses.trend || 0, true);
        }
        
        // Update Salary KPI
        if (data.salary) {
            console.log('Updating Salary KPI:', data.salary);
            document.getElementById('totalSalary').textContent = 'Rs. ' + formatNumber(data.salary.total || 0);
            document.getElementById('employeesPaid').textContent = data.salary.employees || 0;
            document.getElementById('salaryPayments').textContent = data.salary.payments || 0;
        }
        
        // Update Stock KPI
        if (data.stock) {
            console.log('Updating Stock KPI:', data.stock);
            document.getElementById('stockReduced').textContent = (data.stock.items_moved || 0) + ' Items';
            document.getElementById('productsMovement').textContent = data.stock.products || 0;
            document.getElementById('totalQtyMoved').textContent = data.stock.qty || 0;
            document.getElementById('stockTrend').textContent = '-' + (data.stock.items_moved || 0) + ' items';
        }
        
        // Update Printer KPI
        if (data.printer) {
            console.log('Updating Printer KPI:', data.printer);
            document.getElementById('totalPrints').textContent = (data.printer.total_prints || 0) + ' Prints';
            document.getElementById('printCost').textContent = 'Rs. ' + formatNumber(data.printer.cost || 0);
            document.getElementById('printRevenue').textContent = 'Rs. ' + formatNumber(data.printer.revenue || 0);
        }
        
        // Update Suppliers KPI
        if (data.suppliers) {
            console.log('Updating Suppliers KPI:', data.suppliers);
            document.getElementById('supplierCredit').textContent = 'Rs. ' + formatNumber(data.suppliers.credit_balance || 0);
            document.getElementById('totalSuppliers').textContent = data.suppliers.total || 0;
            document.getElementById('suppliersWithCredit').textContent = data.suppliers.with_credit || 0;
        }
        
        // Update Bank Balance KPI
        if (data.bank) {
            console.log('Updating Bank KPI:', data.bank);
            document.getElementById('totalBankBalance').textContent = 'Rs. ' + formatNumber(data.bank.total_balance || 0);
            document.getElementById('bankAccounts').textContent = data.bank.accounts || 0;
            document.getElementById('cashInHand').textContent = 'Rs. ' + formatNumber(data.bank.cash_in_hand || 0);
            if (data.bank.trend !== undefined) {
                updateTrend('bankTrend', data.bank.trend || 0);
            }
        }
        
        // Update Customers KPI
        if (data.customers) {
            console.log('Updating Customers KPI:', data.customers);
            document.getElementById('totalCustomers').textContent = data.customers.total || 0;
            document.getElementById('customersWithCredit').textContent = data.customers.with_credit || 0;
            document.getElementById('customerCredit').textContent = 'Rs. ' + formatNumber(data.customers.credit_amount || 0);
        }
        
        console.log('updateKPICards: All KPI cards updated successfully');
        
    } catch (error) {
        console.error('updateKPICards: Error updating DOM elements:', error);
        alert('Error updating KPI cards: ' + error.message);
    }
}

function updateTrend(elementId, value, inverse = false) {
    const element = document.getElementById(elementId);
    const isPositive = value >= 0;
    const displayPositive = inverse ? !isPositive : isPositive;
    
    element.className = 'kpi-trend ' + (displayPositive ? 'up' : 'down');
    element.textContent = (value >= 0 ? '+' : '') + value.toFixed(1) + '%';
}

// ============================================
// CHART INITIALIZATION FUNCTIONS
// ============================================

async function initFinancialChart() {
    const ctx = document.getElementById('financialChart').getContext('2d');
    const data = await fetchChartData('financial_overview.php', { view: 'daily' });
    
    if (charts.financial) charts.financial.destroy();
    
    charts.financial = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.labels || [],
            datasets: [
                {
                    label: 'Income',
                    data: data?.income || [],
                    backgroundColor: colors.success,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Expenses',
                    data: data?.expenses || [],
                    backgroundColor: colors.danger,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7
                },
                {
                    label: 'Profit',
                    data: data?.profit || [],
                    backgroundColor: colors.primary,
                    borderRadius: 4,
                    barPercentage: 0.8,
                    categoryPercentage: 0.7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleColor: '#f1f5f9',
                    bodyColor: '#94a3b8',
                    borderColor: 'rgba(148, 163, 184, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rs. ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + formatNumber(value);
                        }
                    }
                }
            }
        }
    });
}

async function initExpensesByCategoryChart() {
    const ctx = document.getElementById('expensesByCategoryChart').getContext('2d');
    const data = await fetchChartData('expenses_by_category.php');
    
    if (charts.expensesByCategory) charts.expensesByCategory.destroy();
    
    const categoryColors = data?.colors || [
        colors.danger, colors.warning, colors.info, colors.success,
        colors.purple, colors.pink, colors.cyan, colors.primary
    ];
    
    charts.expensesByCategory = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data?.labels || [],
            datasets: [{
                data: data?.values || [],
                backgroundColor: categoryColors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        generateLabels: function(chart) {
                            const dataset = chart.data.datasets[0];
                            const total = dataset.data.reduce((a, b) => a + b, 0);
                            return chart.data.labels.map((label, i) => ({
                                text: `${label} (${((dataset.data[i] / total) * 100).toFixed(1)}%)`,
                                fillStyle: dataset.backgroundColor[i],
                                hidden: false,
                                index: i
                            }));
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return `Rs. ${formatNumber(context.parsed)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
}

async function initProductTrendChart() {
    const ctx = document.getElementById('productTrendChart').getContext('2d');
    const data = await fetchChartData('product_trend.php');
    
    if (charts.productTrend) charts.productTrend.destroy();
    
    charts.productTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data?.labels || [],
            datasets: [{
                label: 'Products Sold',
                data: data?.values || [],
                borderColor: colors.primary,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)'
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                }
            }
        }
    });
}

async function initStockReductionChart() {
    const ctx = document.getElementById('stockReductionChart').getContext('2d');
    const data = await fetchChartData('stock_reduction.php');
    
    if (charts.stockReduction) charts.stockReduction.destroy();
    
    charts.stockReduction = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.products || [],
            datasets: [{
                label: 'Quantity Reduced',
                data: data?.quantities || [],
                backgroundColor: colors.cyan,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

async function initProfitableProductsChart() {
    const ctx = document.getElementById('profitableProductsChart').getContext('2d');
    const data = await fetchChartData('profitable_products.php');
    
    if (charts.profitableProducts) charts.profitableProducts.destroy();
    
    charts.profitableProducts = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.products || [],
            datasets: [{
                label: 'Profit (Rs.)',
                data: data?.profits || [],
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(34, 211, 238, 0.8)',
                    'rgba(6, 182, 212, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(244, 63, 94, 0.8)'
                ],
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Profit: Rs. ' + formatNumber(context.parsed.x);
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + formatNumber(value);
                        }
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

async function initSalaryChart() {
    const ctx = document.getElementById('salaryChart').getContext('2d');
    const data = await fetchChartData('salary_payments.php');
    
    if (charts.salary) charts.salary.destroy();
    
    charts.salary = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.employees || [],
            datasets: [{
                label: 'Salary Paid',
                data: data?.amounts || [],
                backgroundColor: colors.warning,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Paid: Rs. ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.1)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rs. ' + formatNumber(value);
                        }
                    }
                }
            }
        }
    });
}

async function initPrinterUsageChart() {
    const ctx = document.getElementById('printerUsageChart').getContext('2d');
    const data = await fetchChartData('printer_usage.php');
    
    if (charts.printerUsage) charts.printerUsage.destroy();
    
    charts.printerUsage = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data?.printers || [],
            datasets: [{
                data: data?.counts || [],
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.purple
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                }
            }
        }
    });
}

async function initSuppliersCreditChart() {
    const ctx = document.getElementById('suppliersCreditChart').getContext('2d');
    const data = await fetchChartData('suppliers_credit.php');
    
    if (charts.suppliersCredit) charts.suppliersCredit.destroy();
    
    charts.suppliersCredit = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.suppliers || [],
            datasets: [{
                label: 'Credit Balance (Rs.)',
                data: data?.balances || [],
                backgroundColor: data?.colors || colors.danger,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Balance: Rs. ' + formatNumber(context.parsed.x);
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { callback: value => 'Rs. ' + formatNumber(value) }
                }
            }
        }
    });
}

async function initGrnTrendChart() {
    const ctx = document.getElementById('grnTrendChart').getContext('2d');
    const data = await fetchChartData('grn_trend.php');
    
    if (charts.grnTrend) charts.grnTrend.destroy();
    
    charts.grnTrend = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data?.labels || [],
            datasets: [
                {
                    label: 'GRN Total (Rs.)',
                    data: data?.totals || [],
                    borderColor: colors.info,
                    backgroundColor: 'rgba(57, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                },
                {
                    label: 'GRN Count',
                    data: data?.counts || [],
                    borderColor: colors.purple,
                    borderDash: [5, 5],
                    tension: 0.4,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            if (context.datasetIndex === 0) return 'Total: Rs. ' + formatNumber(context.parsed.y);
                            return 'Count: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { callback: value => 'Rs. ' + formatNumber(value) }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { beginAtZero: true }
                }
            }
        }
    });
}

async function initBankBalanceChart() {
    const ctx = document.getElementById('bankBalanceChart').getContext('2d');
    const data = await fetchChartData('bank_balance_trend.php');
    
    if (charts.bankBalance) charts.bankBalance.destroy();
    
    charts.bankBalance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.accounts || [],
            datasets: [{
                label: 'Current Balance (Rs.)',
                data: data?.balances || [],
                backgroundColor: data?.colors || colors.primary,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Balance: Rs. ' + formatNumber(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { callback: value => 'Rs. ' + formatNumber(value) }
                }
            }
        }
    });
}

async function initCustomersAnalysisChart() {
    const ctx = document.getElementById('customersAnalysisChart').getContext('2d');
    const data = await fetchChartData('customers_analysis.php');
    
    if (charts.customersAnalysis) charts.customersAnalysis.destroy();
    
    charts.customersAnalysis = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data?.credit?.customers || [],
            datasets: [
                {
                    label: 'Outstanding Credit (Rs.)',
                    data: data?.credit?.amounts || [],
                    backgroundColor: colors.danger + 'bb',
                    borderRadius: 4,
                    order: 1
                },
                {
                    label: 'Purchase Volume (Selected Period)',
                    data: data?.top_buyers?.volumes || [],
                    backgroundColor: colors.success + 'bb',
                    borderRadius: 4,
                    order: 2
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rs. ' + formatNumber(context.parsed.x);
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { callback: value => 'Rs. ' + formatNumber(value) }
                }
            }
        }
    });
}

async function updateBestSellingTable() {
    const data = await fetchChartData('best_selling.php');
    const tbody = document.getElementById('bestSellingBody');
    
    if (!data || !data.products) {
        tbody.innerHTML = '<tr><td colspan="4" class="empty-state"><i class="fas fa-inbox"></i><p>No data available</p></td></tr>';
        return;
    }
    
    tbody.innerHTML = data.products.map((product, index) => {
        const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
        return `
            <tr>
                <td><span class="rank-badge ${rankClass}">${index + 1}</span></td>
                <td>
                    <div class="product-cell">
                        <div class="product-avatar">${product.name.charAt(0).toUpperCase()}</div>
                        <span>${product.name}</span>
                    </div>
                </td>
                <td><strong>${product.qty_sold}</strong></td>
                <td class="amount-positive">Rs. ${formatNumber(product.revenue)}</td>
            </tr>
        `;
    }).join('');
}

async function updateEmployeePerformanceTable() {
    const data = await fetchChartData('employee_performance.php');
    const tbody = document.getElementById('employeePerformanceBody');
    
    if (!data || !data.employees) {
        tbody.innerHTML = '<tr><td colspan="4" class="empty-state"><i class="fas fa-users"></i><p>No data available</p></td></tr>';
        return;
    }
    
    tbody.innerHTML = data.employees.map((employee, index) => {
        const rankClass = index === 0 ? 'gold' : index === 1 ? 'silver' : index === 2 ? 'bronze' : '';
        return `
            <tr>
                <td><span class="rank-badge ${rankClass}">${index + 1}</span></td>
                <td>
                    <div class="product-cell">
                        <div class="product-avatar">${employee.name.charAt(0).toUpperCase()}</div>
                        <span>${employee.name}</span>
                    </div>
                </td>
                <td class="amount-positive">Rs. ${formatNumber(employee.sales)}</td>
                <td>Rs. ${formatNumber(employee.commission)}</td>
            </tr>
        `;
    }).join('');
}

async function updateFastMovingTable() {
    const data = await fetchChartData('fast_moving.php');
    const tbody = document.getElementById('fastMovingBody');
    
    if (!data || !data.products) {
        tbody.innerHTML = '<tr><td colspan="8" class="empty-state"><i class="fas fa-rocket"></i><p>No data available</p></td></tr>';
        return;
    }
    
    tbody.innerHTML = data.products.map((product, index) => {
        const velocityColor = product.velocity >= 5 ? 'success' : product.velocity >= 2 ? 'warning' : 'info';
        return `
            <tr>
                <td><span class="rank-badge ${index < 3 ? ['gold','silver','bronze'][index] : ''}">${index + 1}</span></td>
                <td>${product.name}</td>
                <td><span class="category-tag" style="background: rgba(99, 102, 241, 0.2); color: ${colors.primary}">${product.category || 'N/A'}</span></td>
                <td>${product.initial_stock}</td>
                <td><strong>${product.sold}</strong></td>
                <td>${product.current_stock}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="progress-bar" style="width: 80px;">
                            <div class="progress-fill ${velocityColor}" style="width: ${Math.min(product.velocity * 10, 100)}%"></div>
                        </div>
                        <span>${product.velocity.toFixed(1)}/day</span>
                    </div>
                </td>
                <td>${product.days_to_deplete > 0 ? product.days_to_deplete + ' days' : 'Out of Stock'}</td>
            </tr>
        `;
    }).join('');
}

// ============================================
// HELPER FUNCTIONS
// ============================================

function formatNumber(num) {
    if (num === null || num === undefined) return '0.00';
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// ============================================
// MAIN REFRESH FUNCTION
// ============================================

async function refreshAllCharts() {
    // Show loading overlay
    showLoading();
    const refreshBtn = document.querySelector('.refresh-btn i');
    refreshBtn.classList.add('fa-spin');
    
    try {
        await Promise.all([
            updateKPICards(),
            initFinancialChart(),
            initExpensesByCategoryChart(),
            initProductTrendChart(),
            initStockReductionChart(),
            initProfitableProductsChart(),
            initSalaryChart(),
            initPrinterUsageChart(),
            initSuppliersCreditChart(),
            initGrnTrendChart(),
            initBankBalanceChart(),
            initCustomersAnalysisChart(),
            updateBestSellingTable(),
            updateEmployeePerformanceTable(),
            updateFastMovingTable()
        ]);
    } catch (error) {
        console.error('Error refreshing charts:', error);
    } finally {
        refreshBtn.classList.remove('fa-spin');
        // Hide loading overlay after a brief delay for smooth transition
        setTimeout(hideLoading, 100);
    }
}

// ============================================
// INITIALIZE ON DOM LOAD
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    refreshAllCharts();
});
</script>
