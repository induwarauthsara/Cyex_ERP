<?php
include 'nav.php';
?>
<title>Customers - Admin Panel</title>

<style>
    body {
        /* background-color: #1a1a1a; */
        color: white;
    }

    .customers-container {
        padding: 20px;
        max-width: 1600px;
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
        color: white;
    }

    .page-header h1 i {
        color: white;
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
        margin: 2px;
    }

    .customers-table-container {
        background: transparent;
        padding: 0;
    }

    table.customers-table {
        width: 100% !important;
        background: transparent !important;
        color: white !important;
    }

    table.customers-table thead th {
        background: transparent !important;
        font-weight: 600;
        color: white !important;
        padding: 12px !important;
        border-bottom: 2px solid #444 !important;
    }

    table.customers-table tbody td {
        padding: 10px !important;
        vertical-align: middle;
        color: white !important;
        border-bottom: 1px solid #333 !important;
    }

    table.customers-table tbody tr {
        background: transparent !important;
    }

    table.customers-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05) !important;
    }

    .customer-type-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
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
        white-space: nowrap;
        display: flex;
        gap: 3px;
        align-items: center;
    }

    .stat-value {
        font-weight: 600;
        color: white;
    }

    .stat-value-white {
        font-weight: 600;
        color: white !important;
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

    /* DataTable custom styles - Dark Theme */
    .dataTables_wrapper {
        color: white !important;
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        color: white !important;
    }

    .dataTables_wrapper .dataTables_length label,
    .dataTables_wrapper .dataTables_filter label {
        color: white !important;
    }

    .dataTables_wrapper .dataTables_length select {
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #444;
        background: #2a2a2a;
        color: white;
    }

    .dataTables_wrapper .dataTables_filter input {
        padding: 5px 10px;
        border-radius: 4px;
        border: 1px solid #444;
        margin-left: 8px;
        background: #2a2a2a;
        color: white;
    }

    .dataTables_wrapper .dataTables_info {
        padding-top: 15px;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 15px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 10px;
        margin: 0 2px;
        border-radius: 4px;
        border: 1px solid #444 !important;
        background: #2a2a2a !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #3a3a3a !important;
        border-color: #007bff !important;
        color: white !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #007bff !important;
        color: white !important;
        border-color: #007bff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: #666 !important;
    }

    div.dt-buttons {
        margin-bottom: 15px;
    }

    .dt-button {
        padding: 8px 15px !important;
        border-radius: 4px !important;
        margin-right: 5px !important;
        background: #007bff !important;
        color: white !important;
        border: none !important;
    }

    .dt-button:hover {
        background: #0056b3 !important;
    }

    /* DataTable sorting icons */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:after {
        color: white !important;
    }

    /* Remove border around entire table wrapper */
    .dataTables_wrapper {
        border: none !important;
    }

    table.dataTable {
        border-collapse: collapse !important;
        border: none !important;
    }

    /* Sweet Alert Dark Theme */
    .swal2-popup {
        background: #2a2a2a !important;
        color: white !important;
    }

    .swal2-title {
        color: white !important;
    }

    .swal2-html-container {
        color: white !important;
    }

    .swal2-input {
        background: #1a1a1a !important;
        color: white !important;
        border: 1px solid #444 !important;
    }

    .swal2-input:focus {
        background: white !important;
        color: black !important;
        border: 1px solid #007bff !important;
        outline: none !important;
    }

    .swal2-select {
        background: #1a1a1a !important;
        color: white !important;
        border: 1px solid #444 !important;
    }

    .swal2-select:focus {
        background: white !important;
        color: black !important;
        border: 1px solid #007bff !important;
        outline: none !important;
    }
</style>

<div class="customers-container">
    <div class="page-header">
        <h1><i class="fas fa-users"></i> Customers Management</h1>
        <button class="btn btn-primary" onclick="openAddCustomerModal()">
            <i class="fas fa-plus"></i> Add New Customer
        </button>
    </div>

    <div class="customers-table-container">
        <table id="DataTable" class="customers-table display responsive nowrap" style="width:100%">
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
                <!-- Data will be loaded via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script>
let customersTable;

// Load customers on page load
$(document).ready(function() {
    loadCustomersData();
});

// Load customers data
function loadCustomersData() {
    fetch('/inc/api/customers_list.php?page=1&per_page=10000')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                initializeDataTable(data.customers);
            } else {
                Swal.fire('Error', 'Failed to load customers', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error loading customers', 'error');
        });
}

// Initialize DataTable
function initializeDataTable(customers) {
    // Destroy existing table if any
    if ($.fn.DataTable.isDataTable('#DataTable')) {
        $('#DataTable').DataTable().destroy();
    }

    // Prepare data for DataTable
    const tableData = customers.map(customer => {
        const typeClass = `type-${customer.type}`;
        const outstandingClass = customer.statistics.outstanding_balance > 0 ? 'negative' : 'positive';

        return [
            customer.id,
            `<strong>${customer.name}</strong>`,
            customer.mobile,
            `<span class="customer-type-badge ${typeClass}">${customer.type.toUpperCase()}</span>`,
            `<span class="stat-value-white">Rs. ${parseFloat(customer.extra_fund).toFixed(2)}</span>`,
            `<span class="stat-value-white">${customer.statistics.invoice_count}</span>`,
            `<span class="stat-value positive">Rs. ${parseFloat(customer.statistics.total_purchases).toFixed(2)}</span>`,
            `<span class="stat-value ${outstandingClass}">Rs. ${parseFloat(customer.statistics.outstanding_balance).toFixed(2)}</span>`,
            `<div class="actions-cell">
                <button class="btn btn-info btn-sm" onclick="viewCustomer(${customer.id})" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-sm" onclick="editCustomer(${customer.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteCustomer(${customer.id}, '${customer.name.replace(/'/g, "\\'")}', ${customer.statistics.invoice_count})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`
        ];
    });

    // Initialize DataTable
    customersTable = $('#DataTable').DataTable({
        data: tableData,
        columns: [
            { title: "ID" },
            { title: "Name" },
            { title: "Mobile" },
            { title: "Type" },
            { title: "Extra Fund" },
            { title: "Invoices" },
            { title: "Total Sales" },
            { title: "Outstanding" },
            { title: "Actions", orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: 'lBfrtip',
        buttons: [
            {
                extend: 'copy',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'excel',
                title: 'Customers List',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'pdf',
                title: 'Customers List',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            },
            {
                extend: 'print',
                title: 'Customers List',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7]
                }
            }
        ],
        responsive: true,
        language: {
            search: "Search Customers:",
            lengthMenu: "Show _MENU_ customers per page",
            info: "Showing _START_ to _END_ of _TOTAL_ customers",
            infoEmpty: "No customers available",
            infoFiltered: "(filtered from _MAX_ total customers)",
            zeroRecords: "No matching customers found",
            emptyTable: "No customers available in table"
        }
    });
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
            loadCustomersData();
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
                    loadCustomersData();
                }
            });
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
        });
}

// Delete customer
function deleteCustomer(customerId, customerName, invoiceCount) {
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
                    loadCustomersData();
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
</script>
