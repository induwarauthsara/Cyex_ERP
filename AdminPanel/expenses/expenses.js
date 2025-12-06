/**
 * Expenses Management - Frontend JavaScript
 * Handles all UI interactions and API calls
 */

const API_BASE = '/api/v1/expenses';
let expensesTable = null;
let recurringTable = null;
let categoriesData = [];
let categoryChartInstance = null;
let trendChartInstance = null;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    loadSummary();
    loadCategories();
    loadExpenses();
    initializeExpenseTypeToggle();
    initializeExpenseForm();
    initializeCategoryForm();
    setDefaultDates();
    renderIconPicker();
    initializeColorPicker();
    initializeIconSearch();
});

function initializeColorPicker() {
    const colorInput = document.getElementById('categoryColor');
    const colorPreview = document.getElementById('colorPreview');
    
    if (colorInput && colorPreview) {
        // Update on change
        colorInput.addEventListener('input', function() {
            colorPreview.style.backgroundColor = this.value;
        });
        
        // Initialize
        colorPreview.style.backgroundColor = colorInput.value;
    }
}

const availableIcons = [
    'money-bill', 'credit-card', 'coins', 'wallet', 'receipt',
    'shopping-cart', 'shopping-bag', 'store', 'tags',
    'home', 'building', 'warehouse',
    'car', 'truck', 'bus', 'gas-pump', 'wrench',
    'utensils', 'hamburger', 'pizza-slice', 'coffee',
    'laptop', 'mobile-alt', 'wifi', 'bolt', 'lightbulb',
    'heart-pulse', 'first-aid', 'pills',
    'plane', 'suitcase', 'map-marker-alt',
    'graduation-cap', 'book',
    'gift', 'gamepad', 'film', 'music',
    'child', 'paw', 'users', 'briefcase',
    'exclamation-circle', 'question-circle',
    'star', 'chart-line', 'chart-pie', 'calendar',
    'clock', 'bell', 'envelope', 'folder',
    'lock', 'user-shield', 'server', 'database',
    'cloud', 'print', 'seedling', 'tree',
    'tshirt', 'socks', 'dumbbell', 'bicycle', 'taxi', 
    'train', 'subway', 'ship', 'beer', 'wine-glass', 
    'cocktail', 'couch', 'bed', 'tv', 'camera', 
    'tools', 'hammer', 'broom', 'cut', 'globe',
    // Business & Expenses
    'file-invoice-dollar', 'file-invoice', 'money-check-alt', 'piggy-bank', 'landmark',
    'hand-holding-usd', 'calculator', 'balance-scale', 'percent', 'search-dollar',
    'chart-bar', 'chart-area', 'project-diagram', 'sitemap', 'users-cog',
    'user-tie', 'handshake', 'id-card', 'clipboard-list', 'copy',
    'boxes', 'shipping-fast', 'dolly', 'hard-hat', 'industry',
    'bullhorn', 'ad', 'rocket', 'medal', 'trophy'
];

function renderIconPicker(filterText = '') {
    const grid = document.getElementById('iconPickerGrid');
    if (!grid) return;
    
    grid.innerHTML = '';
    const currentIcon = document.getElementById('categoryIcon').value || 'money-bill';
    
    const filteredIcons = availableIcons.filter(icon => 
        icon.toLowerCase().includes(filterText.toLowerCase())
    );
    
    if (filteredIcons.length === 0) {
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; color: #95a5a6; font-size: 13px; padding: 10px;">No icons found</div>';
        return;
    }
    
    filteredIcons.forEach(icon => {
        const div = document.createElement('div');
        div.className = `icon-option ${icon === currentIcon ? 'selected' : ''}`;
        div.innerHTML = `<i class="fas fa-${icon}"></i>`;
        div.title = icon;
        div.onclick = () => selectIcon(icon);
        grid.appendChild(div);
    });
}

function initializeIconSearch() {
    const searchInput = document.getElementById('iconSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            renderIconPicker(this.value);
        });
    }
}

function selectIcon(icon) {
    document.getElementById('categoryIcon').value = icon;
    document.getElementById('selectedIconName').textContent = icon;
    
    const displayElement = document.getElementById('selectedIconDisplay');
    displayElement.className = `fas fa-${icon}`;
    
    // Update grid selection
    document.querySelectorAll('.icon-option').forEach(el => {
        el.classList.remove('selected');
        if (el.querySelector('i').classList.contains(`fa-${icon}`)) {
            el.classList.add('selected');
        }
    });
}

