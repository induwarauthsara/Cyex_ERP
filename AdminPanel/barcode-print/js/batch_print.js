// File: /AdminPanel/barcode-print/js/batch_print.js

import {
    CONFIG,
    handleError,
    showSuccess,
    confirmAction
} from '../../js/config.js';

let batchGroups = [];
let grnTable, poTable;

$(document).ready(function() {
    // Initialize Select2 for product search
    initializeProductSearch();

    // Initialize DataTables
    initializeDataTables();

    // Event handlers for source selection buttons
    $('#fromGRNBtn').on('click', () => openModal('grnModal'));
    $('#fromPOBtn').on('click', () => openModal('poModal'));
    $('#manualBtn').on('click', toggleProductSearch);

    // Event handlers for actions
    $('#addProductBtn').on('click', addProduct);
    $('#estimateBtn').on('click', showPrintStatistics);
    $('#previewBtn').on('click', showPreview);
    $('#printBtn').on('click', printBarcodes);

    // Event handlers for batch groups
    $('#batchGroups').on('click', '.delete-group', deleteBatchGroup);
    $('#batchGroups').on('click', '.edit-group', editBatchGroup);
    $('#batchGroups').on('change', '.batch-qty', updateBatchQuantity);
    $('#batchGroups').on('click', '.batch-collapse', toggleBatchCollapse);

    // Event handlers for settings changes
    $('#paperSize, #printMode, #sorting').on('change', refreshPreview);
    $('input[type="checkbox"]').on('change', refreshPreview);
});

function initializeProductSearch() {
    $('#productSearch').select2({
        placeholder: 'Search products...',
        allowClear: true,
        ajax: {
            url: CONFIG.API_ENDPOINTS.PRODUCTS,
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term,
                    action: 'search',
                    include_batches: true
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(product => ({
                        id: product.product_id,
                        text: product.product_name,
                        batches: product.batches || [],
                        price: product.selling_price,
                        category: product.category_name,
                        unit: product.unit
                    }))
                };
            },
            cache: true
        }
    });
}

function initializeDataTables() {
    // Initialize GRN DataTable
    grnTable = $('#grnTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: CONFIG.API_ENDPOINTS.GRN,
            type: 'POST',
            data: function(d) {
                return {
                    ...d,
                    action: 'list',
                    status: 'completed'
                };
            }
        },
        columns: [{
                data: 'grn_number'
            },
            {
                data: 'receipt_date',
                render: data => moment(data).format('YYYY-MM-DD')
            },
            {
                data: 'supplier_name'
            },
            {
                data: 'status',
                render: data => `<span class="status-badge ${data}">${data}</span>`
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="select-grn btn btn-sm bg-blue-500 hover:bg-blue-600 text-white"
                                data-id="${data.grn_id}">
                            Select
                        </button>
                    `;
                }
            }
        ]
    });

    // Initialize PO DataTable
    poTable = $('#poTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: CONFIG.API_ENDPOINTS.PURCHASE_ORDERS,
            type: 'POST',
            data: function(d) {
                return {
                    ...d,
                    action: 'list',
                    status: 'ordered'
                };
            }
        },
        columns: [{
                data: 'po_number'
            },
            {
                data: 'order_date',
                render: data => moment(data).format('YYYY-MM-DD')
            },
            {
                data: 'supplier_name'
            },
            {
                data: 'status',
                render: data => `<span class="status-badge ${data}">${data}</span>`
            },
            {
                data: null,
                render: function(data) {
                    return `
                        <button class="select-po btn btn-sm bg-blue-500 hover:bg-blue-600 text-white"
                                data-id="${data.po_id}">
                            Select
                        </button>
                    `;
                }
            }
        ]
    });

    // Event handlers for selection
    $('#grnTable').on('click', '.select-grn', selectGRN);
    $('#poTable').on('click', '.select-po', selectPO);
}

async function selectGRN() {
    const grnId = $(this).data('id');

    try {
        const response = await fetch(`${CONFIG.API_ENDPOINTS.GRN}?action=get_items&id=${grnId}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load GRN items');
        }

        // Process items into batch groups
        const newGroups = processItemsIntoBatchGroups(data.items);

        // Merge with existing groups
        mergeBatchGroups(newGroups);

        closeModal('grnModal');
        refreshBatchGroups();

    } catch (error) {
        handleError(error);
    }
}

