// Sales Return JavaScript
$(document).ready(function() {
    // Initialize DataTable for recent returns
    $('#recent-returns-table').DataTable({
        responsive: true,
        order: [
                [1, 'desc']
            ] // Sort by date descending
    });

    // Check if invoice number is provided in URL
    const urlParams = new URLSearchParams(window.location.search);
    const invoiceNumber = urlParams.get('invoice');
    if (invoiceNumber) {
        $('#invoice-search').val(invoiceNumber);
        searchInvoice();
    }

    // Search for invoice when button is clicked
    $('#search-btn').click(function() {
        searchInvoice();
    });

    // Also search when Enter key is pressed in the search input
    $('#invoice-search').keypress(function(e) {
        if (e.which === 13) {
            searchInvoice();
        }
    });

    // Handle return reason change
    $('#return-reason').change(function() {
        if ($(this).val() === 'Other') {
            $('#other-reason-group').show();
        } else {
            $('#other-reason-group').hide();
        }
    });

    // Cancel return button
    $('#cancel-return').click(function() {
        resetReturnForm();
    });

    // Process return button
    $('#process-return').click(function() {
        processReturn();
    });

    // View return details
    $(document).on('click', '.view-return', function() {
        const returnId = $(this).data('id');
        showReturnDetails(returnId);
    });

    // Print return receipt
    $(document).on('click', '.print-return', function() {
        const returnId = $(this).data('id');
        printReturnReceipt(returnId);
    });

    // Close modal when clicking on X
    $('.close').click(function() {
        $('#return-details-modal').hide();
    });

    // Close modal when clicking outside of it
    $(window).click(function(e) {
        if ($(e.target).is('#return-details-modal')) {
            $('#return-details-modal').hide();
        }
    });
});

// Functions

/**
 * Search for invoice by number
 */
function searchInvoice(bypassWindow = false) {
    const invoiceNumber = $('#invoice-search').val().trim();

    if (!invoiceNumber) {
        showAlert('Please enter an invoice number', 'error');
        return;
    }

    // Reset any previous search results
    resetReturnForm();

    // Show loading indicator
    $('#invoice-result').html('<p><i class="fas fa-spinner fa-spin"></i> Searching...</p>');

    // Make AJAX request to search for invoice
    $.ajax({
        url: 'search_invoice.php',
        type: 'POST',
        data: {
            invoice_number: invoiceNumber,
            bypass_return_window: bypassWindow
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                loadInvoiceDetails(response.invoice);
            } else {
                // Check if it's an expired invoice
                if (response.expired) {
                    // Show message with return anyway button
                    $('#invoice-result').html(`
                        <div class="expired-invoice-warning">
                            <p class="warning"><i class="fas fa-exclamation-triangle"></i> ${response.message}</p>
                            <button id="return-anyway-btn" class="btn secondary" data-invoice="${response.invoice_number}">
                                <i class="fas fa-undo-alt"></i> Return Anyway
                            </button>
                        </div>
                    `);

                    // Add click handler for the return anyway button
                    $('#return-anyway-btn').click(function() {
                        const invoiceNum = $(this).data('invoice');
                        $('#invoice-search').val(invoiceNum);
                        searchInvoice(true); // Call search again with bypass flag
                    });
                } else {
                    $('#invoice-result').html(`<p class="error"><i class="fas fa-exclamation-circle"></i> ${response.message}</p>`);
                }
            }
        },
        error: function() {
            $('#invoice-result').html('<p class="error"><i class="fas fa-exclamation-circle"></i> Error connecting to server. Please try again.</p>');
        }
    });
}

/**
 * Load invoice details into the return form
 */
