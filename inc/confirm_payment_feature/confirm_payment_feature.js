// onload, fetch customer customer_extra_fund.php it's number. (ex: 520.00) if it's not '0.00', get it to the 'extraPaidAmount' variable.
let useCustomerExtraFundAmount = 0;

function customerSavedExtraFund() {
    // window.addEventListener('load', () => {
    // Check if "customerPhone" exists in localStorage
    const customerPhone = localStorage.getItem('customerPhone');

    if (customerPhone) {
        // Send request with customerPhone value
        fetch('/inc/confirm_payment_feature/customer_extra_fund.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ customerPhone }),
            })
            .then(response => response.text())
            .then(data => {
                const parsedAmount = parseFloat(data);
                const useCustomerExtraFundElement = document.getElementById('useCustomerExtraFundElement');
                if (!isNaN(parsedAmount) && parsedAmount > 0) {
                    useCustomerExtraFundAmount = parsedAmount;
                    useCustomerExtraFundElement.innerHTML = `<label for="useCustomerExtraFund"><strong>Customer Extra Fund:</strong> Rs. ${formatCurrency(useCustomerExtraFundAmount)}</label> <input type="checkbox" id="useCustomerExtraFund" name="useCustomerExtraFund" onchange="handleUseCustomerExtraFund()">`;
                } else {
                    useCustomerExtraFundAmount = 0.00;
                    console.log('No extra funds or invalid data received.');
                    useCustomerExtraFundElement.innerHTML = ''
                }
            })
            .catch(error => {
                console.error('Error fetching customer extra fund:', error);
            });
    } else {
        useCustomerExtraFundAmount = 0.00;
        console.log('No customerPhone found in localStorage.');
    }
    // });
}

