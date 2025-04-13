// Cash Register Functionality

// Open Cash Register Modal
function openCashRegister() {
    // Show loading overlay
    showLoadingAnimation("Fetching register data...");

    // First check if register is already opened
    $.ajax({
                url: '/AdminPanel/api/cash_register.php',
                method: 'POST',
                data: {
                    action: 'get_register_status'
                },
                success: function(response) {
                        try {
                            // Hide loading overlay
                            hideLoadingAnimation();

                            const data = typeof response === 'object' ? response : JSON.parse(response);

                            if (data.success) {
                                const registerStatus = data.data;
                                const isOpen = registerStatus.is_open;
                                const registerDetails = registerStatus.details || {};

                                // Create HTML content with register details
                                let registerDetailsHtml = `
                        <div class="register-summary">
                            <h3>Today's Register Details</h3>
                            <p><strong>Date:</strong> ${registerStatus.today_date || ''}</p>
                            ${isOpen ? `
                                <p><strong>Opening Balance:</strong> Rs. ${parseFloat(registerDetails.opening_balance || 0).toFixed(2)}</p>
                                <p><strong>Total Sales:</strong> Rs. ${parseFloat(registerDetails.total_sales || 0).toFixed(2)}</p>
                                <p><strong>Cash Sales:</strong> Rs. ${parseFloat(registerDetails.cash_sales || 0).toFixed(2)}</p>
                                <p><strong>Card Sales:</strong> Rs. ${parseFloat(registerDetails.card_sales || 0).toFixed(2)}</p>
                                <p><strong>Petty Cash:</strong> Rs. ${parseFloat(registerDetails.cash_out || 0).toFixed(2)}</p>
                                <p><strong>Cash Drawer Amount:</strong> Rs. ${parseFloat(registerDetails.expected_cash || 0).toFixed(2)}</p>
                            ` : '<p>No open register session for today</p>'}
                        </div>
                    `;
                    
                    // Create buttons based on register status
                    let buttonsHtml = `
                        <div style="margin: 20px 0;">
                            ${!isOpen ? `<button id="openRegister" class="swal2-styled" onclick="openRegisterModal()">Open Register</button>` : ''}
                            ${isOpen ? `<button id="closeRegister" class="swal2-styled" onclick="closeRegisterModal()">Close Register</button>` : ''}
                            ${isOpen ? `<button id="cashOut" class="swal2-styled" onclick="cashOutModal()">Petty Cash</button>` : ''}
                            <button id="viewRegister" class="swal2-styled" onclick="viewRegisterDetails()">View Register History</button>
                            ${isOpen && registerStatus.register_id ? `<button id="printRegister" class="swal2-styled" onclick="printRegisterReport(${registerStatus.register_id})">Print Current Report</button>` : ''}
                        </div>
                    `;
                    
                    // Show modal with register details and buttons
                    Swal.fire({
                        title: 'Cash Register',
                        html: `
                            <div style="text-align: left;">
                                ${registerDetailsHtml}
                                ${buttonsHtml}
                            </div>
                        `,
                        showConfirmButton: false,
                        showCancelButton: true,
                        cancelButtonText: 'Close',
                        width: '600px'
                    });
                    
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to get register status',
                        icon: 'error'
                    });
                }
                
            } catch (e) {
                console.error('JSON Parse error:', e, 'Raw response:', response);
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid server response: ' + e.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            // Hide loading overlay
            hideLoadingAnimation();
            
            console.error('AJAX error:', status, error);
            Swal.fire({
                title: 'Error!',
                text: 'Server error: ' + error,
                icon: 'error'
            });
        }
    });
}

// Loading animation functions
function showLoadingAnimation(message = 'Loading...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoadingAnimation() {
    Swal.close();
}

