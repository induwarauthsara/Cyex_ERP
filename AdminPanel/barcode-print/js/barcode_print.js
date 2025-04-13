// File: /AdminPanel/barcode-print/js/barcode_print.js

import {
    CONFIG,
    handleError,
    showSuccess as configShowSuccess,
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
                        barcode_symbology: product.barcode_symbology || 'CODE128',
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

    // Handle print format change to show/hide thermal printer settings
    $('#printFormat').on('change', function() {
        const printFormat = $(this).val();
        if (printFormat === 'thermal') {
            $('#thermalPrinterSettings').removeClass('hidden');
        } else {
            $('#thermalPrinterSettings').addClass('hidden');
        }
    });

    // Trigger change on page load to set initial state
    $('#printFormat').trigger('change');

    // When a product is selected
    $('#productSearch').on('select2:select', function(e) {
        let data = e.params.data;

        // Make sure barcode is valid
        if (!data.barcode || data.barcode.trim() === '') {
            // If barcode is empty, use product ID as fallback
            data.barcode = 'PROD' + data.id;
        }

        $("#barcode").val(data.barcode);
        $("#price").val(data.price);

        // Set the barcode symbology if available
        if (data.barcode_symbology) {
            $("#barcode_symbology").val(data.barcode_symbology);
        }

        if (data.batches && data.batches.length > 0) {
            $("#batchSelectContainer").show();
            let batchSelect = $("#batchSelect");
            batchSelect.empty();
            batchSelect.append('<option value="">Select Batch</option>');

            data.batches.forEach(batch => {
                // Ensure we have valid values for all fields
                const discountPrice = batch.discount_price || '';
                const promotionalPrice = batch.promotional_price || '';
                const cost = batch.cost || 0;
                const expiry = batch.expiry_date || '';

                batchSelect.append(`<option value="${batch.id}" 
                    data-discount_price="${discountPrice}" 
                    data-promotional_price="${promotionalPrice}"
                    data-cost="${cost}" 
                    data-expire="${expiry}">
                    ${batch.batch_code} (${batch.stock} units)
                </option>`);
            });
        } else {
            $("#batchSelectContainer").hide();
        }
    });

    // When a batch is selected
    $('#batchSelect').change(function() {
        let selectedOption = $(this).find('option:selected');

        // Check if the option is not the default "Select Batch" option
        if (selectedOption.val()) {
            // Check for discount price first, then promotional price
            let discountPrice = selectedOption.data('discount_price');
            let promotionalPrice = selectedOption.data('promotional_price');

            if (discountPrice) {
                $("#promotional_price").val(discountPrice);
            } else if (promotionalPrice) {
                $("#promotional_price").val(promotionalPrice);
            } else {
                $("#promotional_price").val('');
            }

            // Set cost price if available
            if (selectedOption.data('cost')) {
                $("#cost").val(selectedOption.data('cost'));
            }

            // Set expiry date if available
            if (selectedOption.data('expire')) {
                $("#expiry").val(selectedOption.data('expire'));
            }
        } else {
            // Clear fields if no batch is selected
            $("#promotional_price").val('');
            $("#cost").val('');
            $("#expiry").val('');
        }
    });
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
            barcode: item.barcode || (item.product_id),
            quantity: item.received_qty,
            barcode_symbology: item.barcode_symbology || 'CODE128'
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

    // Validate barcode
    let barcode = selected.barcode || '';
    if (!barcode.trim()) {
        barcode = 'PROD' + selected.id;
    }

    // If product has batches, show batch selection modal
    if (selected.batches && selected.batches.length > 0) {
        showBatchModal(selected);
    } else {
        // Add product without batch
        items.push({
            product_id: selected.id,
            product_name: selected.text,
            price: selected.price,
            barcode: barcode,
            quantity: 1,
            barcode_symbology: selected.barcode_symbology || 'CODE128'
        });
        refreshItemsTable();
    }

    $('#productSearch').val(null).trigger('change');
}

