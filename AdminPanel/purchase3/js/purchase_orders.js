// File: /AdminPanel/purchase/js/purchase_orders.js

import {
    CONFIG,
    formatCurrency,
    formatDate,
    handleError,
    showSuccess,
    confirmAction
} from '../../js/config.js';

$(document).ready(function() {
    // Initialize Select2 for supplier filter
    $('#filterSupplier').select2({
        placeholder: 'Select Supplier',
        allowClear: true,
        ajax: {
            url: CONFIG.API_ENDPOINTS.SUPPLIERS,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    action: 'search'
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(supplier => ({
                        id: supplier.supplier_id,
                        text: supplier.supplier_name
                    }))
                };
            },
            cache: true
        }
    });

    // Initialize DataTable
    const table = $('#purchaseOrdersTable').DataTable({
        serverSide: true,
        processing: true,
        responsive: true,
        ajax: {
            url: CONFIG.API_ENDPOINTS.PURCHASE_ORDERS,
            type: 'POST',
            data: function(d) {
                return {
                    ...d,
                    action: 'list',
                    status: $('#filterStatus').val(),
                    supplier_id: $('#filterSupplier').val(),
                    date_from: $('#filterDateFrom').val(),
                    date_to: $('#filterDateTo').val()
                };
            }
        },
        columns: [{
                data: 'po_number',
                render: function(data, type, row) {
                    return `<span class="font-medium">${data}</span>`;
                }
            },
            {
                data: 'order_date',
                render: function(data) {
                    return formatDate(data);
                }
            },
            {
                data: 'supplier_name'
            },
            {
                data: 'total_amount',
                render: function(data) {
                    return formatCurrency(data);
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const statusClasses = {
                        draft: 'bg-gray-200 text-gray-800',
                        pending: 'bg-yellow-200 text-yellow-800',
                        approved: 'bg-blue-200 text-blue-800',
                        ordered: 'bg-purple-200 text-purple-800',
                        received: 'bg-green-200 text-green-800',
                        cancelled: 'bg-red-200 text-red-800'
                    };
                    return `<span class="px-2 py-1 rounded-full text-xs font-medium ${statusClasses[data]}">${data.toUpperCase()}</span>`;
                }
            },
            {
                data: 'created_by_name'
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const actions = [];

                    // View action always available
                    actions.push(`<button class="viewBtn text-blue-600 hover:text-blue-800 mr-2" data-id="${row.po_id}">
                        <i class="fas fa-eye"></i>
                    </button>`);

                    // Edit only for draft/pending status
                    if (['draft', 'pending'].includes(row.status)) {
                        actions.push(`<button class="editBtn text-green-600 hover:text-green-800 mr-2" data-id="${row.po_id}">
                            <i class="fas fa-edit"></i>
                        </button>`);
                    }

                    // Delete only for draft status
                    if (row.status === 'draft') {
                        actions.push(`<button class="deleteBtn text-red-600 hover:text-red-800" data-id="${row.po_id}">
                            <i class="fas fa-trash"></i>
                        </button>`);
                    }

                    // Create GRN for ordered status
                    if (row.status === 'ordered') {
                        actions.push(`<button class="createGRNBtn text-purple-600 hover:text-purple-800 ml-2" data-id="${row.po_id}">
                            <i class="fas fa-box"></i>
                        </button>`);
                    }

                    return actions.join('');
                }
            }
        ],
        order: [
            [1, 'desc']
        ],
        dom: 'Bfrtip',
        buttons: [{
                extend: 'copy',
                className: 'hidden'
            },
            {
                extend: 'excel',
                className: 'hidden'
            },
            {
                extend: 'pdf',
                className: 'hidden'
            },
            {
                extend: 'print',
                className: 'hidden'
            }
        ]
    });

    // Filter change handlers
    $('#filterStatus, #filterSupplier, #filterDateFrom, #filterDateTo').on('change', function() {
        table.ajax.reload();
    });

    // Export button handlers
    $('#btnExport').on('click', function(e) {
        e.stopPropagation();
        $('#exportMenu').toggleClass('hidden');
    });

    // Close export menu when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#btnExport').length) {
            $('#exportMenu').addClass('hidden');
        }
    });

    // Export button click handlers
    $('.exportBtn').on('click', function() {
        const type = $(this).data('type');
        table.button(`.buttons-${type}`).trigger();
        $('#exportMenu').addClass('hidden');
    });

    // Create PO button handler
    $('#btnCreatePO').on('click', function() {
        window.location.href = 'create_po.php';
    });

    // View button handler
    $('#purchaseOrdersTable').on('click', '.viewBtn', function() {
        const poId = $(this).data('id');
        window.location.href = `view_po.php?id=${poId}`;
    });

    // Edit button handler
    $('#purchaseOrdersTable').on('click', '.editBtn', function() {
        const poId = $(this).data('id');
        window.location.href = `edit_po.php?id=${poId}`;
    });

    // Delete button handler
    $('#purchaseOrdersTable').on('click', '.deleteBtn', async function() {
        const poId = $(this).data('id');

        if (await confirmAction('Are you sure you want to delete this purchase order?')) {
            try {
                const response = await fetch(CONFIG.API_ENDPOINTS.PURCHASE_ORDERS, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        po_id: poId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showSuccess('Purchase order deleted successfully');
                    table.ajax.reload();
                } else {
                    throw new Error(data.message || 'Failed to delete purchase order');
                }
            } catch (error) {
                handleError(error);
            }
        }
    });

    // Create GRN button handler
    $('#purchaseOrdersTable').on('click', '.createGRNBtn', function() {
        const poId = $(this).data('id');
        window.location.href = `create_grn.php?po_id=${poId}`;
    });
});