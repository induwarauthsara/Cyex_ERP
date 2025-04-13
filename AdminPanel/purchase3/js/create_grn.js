import {
    CONFIG,
    handleError,
    showSuccess,
    confirmAction
} from '../../js/config.js';

let items = [];
const grnId = $('#grnId').val();
const poId = $('#poId').val();

$(document).ready(async function() {
    // Load items if editing or creating from PO
    if (grnId) {
        await loadGRNItems();
    } else if (poId) {
        await loadPOItems();
    }

    // Form submission handlers
    $('#grnForm').on('submit', submitForm);
    $('#saveDraftBtn').on('click', () => submitForm(null, true));

    // Item table handlers
    $('#itemsTable').on('change', '.received-qty', validateQuantity);
    $('#itemsTable').on('change', '.cost', validateCost);
    $('#itemsTable').on('change', '.selling-price', validateSellingPrice);
    $('#itemsTable').on('change', '.batch-number', validateBatchNumber);
    $('#itemsTable').on('change', '.expiry-date', validateExpiryDate);
});

async function loadGRNItems() {
    try {
        const response = await fetch(`${CONFIG.API_ENDPOINTS.GRN}?action=get_items&id=${grnId}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load GRN items');
        }

        items = data.items;
        refreshItemsTable();

    } catch (error) {
        handleError(error);
    }
}

async function loadPOItems() {
    try {
        const response = await fetch(`${CONFIG.API_ENDPOINTS.PURCHASE_ORDERS}?action=get_items&id=${poId}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to load PO items');
        }

        items = data.items.map(item => ({
            ...item,
            received_qty: item.quantity - item.received_qty,
            batch_number: generateBatchNumber(item.product_id)
        }));
        refreshItemsTable();

    } catch (error) {
        handleError(error);
    }
}

