// Updated Quick Cash counts with shortcuts and CSS adjustments for uniform width and non-overlapping counts
let quickCashCounts = {
    10: 0,
    20: 0,
    50: 0,
    100: 0,
    500: 0,
    1000: 0,
    5000: 0
};

let modalOpen = false; // Track if the modal is currently open

// Helper function to format numbers with commas
function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Function to open Confirm Payment modal with selectable payment methods
function openConfirmPaymentModal(paymentMethod = 'Cash') {
    if (productList.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cart is Empty',
            text: 'Please add product before payment. Thank you!',
        });
        return;
    }

    const subtotal = parseFloat($('#total-without-discount').text().replace(/,/g, ''));
    const discount = discountType === 'percentage' ?
        subtotal * discountValue / 100 :
        discountValue;
    const totalPayable = parseFloat($('#total-payable').text().replace(/,/g, ''));
    const customerName = $('#name').val() || 'Walk-in Customer';
    const customerNumber = $('#tele').val() || '0';

    // Display discount with percentage if applicable
    const discountText = discountType === 'percentage' ?
        `Rs. ${formatCurrency(discount)} (${discountValue}%)` :
        `Rs. ${formatCurrency(discount)}`;

    // Modal content HTML with Quick Cash integration
    const modalContent = `
        <div style="display: flex; flex-direction: row; max-width: 800px;">
            <div style="flex: 2; text-align: left;">
                <p><strong>Customer:</strong> ${customerName}</p>
                <p><strong>Customer Number:</strong> ${customerNumber}</p>
                <hr style="border-top: 1px solid #ddd; margin: 15px 0;" />
                
                <p><strong>Subtotal:</strong> Rs. ${formatCurrency(subtotal)}</p>
                <p><strong>Discount:</strong> ${discountText}</p>
                <p><strong>Total Payable:</strong> 
                    <span id="total-payable" style="color: #d9534f; font-size: 1.5em; font-weight: bold;">
                        Rs. ${formatCurrency(totalPayable)}
                    </span>
                </p>
                <hr style="border-top: 1px solid #ddd; margin: 15px 0;" />
                
                <p><strong>Payment Method:</strong>
                    <select id="payment-method" style="font-weight: 600; color: #28a745;" onchange="toggleCashSections()">
                        <option value="Cash" ${paymentMethod === 'Cash' ? 'selected' : ''}>Cash</option>
                        <option value="Card" ${paymentMethod === 'Card' ? 'selected' : ''}>Card</option>
                        <option value="Credit" ${paymentMethod === 'Credit' ? 'selected' : ''}>Credit</option>
                    </select>
                </p>

                <div id="cash-section">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 15px;">
                        <label for="amount-received" style="font-weight: bold;">Amount Received:</label>
                        <input type="number" id="amount-received" class="swal2-input" style="width: 150px;" placeholder="Enter cash received" autofocus>
                    </div>
                    <p><strong>Change to Return:</strong> <span id="change-amount" style="color: #dc3545;">Rs. 0.00</span></p>
                </div>

                <div style="margin-top: 15px;">
                    <label>
                        <input type="checkbox" id="print-receipt" style="margin-right: 10px;" checked>
                        Print Bill
                    </label>
                </div>
            </div>

            <div id="quick-cash-container" style="flex: 1; padding-left: 20px;">
                <h3 style="text-align: center; margin-bottom: 10px;">Quick Cash</h3>
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    ${generateQuickCashButtons()}
                </div>
            </div>
        </div>
    `;

    Swal.fire({
        title: '<strong>Finalize Payment</strong>',
        html: modalContent,
        width: '620px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check-circle"></i> Confirm Payment',
        cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
        stopKeydownPropagation: false,
        customClass: {
            popup: 'swal2-popup-custom',
            confirmButton: 'swal2-confirm-button-custom',
            cancelButton: 'swal2-cancel-button-custom'
        },
        preConfirm: () => {
            const amountReceived = parseFloat((document.getElementById('amount-received') && document.getElementById('amount-received').value) || '0');
            const paymentMethod = document.getElementById('payment-method').value;
            const changeToReturn = (amountReceived - totalPayable).toFixed(2);
            const printReceipt = document.getElementById('print-receipt').checked;

            if (amountReceived < totalPayable && paymentMethod === 'Cash') {
                Swal.showValidationMessage('Received amount is less than total payable.');
                return false;
            }

            clearCart();
            clearQuickCash();

            return {
                paymentMethod,
                amountReceived,
                changeToReturn,
                printReceipt
            };
        },
        didOpen: () => {
            modalOpen = true; // Set modal as open
            toggleCashSections(); // Adjust visibility based on selected payment method

            const amountInput = document.getElementById('amount-received');
            const changeDisplay = document.getElementById('change-amount');

            if (amountInput) {
                amountInput.addEventListener('input', () => {
                    const amountReceived = parseFloat(amountInput.value || '0');
                    const change = amountReceived - totalPayable;
                    changeDisplay.innerText = `Rs. ${formatCurrency(change >= 0 ? change : 0)}`;
                });
            }

            // Attach keydown listener for shortcuts
            $(document).on('keydown.quickCashShortcuts', handleKeyShortcuts);
        },
        willClose: () => {
            modalOpen = false; // Set modal as closed
            $(document).off('keydown.quickCashShortcuts'); // Remove shortcut listener when modal closes
        }
    });
}

