// File: /AdminPanel/barcode-print/js/barcode_print.js

import {
    CONFIG,
    handleError,
    showSuccess,
    confirmAction
} from '../../js/config.js';

let items = [];
const grnId = $('#grnId').val();

$(document).ready(async function() {
    // Initialize Select2 for product search
    $('#productSearch').select2({
        placeholder: 'Search products...',
        allowClear: true,
        ajax: {
            url: '../../../inc/fetch_product.php',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    search: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(product => ({
                        id: product.product_id,
                        text: product.product_name,
                        price: product.selling_price,
                        barcode: product.barcode,
                        batches: product.batches
                    }))
                };
            },
            cache: true
        }
    });

    // Load items if coming from GRN
    if (grnId) {
        await loadGRNItems();
    }

    // Event handlers
    $('#addProductBtn').on('click', addProduct);
    $('#previewBtn').on('click', showPreview);
    $('#printBtn').on('click', printBarcodes);
    $('#saveTemplateBtn').on('click', saveTemplate);
    $('#loadTemplate').on('change', loadTemplate);

    // Fix: Use event delegation for dynamic elements
    $(document).on('click', '.deleteItem', function() {
        const index = $(this).closest('tr').data('index');
        items.splice(index, 1);
        refreshItemsTable();
    });

    $(document).on('change', '.item-qty', updateQuantity);

    // Define updateBatch function
    window.updateBatch = function(event) {
        const index = $(event.target).closest('tr').data('index');
        const batchId = $(event.target).val();

        if (index >= 0 && index < items.length) {
            const product = items[index];
            const batch = product.batches.find(b => b.batch_id === batchId);

            if (batch) {
                items[index] = {
                    ...items[index],
                    batch_id: batch.batch_id,
                    batch_number: batch.batch_number,
                    price: batch.selling_price
                };
                refreshItemsTable();
            }
        }
    };
});

// Function to update quantity when changed
function updateQuantity() {
    const index = $(this).closest('tr').data('index');
    const quantity = parseInt($(this).val()) || 1;

    if (quantity < 1) {
        $(this).val(1);
        return;
    }

    if (index >= 0 && index < items.length) {
        items[index].quantity = quantity;
    }
}

async function loadGRNItems() {
    try {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');

        const response = await fetch(`../../api/grn.php?action=get_items&id=${grnId}`);
        const data = await response.json();

        $('#loadingOverlay').removeClass('flex').addClass('hidden');

        if (!data.success) {
            throw new Error(data.message || 'Failed to load GRN items');
        }

        items = data.items.map(item => ({
            product_id: item.product_id,
            product_name: item.product_name,
            batch_id: item.batch_id,
            batch_number: item.batch_number,
            price: item.selling_price,
            barcode: item.barcode || ('PROD' + item.product_id),
            quantity: item.received_qty
        }));

        refreshItemsTable();

    } catch (error) {
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        showError(error.message || 'An error occurred while loading GRN items');
    }
}

function addProduct() {
    const selected = $('#productSearch').select2('data')[0];
    if (!selected) return;

    // If product has batches, show batch selection modal
    if (selected.batches && selected.batches.length > 0) {
        showBatchModal(selected);
    } else {
        // Add product without batch
        items.push({
            product_id: selected.id,
            product_name: selected.text,
            price: selected.price,
            barcode: selected.barcode || ('PROD' + selected.id),
            quantity: 1
        });
        refreshItemsTable();
    }

    $('#productSearch').val(null).trigger('change');
}

function showBatchModal(product) {
    Swal.fire({
                title: 'Select Batch',
                html: `
            <select id="batchSelect" class="form-input w-full">
                ${product.batches.map(batch => `
                    <option value="${batch.batch_id}" 
                            data-number="${batch.batch_number}"
                            data-price="${batch.selling_price}">
                        ${batch.batch_number} (${batch.quantity} in stock)
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
            const option = select.options[select.selectedIndex];
            const quantity = parseInt(document.getElementById('batchQuantity').value);

            if (quantity < 1) {
                Swal.showValidationMessage('Quantity must be greater than 0');
                return false;
            }

            return {
                batch_id: select.value,
                batch_number: option.dataset.number,
                price: parseFloat(option.dataset.price),
                quantity: quantity
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            items.push({
                product_id: product.id,
                product_name: product.text,
                batch_id: result.value.batch_id,
                batch_number: result.value.batch_number,
                price: result.value.price,
                barcode: product.barcode || (product.id),
                quantity: result.value.quantity
            });
            refreshItemsTable();
        }
    });
}

