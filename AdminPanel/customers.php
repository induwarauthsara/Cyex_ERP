<?php
include 'nav.php';
?>
<title>Customers - Admin Panel</title>

<style>
    .customers-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        margin: 0;
        color: #333;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-warning {
        background: #ffc107;
        color: #333;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-info {
        background: #17a2b8;
        color: white;
    }

    .btn-sm {
        padding: 5px 10px;
        font-size: 12px;
    }

    .filters-section {
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filters-row {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-group {
        flex: 1;
        min-width: 200px;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #555;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .customers-table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    table.customers-table {
        width: 100%;
        border-collapse: collapse;
    }

    table.customers-table thead {
        background: #f8f9fa;
    }

    table.customers-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #dee2e6;
    }

    table.customers-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    table.customers-table tbody tr:hover {
        background: #f8f9fa;
    }

    .customer-type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .type-regular {
        background: #e3f2fd;
        color: #1976d2;
    }

    .type-vip {
        background: #fff3e0;
        color: #f57c00;
    }

    .type-wholesale {
        background: #f3e5f5;
        color: #7b1fa2;
    }

    .actions-cell {
        display: flex;
        gap: 5px;
    }

    .stat-value {
        font-weight: 600;
        color: #333;
    }

    .positive {
        color: #28a745;
    }

    .negative {
        color: #dc3545;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        color: #ddd;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        padding: 20px;
        background: white;
        border-top: 1px solid #f0f0f0;
    }

    .pagination button {
        padding: 8px 15px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        border-radius: 5px;
    }

    .pagination button:hover:not(:disabled) {
        background: #f8f9fa;
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination .page-info {
        color: #666;
        font-size: 14px;
    }
</style>

<div class="customers-container">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Customers Management</h1>
        <button class="btn btn-primary" onclick="openAddCustomerModal()">
            <i class="fas fa-plus"></i> Add New Customer
        </button>
    </div>

    <div class="filters-section">
        <div class="filters-row">
            <div class="filter-group">
                <label for="searchInput">Search</label>
                <input type="text" id="searchInput" placeholder="Search by name or mobile..." onkeyup="searchCustomers()">
            </div>
            <div class="filter-group">
                <label for="typeFilter">Customer Type</label>
                <select id="typeFilter" onchange="loadCustomers()">
                    <option value="all">All Types</option>
                    <option value="regular">Regular</option>
                    <option value="vip">VIP</option>
                    <option value="wholesale">Wholesale</option>
                </select>
            </div>
            <div class="filter-group">
                <label>&nbsp;</label>
                <button class="btn btn-info" onclick="resetFilters()">
                    <i class="fas fa-sync"></i> Reset Filters
                </button>
            </div>
        </div>
    </div>

    <div class="customers-table-container">
        <div id="customersTableContent">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i> Loading customers...
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let searchTimeout = null;

// Load customers on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCustomers();
});

// Search with debounce
function searchCustomers() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadCustomers();
    }, 500);
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('typeFilter').value = 'all';
    currentPage = 1;
    loadCustomers();
}

// Load customers
function loadCustomers(page = currentPage) {
    currentPage = page;
    const search = document.getElementById('searchInput').value;
    const type = document.getElementById('typeFilter').value;

    const url = `/inc/api/customers_list.php?page=${page}&search=${encodeURIComponent(search)}&type=${type}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderCustomersTable(data.customers, data.pagination);
            } else {
                showError('Failed to load customers');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error loading customers');
        });
}

// Render customers table
function renderCustomersTable(customers, pagination) {
    if (customers.length === 0) {
        document.getElementById('customersTableContent').innerHTML = `
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No customers found</h3>
                <p>Try adjusting your search criteria or add a new customer</p>
            </div>
        `;
        return;
    }

    let html = `
        <table class="customers-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Type</th>
                    <th>Extra Fund</th>
                    <th>Invoices</th>
                    <th>Total Sales</th>
                    <th>Outstanding</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
    `;

    customers.forEach(customer => {
        const typeClass = `type-${customer.type}`;
        const outstandingClass = customer.statistics.outstanding_balance > 0 ? 'negative' : 'positive';

        html += `
            <tr>
                <td>${customer.id}</td>
                <td><strong>${customer.name}</strong></td>
                <td>${customer.mobile}</td>
                <td><span class="customer-type-badge ${typeClass}">${customer.type.toUpperCase()}</span></td>
                <td class="stat-value">Rs. ${parseFloat(customer.extra_fund).toFixed(2)}</td>
                <td class="stat-value">${customer.statistics.invoice_count}</td>
                <td class="stat-value positive">Rs. ${parseFloat(customer.statistics.total_purchases).toFixed(2)}</td>
                <td class="stat-value ${outstandingClass}">Rs. ${parseFloat(customer.statistics.outstanding_balance).toFixed(2)}</td>
                <td>
                    <div class="actions-cell">
                        <button class="btn btn-info btn-sm" onclick="viewCustomer(${customer.id})" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editCustomer(${customer.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="deleteCustomer(${customer.id}, '${customer.name}', ${customer.statistics.invoice_count})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
        <div class="pagination">
            <button ${pagination.current_page === 1 ? 'disabled' : ''} onclick="loadCustomers(1)">
                <i class="fas fa-angle-double-left"></i>
            </button>
            <button ${pagination.current_page === 1 ? 'disabled' : ''} onclick="loadCustomers(${pagination.current_page - 1})">
                <i class="fas fa-angle-left"></i> Previous
            </button>
            <span class="page-info">Page ${pagination.current_page} of ${pagination.total_pages} (${pagination.total} customers)</span>
            <button ${!pagination.has_more ? 'disabled' : ''} onclick="loadCustomers(${pagination.current_page + 1})">
                Next <i class="fas fa-angle-right"></i>
            </button>
            <button ${!pagination.has_more ? 'disabled' : ''} onclick="loadCustomers(${pagination.total_pages})">
                <i class="fas fa-angle-double-right"></i>
            </button>
        </div>
    `;

    document.getElementById('customersTableContent').innerHTML = html;
}

// Add customer modal
function openAddCustomerModal() {
    Swal.fire({
        title: 'Add New Customer',
        html: `
            <div style="text-align: left;">
                <label>Name <span style="color: red;">*</span></label>
                <input type="text" id="customerName" class="swal2-input" placeholder="Customer Name" style="width: 90%;">
                
                <label>Mobile <span style="color: red;">*</span></label>
                <input type="text" id="customerMobile" class="swal2-input" placeholder="07XXXXXXXX" maxlength="10" style="width: 90%;">
                
                <label>Type</label>
                <select id="customerType" class="swal2-input" style="width: 90%;">
                    <option value="regular">Regular</option>
                    <option value="vip">VIP</option>
                    <option value="wholesale">Wholesale</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Customer',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const name = document.getElementById('customerName').value.trim();
            const mobile = document.getElementById('customerMobile').value.trim();
            const type = document.getElementById('customerType').value;

            if (!name) {
                Swal.showValidationMessage('Please enter customer name');
                return false;
            }

            if (!mobile || !/^[0-9]{10}$/.test(mobile)) {
                Swal.showValidationMessage('Please enter valid 10 digit mobile number');
                return false;
            }

            return fetch('/inc/api/customer_add.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, mobile, type })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message);
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Error: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Success!', 'Customer added successfully', 'success');
            loadCustomers();
        }
    });
}

