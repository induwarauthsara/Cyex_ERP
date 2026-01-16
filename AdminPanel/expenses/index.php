<?php
/**
 * Expenses Management Dashboard
 * Main interface for tracking and managing business expenses
 * 
 * @package SrijayaERP
 * @version 1.0
 */

require_once __DIR__ . '/../nav.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses Management - <?php echo $GLOBALS['ERP_COMPANY_NAME']; ?> </title>
    <link rel="stylesheet" href="../acp.css">
    <link rel="stylesheet" href="expenses.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="expenses-container">
        <!-- Dashboard Header -->
        <div class="expenses-header">
            <div class="header-left">
                <h2><i class="fas fa-receipt"></i> Expenses Management</h2>
                <p class="subtitle">Track and manage all business expenses</p>
            </div>
            <div class="header-right">
                <button class="btn btn-primary" onclick="openAddExpenseModal()">
                    <i class="fas fa-plus"></i> Add Expense
                </button>
                <button class="btn btn-secondary" onclick="openCategoryModal()">
                    <i class="fas fa-tags"></i> Manage Categories
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards" id="summaryCards">
            <div class="card card-current-month">
                <div class="card-icon">
                    <i class="fas fa-calendar-days"></i>
                </div>
                <div class="card-content">
                    <h3>Current Month</h3>
                    <p class="amount" id="currentMonthAmount">Loading...</p>
                    <span class="trend" id="monthTrend"></span>
                </div>
            </div>

            <div class="card card-year">
                <div class="card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="card-content">
                    <h3>Year to Date</h3>
                    <p class="amount" id="yearAmount">Loading...</p>
                    <span class="count" id="yearCount"></span>
                </div>
            </div>

            <div class="card card-category">
                <div class="card-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="card-content">
                    <h3>Top Category</h3>
                    <p class="category-name" id="topCategoryName">Loading...</p>
                    <span class="category-amount" id="topCategoryAmount"></span>
                </div>
            </div>

            <div class="card card-pending">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3>Pending Payments</h3>
                    <p class="amount" id="pendingAmount">Loading...</p>
                    <span class="count" id="pendingCount"></span>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="tabs-container">
            <div class="tabs">
                <button class="tab-btn active" data-tab="all-expenses">
                    <i class="fas fa-list"></i> All Expenses
                </button>
                <button class="tab-btn" data-tab="recurring">
                    <i class="fas fa-sync"></i> Recurring Expenses
                </button>
                <button class="tab-btn" data-tab="analytics">
                    <i class="fas fa-chart-line"></i> Analytics
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content active" id="all-expenses">
            <!-- Filters -->
            <div class="filters-section" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
                <div class="filter-group" style="flex: 0 0 auto;">
                    <label>Date Range:</label>
                    <div style="display: flex; gap: 5px; align-items: center;">
                        <input type="date" id="filterStartDate" class="filter-input">
                        <span>to</span>
                        <input type="date" id="filterEndDate" class="filter-input">
                    </div>
                </div>
                <div class="filter-group" style="flex: 0 0 auto;">
                    <label>Category:</label>
                    <select id="filterCategory" class="filter-input">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 0 0 auto;">
                    <label>Status:</label>
                    <select id="filterStatus" class="filter-input">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
                <div class="filter-group" style="flex: 1 1 200px;">
                    <label>Search:</label>
                    <input type="text" id="filterSearch" class="filter-input" placeholder="Search title or reference...">
                </div>
                <button class="btn btn-secondary" onclick="applyFilters()" style="flex: 0 0 auto;">
                    <i class="fas fa-filter"></i> Apply
                </button>
                <button class="btn btn-outline" onclick="clearFilters()" style="flex: 0 0 auto;">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>

            <!-- Expenses Table -->
            <div class="table-container">
                <table id="expensesTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="expensesTableBody">
                        <!-- Populated via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recurring Expenses Tab -->
        <div class="tab-content" id="recurring">
            <div class="recurring-container">
                <h3>Upcoming Recurring Payments</h3>
                <div id="upcomingPaymentsList" class="upcoming-list">
                    <!-- Populated via JavaScript -->
                </div>

                <h3 style="margin-top: 30px;">All Recurring Expenses</h3>
                <div class="table-container">
                    <table id="recurringTable" class="display" style="width:100%">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Amount</th>
                                <th>Category</th>
                                <th>Frequency</th>
                                <th>Next Due</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="recurringTableBody">
                            <!-- Populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Analytics Tab -->
        <div class="tab-content" id="analytics">
            <div class="analytics-container">
                <!-- Analytics Filters -->
                <div class="filters-section" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; margin-bottom: 20px;">
                    <div class="filter-group" style="flex: 0 0 auto;">
                        <label>Date Range:</label>
                        <div style="display: flex; gap: 5px; align-items: center;">
                            <input type="date" id="analyticsStartDate" class="filter-input">
                            <span>to</span>
                            <input type="date" id="analyticsEndDate" class="filter-input">
                        </div>
                    </div>
                    <button class="btn btn-secondary" onclick="applyAnalyticsFilters()" style="flex: 0 0 auto;">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                    <button class="btn btn-outline" onclick="clearAnalyticsFilters()" style="flex: 0 0 auto;">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>

                <div class="chart-row">
                    <div class="chart-card">
                        <h3>Expenses by Category</h3>
                        <div id="categoryChart"></div>
                    </div>
                    <div class="chart-card">
                        <h3>Monthly Trend</h3>
                        <div id="trendChart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Expense Modal -->
    <div id="expenseModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="expenseModalTitle">Add Expense</h3>
                <span class="close" onclick="closeExpenseModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <input type="hidden" id="expenseId" name="expense_id">
                    
                    <!-- Expense Type Selector -->
                    <div class="form-group">
                        <label>Expense Type</label>
                        <div class="type-selector">
                            <button type="button" class="type-btn active" data-type="one-time">
                                <i class="fas fa-shopping-bag"></i> One-Time
                            </button>
                            <button type="button" class="type-btn" data-type="recurring">
                                <i class="fas fa-sync"></i> Recurring
                            </button>
                        </div>
                        <input type="hidden" id="expenseType" name="expense_type" value="one-time">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" required placeholder="e.g., Office Rent">
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount *</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category_id" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paymentMethod">Payment Method</label>
                            <select id="paymentMethod" name="payment_method">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Debit Card">Debit Card</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <!-- One-Time Fields -->
                    <div id="oneTimeFields">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expenseDate">Expense Date</label>
                                <input type="datetime-local" id="expenseDate" name="expense_date">
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="referenceNo">Reference Number</label>
                            <input type="text" id="referenceNo" name="reference_no" placeholder="Receipt/Invoice number">
                        </div>
                    </div>

                    <!-- Recurring Fields -->
                    <div id="recurringFields" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="frequency">Frequency *</label>
                                <select id="frequency" name="frequency">
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="annually">Annually</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="startDate">Start Date</label>
                                <input type="date" id="startDate" name="start_date">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="endDate">End Date (Optional)</label>
                                <input type="date" id="endDate" name="end_date">
                            </div>
                            <div class="form-group">
                                <label for="remindDays">Remind Before (Days)</label>
                                <input type="number" id="remindDays" name="remind_days_before" value="3" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" name="notes" rows="3" placeholder="Additional details..."></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="closeExpenseModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Management Modal -->
    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Manage Expense Categories</h3>
                <span class="close" onclick="closeCategoryModal()">&times;</span>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary" onclick="openAddCategoryForm()" style="margin-bottom: 15px;">
                    <i class="fas fa-plus"></i> Add New Category
                </button>
                
                <div id="addCategoryForm" style="display: none; margin-bottom: 20px; padding: 15px; background: #f5f5f5; border-radius: 8px;">
                    <form id="categoryForm">
                        <input type="hidden" id="categoryId" name="category_id">
                        <div class="form-group">
                            <label for="categoryName">Category Name *</label>
                            <input type="text" id="categoryName" name="category_name" required>
                        </div>
                        <div class="form-group">
                            <label for="categoryDescription">Description</label>
                            <input type="text" id="categoryDescription" name="description">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="categoryColor">Color</label>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <input type="color" id="categoryColor" name="color_code" value="#808080" style="width: 50px; height: 40px; padding: 2px;">
                                    <div id="colorPreview" class="color-preview-box" style="width: 40px; height: 40px; border-radius: 6px; background-color: #808080; border: 1px solid #ddd;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Icon</label>
                                <div class="icon-selector-container">
                                    <input type="hidden" id="categoryIcon" name="icon" value="money-bill">
                                    <div class="selected-icon-preview">
                                        <i class="fas fa-money-bill" id="selectedIconDisplay"></i>
                                        <span id="selectedIconName">money-bill</span>
                                    </div>
                                    <input type="text" id="iconSearchInput" placeholder="Search icons..." class="icon-search-input" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 10px; font-size: 14px;">
                                    <div class="icon-grid" id="iconPickerGrid">
                                        <!-- Icons will be populated via JS -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="closeAddCategoryForm()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Category</button>
                        </div>
                    </form>
                </div>

                <!-- Category Details View (Hidden by default) -->
                <div id="categoryDetailsView" style="display: none;">
                    <div class="details-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                        <button class="btn btn-sm btn-outline" onclick="closeCategoryDetails()">
                            <i class="fas fa-arrow-left"></i> Back
                        </button>
                        <div id="detailHeaderInfo" style="display: flex; align-items: center; gap: 10px;">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <div class="details-stats" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 25px;">
                        <div class="stat-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                            <span style="display: block; font-size: 12px; color: #7f8c8d; text-transform: uppercase;">Total Spent</span>
                            <span id="detailTotalAmount" style="display: block; font-size: 20px; font-weight: bold; color: #2c3e50; margin-top: 5px;">Rs. 0.00</span>
                        </div>
                        <div class="stat-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                            <span style="display: block; font-size: 12px; color: #7f8c8d; text-transform: uppercase;">This Month</span>
                            <span id="detailMonthAmount" style="display: block; font-size: 20px; font-weight: bold; color: #2c3e50; margin-top: 5px;">Rs. 0.00</span>
                        </div>
                        <div class="stat-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center;">
                            <span style="display: block; font-size: 12px; color: #7f8c8d; text-transform: uppercase;">Transactions</span>
                            <span id="detailCount" style="display: block; font-size: 20px; font-weight: bold; color: #2c3e50; margin-top: 5px;">0</span>
                        </div>
                    </div>

                    <h4 style="margin: 0 0 15px 0; color: #2c3e50;">Recent Activity</h4>
                    <div class="table-container" style="padding: 0; box-shadow: none;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8f9fa;">
                                    <th style="padding: 10px; text-align: left; font-size: 13px;">Date</th>
                                    <th style="padding: 10px; text-align: left; font-size: 13px;">Title</th>
                                    <th style="padding: 10px; text-align: right; font-size: 13px;">Amount</th>
                                    <th style="padding: 10px; text-align: center; font-size: 13px;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="detailTransactionsBody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="categories-grid" id="categoriesList">
                    <!-- Populated via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="expenses.js"></script>
</body>
</html>
