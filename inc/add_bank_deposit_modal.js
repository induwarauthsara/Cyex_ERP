        function add_bankDeposit(refresh) {
            // Add Pop Up box for Bank Deposit. Inputs : Select Bank, Amount
            // Bank Account List :  /inc/fetch_bank_accounts.php (JSON)
            fetch("/inc/fetch_bank_accounts.php")
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        title: 'Bank Deposit',
                        html: '<label for="bankAccount" class="swal2-label">Select Bank Account:</label>' +
                            '<select id="bankAccount" class="swal2-input">' +
                            data.map(account => `<option value="${account}">${account}</option>`).join('') +
                            '</select>' +
                            '<label for="depositAmount" class="swal2-label">Amount (Rs.):</label>' +
                            '<input id="depositAmount" class="swal2-input" placeholder="Enter amount">',
                        focusConfirm: false,
                        preConfirm: () => {
                            const bankAccount = Swal.getPopup().querySelector('#bankAccount').value;
                            const depositAmount = Swal.getPopup().querySelector('#depositAmount').value;
                            if (bankAccount && depositAmount && !isNaN(depositAmount)) {
                                return fetch("/inc/add_bank_deposit.php?bankAccount=" + encodeURIComponent(bankAccount) + "&amount=" + depositAmount, {
                                        method: 'GET',
                                    })
                                    .then(response => response.text())
                                    .then(html => {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: "Successfully deposited Rs. " + depositAmount + " to " + bankAccount,
                                            showConfirmButton: false,
                                            timer: 2000 // Close alert after 2 seconds
                                        });
                                        if (refresh) {
                                            setTimeout(() => {
                                                location.reload();
                                            }, 3000); // 3-second delay before reloading
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Something went wrong!',
                                        });
                                    });
                            } else {
                                Swal.showValidationMessage(`Please select bank account and enter amount (numeric value).`);
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                    });
                });
        }