function refreshItemsTable() {
    const tbody = $('#itemsTable tbody');
    tbody.empty();

    items.forEach((item, index) => {
        const row = `
            <tr data-index="${index}">
                <td class="px-4 py-2">${item.product_name}</td>
                <td class="px-4 py-2">
                    ${item.batch_number || 'N/A'}
                </td>
                <td class="px-4 py-2">
                    ${formatCurrency(item.price)}
                </td>
                <td class="px-4 py-2">
                    <input type="number" class="form-input w-24 item-qty" 
                           value="${item.quantity}" min="1">
                </td>
                <td class="px-4 py-2">
                    <button class="deleteItem text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    // Log items count for debugging
    console.log('Items in table:', items.length);
}

async function showPreview() {
    if (items.length === 0) {
                        console.log('Items to print3:', items.length);

        Swal.fire({
            title: 'No Items',
            text: 'Please add items to print',
            icon: 'warning'
        });
        return;
    }

    try {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
        
        const settings = getSettings();
        const response = await fetch('../api/barcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'preview',
                settings: settings,
                items: items
            })
        });

        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        
        if (!response.ok) {
            throw new Error('Failed to generate preview');
        }

        const blob = await response.blob();
        const url = URL.createObjectURL(blob);

        $('#previewContent').html(`
            <iframe src="${url}" width="100%" height="600px" frameborder="0"></iframe>
        `);
        $('#previewModal').removeClass('hidden');

    } catch (error) {
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        showError(error.message || 'An error occurred while generating preview');
    }
}

function closePreviewModal() {
    $('#previewModal').addClass('hidden');
}

async function printBarcodes() {
    if (items.length === 0) {
        console.log('Items to print4:', items.length);

        Swal.fire({
            title: 'No Items',
            text: 'Please add items to print',
            icon: 'warning'
        });
        return;
    }

    try {
        // Show loading overlay
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
        
        // Get settings
        const settings = getSettings();
        
        const response = await fetch('../api/barcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'print',
                settings: settings,
                items: items
            })
        });

        $('#loadingOverlay').removeClass('flex').addClass('hidden');

        if (!response.ok) {
            throw new Error('Failed to generate barcodes');
        }

        const blob = await response.blob();
        const url = URL.createObjectURL(blob);
        
        // Open in new window for printing
        const printWindow = window.open(url);

        printWindow.onload = function () {
            printWindow.print();
            // Revoke URL after a delay to ensure print dialog is shown
            setTimeout(() => {
                URL.revokeObjectURL(url);
            }, 2000);
        };

    } catch (error) {
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        showError(error.message || 'An error occurred while generating barcodes');
    }
}

async function saveTemplate() {
    const templateName = $('#templateName').val().trim();
    if (!templateName) {
        Swal.fire({
            title: 'Template Name Required',
            text: 'Please enter a name for the template',
            icon: 'warning'
        });
        return;
    }

    try {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
        
        const settings = getSettings();
        const response = await fetch('../api/barcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'save_template',
                template_name: templateName,
                settings: settings
            })
        });

        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to save template');
        }

        showSuccess('Template saved successfully');
        $('#templateName').val('');

        // Reload template list
        const templateSelect = $('#loadTemplate');
        templateSelect.append(new Option(templateName, data.template_id));

    } catch (error) {
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        showError(error.message || 'An error occurred while saving template');
    }
}

async function loadTemplate() {
    const templateId = $(this).val();
    if (!templateId) return;

    try {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
        
        const response = await fetch(`../api/barcode.php?action=get_template&id=${templateId}`);
        
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load template');
        }

        // Apply template settings
        const settings = data.settings;
        $('#paperSize').val(settings.paper_size);
        $('#margin').val(settings.margin);
        $('#gapBetween').val(settings.gap_between);
        $('#fontSize').val(settings.font_size);
        $('#barcodeHeight').val(settings.barcode_height);
        $('#showPrice').prop('checked', settings.show_price);
        $('#showUnit').prop('checked', settings.show_unit);
        $('#showCategory').prop('checked', settings.show_category);
        $('#showPromoPrice').prop('checked', settings.show_promo_price);
        $('#showShopName').prop('checked', settings.show_shop_name);
        $('#shopName').val(settings.shop_name);
        $('#showProductName').prop('checked', settings.show_product_name);

    } catch (error) {
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        showError(error.message || 'An error occurred while loading template');
    }
}

function getSettings() {
    return {
        paper_size: parseFloat($('#paperSize').val()),
        margin: parseFloat($('#margin').val()) || 1,
        gap_between: parseFloat($('#gapBetween').val()) || 3,
        font_size: parseFloat($('#fontSize').val()) || 8,
        barcode_height: parseFloat($('#barcodeHeight').val()) || 10,
        show_price: $('#showPrice').prop('checked'),
        show_unit: $('#showUnit').prop('checked'),
        show_category: $('#showCategory').prop('checked'),
        show_promo_price: $('#showPromoPrice').prop('checked'),
        show_shop_name: $('#showShopName').prop('checked'),
        shop_name: $('#shopName').val(),
        show_product_name: $('#showProductName').prop('checked')
    };
}

function formatCurrency(amount) {
    return 'Rs. ' + parseFloat(amount).toFixed(2);
}

// Error handling functions
function showError(message) {
    Swal.fire({
        title: 'Error',
        text: message,
        icon: 'error'
    });
}

function showSuccess(message) {
    Swal.fire({
        title: 'Success',
        text: message,
        icon: 'success'
    });
}