function loadInvoiceDetails(invoice) {
    // Reset previous data and event handlers
    resetReturnForm();

    // First, unbind any existing event handlers to prevent duplicates
    $('#items-table').off('change', '.return-checkbox');

    // Set invoice details
    $('#invoice-number').text(invoice.invoice_number);
    $('#invoice-date').text(formatDate(invoice.invoice_date));
    $('#customer-name').text(invoice.customer_name);
    $('#invoice-total').text(formatCurrency(invoice.total_amount));
    $('#invoice-paid').text(formatCurrency(invoice.advance));
    $('#invoice-balance').text(formatCurrency(invoice.balance));

    // Clear the table body completely
    $('#items-body').empty();

    // Destroy existing DataTable if it exists to ensure complete reset
    if ($.fn.DataTable.isDataTable('#items-table')) {
        $('#items-table').DataTable().destroy();
    }

    // Check if this invoice is fully returned - show warning but continue
    if (invoice.is_fully_returned) {
        // Show warning at the top of the return section
        $('#invoice-result').html(`
            <div class="warning-box">
                <p class="warning"><i class="fas fa-exclamation-triangle"></i> This invoice has already been fully returned by total amount, but you can still return individual items if they have remaining quantities.</p>
            </div>
        `);
    } else {
        $('#invoice-result').html('');
    }

    // Populate items table with new data
    let itemsHtml = '';
    let allItemsFullyReturned = true; // Flag to check if all items are fully returned

    invoice.items.forEach(function(item) {
        // Check if item has already been fully returned
        const isFullyReturned = (item.returned_qty >= item.original_qty);

        // If at least one item is not fully returned, set flag to false
        if (!isFullyReturned) {
            allItemsFullyReturned = false;
        }

        // Create the row with disabled checkbox if fully returned
        itemsHtml += `
            <tr data-item-id="${item.id}" data-batch-id="${item.batch_id}" data-price="${item.sale_price}" class="${isFullyReturned ? 'fully-returned' : ''}">
                <td><input type="checkbox" class="return-checkbox" onchange="updateReturnTotals()" ${isFullyReturned ? 'disabled' : ''}></td>
                <td>${item.item_name}</td>
                <td>${item.batch_number || 'N/A'}</td>
                <td>${formatCurrency(item.sale_price)}</td>
                <td>${item.original_qty}</td>
                <td>${item.returned_qty}</td>
                <td>
                    <input type="number" class="qty-input" min="0.001" max="${item.qty}" value="1" step="0.001"
                        onchange="validateQty(this, ${item.qty})" oninput="updateRowTotal(this)" disabled>
                </td>
                <td class="row-total">0.00</td>
            </tr>
        `;
    });

    // Now set the HTML to the tbody
    $('#items-body').html(itemsHtml);

    // If all items are fully returned, show message
    if (allItemsFullyReturned && invoice.items.length > 0) {
        $('#invoice-result').html(`
            <div class="error-box">
                <p class="error"><i class="fas fa-exclamation-circle"></i> All items in this invoice have already been fully returned.</p>
            </div>
        `);
    }

    // Initialize DataTable for items with the fresh data
    $('#items-table').DataTable({
        paging: false,
        searching: false,
        info: false,
        destroy: true // Ensure any existing instance is fully destroyed
    });

    // Show return section
    $('.return-section').show();

    // Re-bind the event handlers for checkboxes
    $('#items-table').on('change', '.return-checkbox', function() {
        const row = $(this).closest('tr');
        const qtyInput = row.find('.qty-input');

        if ($(this).is(':checked')) {
            qtyInput.prop('disabled', false);
            updateRowTotal(qtyInput[0]);
        } else {
            qtyInput.prop('disabled', true);
            row.find('.row-total').text('0.00');
        }

        updateReturnTotals();
    });

    // Reset return totals
    updateReturnTotals();
}

/**
 * Validate quantity input
 */
function validateQty(input, max) {
    let value = parseFloat($(input).val());

    if (isNaN(value) || value <= 0) {
        $(input).val(0.001); // Minimum value for decimal quantities
        showAlert('Return quantity must be greater than zero', 'warning');
    } else if (value > max) {
        $(input).val(max);
        showAlert(`Maximum return quantity is ${max}`, 'warning');
    }

    updateRowTotal(input);
}

/**
 * Update row total amount
 */
function updateRowTotal(input) {
    const row = $(input).closest('tr');
    const price = parseFloat(row.data('price'));
    const qty = parseFloat($(input).val()) || 0;
    const total = price * qty;

    row.find('.row-total').text(formatCurrency(total));
    updateReturnTotals();
}

/**
 * Update return totals in summary
 */
function updateReturnTotals() {
    let totalItems = 0;
    let totalAmount = 0;

    $('#items-table tbody tr').each(function() {
        const checkbox = $(this).find('.return-checkbox');

        if (checkbox.is(':checked')) {
            totalItems++;
            const rowTotal = parseFloat($(this).find('.row-total').text().replace(/[^0-9.-]+/g, '')) || 0;
            totalAmount += rowTotal;
        }
    });

    $('#return-items-count').text(totalItems);
    $('#return-total-amount').text(formatCurrency(totalAmount));

    // Enable/disable process button based on selections
    if (totalItems > 0) {
        $('#process-return').prop('disabled', false);
    } else {
        $('#process-return').prop('disabled', true);
    }
}

/**
 * Process the return
 */
