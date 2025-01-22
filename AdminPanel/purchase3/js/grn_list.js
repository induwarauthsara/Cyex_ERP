import {
    CONFIG,
    formatDate,
    handleError,
    showSuccess,
    confirmAction
} from '../../js/config.js';

let selectedGRNs = new Set();

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
    const table = $('#grnTable').DataTable({
        serverSide: true,
        processing: true,
        responsive: true,
        ajax: {
            url: CONFIG.API_ENDPOINTS.GRN,
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
                data: 'grn_id',
                orderable: false,
                render: function(data) {
                    return `<input type="checkbox" class="form-checkbox select-item" value="${data}">`;
                }
            },
            {
                data: 'grn_number',
                render: function(data, type, row) {
                    return `<span class="font-medium">${data}</span>`;
                }
            },
            {
                data: 'po_number',
                render: function(data) {
                    return `<a href="view_po.php?id=${data}" class="text-blue-600 hover:text-blue-800">${data}</a>`;
                }
            },
            {
                data: 'receipt_date',
                render: function(data) {
                    return formatDate(data);
                }
            },
            {
                data: 'supplier_name'
            },
            {
                data: 'invoice_number'
            },
            {
                data: 'status',
                render: function(data) {
                    const statusClasses = {
                        draft: 'bg-gray-200 text-gray-800',
                        completed: 'bg-green-200 text-green-800',
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
                    actions.push(`<button class="viewBtn text-blue-600 hover:text-blue-800 mr-2" 
                                        data-id="${row.grn_id}" title="View GRN">
                        <i class="fas fa-eye"></i>
                    </button>`);

                    // Print action always available
                    actions.push(`<button class="printBtn text-purple-600 hover:text-purple-800 mr-2" 
                                        data-id="${row.grn_id}" title="Print GRN">
                        <i class="fas fa-print"></i>
                    </button>`);

                    // Edit only for draft status
                    if (row.status === 'draft') {
                        actions.push(`<button class="editBtn text-green-600 hover:text-green-800 mr-2" 
                                            data-id="${row.grn_id}" title="Edit GRN">
                            <i class="fas fa-edit"></i>
                        </button>`);

                        actions.push(`<button class="deleteBtn text-red-600 hover:text-red-800" 
                                            data-id="${row.grn_id}" title="Delete GRN">
                            <i class="fas fa-trash"></i>
                        </button>`);
                    }

                    return actions.join('');
                }
            }
        ],
        order: [
            [3, 'desc']
        ], // Order by receipt date
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

    // Checkbox handlers
    $('#selectAll').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.select-item').prop('checked', isChecked);
        updateSelectedGRNs();
    });

    $('#grnTable').on('change', '.select-item', updateSelectedGRNs);

    // Action button handlers
    $('#grnTable').on('click', '.viewBtn', function() {
        const grnId = $(this).data('id');
        window.location.href = `view_grn.php?id=${grnId}`;
    });

    $('#grnTable').on('click', '.printBtn', function() {
        const grnId = $(this).data('id');
        printGRN(grnId);
    });

    $('#grnTable').on('click', '.editBtn', function() {
        const grnId = $(this).data('id');
        window.location.href = `edit_grn.php?id=${grnId}`;
    });

    $('#grnTable').on('click', '.deleteBtn', async function() {
        const grnId = $(this).data('id');
        if (await confirmAction('Are you sure you want to delete this GRN?')) {
            deleteGRN(grnId);
        }
    });

    // Barcode print button handler
    $('#btnBarcodePrint').on('click', function() {
        if (selectedGRNs.size === 0) {
            Swal.fire({
                title: 'No GRNs Selected',
                text: 'Please select at least one GRN to print barcodes',
                icon: 'warning'
            });
            return;
        }
        showBarcodePrintModal();
    });
});