$(document).ready(function() {
    customerSavedExtraFund();
});

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
    customerSavedExtraFund();
    if (productList.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Cart is Empty',
            text: 'Please add product before payment. Thank you!',
        });
        return;
    }

    const subtotal = parseFloat($('#total-without-discount').text().replace(/,/g, ''));
    const discount = discountType === 'percentage' ? subtotal * discountValue / 100 : discountValue;
    var totalPayable = parseFloat($('#total-payable').text().replace(/,/g, ''));
    const customerName = $('#name').val() || 'Walk-in Customer';
    const customerNumber = $('#tele').val() || '0';

    const discountText = discountType === 'percentage' ?
        `Rs. ${formatCurrency(discount)} (${discountValue}%)` :
        `Rs. ${formatCurrency(discount)}`;

    let modalContent = `
        <div style="flex-direction: column; max-width: 800px;">
            <p><strong>Customer:</strong> ${customerName}</p>
            <p><strong>Customer Number:</strong> ${customerNumber}</p>
            <p id="useCustomerExtraFundElement">
            ${useCustomerExtraFundAmount > 0 ? `<label for="useCustomerExtraFund"><strong>Customer Extra Fund:</strong> Rs. ${formatCurrency(useCustomerExtraFundAmount)}</label> <input type="checkbox" id="useCustomerExtraFund" name="useCustomerExtraFund" onchange="handleUseCustomerExtraFund()">` : ''}
            </p>

            
            <hr style="border-top: 1px solid #ddd; margin: 15px 0;" />

            <p><strong>Subtotal:</strong> Rs. ${formatCurrency(subtotal)}</p>
            <p><strong>Discount:</strong> ${discountText}</p>
            <p><strong>Total Payable:</strong> 
                <span id="modal-total-payable" style="color: #d9534f; font-size: 1.5em; font-weight: bold;">
                     ${formatCurrency(totalPayable)}
                </span>
            </p>
            <hr style="border-top: 1px solid #ddd; margin: 15px 0;" />

            <!-- Payment Tabs -->
            <div class="payment-tabs" style="display: flex; gap: 10px; margin-bottom: 10px;">
                <button onclick="showPaymentTab('cash-tab')" class="tab-button active" id="tab-cash">Cash</button>
                <button onclick="showPaymentTab('card-tab')" class="tab-button" id="tab-card">Card</button>
                <button onclick="showPaymentTab('bank-tab')" class="tab-button" id="tab-bank">Online Trans.</button>
            </div>

            <!-- Credit Payment Method -->
            <p> <input type="checkbox" id="creditPayment" style="margin-right: 10px;"> <label for="creditPayment"> Credit Payment </label> <br> </p>
            
            <!-- Tab Contents -->
            <div id="cash-tab" class="tab-content active">
                <div id="quick-cash-container">
                    <h3>Quick Cash</h3>
                    <div style="display: flex; flex-direction: column; gap: 5px;">
                        ${generateQuickCashButtons()}
                    </div>
                </div>
                <label><strong>Amount Received:</strong></label>
                <input type="number" id="cash-amount-received" oninput="calculateBalance()" class="swal2-input" placeholder="Enter cash received" autofocus>
                <p><strong>Change to Return:</strong> <span id="change-amount" style="color: #dc3545;">Rs. 0.00</span></p>
            </div>

            <div id="card-tab" class="tab-content">
                <label><strong>Card Payment Amount:</strong></label>
                <input type="number" id="card-amount" oninput="calculateBalance()" class="swal2-input" placeholder="Enter card amount">
            </div>

            <div id="bank-tab" class="tab-content">
                <label><strong>Online Transfer Amount:</strong></label>
                <input type="number" id="bank-amount" oninput="calculateBalance()" class="swal2-input" placeholder="Enter bank transfer amount">
            </div>

            <!-- Balance and Print Option -->
            <hr style="border-top: 1px solid #ddd; margin: 15px 0;" />
            <p><strong>Balance:</strong> <span id="balance-amount" style="color: #28a745; font-size: 1.5em; font-weight: bold;">Rs. 0.00</span></p>

             <!-- Extra Paid Checkbox Container -->
            <div id="extra-paid-checkbox-container" style="display: none; margin-top: 10px;"></div> <br>

            <label>
                <input type="checkbox" id="print-receipt" style="margin-right: 10px;" checked>
                Print Bill
            </label>
        </div>
        
    `;


    if (paymentMethod === 'Credit') {
        setTimeout(() => {
            $('#creditPayment').prop('checked', true);
        }, 0); // Ensure the checkbox exists before trying to modify it
    }

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
        preConfirm: async () => {
            const cashAmount = parseFloat(document.getElementById('cash-amount-received').value || '0');
            const cardAmount = parseFloat(document.getElementById('card-amount').value || '0');
            const bankAmount = parseFloat(document.getElementById('bank-amount').value || '0');
            const totalReceived = cashAmount + cardAmount + bankAmount;
            var balance = totalPayable - totalReceived;
            if (document.getElementById('useCustomerExtraFund')?.checked) {
                balance = balance - useCustomerExtraFundAmount;
            }
            const printReceipt = document.getElementById('print-receipt').checked;

            if (balance > 0 && !$('#creditPayment').is(':checked')) {
                Swal.showValidationMessage('Insufficient amount received.');
                return false;
            }

            // Determine the active payment method based on the active tab
            let paymentMethod = 'Cash';
            if (document.getElementById('card-tab').classList.contains('active')) {
                paymentMethod = 'Card';
            } else if (document.getElementById('bank-tab').classList.contains('active')) {
                paymentMethod = 'Online Transfer';
            }

            // Check if the "Customer Extra Paid" checkbox is checked
            const extraPaidAddToCustomerFund = balance < 0 && document.getElementById('extra-paid-checkbox')?.checked || false;
            const extraPaidAmount = balance < 0 ? Math.abs(balance) : 0;

            // Step 1: Fetch cashier name from get_cashier_name.php
            let cashierName = 'Unknown Cashier';
            try {
                const cashierResponse = await fetch('/inc/confirm_payment_feature/get_cashier_name.php');
                if (cashierResponse.ok) {
                    const cashierData = await cashierResponse.json();
                    cashierName = cashierData.name;
                }
            } catch (error) {
                console.error("Error fetching cashier name:", error);
            }

            // Step 2: Prepare data to send to the backend
            const invoiceData = {
                cashierName,
                customerName: document.getElementById('name').value || 'Walk-in Customer',
                customerNumber: document.getElementById('tele').value || '0',
                subtotal: parseFloat(document.getElementById('total-without-discount').innerText.replace(/,/g, '')),
                discountValue,
                discountType,
                totalPayable,
                cashAmount,
                cardAmount,
                bankAmount,
                totalReceived,
                balance,
                printReceipt,
                quickCashCounts,
                productList,
                paymentMethod,
                extraPaidAddToCustomerFund,
                extraPaidAmount,
                bool_useCustomerExtraFund: document.getElementById('useCustomerExtraFund')?.checked || false,
                useCustomerExtraFundAmount,
                creditPayment: document.getElementById('creditPayment')?.checked || false,
                individualDiscountMode // Add the individual discount mode flag
            };

            // Step 3: Send data to submit-invoice.php
            try {
                const response = await fetch('submit-invoice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(invoiceData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    clearCart();
                    clearQuickCash();
                    Swal.fire({
                        icon: 'success',
                        title: 'Invoice Submitted',
                        text: 'The invoice has been successfully submitted.',
                        confirmButtonText: 'OK',
                        timer: 1000,
                        timerProgressBar: true                        
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: result.message || 'An error occurred while submitting the invoice.'
                    });
                    return false;
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Error',
                    text: 'Could not connect to the server. Please try again later.'
                });
                return false;
            }
        },
        didOpen: () => {
            modalOpen = true;
            // Show the specified tab on modal open
            if (paymentMethod === 'Card') {
                showPaymentTab('card-tab');
            } else {
                showPaymentTab('cash-tab');
            }
        },
        willClose: () => {
            modalOpen = false;
        }
    });
}

