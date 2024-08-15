function addFund(invoice_number, invoice_balance, payment_status) {
    // Check Payment Status 01. "Add Fund" 02. "Full Payment"
    if (payment_status === "Add Fund") {
        // Add Fund
        fetch("/inc/fetch_bank_accounts.php")
            .then(response => response.json())
            .then(data => {
                    Swal.fire({
                                title: 'Add Fund',
                                html: `
                        <br>Balance Payment <b>Rs. ${invoice_balance}</b> <br>
                        <label for="fund_amount">Add Fund:</label>
                        <input type="number" id="fund_amount" class="swal2-input"> <br>
                        <br> New Balance <b>Rs. <span id='newBalance'>${invoice_balance}</span></b><br>
                        <label for="bankAccount" class="swal2-label"> Account:</label>
                        <select id="bankAccount" class="swal2-input">
                            <option value="cash_in_hand" selected>Cash In Hand</option>
                            ${data.map(account => `<option value="${account}">${account}</option>`).join('')}
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add Fund',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const fund_amount = Swal.getPopup().querySelector('#fund_amount').value;
                        const account = Swal.getPopup().querySelector('#bankAccount').value;
                        if (!fund_amount) {
                            Swal.showValidationMessage(`Please enter an amount`);
                        } else {
                            return fetch(`/invoice/add-fund-submit.php?account=${account}&status=addFund&invoice_number=${invoice_number}&fund_amount=${fund_amount}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(response.statusText);
                                    }
                                    return response.json();
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(`Request failed: ${error}`);
                                });
                        }
                    },
                    didOpen: () => {
                        // onInput Event for #fund_amount input
                        Swal.getPopup().querySelector('#fund_amount').addEventListener('input', function () {
                            const fund_amount = parseFloat(this.value) || 0;
                            const newBalance = invoice_balance - fund_amount;
                            document.getElementById('newBalance').innerText = newBalance.toFixed(2);
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: `Fund Added Successfully`,
                            icon: 'success',
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
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

    } else if (payment_status === "Full Payment") {
        // Make Full Payment
        fetch("/inc/fetch_bank_accounts.php")
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: 'Make Full Payment',
                    html: `
                        <label for="confirm_full_payment">Are you sure to make this invoice full payment?</label>
                        <br>Balance Payment <b>Rs. ${invoice_balance}</b> <br>
                        <label for="bankAccount" class="swal2-label"> Account:</label> 
                        <select id="bankAccount" class="swal2-input">
                            <option value="cash_in_hand" selected>Cash In Hand</option>
                            ${data.map(account => `<option value="${account}">${account}</option>`).join('')}
                        </select>
                        <input type="hidden" id="confirm_full_payment" value="yes" class="swal2-input">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Make Full Payment',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const confirm_full_payment = Swal.getPopup().querySelector('#confirm_full_payment').value;
                        const account = Swal.getPopup().querySelector('#bankAccount').value;

                        if (!confirm_full_payment) {
                            Swal.showValidationMessage(`Please confirm full payment`);
                        } else {
                            return fetch(`/invoice/add-fund-submit.php?account=${account}&status=fullPayment&invoice_number=${invoice_number}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(response.statusText);
                                    }
                                    return response.json();
                                })
                                .catch(error => {
                                    Swal.showValidationMessage(`Request failed: ${error}`);
                                });
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: `Full Payment Made Successfully`,
                            icon: 'success',
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
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
    } else {
        // Error
        Swal.fire({
            icon: 'error',
            title: 'Invalid Payment Status',
            text: 'The payment status provided is not valid.',
        });
    }
}


// Example Code for SweetAlert2
// Swal.fire({
//     title: 'Edit Cash In Hand Amount',
//     html: ` < label
//                     for = "new_cash_in_hand_amount" > Amount: < /label> <
//                     input type = "number"
//                     id = "new_cash_in_hand_amount"
//                     value = "${cash_in_hand_amount}"
//                     class = "swal2-input" > `,
//     showCancelButton: true,
//     confirmButtonText: 'Update',
//     showLoaderOnConfirm: true,
//     preConfirm: () => {
//         const new_cash_in_hand_amount = Swal.getPopup().querySelector('#new_cash_in_hand_amount').value;
//         if (!new_cash_in_hand_amount) {
//             Swal.showValidationMessage(`
//                     Please enter amount `);
//         }
//         return fetch(` / AdminPanel / api / edit_cash_in_hand.php ? new_cash_in_hand_amount = $ { new_cash_in_hand_amount }
//                     `)
//             .then(response => {
//                 if (!response.ok) {
//                     throw new Error(response.statusText)
//                 }
//                 return response.json()
//             })
//             .catch(error => {
//                 Swal.showValidationMessage(`
//                     Request failed : $ { error }
//                     `)
//             })
//     },
//     allowOutsideClick: () => !Swal.isLoading()
// }).then((result) => {
//     if (result.isConfirmed) {
//         Swal.fire({
//             title: `
//                     Cash In Hand Balance Updated Successfully `,
//             icon: 'success',
//             timer: 1000
//         }).then(() => {
//             location.reload();
//         });
//     }
// });