function refreshItemsTable() {
    const tbody = $('#itemsTable tbody');
    tbody.empty();

    items.forEach((item, index) => {
        const row = `
            <tr data-index="${index}">
                <td class="px-4 py-2">
                    ${item.product_name}
                    <input type="hidden" class="product-id" value="${item.product_id}">
                </td>
                <td class="px-4 py-2 text-center">${item.quantity}</td>
                <td class="px-4 py-2">
                    <input type="number" class="form-input received-qty w-24" 
                           value="${item.received_qty}" min="0" max="${item.quantity}">
                </td>
                <td class="px-4 py-2">
                    <input type="number" class="form-input cost w-32" 
                           value="${item.unit_cost}" min="0" step="0.01">
                </td>
                <td class="px-4 py-2">
                    <input type="number" class="form-input selling-price w-32" 
                           value="${item.selling_price || item.unit_cost * 1.2}" min="0" step="0.01">
                </td>
                <td class="px-4 py-2">
                    <input type="text" class="form-input batch-number w-32" 
                           value="${item.batch_number || ''}" required>
                </td>
                <td class="px-4 py-2">
                    <input type="date" class="form-input expiry-date w-32" 
                           value="${item.expiry_date || ''}">
                </td>
                <td class="px-4 py-2">
                    <input type="text" class="form-input notes w-full" 
                           value="${item.notes || ''}">
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function generateBatchNumber(productId) {
    const date = new Date();
    const year = date.getFullYear().toString().substr(-2);
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    const random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

    return `B${year}${month}${day}-${productId}-${random}`;
}

function validateQuantity() {
    const row = $(this).closest('tr');
    const index = row.data('index');
    const qty = parseInt($(this).val());
    const maxQty = items[index].quantity;

    if (qty < 0 || qty > maxQty) {
        Swal.fire({
            title: 'Invalid Quantity',
            text: `Quantity must be between 0 and ${maxQty}`,
            icon: 'error'
        });
        $(this).val(Math.min(Math.max(0, qty), maxQty));
    }

    items[index].received_qty = qty;
}

function validateCost() {
    const row = $(this).closest('tr');
    const index = row.data('index');
    const cost = parseFloat($(this).val());

    if (cost < 0) {
        Swal.fire({
            title: 'Invalid Cost',
            text: 'Cost cannot be negative',
            icon: 'error'
        });
        $(this).val(0);
    }

    items[index].unit_cost = cost;
}

function validateSellingPrice() {
    const row = $(this).closest('tr');
    const index = row.data('index');
    const price = parseFloat($(this).val());
    const cost = items[index].unit_cost;

    if (price < 0) {
        Swal.fire({
            title: 'Invalid Price',
            text: 'Selling price cannot be negative',
            icon: 'error'
        });
        $(this).val(0);
        return;
    }

    if (price < cost) {
        Swal.fire({
            title: 'Warning',
            text: 'Selling price is less than cost price',
            icon: 'warning'
        });
    }

    items[index].selling_price = price;
}

function validateBatchNumber() {
    const row = $(this).closest('tr');
    const index = row.data('index');
    const batchNumber = $(this).val().trim();

    if (!batchNumber) {
        Swal.fire({
            title: 'Required Field',
            text: 'Batch number is required',
            icon: 'error'
        });
        return;
    }

    // Store batch number in items array
    items[index].batch_number = batchNumber;
}

function validateExpiryDate() {
    const row = $(this).closest('tr');
    const index = row.data('index');
    const expiryDate = $(this).val();

    if (expiryDate) {
        const today = new Date();
        const expiry = new Date(expiryDate);

        if (expiry <= today) {
            Swal.fire({
                title: 'Invalid Date',
                text: 'Expiry date must be in the future',
                icon: 'error'
            });
            $(this).val('');
            return;
        }
    }

    items[index].expiry_date = expiryDate;
}

async function validateBatchUniqueness() {
    const batches = items.map(item => ({
        product_id: item.product_id,
        batch_number: item.batch_number
    }));

    try {
        const response = await fetch(CONFIG.API_ENDPOINTS.GRN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'validate_batches',
                batches: batches
            })
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Batch validation failed');
        }

        if (data.duplicates.length > 0) {
            const duplicatesList = data.duplicates
                .map(d => `${d.product_name}: ${d.batch_number}`)
                .join('\n');

            Swal.fire({
                title: 'Duplicate Batch Numbers',
                text: `The following batch numbers already exist:\n${duplicatesList}`,
                icon: 'error'
            });
            return false;
        }

        return true;

    } catch (error) {
        handleError(error);
        return false;
    }
}

async function submitForm(event, isDraft = false) {
    if (event) event.preventDefault();

    // Validate required fields
    if (!$('#receiptDate').val()) {
        Swal.fire({
            title: 'Required Field',
            text: 'Receipt date is required',
            icon: 'error'
        });
        return;
    }

    // Validate at least one item is received
    const hasReceivedItems = items.some(item => item.received_qty > 0);
    if (!hasReceivedItems) {
        Swal.fire({
            title: 'No Items Received',
            text: 'Please enter received quantity for at least one item',
            icon: 'error'
        });
        return;
    }

    // Validate batch numbers if completing GRN
    if (!isDraft && !(await validateBatchUniqueness())) {
        return;
    }

    // Confirm completion if not draft
    if (!isDraft) {
        const confirmed = await confirmAction(
            'Are you sure you want to complete this GRN? This action cannot be undone.'
        );
        if (!confirmed) return;
    }

    try {
        const formData = {
            action: grnId ? 'update' : 'create',
            grn_id: grnId,
            po_id: poId,
            receipt_date: $('#receiptDate').val(),
            invoice_number: $('#invoiceNumber').val(),
            invoice_date: $('#invoiceDate').val(),
            notes: $('#notes').val(),
            status: isDraft ? 'draft' : 'completed',
            items: items.filter(item => item.received_qty > 0).map(item => ({
                product_id: item.product_id,
                received_qty: item.received_qty,
                cost: item.unit_cost,
                selling_price: item.selling_price,
                batch_number: item.batch_number,
                expiry_date: item.expiry_date,
                notes: item.notes
            }))
        };

        const response = await fetch(CONFIG.API_ENDPOINTS.GRN, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Failed to save GRN');
        }

        await Swal.fire({
            title: 'Success!',
            text: `GRN ${isDraft ? 'saved as draft' : 'completed'} successfully`,
            icon: 'success'
        });

        if (!isDraft) {
            // Ask if user wants to print barcodes
            const printBarcodes = await Swal.fire({
                title: 'Print Barcodes?',
                text: 'Would you like to print barcodes for the received items?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, print barcodes',
                cancelButtonText: 'No, skip printing'
            });

            if (printBarcodes.isConfirmed) {
                window.location.href = `barcode_print.php?grn_id=${data.grn_id}`;
                return;
            }
        }

        window.location.href = 'grn_list.php';

    } catch (error) {
        handleError(error);
    }
}