function handleUseCustomerExtraFund() {
    const useCustomerExtraFund = document.getElementById('useCustomerExtraFund');
    if (useCustomerExtraFund.checked) {
        var totalPayable = parseFloat(document.querySelector('#modal-total-payable').textContent.replace('Rs.', '').trim());
        var updatedTotalPayable = totalPayable - useCustomerExtraFundAmount;
        $('#modal-total-payable').text(`${formatCurrency(updatedTotalPayable)}`);
        calculateBalance();
    } else {
        const totalPayable = parseFloat($('#total-payable').text().replace(/,/g, ''));
        $('#modal-total-payable').text(`${formatCurrency(totalPayable)}`);
        calculateBalance();
    }
}

function showPaymentTab(tabId) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-button');
    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(button => button.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    document.querySelector(`button[onclick="showPaymentTab('${tabId}')"]`).classList.add('active');
}

function calculateBalance() {
    const totalPayable = parseFloat($('#modal-total-payable').text().replace(/,/g, ''));
    const cashAmount = parseFloat(document.getElementById('cash-amount-received').value || '0');
    const cardAmount = parseFloat(document.getElementById('card-amount').value || '0');
    const bankAmount = parseFloat(document.getElementById('bank-amount').value || '0');
    const totalPaid = cashAmount + cardAmount + bankAmount;

    const balance = totalPayable - totalPaid;
    document.getElementById('balance-amount').innerText = `Rs. ${formatCurrency(balance)}`;

    if (balance < 0) {
        // Customer paid extra, show the "Customer Extra Paid" checkbox
        const extraPaidAmount = Math.abs(balance).toFixed(2);
        document.getElementById('extra-paid-checkbox-container').innerHTML = `
            <label>
                <input type="checkbox" id="extra-paid-checkbox">
                Customer Extra Paid. Add Rs. ${extraPaidAmount} Fund to Customer Account
            </label>
        `;
        document.getElementById('extra-paid-checkbox-container').style.display = 'block';
    } else {
        // Balance is zero or positive, hide the checkbox
        document.getElementById('extra-paid-checkbox-container').style.display = 'none';
    }

    if (balance <= 0) {
        document.getElementById('change-amount').innerText = `Rs. ${formatCurrency(-balance)}`;
    } else {
        document.getElementById('change-amount').innerText = '0.00';
    }
}