function updateSelectedGRNs() {
    selectedGRNs.clear();
    $('.select-item:checked').each(function() {
        selectedGRNs.add($(this).val());
    });

    const allChecked = $('.select-item').length === selectedGRNs.size;
    $('#selectAll').prop('checked', allChecked);

    // Update barcode print button state
    $('#btnBarcodePrint').prop('disabled', selectedGRNs.size === 0)
        .toggleClass('opacity-50 cursor-not-allowed', selectedGRNs.size === 0);
}

async function deleteGRN(grnId) {
    try {
        const response = await fetch(CONFIG.API_ENDPOINTS.GRN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'delete',
                grn_id: grnId
            })
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('GRN deleted successfully');
            $('#grnTable').DataTable().ajax.reload();
        } else {
            throw new Error(data.message || 'Failed to delete GRN');
        }
    } catch (error) {
        handleError(error);
    }
}

async function printGRN(grnId) {
    try {
        const response = await fetch(`${CONFIG.API_ENDPOINTS.GRN}?action=print&id=${grnId}`);

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const printWindow = window.open(url);

            printWindow.onload = function() {
                printWindow.print();
                window.URL.revokeObjectURL(url);
            };
        } else {
            throw new Error('Failed to generate GRN print view');
        }
    } catch (error) {
        handleError(error);
    }
}

async function showBarcodePrintModal() {
    try {
        const response = await fetch(CONFIG.API_ENDPOINTS.GRN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'get_barcode_items',
                grn_ids: Array.from(selectedGRNs)
            })
        });

        const data = await response.json();

        if (data.success) {
            populateBarcodeItemsTable(data.items);
            $('#barcodePrintModal').removeClass('hidden');
        } else {
            throw new Error(data.message || 'Failed to load barcode items');
        }
    } catch (error) {
        handleError(error);
    }
}

function populateBarcodeItemsTable(items) {
    const tbody = $('#barcodeItemsTable tbody');
    tbody.empty();

    items.forEach(item => {
        const row = `
            <tr data-item-id="${item.product_id}" data-batch-id="${item.batch_id}">
                <td>${item.product_name}</td>
                <td>${item.batch_number}</td>
                <td>
                    <input type="number" class="form-input w-24 barcode-qty" 
                           value="${item.received_qty}" min="1">
                </td>
                <td>
                    <button class="text-red-600 hover:text-red-800 delete-item">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Add handlers for quantity changes and item deletion
    $('.barcode-qty').on('change', updateBarcodeQuantity);
    $('.delete-item').on('click', deleteBarcodePrintItem);
}

function updateBarcodeQuantity() {
    // Validate and update quantity
    const qty = parseInt($(this).val());
    if (qty < 1) {
        $(this).val(1);
    }
}

function deleteBarcodePrintItem() {
    $(this).closest('tr').remove();
}

function closeBarcodeModal() {
    $('#barcodePrintModal').addClass('hidden');
}

async function printBarcodes() {
    const items = [];
    $('#barcodeItemsTable tbody tr').each(function() {
        items.push({
            product_id: $(this).data('item-id'),
            batch_id: $(this).data('batch-id'),
            quantity: parseInt($(this).find('.barcode-qty').val())
        });
    });

    if (items.length === 0) {
        Swal.fire({
            title: 'No Items',
            text: 'No items to print',
            icon: 'warning'
        });
        return;
    }

    const printData = {
        paper_size: $('#paperSize').val(),
        show_price: $('#showPrice').prop('checked'),
        show_unit: $('#showUnit').prop('checked'),
        show_category: $('#showCategory').prop('checked'),
        show_promo_price: $('#showPromoPrice').prop('checked'),
        items: items
    };

    try {
        const response = await fetch(CONFIG.API_ENDPOINTS.BARCODE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'print',
                data: printData
            })
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const printWindow = window.open(url);

            printWindow.onload = function() {
                printWindow.print();
                window.URL.revokeObjectURL(url);
                closeBarcodeModal();
            };
        } else {
            throw new Error('Failed to generate barcode print');
        }
    } catch (error) {
        handleError(error);
    }
}