function handleKeyShortcuts(event) {
    if (event.key === 'F1') {
        document.getElementById('payment-method').value = 'Cash';
        toggleCashSections();
    }
    if (event.key === 'F2') {
        document.getElementById('payment-method').value = 'Card';
        toggleCashSections();
    }
    if (event.key === 'F3') incrementQuickCash(10);
    if (event.key === 'F4') incrementQuickCash(20);
    if (event.key === 'F5') incrementQuickCash(50);
    if (event.key === 'F6') incrementQuickCash(100);
    if (event.key === 'F7') incrementQuickCash(500);
    if (event.key === 'F8') incrementQuickCash(1000);
    if (event.key === 'F9') incrementQuickCash(5000);
    if (event.key === 'F10') clearQuickCash();
}

// Function to handle Quick Cash key shortcuts
function handleKeyShortcuts(event) {
    if (event.key === 'F1') {
        event.preventDefault();
        document.getElementById('payment-method').value = 'Cash';
        toggleCashSections();
    }
    if (event.key === 'F2') {
        event.preventDefault();
        document.getElementById('payment-method').value = 'Card';
        toggleCashSections();
    }
    if (event.key === 'F3') {
        event.preventDefault();
        incrementQuickCash(10);
    }
    if (event.key === 'F4') {
        event.preventDefault();
        incrementQuickCash(20);
    }
    if (event.key === 'F5') {
        event.preventDefault();
        incrementQuickCash(50);
    }
    if (event.key === 'F6') {
        event.preventDefault();
        incrementQuickCash(100);
    }
    if (event.key === 'F7') {
        event.preventDefault();
        incrementQuickCash(500);
    }
    if (event.key === 'F8') {
        event.preventDefault();
        incrementQuickCash(1000);
    }
    if (event.key === 'F9') {
        event.preventDefault();
        incrementQuickCash(5000);
    }
    if (event.key === 'F10') {
        event.preventDefault();
        clearQuickCash();
    }
}

// Function to show/hide Cash sections based on payment method
function toggleCashSections() {
    const paymentMethod = document.getElementById('payment-method').value;
    const cashSection = document.getElementById('cash-section');
    const quickCashContainer = document.getElementById('quick-cash-container');

    if (paymentMethod === 'Cash') {
        cashSection.style.display = 'block';
        quickCashContainer.style.display = 'block';
    } else {
        cashSection.style.display = 'none';
        quickCashContainer.style.display = 'none';
    }
}

