        function add_pettycash() {
            // Fetch Account List from fetch_bank_accounts.php
            fetch("inc/fetch_bank_accounts.php")
                .then(response => response.json())
                .then(data => {
                        Swal.fire({
                                    title: 'Add Petty Cash',
                                    html: `<label for="petty_cash_for" class="swal2-label">Petty Cash For:</label>
                            <input id="petty_cash_for" class="swal2-input" placeholder="Enter purpose">
                            <label for="petty_cash_amount" class="swal2-label">Amount (Rs.):</label>
                            <input id="petty_cash_amount" class="swal2-input" placeholder="Enter amount">
                            <label for="petty_cash_account" class="swal2-label">Account:</label>
                            <select id="petty_cash_account" class="swal2-select">
                               <option value="cash_in_hand" selected>Cash in Hand</option>
                                ${Array.isArray(data) ? data.map(account => `<option value="${account}">${account}</option>`).join('') : console.error('Error fetching accounts. data : ' + data)}
                        </select>`,
                        focusConfirm: false,
                        preConfirm: () => {
                            const petty_cash_for = Swal.getPopup().querySelector('#petty_cash_for').value;
                            const petty_cash_amount = Swal.getPopup().querySelector('#petty_cash_amount').value;
                            const petty_cash_account = Swal.getPopup().querySelector('#petty_cash_account').value;
                            if (petty_cash_for && petty_cash_amount && !isNaN(petty_cash_amount) && petty_cash_account) {
                                return fetch("inc/add_petty_cash.php?for=" + encodeURIComponent(petty_cash_for) + "&amount=" + petty_cash_amount + "&account=" + petty_cash_account, {
                                        method: 'GET',
                                    })
                                    .then(response => response.text())
                                    .then(html => {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: "Successfully added Rs. " + petty_cash_amount + " for " + petty_cash_for,
                                            showConfirmButton: false,
                                            timer: 2000 // Close alert after 2 seconds
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
                            } else {
                                Swal.showValidationMessage(`Please enter both petty cash for and amount (numeric value).`);
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