// Tab Navigation
function initializeTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.dataset.tab;
            
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to clicked tab and its content
            this.classList.add('active');
            document.getElementById(tabName).classList.add('active');
            
            // Load data for specific tabs
            if (tabName === 'recurring' && !recurringTable) {
                loadRecurringExpenses();
                loadUpcomingPayments();
            } else if (tabName === 'analytics') {
                loadAnalytics();
            }
        });
    });
}

// Load Dashboard Summary
async function loadSummary() {
    try {
        const response = await fetch(`${API_BASE}/summary.php`, {
            headers: getAuthHeaders()
        });
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            // Update summary cards
            document.getElementById('currentMonthAmount').textContent = 
                'Rs. ' + formatNumber(data.totals.current_month);
            
            document.getElementById('yearAmount').textContent = 
                'Rs. ' + formatNumber(data.totals.year_to_date);
            
            document.getElementById('yearCount').textContent = 
                `${data.totals.current_month_count} transactions this month`;
            
            // Month trend
            const trendEl = document.getElementById('monthTrend');
            const change = data.totals.month_over_month_change;
            if (change > 0) {
                trendEl.innerHTML = `<i class="fas fa-arrow-up"></i> ${change.toFixed(1)}% from last month`;
                trendEl.className = 'trend increase';
            } else if (change < 0) {
                trendEl.innerHTML = `<i class="fas fa-arrow-down"></i> ${Math.abs(change).toFixed(1)}% from last month`;
                trendEl.className = 'trend decrease';
            } else {
                trendEl.textContent = 'No change from last month';
                trendEl.className = 'trend';
            }
            
            // Top category
            if (data.top_category) {
                document.getElementById('topCategoryName').textContent = data.top_category.category_name;
                document.getElementById('topCategoryAmount').textContent = 
                    `Rs. ${formatNumber(data.top_category.total_amount)} (${data.top_category.percentage}%)`;
            }
            
            // Pending/Unpaid payments (including partial)
            const unpaidAmount = (data.status_overview.unpaid?.remaining || 0) + 
                                (data.status_overview.partial?.remaining || 0) + 
                                (data.status_overview.overdue?.remaining || 0);
            const unpaidCount = (data.status_overview.unpaid?.count || 0) + 
                               (data.status_overview.partial?.count || 0) + 
                               (data.status_overview.overdue?.count || 0);
            
            document.getElementById('pendingAmount').textContent = 
                'Rs. ' + formatNumber(unpaidAmount);
            document.getElementById('pendingCount').textContent = 
                `${unpaidCount} pending`;
        }
    } catch (error) {
        console.error('Error loading summary:', error);
        Swal.fire('Error', 'Failed to load dashboard summary', 'error');
    }
}

// Load Categories
async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE}/categories.php`, {
            headers: getAuthHeaders()
        });
        const result = await response.json();
        
        if (result.success) {
            categoriesData = result.data;
            populateCategoryDropdowns();
            populateCategoryFilter();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function populateCategoryDropdowns() {
    const selects = ['category', 'filterCategory'];
    
    selects.forEach(selectId => {
        const select = document.getElementById(selectId);
        if (!select) return;
        
        // Clear existing options (except first one for filter)
        if (selectId === 'filterCategory') {
            select.innerHTML = '<option value="">All Categories</option>';
        } else {
            select.innerHTML = '<option value="">Select Category</option>';
        }
        
        categoriesData.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.category_id;
            option.textContent = cat.category_name;
            select.appendChild(option);
        });
    });
}

function populateCategoryFilter() {
    const filterSelect = document.getElementById('filterCategory');
    // Already handled in populateCategoryDropdowns
}

// Load Expenses
async function loadExpenses(filters = {}) {
    try {
        const queryParams = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/list.php?${queryParams}`, {
            headers: getAuthHeaders()
        });
        const result = await response.json();
        
        if (result.success) {
            displayExpenses(result.data);
        }
    } catch (error) {
        console.error('Error loading expenses:', error);
        Swal.fire('Error', 'Failed to load expenses', 'error');
    }
}