// Generate Quick Cash buttons with denomination, count, and shortcut displays
function generateQuickCashButtons() {
    const denominations = [{
            value: 10,
            shortcut: 'F3'
        },
        {
            value: 20,
            shortcut: 'F4'
        },
        {
            value: 50,
            shortcut: 'F5'
        },
        {
            value: 100,
            shortcut: 'F6'
        },
        {
            value: 500,
            shortcut: 'F7'
        },
        {
            value: 1000,
            shortcut: 'F8'
        },
        {
            value: 5000,
            shortcut: 'F9'
        }
    ];

    return denominations.map(({
        value,
        shortcut
    }) => `
        <button onclick="incrementQuickCash(${value})" 
                style="position: relative; background-color: #ffa726; border: none; width: 100%; padding: 10px; color: white; font-weight: bold;">
            Rs. ${formatCurrency(value)}
            <span id="count-${value}" style="position: absolute; top: 5px; right: 36px; font-size: 0.8em; background: #333; color: #fff; border-radius: 50%; padding: 2px 6px;">
                ${quickCashCounts[value] || ''}
            </span>
            <span style="position: absolute; top: 5px; right: 10px; font-size: 0.8em; color: #555;">
                ${shortcut}
            </span>
        </button>
    `).join('') + `<button onclick="clearQuickCash()" style="position: relative; background-color: #e57373; border: none; width: 100%; padding: 10px; color: white; font-weight: bold;">
                    Clear <span style="position: absolute; top: 5px; right: 10px; font-size: 0.8em; color: #555;">F10</span>
                   </button>`;
}


// Function to increment Quick Cash by denomination
function incrementQuickCash(denomination) {
    quickCashCounts[denomination] = (quickCashCounts[denomination] || 0) + 1;
    updateQuickCashDisplay();
    calculateQuickCashTotal();
}

// Function to calculate total from Quick Cash and update Amount Received
function calculateQuickCashTotal() {
    const totalQuickCash = Object.entries(quickCashCounts)
        .reduce((total, [denomination, count]) => total + (parseInt(denomination) * count), 0);

    // Check if the modal is open and update the Amount Received field if it is
    if (modalOpen) {
        const amountReceivedInput = document.getElementById('amount-received');
        if (amountReceivedInput) {
            amountReceivedInput.value = totalQuickCash.toFixed(2);
        }
    }

    // Update the displayed change amount only if the element exists
    const changeDisplay = document.getElementById('change-amount');
    if (changeDisplay) {
        const totalPayable = parseFloat($('#total-payable').text().replace(/,/g, ''));
        const change = totalQuickCash - totalPayable;
        changeDisplay.innerText = change >= 0 ? formatCurrency(change.toFixed(2)) : '0.00';
    }
}

// Function to update Quick Cash button counters
function updateQuickCashDisplay() {
    for (let denomination in quickCashCounts) {
        const count = quickCashCounts[denomination];
        const countElement = document.getElementById(`count-${denomination}`);
        if (countElement) {
            countElement.innerText = count > 0 ? count : '';
        }
    }
}

// Function to clear Quick Cash counts and reset Amount Received
function clearQuickCash() {
    quickCashCounts = { 10: 0, 20: 0, 50: 0, 100: 0, 500: 0, 1000: 0, 5000: 0 };
    updateQuickCashDisplay();
    document.getElementById('amount-received').value = '';
    document.getElementById('change-amount').innerText = '0.00';
}

// Function to handle Quick Cash key shortcuts
let enterPressCount = 0; // Counter for tracking consecutive Enter presses

$(document).on('keydown', function(event) {
    if (modalOpen) {
        // If modal is open, change payment method with F1 or F2
        if (event.key === 'F1') {
            event.preventDefault();
            document.getElementById('payment-method').value = 'Cash';
        }
        if (event.key === 'F2') {
            event.preventDefault();
            document.getElementById('payment-method').value = 'Card';
        }

        // Check for double Enter press to confirm payment
        if (event.key === 'Enter') {
            event.preventDefault();
            enterPressCount++;
            if (enterPressCount === 2) {
                // Trigger the Confirm Payment button click
                $('.swal2-confirm').click(); // Simulate clicking the Confirm Payment button
                enterPressCount = 0; // Reset counter after confirmation
            }
            // Reset the counter if the second Enter is not pressed within 500ms
            setTimeout(() => {
                enterPressCount = 0;
            }, 500);
        }
    } else {
        // If modal is not open, open with selected payment method
        if (event.key === 'F1') {
            event.preventDefault();
            openConfirmPaymentModal('Cash');
        }
        if (event.key === 'F2') {
            event.preventDefault();
            openConfirmPaymentModal('Card');
        }
    }
});