function showBatchModal(product) {
    // Validate barcode
    let barcode = product.barcode || '';
    if (!barcode.trim()) {
        barcode = 'PROD' + product.id;
    }

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
                quantity: quantity,
                barcode_symbology: product.barcode_symbology || 'CODE128'
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
                barcode: barcode,
                quantity: result.value.quantity,
                barcode_symbology: result.value.barcode_symbology
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
        Swal.fire({
            title: 'No Items',
            text: 'Please add items to print',
            icon: 'warning'
        });
        return;
    }

    try {
        $('#loadingOverlay').removeClass('hidden').addClass('flex');
        
        // Get settings and ensure paper_size is numeric
        const settings = getSettings();
        settings.paper_size = parseFloat(settings.paper_size);
        
        // Create items array with expanded quantities
        const expandedItems = [];
        items.forEach(item => {
            const qty = parseInt(item.quantity) || 1;
            for (let i = 0; i < qty; i++) {
                expandedItems.push({
                    ...item,
                    quantity: 1  // Set quantity to 1 for each expanded item
                });
            }
        });
        
        // Send request to generate PDF preview
        const response = await fetch('./api/barcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'preview',
                settings: settings,
                items: expandedItems
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
        
        // Get settings and ensure paper_size is numeric
        const settings = getSettings();
        settings.paper_size = parseFloat(settings.paper_size);
        
        // Create items array with expanded quantities
        const expandedItems = [];
        items.forEach(item => {
            const qty = parseInt(item.quantity) || 1;
            for (let i = 0; i < qty; i++) {
                expandedItems.push({
                    ...item,
                    quantity: 1  // Set quantity to 1 for each expanded item
                });
            }
        });
        
        // For PDF printing
        const response = await fetch('./api/barcode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'print',
                settings: settings,
                items: expandedItems
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

        if (printWindow) {
            printWindow.onload = function () {
                printWindow.print();
                // Revoke URL after a delay to ensure print dialog is shown
                setTimeout(() => {
                    URL.revokeObjectURL(url);
                }, 2000);
            };
        } else {
            // If popup blocked or window didn't open
            Swal.fire({
                title: 'Popup Blocked',
                text: 'Please allow popups to print barcodes',
                icon: 'warning',
                confirmButtonText: 'Open PDF',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, '_blank');
                }
            });
        }

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
        const response = await fetch('./api/save_template.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
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
        
        const response = await fetch(`./api/get_template.php?id=${templateId}`);
        
        $('#loadingOverlay').removeClass('flex').addClass('hidden');
        
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load template');
        }

        // Apply template settings
        const settings = data.settings;
        $('#paperSize').val(settings.paper_size);
        $('#margin').val(settings.margin || 1);
        $('#gapBetween').val(settings.gap_between || 2.5);
        $('#fontSize').val(settings.font_size || 9);
        $('#barcodeHeight').val(settings.barcode_height || 15);
        $('#showPrice').prop('checked', settings.show_price);
        $('#showUnit').prop('checked', settings.show_unit);
        $('#showCategory').prop('checked', settings.show_category);
        $('#showPromoPrice').prop('checked', settings.show_promo_price);
        $('#showShopName').prop('checked', settings.show_shop_name);
        $('#shopName').val(settings.shop_name);
        $('#showProductName').prop('checked', settings.show_product_name);

        // Don't clear or modify the existing products
        
        // Update the preview with new settings but keep existing products
        if (typeof updatePreview === 'function') {
            updatePreview();
        }

        showSuccess(`Template "${data.template_name}" loaded successfully`);

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

// Function to generate preview HTML
function generatePreviewHTML(items, settings) {
    let html = '<h3 class="text-xl font-semibold mb-4">Live Preview</h3>';
    html += `<div class="flex flex-wrap gap-2 justify-center">`;

    // Determine dimensions based on paper size
    let labelWidth, labelHeight;
    switch (settings.paper_size) {
        case 36:
            labelWidth = 36;
            labelHeight = 13;
            break;
        case 24:
            labelWidth = 24;
            labelHeight = 12;
            break;
        case 18:
            labelWidth = 18;
            labelHeight = 10;
            break;
        case 30:
        default:
            labelWidth = 30;
            labelHeight = 15;
    }

    // Calculate display size (scaled for preview)
    const scale = 3; // Scale for display purposes
    const displayWidth = labelWidth * scale;
    const displayHeight = labelHeight * scale;
    const margin = settings.margin || 0.1;

    // Generate a unique timestamp to use for all barcode IDs
    const timestamp = Date.now();

    items.forEach((item, index) => {
        // Create a unique ID for each barcode
        const barcodeId = `barcode-${index}-${timestamp}`;
        const barcode = item.barcode || `PROD${item.product_id}`;

        // Create a barcode preview card for each item
        html += `
            <div class="barcode-preview border p-1 relative bg-white" 
                 style="width: ${displayWidth}px; height: ${displayHeight}px; margin: ${settings.gap_between || 0}px;">
                <div class="absolute inset-0 p-${margin}" style="overflow: hidden;">
                    ${settings.show_shop_name ? 
                        `<div class="text-center font-bold" style="font-size: ${Math.min(settings.font_size * 0.9, 7)}pt; line-height: 1;">
                            ${settings.shop_name || '<?php echo $ERP_COMPANY_NAME; ?>'}
                        </div>` : ''}
                    
                    ${settings.show_product_name ? 
                        `<div class="text-center overflow-hidden" style="font-size: ${settings.font_size * 0.9}pt; line-height: 1.1; 
                        white-space: nowrap; text-overflow: ellipsis;">
                            ${item.product_name}
                        </div>` : ''}
                    
                    <div class="flex justify-center" style="height: ${settings.barcode_height}px;">
                        <svg id="${barcodeId}" class="w-full"></svg>
                    </div>
                    
                    <div class="flex justify-between items-center" style="font-size: ${settings.font_size * 0.7}pt; transform: translateY(10px);">
                        ${settings.show_category && item.category ? 
                            `<div class="text-left">${item.category}</div>` : 
                            '<div></div>'
                        }
                        
                        <div class="text-center flex-grow">
                            ${settings.show_price ? 
                                `<div class="font-bold ${settings.show_promo_price && item.discount_price ? 'line-through' : ''}">
                                    Rs. ${parseFloat(item.price).toFixed(2)}
                                </div>` : ''
                            }
                            ${settings.show_promo_price && item.discount_price ? 
                                `<div class="font-bold text-green-600">
                                    Rs. ${parseFloat(item.discount_price).toFixed(2)}
                                </div>` : ''
                            }
                        </div>
                        
                        ${settings.show_unit ? 
                            `<div class="text-right">${item.unit || 'pcs'}</div>` : 
                            '<div></div>'
                        }
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';

    // Add script to render barcodes after HTML is added to DOM
    html += `
    <script>
        function initializeBarcodes() {
            const timestamp = ${timestamp};
            ${items.map((item, index) => {
                const barcode = item.barcode || `PROD${item.product_id}`;
                
                return `
                    try {
                        const barcodeElement = document.getElementById("barcode-${index}-${timestamp}");
                        if (barcodeElement) {
                            JsBarcode(barcodeElement, "${barcode}", {
                                format: "CODE128",
                                width: 1,
                                height: ${settings.barcode_height},
                                displayValue: true,
                                fontSize: ${settings.font_size * 1.5},
                                margin: 0,
                                background: "#ffffff"
                            });
                        } else {
                            console.error("Barcode element not found for index ${index}");
                        }
                    } catch(e) {
                        console.error("Error rendering barcode for ${item.product_name}:", e);
                    }
                `;
            }).join('')}
        }

        // Initialize barcodes after a short delay to ensure DOM is ready
        setTimeout(initializeBarcodes, 100);
    <\/script>`;

    return html;
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

// Use imported showSuccess function
function showSuccess(message) {
    configShowSuccess(message);
}