async function selectPO() {
    const poId = $(this).data('id');

    try {
        const response = await fetch(`${CONFIG.API_ENDPOINTS.PURCHASE_ORDERS}?action=get_items&id=${poId}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load PO items');
        }

        // Process items into batch groups
        const newGroups = processItemsIntoBatchGroups(data.items.map(item => ({
            ...item,
            batch_number: generateBatchNumber(item.product_id)
        })));

        // Merge with existing groups
        mergeBatchGroups(newGroups);

        closeModal('poModal');
        refreshBatchGroups();

    } catch (error) {
        handleError(error);
    }
}

function processItemsIntoBatchGroups(items) {
    const groups = {};

    items.forEach(item => {
        const batchKey = item.batch_number || generateBatchNumber(item.product_id);

        if (!groups[batchKey]) {
            groups[batchKey] = {
                batch_number: batchKey,
                items: [],
                collapsed: false
            };
        }

        groups[batchKey].items.push({
            product_id: item.product_id,
            product_name: item.product_name,
            quantity: item.received_qty || item.quantity,
            price: item.selling_price || item.unit_cost,
            category: item.category_name,
            unit: item.unit
        });
    });

    return groups;
}

function mergeBatchGroups(newGroups) {
    Object.entries(newGroups).forEach(([batchNumber, group]) => {
        if (batchGroups[batchNumber]) {
            // Merge items for existing batch
            group.items.forEach(newItem => {
                const existingItem = batchGroups[batchNumber].items.find(
                    item => item.product_id === newItem.product_id
                );

                if (existingItem) {
                    existingItem.quantity += newItem.quantity;
                } else {
                    batchGroups[batchNumber].items.push(newItem);
                }
            });
        } else {
            // Add new batch group
            batchGroups[batchNumber] = group;
        }
    });
}

function refreshBatchGroups() {
    const container = $('#batchGroups');
    container.empty();

    Object.entries(batchGroups).forEach(([batchNumber, group]) => {
                const totalQuantity = group.items.reduce((sum, item) => sum + item.quantity, 0);

                const groupHtml = `
            <div class="batch-group border dark:border-gray-700 rounded-lg p-4" 
                 data-batch="${batchNumber}">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <button class="batch-collapse ${group.collapsed ? 'rotate-180' : ''}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <h3 class="font-semibold dark:text-white">
                            Batch: ${batchNumber}
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            (${group.items.length} products, ${totalQuantity} labels)
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button class="edit-group text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="delete-group text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="batch-items mt-4 ${group.collapsed ? 'hidden' : ''}">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${group.items.map(item => `
                                <tr>
                                    <td class="py-2">${item.product_name}</td>
                                    <td class="py-2 text-center">
                                        <input type="number" 
                                               class="form-input w-24 batch-qty"
                                               value="${item.quantity}"
                                               min="1"
                                               data-product="${item.product_id}">
                                    </td>
                                    <td class="py-2 text-right">
                                        ${formatCurrency(item.price)}
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        container.append(groupHtml);
    });

    // Update preview if available
    if ($('#previewModal').is(':visible')) {
        refreshPreview();
    }
}

function toggleProductSearch() {
    $('#productSearchCard').toggleClass('hidden');
}

async function addProduct() {
    const selected = $('#productSearch').select2('data')[0];
    if (!selected) return;

    try {
        // If product has batches, show batch selection
        if (selected.batches && selected.batches.length > 0) {
            const batch = await selectBatch(selected);
            if (!batch) return;

            const batchGroup = {
                [batch.batch_number]: {
                    batch_number: batch.batch_number,
                    items: [{
                        product_id: selected.id,
                        product_name: selected.text,
                        quantity: batch.quantity,
                        price: batch.price || selected.price,
                        category: selected.category,
                        unit: selected.unit
                    }],
                    collapsed: false
                }
            };

            mergeBatchGroups(batchGroup);
        } else {
            // Generate new batch number for product
            const batchNumber = generateBatchNumber(selected.id);
            const batchGroup = {
                [batchNumber]: {
                    batch_number: batchNumber,
                    items: [{
                        product_id: selected.id,
                        product_name: selected.text,
                        quantity: 1,
                        price: selected.price,
                        category: selected.category,
                        unit: selected.unit
                    }],
                    collapsed: false
                }
            };

            mergeBatchGroups(batchGroup);
        }

        refreshBatchGroups();
        $('#productSearch').val(null).trigger('change');

    } catch (error) {
        handleError(error);
    }
}

function selectBatch(product) {
    return new Promise((resolve) => {
        Swal.fire({
            title: 'Select Batch',
            html: `
                <select id="batchSelect" class="form-input w-full">
                    ${product.batches.map(batch => `
                        <option value="${batch.batch_number}" 
                                data-price="${batch.selling_price}"
                                data-quantity="${batch.quantity}">
                            ${batch.batch_number} 
                            (${batch.quantity} in stock, ${formatCurrency(batch.selling_price)})
                        </option>
                    `).join('')}
                </select>
                <div class="mt-4">
                    <label>Quantity:</label>
                    <input type="number" id="batchQuantity" class="form-input w-full" 
                           min="1" value="1">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Add',
            preConfirm: () => {
                const select = document.getElementById('batchSelect');
                const quantity = parseInt(document.getElementById('batchQuantity').value);
                const option = select.options[select.selectedIndex];

                if (quantity < 1) {
                    Swal.showValidationMessage('Quantity must be greater than 0');
                    return false;
                }

                return {
                    batch_number: select.value,
                    price: parseFloat(option.dataset.price),
                    quantity: quantity
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                resolve(result.value);
            } else {
                resolve(null);
            }
        });
    });
}

function deleteBatchGroup() {
    const batchNumber = $(this).closest('.batch-group').data('batch');
    delete batchGroups[batchNumber];
    refreshBatchGroups();
}

function editBatchGroup() {
    const batchNumber = $(this).closest('.batch-group').data('batch');
    const group = batchGroups[batchNumber];

    Swal.fire({
                title: 'Edit Batch',
                html: `
            <div class="space-y-4">
                <div>
                    <label class="form-label">Batch Number</label>
                    <input type="text" id="editBatchNumber" class="form-input w-full" 
                           value="${batchNumber}">
                </div>
                ${group.items.map((item, index) => `
                    <div class="border dark:border-gray-700 rounded p-4">
                        <div class="font-semibold">${item.product_name}</div>
                        <div class="mt-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" 
                                   class="form-input w-full edit-item-qty" 
                                   data-index="${index}"
                                   value="${item.quantity}" 
                                   min="1">
                        </div>
                    </div>
                `).join('')}
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save',
        preConfirm: () => {
            const newBatchNumber = document.getElementById('editBatchNumber').value;
            const quantities = Array.from(document.getElementsByClassName('edit-item-qty'))
                .map(input => ({
                    index: parseInt(input.dataset.index),
                    quantity: parseInt(input.value)
                }));

            if (!newBatchNumber.trim()) {
                Swal.showValidationMessage('Batch number is required');
                return false;
            }

            if (newBatchNumber !== batchNumber && batchGroups[newBatchNumber]) {
                Swal.showValidationMessage('Batch number already exists');
                return false;
            }

            return {
                newBatchNumber,
                quantities
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const { newBatchNumber, quantities } = result.value;

            // Update quantities
            quantities.forEach(({ index, quantity }) => {
                group.items[index].quantity = quantity;
            });

            // Update batch number if changed
            if (newBatchNumber !== batchNumber) {
                batchGroups[newBatchNumber] = group;
                delete batchGroups[batchNumber];
            }

            refreshBatchGroups();
        }
    });
}

function updateBatchQuantity() {
    const row = $(this).closest('tr');
    const batchGroup = $(this).closest('.batch-group');
    const batchNumber = batchGroup.data('batch');
    const productId = $(this).data('product');
    const quantity = parseInt($(this).val());

    if (quantity < 1) {
        $(this).val(1);
        return;
    }

    const item = batchGroups[batchNumber].items.find(item => item.product_id === productId);
    if (item) {
        item.quantity = quantity;
    }
}

function toggleBatchCollapse() {
    const group = $(this).closest('.batch-group');
    const batchNumber = group.data('batch');
    $(this).toggleClass('rotate-180');
    group.find('.batch-items').toggleClass('hidden');
    batchGroups[batchNumber].collapsed = !batchGroups[batchNumber].collapsed;
}

async function showPrintStatistics() {
    if (Object.keys(batchGroups).length === 0) {
        Swal.fire({
            title: 'No Items',
            text: 'Add some items to print first',
            icon: 'warning'
        });
        return;
    }

    try {
        const settings = getPrintSettings();
        const response = await fetch(CONFIG.API_ENDPOINTS.BARCODE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'calculate_stats',
                settings: settings,
                batch_groups: batchGroups
            })
        });

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Failed to calculate statistics');
        }

        const stats = data.stats;
        $('#statsContent').html(`
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Labels</div>
                    <div class="text-2xl font-bold dark:text-white">${stats.total_labels}</div>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Sheets</div>
                    <div class="text-2xl font-bold dark:text-white">${stats.total_sheets}</div>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Batches</div>
                    <div class="text-2xl font-bold dark:text-white">${stats.total_batches}</div>
                </div>
                <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Estimated Time</div>
                    <div class="text-2xl font-bold dark:text-white">
                        ${formatDuration(stats.estimated_time)}
                    </div>
                </div>
            </div>
            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                <div class="font-semibold text-yellow-800 dark:text-yellow-200">Paper Usage</div>
                <div class="mt-2 text-yellow-600 dark:text-yellow-300">
                    ${stats.paper_usage}
                </div>
            </div>
            ${stats.warnings ? `
                <div class="mt-4 p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                    <div class="font-semibold text-red-800 dark:text-red-200">Warnings</div>
                    <ul class="mt-2 list-disc list-inside text-red-600 dark:text-red-300">
                        ${stats.warnings.map(warning => `<li>${warning}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        `);

        openModal('statsModal');

    } catch (error) {
        handleError(error);
    }
}

async function showPreview() {
    if (Object.keys(batchGroups).length === 0) {
        Swal.fire({
            title: 'No Items',
            text: 'Add some items to print first',
            icon: 'warning'
        });
        return;
    }

    try {
        const settings = getPrintSettings();
        const response = await fetch(CONFIG.API_ENDPOINTS.BARCODE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'preview',
                settings: settings,
                batch_groups: batchGroups
            })
        });

        if (!response.ok) {
            throw new Error('Failed to generate preview');
        }

        const blob = await response.blob();
        const url = URL.createObjectURL(blob);

        $('#previewContent').html(`
            <iframe src="${url}" width="100%" height="600px" frameborder="0"></iframe>
        `);

        openModal('previewModal');

    } catch (error) {
        handleError(error);
    }
}

async function printBarcodes() {
    if (Object.keys(batchGroups).length === 0) {
        Swal.fire({
            title: 'No Items',
            text: 'Add some items to print first',
            icon: 'warning'
        });
        return;
    }

    const confirmed = await confirmAction(
        'Are you sure you want to print these barcodes?',
        'This will send the job to the printer.'
    );

    if (!confirmed) return;

    try {
        const settings = getPrintSettings();
        const response = await fetch(CONFIG.API_ENDPOINTS.BARCODE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'print',
                settings: settings,
                batch_groups: batchGroups
            })
        });

        if (!response.ok) {
            throw new Error('Failed to send print job');
        }

        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        const printWindow = window.open(url);
        
        printWindow.onload = function() {
            printWindow.print();
            URL.revokeObjectURL(url);
        };

        showSuccess('Print job sent successfully');
        closeModal('previewModal');

    } catch (error) {
        handleError(error);
    }
}

function getPrintSettings() {
    return {
        paper_size: $('#paperSize').val(),
        orientation: $('#orientation').val(),
        start_position: parseInt($('#startPosition').val()),
        show_price: $('#showPrice').prop('checked'),
        show_unit: $('#showUnit').prop('checked'),
        show_category: $('#showCategory').prop('checked'),
        show_batch: $('#showBatch').prop('checked'),
        print_mode: $('#printMode').val(),
        sorting: $('#sorting').val(),
        skip_partial: $('#skipPartialSheet').prop('checked')
    };
}

function generateBatchNumber(productId) {
    const date = new Date();
    const year = date.getFullYear().toString().substr(-2);
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    
    return `B${year}${month}${day}-${productId}-${random}`;
}

function formatDuration(seconds) {
    if (seconds < 60) {
        return `${seconds} seconds`;
    }
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes} min ${remainingSeconds} sec`;
}

function formatCurrency(amount) {
    return `Rs. ${parseFloat(amount).toFixed(2)}`;
}

function openModal(modalId) {
    $(`#${modalId}`).removeClass('hidden');
}

function closeModal(modalId) {
    $(`#${modalId}`).addClass('hidden');
}