// View customer details
function viewCustomer(customerId) {
    window.location.href = `/AdminPanel/customer_details.php?id=${customerId}`;
}

// Edit customer
function editCustomer(customerId) {
    // First fetch customer details
    fetch(`/inc/api/customer_get.php?id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                throw new Error(data.message);
            }

            const customer = data.customer;
            
            Swal.fire({
                title: 'Edit Customer',
                html: `
                    <div style="text-align: left;">
                        <label>Name <span style="color: red;">*</span></label>
                        <input type="text" id="editCustomerName" class="swal2-input" value="${customer.name}" style="width: 90%;">
                        
                        <label>Mobile <span style="color: red;">*</span></label>
                        <input type="text" id="editCustomerMobile" class="swal2-input" value="${customer.mobile}" maxlength="10" style="width: 90%;">
                        
                        <label>Type</label>
                        <select id="editCustomerType" class="swal2-input" style="width: 90%;">
                            <option value="regular" ${customer.type === 'regular' ? 'selected' : ''}>Regular</option>
                            <option value="vip" ${customer.type === 'vip' ? 'selected' : ''}>VIP</option>
                            <option value="wholesale" ${customer.type === 'wholesale' ? 'selected' : ''}>Wholesale</option>
                        </select>
                        
                        ${customer.invoice_count > 0 ? `
                        <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px;">
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" id="updatePastInvoices" style="margin-right: 8px;">
                                <span>Update ${customer.invoice_count} past invoice(s) with new name/mobile</span>
                            </label>
                            <small style="color: #856404; display: block; margin-top: 5px;">
                                If unchecked, changes will only affect future invoices
                            </small>
                        </div>
                        ` : ''}
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update Customer',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const name = document.getElementById('editCustomerName').value.trim();
                    const mobile = document.getElementById('editCustomerMobile').value.trim();
                    const type = document.getElementById('editCustomerType').value;
                    const updatePastInvoices = document.getElementById('updatePastInvoices')?.checked || false;

                    if (!name) {
                        Swal.showValidationMessage('Please enter customer name');
                        return false;
                    }

                    if (!mobile || !/^[0-9]{10}$/.test(mobile)) {
                        Swal.showValidationMessage('Please enter valid 10 digit mobile number');
                        return false;
                    }

                    return fetch('/inc/api/customer_edit.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            id: customerId, 
                            name, 
                            mobile, 
                            type,
                            update_past_invoices: updatePastInvoices
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message);
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Error: ${error.message}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    const message = result.value.invoices_updated > 0 
                        ? `Customer updated successfully. ${result.value.invoices_updated} invoice(s) were updated.`
                        : 'Customer updated successfully';
                    
                    Swal.fire('Success!', message, 'success');
                    loadCustomers();
                }
            });
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
}

// Delete customer
function deleteCustomer(customerId, customerName, invoiceCount) {
    if (invoiceCount > 0) {
        Swal.fire({
            title: 'Cannot Delete Customer',
            html: `<p>This customer has <strong>${invoiceCount} invoice(s)</strong> associated with their account.</p>
                   <p>Please remove or reassign these invoices before deleting the customer.</p>`,
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    Swal.fire({
        title: 'Delete Customer?',
        html: `Are you sure you want to delete <strong>${customerName}</strong>?<br><br>
               <span style="color: red;">This action cannot be undone!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/inc/api/customer_delete.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: customerId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Deleted!', 'Customer has been deleted.', 'success');
                    loadCustomers();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Failed to delete customer', 'error');
            });
        }
    });
}

function showError(message) {
    document.getElementById('customersTableContent').innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
            <h3>Error</h3>
            <p>${message}</p>
        </div>
    `;
}
</script>