// Open Register Modal (start of day)
function openRegisterModal() {
    Swal.fire({
        title: 'Open Cash Register',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <div class="form-group">
                    <label for="opening_balance">Opening Float (Cash in Register):</label>
                    <input type="number" id="opening_balance" class="swal2-input" placeholder="Enter opening balance" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label for="register_notes">Notes:</label>
                    <textarea id="register_notes" class="swal2-textarea" placeholder="Optional notes"></textarea>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Open Register',
        focusConfirm: false,
        preConfirm: () => {
            const opening_balance = document.getElementById('opening_balance').value;
            const notes = document.getElementById('register_notes').value;

            if (!opening_balance) {
                Swal.showValidationMessage('Please enter opening balance');
                return false;
            }

            return { opening_balance, notes };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading animation
            showLoadingAnimation('Opening register...');
            
            // Send data to server
            $.ajax({
                url: '/AdminPanel/api/cash_register.php',
                method: 'POST',
                data: {
                    action: 'open_register',
                    opening_balance: result.value.opening_balance,
                    notes: result.value.notes
                },
                success: function(response) {
                    // Hide loading animation
                    hideLoadingAnimation();
                    
                    try {
                        // Try to parse as JSON if it's a string
                        const data = typeof response === 'object' ? response : JSON.parse(response);
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Cash register opened successfully',
                                icon: 'success',
                                timer: 2000
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to open cash register',
                                icon: 'error'
                            });
                        }
                    } catch (e) {
                        console.error('JSON Parse error:', e, 'Raw response:', response);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Invalid server response: ' + e.message,
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Hide loading animation
                    hideLoadingAnimation();
                    
                    console.error('AJAX error:', status, error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Server error: ' + error,
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// Close Register Modal (end of day)
function closeRegisterModal() {
    // Show loading animation
    showLoadingAnimation('Fetching register summary...');
    
    // First fetch register summary
    $.ajax({
        url: '/AdminPanel/api/cash_register.php',
        method: 'POST',
        data: {
            action: 'get_register_summary'
        },
        success: function(response) {
            // Hide loading animation
            hideLoadingAnimation();
            
            try {
                // Try to parse as JSON if it's a string
                const data = typeof response === 'object' ? response : JSON.parse(response);
                if (data.success) {
                    const summary = data.data;
                    
                    // Debug log
                    console.log('Register Summary Data:', summary);
                    console.log('Register ID from API:', summary.register_id, 'Type:', typeof summary.register_id);

                    Swal.fire({
                        title: 'Close Cash Register',
                        html: `
                            <div style="text-align: left; margin: 20px 0;">
                                <div class="register-summary">
                                    <h3>Register Summary</h3>
                                    <p><strong>Opening Balance:</strong> ${parseFloat(summary.opening_balance).toFixed(2)}</p>
                                    <p><strong>Total Sales:</strong> ${parseFloat(summary.total_sales).toFixed(2)}</p>
                                    <p><strong>Cash Sales:</strong> ${parseFloat(summary.cash_sales).toFixed(2)}</p>
                                    <p><strong>Card Sales:</strong> ${parseFloat(summary.card_sales).toFixed(2)}</p>
                                    <p><strong>Petty Cash:</strong> ${parseFloat(summary.cash_out).toFixed(2)}</p>
                                    <p><strong>Cash Drawer Amount:</strong> ${parseFloat(summary.expected_cash).toFixed(2)}</p>
                                </div>
                                <input type="hidden" id="register_id" value="${summary.register_id}">
                                <div class="form-group">
                                    <label for="cash_out">Cash Out:</label>
                                    <input type="number" id="cash_out" class="swal2-input" placeholder="Enter cash out amount" step="0.01" min="0">
                                </div>
                                <div class="form-group">
                                    <label for="cash_drawer_balance">Cash Drawer Balance:</label>
                                    <input type="number" id="cash_drawer_balance" class="swal2-input" placeholder="Enter cash drawer balance" step="0.01" min="0">
                                </div>
                                <div class="form-group">
                                    <label for="bank_deposit">Bank Deposit Amount:</label>
                                    <input type="number" id="bank_deposit" class="swal2-input" placeholder="Enter bank deposit" step="0.01" min="0" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="closing_notes">Notes:</label>
                                    <textarea id="closing_notes" class="swal2-textarea" placeholder="Optional notes"></textarea>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Close Register',
                        focusConfirm: false,
                        preConfirm: () => {
                            // Get form values
                            const cash_out = document.getElementById('cash_out').value;
                            const cash_drawer_balance = document.getElementById('cash_drawer_balance').value;
                            const bank_deposit = document.getElementById('bank_deposit').value;
                            const notes = document.getElementById('closing_notes').value;
                            const register_id = document.getElementById('register_id').value;
                            
                            if (!cash_out) {
                                Swal.showValidationMessage('Please enter cash out amount');
                                return false;
                            }
                            
                            if (!cash_drawer_balance) {
                                Swal.showValidationMessage('Please enter cash drawer balance');
                                return false;
                            }
                            
                            return { cash_out, cash_drawer_balance, bank_deposit, notes, register_id };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading animation
                            showLoadingAnimation('Closing register...');
                            
                            // Debug log
                            console.log('Register ID:', result.value.register_id, 'Type:', typeof result.value.register_id);
                            const parsedRegisterID = parseInt(result.value.register_id, 10);
                            console.log('Parsed Register ID:', parsedRegisterID, 'Type:', typeof parsedRegisterID);
                            
                            // Send data to server
                            $.ajax({
                                url: '/AdminPanel/api/cash_register.php',
                                method: 'POST',
                                data: {
                                    action: 'close_register',
                                    register_id: parsedRegisterID,
                                    cash_out: result.value.cash_out,
                                    cash_drawer_balance: result.value.cash_drawer_balance,
                                    bank_deposit: result.value.bank_deposit,
                                    notes: result.value.notes
                                },
                                success: function(closeResponse) {
                                    // Hide loading animation
                                    hideLoadingAnimation();
                                    
                                    try {
                                        // Try to parse as JSON if it's a string
                                        const closeData = typeof closeResponse === 'object' ? closeResponse : JSON.parse(closeResponse);
                                        if (closeData.success) {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Cash register closed successfully',
                                                icon: 'success',
                                                confirmButtonText: 'Print Report',
                                                showCancelButton: true,
                                                cancelButtonText: 'Close'
                                            }).then((printResult) => {
                                                if (printResult.isConfirmed) {
                                                    printRegisterReport(closeData.data.register_id);
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Error!',
                                                text: closeData.message || 'Failed to close cash register',
                                                icon: 'error'
                                            });
                                        }
                                    } catch (e) {
                                        console.error('JSON Parse error:', e, 'Raw response:', closeResponse);
                                        Swal.fire({
                                            title: 'Error!',
                                            text: 'Invalid server response: ' + e.message,
                                            icon: 'error'
                                        });
                                    }
                                },
                                error: function(xhr, status, error) {
                                    // Hide loading animation
                                    hideLoadingAnimation();
                                    
                                    console.error('AJAX error:', status, error);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Server error: ' + error,
                                        icon: 'error'
                                    });
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to get register summary',
                        icon: 'error'
                    });
                }
            } catch (e) {
                console.error('JSON Parse error:', e, 'Raw response:', response);
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid server response: ' + e.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            // Hide loading animation
            hideLoadingAnimation();
            
            console.error('AJAX error:', status, error);
            Swal.fire({
                title: 'Error!',
                text: 'Server error: ' + error,
                icon: 'error'
            });
        }
    });
}

// Cash Out / Petty Cash Modal - Integrated with existing petty cash system
function cashOutModal() {
    // First check if there's an open register session
    showLoadingAnimation('Checking register status...');
    
    $.ajax({
        url: '/AdminPanel/api/cash_register.php',
        method: 'POST',
        data: {
            action: 'get_register_status'
        },
        success: function(response) {
            hideLoadingAnimation();
            
            try {
                const data = typeof response === 'object' ? response : JSON.parse(response);
                if (data.success && data.data.is_open) {
                    const registerId = data.data.register_id;
                    
                    // Get cashier name first
                    fetch("/inc/confirm_payment_feature/get_cashier_name.php")
                        .then(response => response.json())
                        .then(cashierData => {
                            // Then fetch account list for petty cash
                            fetch("/inc/fetch_bank_accounts.php")
                                .then(response => response.json())
                                .then(accountsData => {
                                    const cashierName = cashierData.success ? cashierData.name : '';
                                    
                                    Swal.fire({
                                        title: 'Petty Cash',
                                        html: `
                                            <div style="text-align: left; margin: 20px 0;">
                                                <div class="form-group">
                                                    <label for="petty_cash_for">Purpose:</label>
                                                    <input type="text" id="petty_cash_for" class="swal2-input" placeholder="Purpose of petty cash">
                                                </div>
                                                <div class="form-group">
                                                    <label for="petty_cash_amount">Amount:</label>
                                                    <input type="number" id="petty_cash_amount" class="swal2-input" placeholder="Enter amount" step="0.01" min="0">
                                                </div>
                                                <div class="form-group">
                                                    <label for="petty_cash_account">Account:</label>
                                                    <select id="petty_cash_account" class="swal2-select">
                                                        <option value="cash_in_hand" selected>Cash in Hand</option>
                                                        ${Array.isArray(accountsData) ? accountsData.map(account => `<option value="${account}">${account}</option>`).join('') : ''}
                                                    </select>
                                                </div>
                                            </div>
                                        `,
                                        showCancelButton: true,
                                        confirmButtonText: 'Submit',
                                        focusConfirm: false,
                                        preConfirm: () => {
                                            const purpose = document.getElementById('petty_cash_for').value;
                                            const amount = document.getElementById('petty_cash_amount').value;
                                            const account = document.getElementById('petty_cash_account').value;

                                            if (!purpose) {
                                                Swal.showValidationMessage('Please enter purpose of petty cash');
                                                return false;
                                            }

                                            if (!amount || isNaN(parseFloat(amount)) || parseFloat(amount) <= 0) {
                                                Swal.showValidationMessage('Please enter a valid amount greater than zero');
                                                return false;
                                            }

                                            return { 
                                                purpose, 
                                                amount, 
                                                employee: cashierName,
                                                account, 
                                                register_id: registerId 
                                            };
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            showLoadingAnimation('Processing petty cash...');
                                            
                                            // Use the existing system's petty cash API to record the transaction
                                            fetch(`/inc/add_petty_cash.php?for=${encodeURIComponent(result.value.purpose)}&amount=${result.value.amount}&account=${result.value.account}&register_id=${result.value.register_id}&employee=${encodeURIComponent(result.value.employee)}`, {
                                                method: 'GET',
                                            })
                                            .then(response => {
                                                hideLoadingAnimation();
                                                
                                                // Also record in the cash register system
                                                $.ajax({
                                                    url: '/AdminPanel/api/cash_register.php',
                                                    method: 'POST',
                                                    data: {
                                                        action: 'cash_out',
                                                        purpose: result.value.purpose,
                                                        amount: result.value.amount,
                                                        employee: result.value.employee
                                                    },
                                                    success: function(regResponse) {
                                                        try {
                                                            const regData = typeof regResponse === 'object' ? regResponse : JSON.parse(regResponse);
                                                            if (regData.success) {
                                                                Swal.fire({
                                                                    title: 'Success!',
                                                                    text: 'Petty cash recorded successfully',
                                                                    icon: 'success',
                                                                    timer: 2000
                                                                }).then(() => {
                                                                    openCashRegister(); // Refresh the register display
                                                                });
                                                            } else {
                                                                Swal.fire({
                                                                    title: 'Warning!',
                                                                    text: 'Petty cash recorded in accounts but not in register: ' + regData.message,
                                                                    icon: 'warning'
                                                                });
                                                            }
                                                        } catch(e) {
                                                            console.error('Error parsing response:', e);
                                                            Swal.fire({
                                                                title: 'Success with Warning!',
                                                                text: 'Petty cash recorded in accounts but there was an issue with the register system',
                                                                icon: 'warning'
                                                            });
                                                        }
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error('AJAX error:', error);
                                                        Swal.fire({
                                                            title: 'Success with Warning!',
                                                            text: 'Petty cash recorded in accounts but register update failed',
                                                            icon: 'warning'
                                                        });
                                                    }
                                                });
                                            })
                                            .catch(error => {
                                                hideLoadingAnimation();
                                                
                                                console.error('Error:', error);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Oops...',
                                                    text: 'Something went wrong with the petty cash system!',
                                                });
                                            });
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Failed to load account list!',
                                    });
                                });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to get cashier information!',
                            });
                        });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'No open register session found. Please open a register first.',
                        icon: 'error'
                    });
                }
            } catch (e) {
                console.error('JSON Parse error:', e, 'Raw response:', response);
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid server response: ' + e.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            hideLoadingAnimation();
            
            console.error('AJAX error:', status, error);
            Swal.fire({
                title: 'Error!',
                text: 'Server error: ' + error,
                icon: 'error'
            });
        }
    });
}

// View Register Details Modal
function viewRegisterDetails() {
    // Show loading animation
    showLoadingAnimation('Fetching register sessions...');
    
    // Fetch all register sessions for today
    $.ajax({
        url: '/AdminPanel/api/cash_register.php',
        method: 'POST',
        data: {
            action: 'get_today_sessions'
        },
        success: function(response) {
            // Hide loading animation
            hideLoadingAnimation();
            
            try {
                // Try to parse as JSON if it's a string
                const data = typeof response === 'object' ? response : JSON.parse(response);
                if (data.success) {
                    const sessions = data.data;

                    let sessionsHtml = '';
                    if (sessions.length > 0) {
                        sessionsHtml = '<table class="register-sessions-table" style="width:100%; border-collapse: collapse;">';
                        sessionsHtml += '<tr><th>Opened At</th><th>Status</th><th>Opening Balance</th><th>Closing Balance</th><th>Action</th></tr>';

                        sessions.forEach(session => {
                            const status = session.closed_at ? 'Closed' : 'Open';
                            sessionsHtml += `<tr>
                                <td>${session.opened_at}</td>
                                <td>${status}</td>
                                <td>${parseFloat(session.opening_balance).toFixed(2)}</td>
                                <td>${session.actual_cash ? parseFloat(session.actual_cash).toFixed(2) : 'N/A'}</td>
                                <td><button onclick="printRegisterReport(${session.id})">Print</button></td>
                            </tr>`;
                        });

                        sessionsHtml += '</table>';
                    } else {
                        sessionsHtml = '<p>No register sessions found for today.</p>';
                    }

                    Swal.fire({
                        title: 'Register Sessions Today',
                        html: sessionsHtml,
                        showCancelButton: true,
                        confirmButtonText: 'Close',
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to get register sessions',
                        icon: 'error'
                    });
                }
            } catch (e) {
                console.error('JSON Parse error:', e, 'Raw response:', response);
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid server response: ' + e.message,
                    icon: 'error'
                });
            }
        },
        error: function(xhr, status, error) {
            // Hide loading animation
            hideLoadingAnimation();
            
            console.error('AJAX error:', status, error);
            Swal.fire({
                title: 'Error!',
                text: 'Server error: ' + error,
                icon: 'error'
            });
        }
    });
}

// Print Register Report
function printRegisterReport(registerId) {
    window.open(`/AdminPanel/cash_register_print.php?id=${registerId}`, '_blank');
}