function processReturn() {
    // Validate form
    const returnReason = $('#return-reason').val();
    if (!returnReason) {
        showAlert('Please select a return reason', 'error');
        return;
    }

    if (returnReason === 'Other' && !$('#other-reason').val()) {
        showAlert('Please specify the return reason', 'error');
        return;
    }

    // Check if any items are selected
    let selectedItems = [];
    $('#items-table tbody tr').each(function() {
        const checkbox = $(this).find('.return-checkbox');

        if (checkbox.is(':checked')) {
            const row = $(this);
            selectedItems.push({
                item_id: row.data('item-id'),
                batch_id: row.data('batch-id'),
                price: parseFloat(row.data('price')),
                quantity: parseFloat(row.find('.qty-input').val()) // Changed to parseFloat for decimal support
            });
        }
    });

    if (selectedItems.length === 0) {
        showAlert('Please select at least one item to return', 'error');
        return;
    }

    // Confirm return
    Swal.fire({
        title: 'Confirm Return',
        text: `Are you sure you want to process this return for ${selectedItems.length} item(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, process return',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Gather form data
            const returnData = {
                invoice_number: $('#invoice-number').text(),
                reason: returnReason === 'Other' ? $('#other-reason').val() : returnReason,
                refund_method: $('#refund-method').val(),
                add_to_stock: $('#add-to-stock').val(),
                note: $('#return-note').val(),
                items: selectedItems
            };

            // Send AJAX request to process return
            $.ajax({
                url: 'process_return.php',
                type: 'POST',
                data: JSON.stringify(returnData),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Return Processed',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'Print Receipt',
                            showCancelButton: true,
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                printReturnReceipt(response.return_id);
                            }
                            resetReturnForm();
                            location.reload(); // Refresh page to show new return in recent returns
                        });
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function() {
                    showAlert('Error connecting to server. Please try again.', 'error');
                }
            });
        }
    });
}

/**
 * Show return details in modal
 */
function showReturnDetails(returnId) {
    // Show loading in modal
    $('#return-details-content').html('<p><i class="fas fa-spinner fa-spin"></i> Loading...</p>');
    $('#return-details-modal').show();

    // Get return details
    $.ajax({
        url: 'get_return_details.php',
        type: 'GET',
        data: { return_id: returnId },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const returnData = response.return;

                // Format return details
                let detailsHtml = `
                    <div class="return-detail-header">
                        <div>
                            <p><strong>Return ID:</strong> ${returnData.return_id}</p>
                            <p><strong>Date:</strong> ${formatDate(returnData.return_date)}</p>
                            <p><strong>Invoice:</strong> ${returnData.invoice_number}</p>
                            <p><strong>Customer:</strong> ${returnData.customer_name}</p>
                        </div>
                        <div>
                            <p><strong>Reason:</strong> ${returnData.return_reason}</p>
                            <p><strong>Refund Method:</strong> ${returnData.refund_method}</p>
                            <p><strong>Amount:</strong> ${formatCurrency(returnData.return_amount)}</p>
                            <p><strong>Processed By:</strong> ${returnData.employee_name}</p>
                        </div>
                    </div>
                    
                    <h3>Returned Items</h3>
                    <table class="details-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Batch</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Added to Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                returnData.items.forEach(function(item) {
                    detailsHtml += `
                        <tr>
                            <td>${item.item_name}</td>
                            <td>${item.batch_number || 'N/A'}</td>
                            <td>${formatCurrency(item.return_price)}</td>
                            <td>${item.quantity_returned}</td>
                            <td>${formatCurrency(item.return_price * item.quantity_returned)}</td>
                            <td>${item.add_to_stock == 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'}</td>
                        </tr>
                    `;
                });

                detailsHtml += `
                        </tbody>
                    </table>
                `;

                if (returnData.return_note) {
                    detailsHtml += `
                        <div class="return-note">
                            <h3>Notes</h3>
                            <p>${returnData.return_note}</p>
                        </div>
                    `;
                }

                // Add print button
                detailsHtml += `
                    <div class="modal-actions">
                        <button class="btn primary" onclick="printReturnReceipt(${returnData.return_id})">
                            <i class="fas fa-print"></i> Print Receipt
                        </button>
                    </div>
                `;

                $('#return-details-content').html(detailsHtml);
            } else {
                $('#return-details-content').html(`<p class="error">${response.message}</p>`);
            }
        },
        error: function() {
            $('#return-details-content').html('<p class="error">Error connecting to server. Please try again.</p>');
        }
    });
}

/**
 * Print return receipt
 */
function printReturnReceipt(returnId) {
    window.open(`print_return_receipt.php?return_id=${returnId}`, '_blank');
}

/**
 * Reset return form
 */
function resetReturnForm() {
    // Hide return section
    $('.return-section').hide();

    // Clear items table and unbind any event handlers to prevent duplicates
    $('#items-table').off('change', '.return-checkbox');

    // Properly destroy DataTable if it exists
    if ($.fn.DataTable.isDataTable('#items-table')) {
        $('#items-table').DataTable().destroy();
    }

    // Clear the table completely
    $('#items-body').empty();

    // Reset form fields
    $('#return-reason').val('');
    $('#other-reason').val('');
    $('#other-reason-group').hide();
    $('#refund-method').val('Store Credit');
    $('#add-to-stock').val('1');
    $('#return-note').val('');

    // Reset summary
    $('#return-items-count').text('0');
    $('#return-total-amount').text('0.00');

    // Disable process button
    $('#process-return').prop('disabled', true);
}

/**
 * Show alert using SweetAlert
 */
function showAlert(message, type) {
    Swal.fire({
        text: message,
        icon: type,
        confirmButtonText: 'OK'
    });
}

/**
 * Format date string
 */
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

/**
 * Format currency
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}