function displayExpenses(expenses) {
    const tbody = document.getElementById('expensesTableBody');
    tbody.innerHTML = '';
    
    expenses.forEach(exp => {
        const row = document.createElement('tr');
        
        const categoryBadge = `<span class="category-badge" style="background: ${exp.category.color_code}20; color: ${exp.category.color_code}">
            <i class="fas fa-${exp.category.icon}"></i> ${exp.category.category_name}
        </span>`;
        
        const statusClass = exp.status.toLowerCase();
        const statusBadge = `<span class="status-badge ${statusClass}">${exp.status}</span>`;
        
        // Payment progress for partial/unpaid expenses
        let paymentInfo = `Rs. ${formatNumber(exp.amount)}`;
        if (exp.status === 'partial') {
            paymentInfo = `
                <div class="payment-info">
                    <div class="payment-progress-bar">
                        <div class="payment-progress-fill" style="width: ${exp.payment_percentage}%"></div>
                    </div>
                    <small class="payment-text">Rs. ${formatNumber(exp.amount_paid)} of Rs. ${formatNumber(exp.amount)} (${exp.payment_percentage}%)</small>
                </div>
            `;
        } else if (exp.status === 'unpaid') {
            paymentInfo = `
                <div class="payment-info">
                    <div class="payment-progress-bar">
                        <div class="payment-progress-fill" style="width: 0%"></div>
                    </div>
                    <small class="payment-text">Rs. 0 of Rs. ${formatNumber(exp.amount)} (0%)</small>
                </div>
            `;
        }
        
        // Action buttons
        let actionButtons = `
            <button class="btn btn-sm btn-secondary" onclick="editExpense(${exp.expense_id})" title="Edit">
                <i class="fas fa-edit"></i>
            </button>
        `;
        
        // Add payment button for partial/unpaid expenses
        if (exp.status === 'partial' || exp.status === 'unpaid') {
            actionButtons += `
                <button class="btn btn-sm btn-primary" onclick="showAddPaymentModal(${exp.expense_id}, '${exp.title}', ${exp.amount}, ${exp.amount_paid})" title="Add Payment">
                    <i class="fas fa-money-bill"></i>
                </button>
            `;
        }
        
        // Payment history button (if there are payments)
        if (exp.amount_paid > 0) {
            actionButtons += `
                <button class="btn btn-sm btn-info" onclick="showPaymentHistory(${exp.expense_id})" title="Payment History">
                    <i class="fas fa-history"></i>
                </button>
            `;
        }
        
        actionButtons += `
            <button class="btn btn-sm btn-danger" onclick="deleteExpense(${exp.expense_id})" title="Delete">
                <i class="fas fa-trash"></i>
            </button>
        `;
        
        row.innerHTML = `
            <td>${formatDate(exp.expense_date)}</td>
            <td>${exp.title}</td>
            <td>${categoryBadge}</td>
            <td>${paymentInfo}</td>
            <td>${exp.payment_method}</td>
            <td>${statusBadge}</td>
            <td>${exp.reference_no || '-'}</td>
            <td>${actionButtons}</td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Initialize DataTable if not already initialized
    if (!expensesTable) {
        expensesTable = $('#expensesTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true
        });
    } else {
        expensesTable.clear();
        expensesTable.rows.add($(tbody).find('tr'));
        expensesTable.draw();
    }
}

// Load Recurring Expenses
async function loadRecurringExpenses() {
    try {
        const response = await fetch(`${API_BASE}/recurring.php`, {
            headers: getAuthHeaders()
        });
        const result = await response.json();
        
        if (result.success) {
            displayRecurringExpenses(result.data);
        }
    } catch (error) {
        console.error('Error loading recurring expenses:', error);
    }
}

function displayRecurringExpenses(recurring) {
    const tbody = document.getElementById('recurringTableBody');
    tbody.innerHTML = '';
    
    recurring.forEach(rec => {
        const row = document.createElement('tr');
        
        const categoryBadge = `<span class="category-badge" style="background: ${rec.category.color_code}20; color: ${rec.category.color_code}">
            <i class="fas fa-${rec.category.icon}"></i> ${rec.category.category_name}
        </span>`;
        
        const statusBadge = rec.is_active 
            ? '<span class="status-badge paid">Active</span>' 
            : '<span class="status-badge overdue">Inactive</span>';
        
        row.innerHTML = `
            <td>${rec.title}</td>
            <td>Rs. ${formatNumber(rec.amount)}</td>
            <td>${categoryBadge}</td>
            <td><i class="fas fa-sync"></i> ${capitalizeFirst(rec.frequency)}</td>
            <td>${formatDate(rec.next_due_date)}</td>
            <td>${statusBadge}</td>
            <td>
                ${rec.is_active ? `<button class="btn btn-sm btn-success" onclick="payRecurring(${rec.recurring_id})">
                    <i class="fas fa-check"></i> Pay
                </button>` : ''}
                <button class="btn btn-sm btn-danger" onclick="deleteRecurring(${rec.recurring_id})">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
    
    if (!recurringTable) {
        recurringTable = $('#recurringTable').DataTable({
            order: [[4, 'asc']],
            pageLength: 25,
            responsive: true
        });
    }
}

// Load Upcoming Payments
async function loadUpcomingPayments() {
    try {
        const response = await fetch(`${API_BASE}/summary.php`, {
            headers: getAuthHeaders()
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data.upcoming_payments) {
            displayUpcomingPayments(result.data.upcoming_payments);
        } else {
            console.warn('No upcoming payments data received');
        }
    } catch (error) {
        console.error('Error loading upcoming payments:', error);
        // Don't show error to user, just log it - this is not critical
    }
}

function displayUpcomingPayments(payments) {
    const container = document.getElementById('upcomingPaymentsList');
    container.innerHTML = '';
    
    if (payments.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 20px;">No upcoming payments</p>';
        return;
    }
    
    payments.forEach(payment => {
        const item = document.createElement('div');
        item.className = `upcoming-item ${payment.status}`;
        
        const daysText = payment.days_until_due < 0 
            ? `${Math.abs(payment.days_until_due)} days overdue`
            : payment.days_until_due === 0 
            ? 'Due today' 
            : `Due in ${payment.days_until_due} days`;
        
        item.innerHTML = `
            <div class="upcoming-info">
                <h4>${payment.title}</h4>
                <p>
                    <i class="fas fa-${payment.category.icon}" style="color: ${payment.category.color_code}"></i>
                    ${payment.category.name} • Rs. ${formatNumber(payment.amount)} • ${daysText}
                </p>
            </div>
            <div class="upcoming-actions">
                <button class="btn btn-success" onclick="payRecurring(${payment.recurring_id})">
                    <i class="fas fa-check"></i> Pay Now
                </button>
            </div>
        `;
        
        container.appendChild(item);
    });
}

// Pay Recurring Expense
async function payRecurring(recurringId) {
    const result = await Swal.fire({
        title: 'Confirm Payment',
        text: 'Mark this recurring expense as paid for this cycle?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Pay Now',
        cancelButtonText: 'Cancel'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        const response = await fetch(`${API_BASE}/pay_recurring.php`, {
            method: 'POST',
            headers: getAuthHeaders(),
            body: JSON.stringify({
                recurring_id: recurringId,
                payment_date: new Date().toISOString().slice(0, 19).replace('T', ' ')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire('Success', data.message, 'success');
            loadSummary();
            loadRecurringExpenses();
            loadUpcomingPayments();
            loadExpenses();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch (error) {
        console.error('Error paying recurring:', error);
        Swal.fire('Error', 'Failed to process payment', 'error');
    }
}

// Expense Type Toggle
function initializeExpenseTypeToggle() {
    const typeBtns = document.querySelectorAll('.type-btn');
    const oneTimeFields = document.getElementById('oneTimeFields');
    const recurringFields = document.getElementById('recurringFields');
    const expenseTypeInput = document.getElementById('expenseType');
    
    typeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const type = this.dataset.type;
            
            typeBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            expenseTypeInput.value = type;
            
            if (type === 'one-time') {
                oneTimeFields.style.display = 'block';
                recurringFields.style.display = 'none';
            } else {
                oneTimeFields.style.display = 'none';
                recurringFields.style.display = 'block';
            }
        });
    });
}

// Expense Form Submission
function initializeExpenseForm() {
    const form = document.getElementById('expenseForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Convert amount to number
        data.amount = parseFloat(data.amount);
        data.category_id = parseInt(data.category_id);
        
        try {
            const expenseId = document.getElementById('expenseId').value;
            const isEdit = expenseId !== '';
            
            const endpoint = isEdit ? `${API_BASE}/update.php` : `${API_BASE}/add.php`;
            const method = isEdit ? 'PUT' : 'POST';
            
            if (isEdit) {
                data.expense_id = parseInt(expenseId);
            }
            
            const response = await fetch(endpoint, {
                method: method,
                headers: getAuthHeaders(),
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire('Success', result.message, 'success');
                closeExpenseModal();
                loadSummary();
                loadExpenses();
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving expense:', error);
            Swal.fire('Error', 'Failed to save expense', 'error');
        }
    });
}

// Category Form Submission
function initializeCategoryForm() {
    const form = document.getElementById('categoryForm');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const categoryId = document.getElementById('categoryId')?.value;
            const isEdit = categoryId && categoryId !== '';
            
            const endpoint = isEdit ? `${API_BASE}/update_category.php` : `${API_BASE}/add_category.php`;
            const method = isEdit ? 'POST' : 'POST'; // Both use POST, update checks ID
            
            // Add ID if editing
            if (isEdit) {
                data.category_id = categoryId;
                // update_category.php accepts JSON body, so data obj is fine
                // But previously add_category was also JSON.
            }
            
            const response = await fetch(endpoint, {
                method: 'POST', // Always POST for these endpoints usually, or PUT if strictly REST
                headers: getAuthHeaders(),
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                Swal.fire('Success', result.message, 'success');
                closeAddCategoryForm();
                loadCategories();
                loadCategoriesList();
            } else {
                Swal.fire('Error', result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving category:', error);
            Swal.fire('Error', 'Failed to save category', 'error');
        }
    });
}

// Delete Expense
async function deleteExpense(expenseId) {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: 'This expense will be permanently deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#e74c3c'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        const response = await fetch(`${API_BASE}/delete.php`, {
            method: 'DELETE',
            headers: getAuthHeaders(),
            body: JSON.stringify({ expense_id: expenseId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire('Deleted!', data.message, 'success');
            loadSummary();
            loadExpenses();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting expense:', error);
        Swal.fire('Error', 'Failed to delete expense', 'error');
    }
}

// Modal Functions
function openAddExpenseModal() {
    document.getElementById('expenseModalTitle').textContent = 'Add Expense';
    document.getElementById('expenseForm').reset();
    document.getElementById('expenseId').value = '';
    document.getElementById('expenseModal').classList.add('show');
}

function closeExpenseModal() {
    document.getElementById('expenseModal').classList.remove('show');
}

function openCategoryModal() {
    document.getElementById('categoryModal').classList.add('show');
    loadCategoriesList();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.remove('show');
}

function openAddCategoryForm(isEdit = false) {
    document.getElementById('addCategoryForm').style.display = 'block';
    
    // Reset if adding new
    if (!isEdit) {
        document.getElementById('categoryForm').reset();
        document.getElementById('categoryId').value = '';
        document.querySelector('#categoryForm button[type="submit"]').textContent = 'Save Category';
        
        if (typeof selectIcon === 'function') {
            selectIcon('money-bill');
        }
    }
    
    // Sync color display
    const colorInput = document.getElementById('categoryColor');
    const colorPreview = document.getElementById('colorPreview');
    if (colorInput && colorPreview) {
        colorPreview.style.backgroundColor = colorInput.value;
    }
}

function closeAddCategoryForm() {
    document.getElementById('addCategoryForm').style.display = 'none';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
}

function editCategory(categoryId) {
    const category = categoriesData.find(c => c.category_id == categoryId);
    if (!category) return;
    
    document.getElementById('categoryId').value = category.category_id;
    document.getElementById('categoryName').value = category.category_name;
    document.getElementById('categoryDescription').value = category.description || '';
    document.getElementById('categoryColor').value = category.color_code;
    
    // Select Icon
    if (typeof selectIcon === 'function') {
        selectIcon(category.icon);
    }
    
    // Update button text
    document.querySelector('#categoryForm button[type="submit"]').textContent = 'Update Category';
    
    openAddCategoryForm(true);
}

async function deleteCategory(categoryId) {
    const result = await Swal.fire({
        title: 'Delete Category?',
        text: 'Are you sure you want to delete this category?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#e74c3c'
    });
    
    if (!result.isConfirmed) return;
    
    try {
        const response = await fetch(`${API_BASE}/delete_category.php`, {
            method: 'POST',
            headers: getAuthHeaders(),
            body: JSON.stringify({ category_id: categoryId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire('Deleted!', data.message, 'success');
            loadCategories();
            loadCategoriesList();
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        Swal.fire('Error', 'Failed to delete category', 'error');
    }
}

async function loadCategoriesList() {
    const container = document.getElementById('categoriesList');
    container.innerHTML = '';
    
    categoriesData.forEach(cat => {
        const item = document.createElement('div');
        item.className = 'category-item';
        item.innerHTML = `
            <div class="category-header">
                <i class="fas fa-${cat.icon}" style="color: ${cat.color_code}"></i>
                <div class="category-actions">
                    <button class="btn-icon" onclick="viewCategory(${cat.category_id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="editCategory(${cat.category_id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    ${cat.category_id > 1 ? `
                    <button class="btn-icon delete" onclick="deleteCategory(${cat.category_id})" title="Delete">
                        <i class="fas fa-times"></i>
                    </button>` : ''}
                </div>
            </div>
            <h4>${cat.category_name}</h4>
        `;
        container.appendChild(item);
    });
}

// Category View Details Logic
async function viewCategory(categoryId) {
    try {
        const response = await fetch(`${API_BASE}/category_details.php?category_id=${categoryId}`, {
            headers: getAuthHeaders()
        });
        const result = await response.json();
        
        if (result.success) {
            const { category, stats, recent_transactions } = result.data;
            
            // Populate Header
            const headerHtml = `
                <i class="fas fa-${category.icon}" style="color: ${category.color}; font-size: 24px;"></i>
                <div>
                    <h3 style="margin: 0; font-size: 18px;">${category.name}</h3>
                    <p style="margin: 0; font-size: 12px; color: #7f8c8d;">${category.description || ''}</p>
                </div>
            `;
            document.getElementById('detailHeaderInfo').innerHTML = headerHtml;
            
            // Populate Stats
            document.getElementById('detailTotalAmount').textContent = 'Rs. ' + formatNumber(stats.total_amount);
            document.getElementById('detailMonthAmount').textContent = 'Rs. ' + formatNumber(stats.current_month_amount);
            document.getElementById('detailCount').textContent = stats.total_count;
            
            // Populate Transactions Table
            const tbody = document.getElementById('detailTransactionsBody');
            tbody.innerHTML = '';
            
            if (recent_transactions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 15px; color: #7f8c8d;">No recent transactions</td></tr>';
            } else {
                recent_transactions.forEach(t => {
                    const statusLabel = formatStatus(t.status);
                    tbody.innerHTML += `
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px; font-size: 13px;">${formatDate(t.date).split(' ')[0]}</td>
                            <td style="padding: 10px; font-size: 13px;">${t.title}</td>
                            <td style="padding: 10px; text-align: right; font-size: 13px; font-weight: 500;">Rs. ${formatNumber(t.amount)}</td>
                            <td style="padding: 10px; text-align: center;">${statusLabel}</td>
                        </tr>
                    `;
                });
            }
            
            // Show Details View
            document.getElementById('categoriesList').style.display = 'none';
            document.querySelector('.modal-body > button.btn-primary').style.display = 'none'; // Hide "Add New" button
            document.getElementById('addCategoryForm').style.display = 'none'; // Ensure add form is hidden
            document.getElementById('categoryDetailsView').style.display = 'block';
            
        } else {
            Swal.fire('Error', result.message, 'error');
        }
    } catch (error) {
        console.error('Error viewing category:', error);
        Swal.fire('Error', 'Failed to load details', 'error');
    }
}

function closeCategoryDetails() {
    document.getElementById('categoryDetailsView').style.display = 'none';
    document.getElementById('categoriesList').style.display = 'grid';
    document.querySelector('.modal-body > button.btn-primary').style.display = 'inline-flex';
}

function formatStatus(status) {
    let color = '#6c757d';
    if (status === 'paid') color = '#28a745';
    if (status === 'unpaid') color = '#ffc107';
    if (status === 'partial') color = '#17a2b8';
    if (status === 'overdue') color = '#dc3545';
    
    return `<span style="color: ${color}; font-size: 12px; font-weight: 500; text-transform: capitalize;">${status}</span>`;
}

// Filters
function applyFilters() {
    const filters = {
        start_date: document.getElementById('filterStartDate').value,
        end_date: document.getElementById('filterEndDate').value,
        category_id: document.getElementById('filterCategory').value,
        status: document.getElementById('filterStatus').value,
        search: document.getElementById('filterSearch').value
    };
    
    // Remove empty filters
    Object.keys(filters).forEach(key => {
        if (!filters[key]) delete filters[key];
    });
    
    loadExpenses(filters);
}

function clearFilters() {
    document.getElementById('filterStartDate').value = '';
    document.getElementById('filterEndDate').value = '';
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterSearch').value = '';
    loadExpenses();
}

function setDefaultDates() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    document.getElementById('filterStartDate').value = firstDay.toISOString().split('T')[0];
    document.getElementById('filterEndDate').value = today.toISOString().split('T')[0];
    
    // Set default for expense date
    document.getElementById('expenseDate').value = new Date().toISOString().slice(0, 16);
    document.getElementById('startDate').value = today.toISOString().split('T')[0];
    
    // Set default for analytics (This Year)
    const firstDayOfYear = new Date(today.getFullYear(), 0, 1);
    document.getElementById('analyticsStartDate').value = firstDayOfYear.toISOString().split('T')[0];
    document.getElementById('analyticsEndDate').value = today.toISOString().split('T')[0];
}

// Partial Payment Functions
function showAddPaymentModal(expenseId, title, totalAmount, amountPaid) {
    const remaining = totalAmount - amountPaid;
    
    Swal.fire({
        title: 'Add Payment',
        html: `
            <div style="text-align: left; margin-bottom: 15px;">
                <strong>Expense:</strong> ${title}<br>
                <strong>Total Amount:</strong> Rs. ${formatNumber(totalAmount)}<br>
                <strong>Already Paid:</strong> Rs. ${formatNumber(amountPaid)}<br>
                <strong>Remaining:</strong> Rs. ${formatNumber(remaining)}
            </div>
            <input type="number" id="paymentAmount" class="swal2-input" placeholder="Payment Amount" max="${remaining}" step="0.01" style="width: 90%;">
            <input type="date" id="paymentDate" class="swal2-input" value="${new Date().toISOString().split('T')[0]}" style="width: 90%;">
            <select id="paymentMethod" class="swal2-input" style="width: 90%;">
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="cheque">Cheque</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
            </select>
            <input type="text" id="referenceNo" class="swal2-input" placeholder="Reference Number (Optional)" style="width: 90%;">
            <textarea id="paymentNotes" class="swal2-textarea" placeholder="Notes (Optional)" style="width: 90%;"></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Payment',
        preConfirm: () => {
            const paymentAmount = parseFloat(document.getElementById('paymentAmount').value);
            const paymentDate = document.getElementById('paymentDate').value;
            const paymentMethod = document.getElementById('paymentMethod').value;
            const referenceNo = document.getElementById('referenceNo').value;
            const notes = document.getElementById('paymentNotes').value;
            
            if (!paymentAmount || paymentAmount <= 0) {
                Swal.showValidationMessage('Please enter a valid payment amount');
                return false;
            }
            
            if (paymentAmount > remaining) {
                Swal.showValidationMessage(`Payment amount cannot exceed remaining balance (Rs. ${formatNumber(remaining)})`);
                return false;
            }
            
            if (!paymentDate) {
                Swal.showValidationMessage('Please select a payment date');
                return false;
            }
            
            return { paymentAmount, paymentDate, paymentMethod, referenceNo, notes };
        }
    }).then(async (result) => {
        if (result.isConfirmed) {
            await addPayment(expenseId, result.value);
        }
    });
}

async function addPayment(expenseId, paymentData) {
    try {
        const response = await fetch(`${API_BASE}/add_payment.php`, {
            method: 'POST',
            headers: {
                ...getAuthHeaders(),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                expense_id: expenseId,
                payment_amount: paymentData.paymentAmount,
                payment_date: paymentData.paymentDate,
                payment_method: paymentData.paymentMethod,
                reference_no: paymentData.referenceNo,
                notes: paymentData.notes
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            Swal.fire({
                icon: 'success',
                title: 'Payment Added',
                html: `
                    Payment of Rs. ${formatNumber(paymentData.paymentAmount)} has been recorded.<br>
                    <strong>Status:</strong> ${result.data.status}<br>
                    <strong>Remaining:</strong> Rs. ${formatNumber(result.data.remaining_amount)}
                `,
                timer: 3000
            });
            loadExpenses();
            loadSummary();
        } else {
            throw new Error(result.message || 'Failed to add payment');
        }
    } catch (error) {
        console.error('Error adding payment:', error);
        Swal.fire('Error', error.message, 'error');
    }
}

async function showPaymentHistory(expenseId) {
    try {
        const response = await fetch(`${API_BASE}/payment_history.php?expense_id=${expenseId}`, {
            headers: getAuthHeaders()
        });
        const result = await response.json();
        
        if (result.success) {
            const { expense, payments, summary } = result.data;
            
            let paymentsHtml = `
                <div style="text-align: left; margin-bottom: 20px;">
                    <h4>${expense.title}</h4>
                    <div class="payment-progress-bar" style="margin: 10px 0;">
                        <div class="payment-progress-fill" style="width: ${expense.payment_percentage}%"></div>
                    </div>
                    <p>
                        <strong>Total:</strong> Rs. ${formatNumber(expense.total_amount)}<br>
                        <strong>Paid:</strong> Rs. ${formatNumber(expense.amount_paid)} (${expense.payment_percentage}%)<br>
                        <strong>Remaining:</strong> Rs. ${formatNumber(expense.remaining_amount)}<br>
                        <strong>Status:</strong> <span class="status-badge ${expense.status}">${expense.status}</span>
                    </p>
                </div>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f5f5f5;">
                                <th style="padding: 8px; border: 1px solid #ddd;">Date</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Amount</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Method</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Reference</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            payments.forEach(payment => {
                paymentsHtml += `
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">${formatDate(payment.payment_date)}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">Rs. ${formatNumber(payment.payment_amount)}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${payment.payment_method}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${payment.reference_no || '-'}</td>
                    </tr>
                `;
            });
            
            paymentsHtml += `
                        </tbody>
                    </table>
                </div>
            `;
            
            Swal.fire({
                title: 'Payment History',
                html: paymentsHtml,
                width: 800,
                confirmButtonText: 'Close'
            });
        } else {
            throw new Error(result.message || 'Failed to load payment history');
        }
    } catch (error) {
        console.error('Error loading payment history:', error);
        Swal.fire('Error', error.message, 'error');
    }
}

// Analytics
async function loadAnalytics() {
    try {
        const startDate = document.getElementById('analyticsStartDate').value;
        const endDate = document.getElementById('analyticsEndDate').value;
        
        let queryParams = new URLSearchParams();
        if (startDate) queryParams.append('start_date', startDate);
        if (endDate) queryParams.append('end_date', endDate);
        
        const response = await fetch(`${API_BASE}/summary.php?${queryParams.toString()}`, {
            headers: getAuthHeaders()
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            console.log('Analytics data received:', result.data);
            renderCategoryChart(result.data.category_breakdown || []);
            renderTrendChart(result.data.monthly_trend || []);
        } else {
            console.error('Analytics API returned error:', result.message);
            Swal.fire('Error', result.message || 'Failed to load analytics data', 'error');
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
        Swal.fire('Error', 'Failed to load analytics: ' + error.message, 'error');
    }
}

function applyAnalyticsFilters() {
    loadAnalytics();
}

function clearAnalyticsFilters() {
    const today = new Date();
    const firstDayOfYear = new Date(today.getFullYear(), 0, 1);
    
    document.getElementById('analyticsStartDate').value = firstDayOfYear.toISOString().split('T')[0];
    document.getElementById('analyticsEndDate').value = today.toISOString().split('T')[0];
    
    loadAnalytics();
}

function renderCategoryChart(categories) {
    if (categoryChartInstance) {
        categoryChartInstance.destroy();
    }
    
    if (categories.length === 0) {
        document.querySelector("#categoryChart").innerHTML = '<p class="text-center text-muted p-3">No data for selected period</p>';
        return;
    }
    // ensure div is clean if it had text
    document.querySelector("#categoryChart").innerHTML = '';

    const options = {
        series: categories.map(c => c.total_amount),
        labels: categories.map(c => c.category_name),
        colors: categories.map(c => c.color_code),
        chart: {
            type: 'donut',
            height: 350
        },
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val, opts) {
                const percent = opts.w.globals.seriesPercent[opts.seriesIndex];
                return percent ? parseFloat(percent).toFixed(1) + '%' : '0%';
            }
        },
        tooltip: {
            enabled: true,
            y: {
                formatter: function(val) {
                    return 'Rs. ' + formatNumber(val);
                },
                title: {
                    formatter: function (seriesName) {
                        return seriesName + ': ';
                    }
                }
            }
        }
    };
    
    categoryChartInstance = new ApexCharts(document.querySelector("#categoryChart"), options);
    categoryChartInstance.render();
}

function renderTrendChart(trendData) {
    if (trendChartInstance) {
        trendChartInstance.destroy();
    }
    
    if (!trendData || trendData.length === 0) {
        document.querySelector("#trendChart").innerHTML = '<p class="text-center text-muted p-3">No data for selected period</p>';
        return;
    }
    // ensure div is clean if it had text
    document.querySelector("#trendChart").innerHTML = '';

    const options = {
        series: [{
            name: 'Total Expenses',
            data: trendData.map(t => t.total)
        }],
        chart: {
            height: 350,
            type: 'bar', // or 'area'
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '45%',
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 2
        },
        xaxis: {
            categories: trendData.map(t => t.month),
            labels: {
                rotate: -45
            }
        },
        yaxis: {
            title: {
                text: 'Amount (Rs.)'
            },
            labels: {
                formatter: function (val) {
                    return formatNumber(val);
                }
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rs. " + formatNumber(val);
                }
            }
        },
        colors: ['#3498db']
    };

    trendChartInstance = new ApexCharts(document.querySelector("#trendChart"), options);
    trendChartInstance.render();
}

// Utility Functions
function getAuthHeaders() {
    const token = localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
    return {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + token
    };
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });
};