function handleKeyShortcuts(event) {
    if (modalOpen) {
        if (event.key === 'F1') {
            event.preventDefault();
            showPaymentTab('cash-tab');
        }
        if (event.key === 'F2') {
            event.preventDefault();
            showPaymentTab('card-tab');
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
}

function generateQuickCashButtons() {
    const denominations = [
        { value: 10, shortcut: 'F3' },
        { value: 20, shortcut: 'F4' },
        { value: 50, shortcut: 'F5' },
        { value: 100, shortcut: 'F6' },
        { value: 500, shortcut: 'F7' },
        { value: 1000, shortcut: 'F8' },
        { value: 5000, shortcut: 'F9' }
    ];

    let buttonsHtml = `
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px;">
            ${denominations.map(({ value, shortcut }) => `
                <button onclick="incrementQuickCash(${value})" style="position: relative; width:100%; background-color: #ffa726; border: none; padding: 10px; color: white; font-weight: bold;">
                    Rs. ${formatCurrency(value)}
                    <span id="count-${value}" style="position: absolute; top: 5px; right: 36px; font-size: 0.8em; background: #333; color: #fff; border-radius: 50%; padding: 2px 6px;">
                        ${quickCashCounts[value] || ''}
                    </span>
                    <span style="position: absolute; top: 5px; right: 10px; font-size: 0.8em; color: #555;">
                        ${shortcut}
                    </span>
                </button>
            `).join('')}
            
            <button onclick="clearQuickCash()" style="grid-column: span 2; width:100%; background-color: #e57373; border: none; padding: 10px; color: white; font-weight: bold; display: flex; align-items: flex-start; justify-content: center; }">
                Clear <span style="position: relative; right: -130px; font-size: 0.8em; color: #555;">F10</span>
            </button>
        </div>
    `;

    return buttonsHtml;
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
        const amountReceivedInput = document.getElementById('cash-amount-received'); // Corrected ID
        if (amountReceivedInput) {
            amountReceivedInput.value = totalQuickCash.toFixed(2);
        }
    }

    // Update the displayed change amount only if the element exists
    const changeDisplay = document.getElementById('change-amount');
    if (changeDisplay) {
        const totalPayable = parseFloat($('#modal-total-payable').text().replace(/,/g, ''));
        const change = totalQuickCash - totalPayable;
        changeDisplay.innerText = change >= 0 ? formatCurrency(change.toFixed(2)) : '0.00';
    }

    // Recalculate balance
    calculateBalance();
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
    quickCashCounts = {
        10: 0,
        20: 0,
        50: 0,
        100: 0,
        500: 0,
        1000: 0,
        5000: 0
    };
    updateQuickCashDisplay();
    const amountReceivedInput = document.getElementById('cash-amount-received'); // Corrected ID
    if (amountReceivedInput) {
        amountReceivedInput.value = '';
    }
    const changeDisplay = document.getElementById('change-amount');
    if (changeDisplay) {
        changeDisplay.innerText = '0.00';
    }

    // Recalculate balance
    calculateBalance();
}

// Function to handle Quick Cash key shortcuts
let enterPressCount = 0; // Counter for tracking consecutive Enter presses

$(document).on('keydown', function(event) {
    if (modalOpen) {
        handleKeyShortcuts(event);
        if (event.key === 'Enter') {
            enterPressCount++;
            if (enterPressCount === 2) {
                $('.swal2-confirm').click();
                enterPressCount = 0;
            }
            setTimeout(() => {
                enterPressCount = 0;
            }, 500);
        }
    } else {
        if (event.key === 'F1') {
            event.preventDefault();
            openConfirmPaymentModal('Cash');
        }
        if (event.key === 'F2') {
            event.preventDefault();
            openConfirmPaymentModal('Card');
        }
        if (event.key === 'Enter') {
            enterPressCount++;
            if (enterPressCount === 2) {
                openConfirmPaymentModal('Cash');
                enterPressCount = 0;
            }
            setTimeout(() => {
                enterPressCount = 0;
            }, 500);
        }
    }
});