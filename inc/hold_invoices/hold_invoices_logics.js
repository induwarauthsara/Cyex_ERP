//  handle the "Hold" button click and send the cart data to the backend.
function holdInvoice() {
    Swal.fire({
        title: 'Hold Invoice',
        text: 'Are you sure you want to hold this invoice?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Hold It',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const customerName = document.getElementById('name').value || "Walk-in Customer";
            const customerNumber = document.getElementById('tele').value || "0";
            const totalAmount = parseFloat(document.getElementById('total-without-discount').textContent.replace(/,/g, '')) || 0;
            const discountText = document.getElementById('discount-amount').textContent;
            const totalPayable = parseFloat(document.getElementById('total-payable').textContent.replace(/,/g, '')) || 0;

            // Extract discount amount and percentage
            const discountAmount = parseFloat(discountText.replace(/[^\d.-]/g, '')); // Extracts numeric part of discount
            const discountPercentageMatch = discountText.match(/\((\d+)%\)/); // Extracts the percentage in parentheses
            const discountPercentage = discountPercentageMatch ? parseFloat(discountPercentageMatch[1]) : null;

            const items = productList.map(item => ({
                name: item.name,
                quantity: item.quantity,
                price: parseFloat(item.price) ? parseFloat(item.price).toFixed(2) : '0.00',
                subtotal: parseFloat(item.subtotal) ? parseFloat(item.subtotal).toFixed(2) : '0.00'
            }));

            // console.log({
            //     "customer_name": customerName,
            //     "customer_number": customerNumber,
            //     "items": items,
            //     "total_amount": totalAmount,
            //     "discount_amount": discountAmount,
            //     "discount_type": discountType, // Include discount type
            //     "discount_value": discountPercentage || discountValue, // Save the extracted percentage if available
            //     "total_payable": totalPayable
            // });

            fetch('/inc/hold_invoices/save_held_invoice.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_name: customerName,
                        customer_number: customerNumber,
                        items: items,
                        total_amount: totalAmount,
                        discount_amount: discountAmount,
                        discount_type: discountType, // Include discount type
                        discount_value: discountPercentage || discountValue, // Save the extracted percentage if available
                        total_payable: totalPayable
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Invoice held successfully.', 'success');
                        clearCart();
                    } else {
                        Swal.fire('Error', 'Failed to hold invoice. Try again.', 'error');
                    }
                })
                .catch(error => console.error('Error:', error));
        }
    });
};


function clearCart() {
    // Clear product list
    productList = [];
    localStorage.removeItem('productList');

    // Clear customer details
    document.getElementById('name').value = 'Walk-in Customer';
    document.getElementById('tele').value = '0';
    localStorage.removeItem('customerName');
    localStorage.removeItem('customerPhone');

    // Clear discount details
    discountType = 'flat'; // Default discount type
    discountValue = 0;
    localStorage.removeItem('discountType');
    localStorage.removeItem('discountValue');

    // Update UI
    renderProductList();
    calculateInvoiceTotal();
}


function loadHeldInvoices() {
    // Show loading animation immediately
    Swal.fire({
        title: 'Loading Held Invoices',
        html: `
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Please wait...</p>
            </div>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            // Add the spinner styles if not already in your CSS
            const style = document.createElement('style');
            style.innerHTML = `
                .loading-spinner {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .spinner {
                    width: 50px;
                    height: 50px;
                    border: 5px solid rgba(0, 0, 0, 0.1);
                    border-radius: 50%;
                    border-top-color: var(--primary, #4361ee);
                    animation: spin 1s ease-in-out infinite;
                    margin-bottom: 15px;
                }
                @keyframes spin {
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);
        }
    });

    // Fetch held invoices data
    fetch('/inc/hold_invoices/get_held_invoices.php')
        .then(response => response.json())
        .then(data => {
                let heldInvoicesHtml;

                if (data.length === 0) {
                    // Display message if no held invoices are available
                    heldInvoicesHtml = `<p style="text-align: center; font-size: 16px; color: #666;">No held invoices found.</p>`;
                } else {
                    // Generate the table of held invoices
                    heldInvoicesHtml = `
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th style="padding: 8px; border: 1px solid #ddd;">Invoice ID</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Customer Name</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Customer Number</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Items</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Total Amount</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Discount Amount</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Discount Type</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Discount Value</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Total Payable</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Status</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Held At</th>
                            <th style="padding: 8px; border: 1px solid #ddd;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(invoice => `
                            <tr>
                                <td style="padding: 8px; border: 1px solid #ddd;">${invoice.id}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">${invoice.customer_name}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">${invoice.customer_number}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">
                                    <ul style="list-style-type: none; padding: 0; margin: 0;">
                                        ${invoice.items.map(item => `<li>${item.name} x ${item.quantity}</li>`).join('')}
                                    </ul>
                                </td>
                                <td style="padding: 8px; border: 1px solid #ddd;">Rs. ${parseFloat(invoice.total_amount || 0).toFixed(2)}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">Rs. ${parseFloat(invoice.discount_amount || 0).toFixed(2)}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">${invoice.discount_type}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">${parseFloat(invoice.discount_value || 0)}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">Rs. ${parseFloat(invoice.total_payable || 0).toFixed(2)}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">${invoice.status}</td>
                                <td style="padding: 8px; border: 1px solid #ddd;">${invoice.held_at}</td>
                                <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                                    <button onclick="retrieveHeldInvoice(${invoice.id})" class="btn-retrieve" style="padding: 5px 10px; border: none; background-color: #4CAF50; color: white; border-radius: 4px; cursor: pointer;">Retrieve</button>
                                    <button onclick="Swal.close(); deleteHeldInvoice(${invoice.id})" class="btn-delete" style="padding: 5px 10px; border: none; background-color: #f44336; color: white; border-radius: 4px; cursor: pointer; margin-left: 5px;">Delete</button>                                        
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>`;
            }

            // Display the held invoices or the empty message in a modal
            Swal.fire({
                title: 'Held Invoices List',
                html: `<div style="max-height: 400px; overflow-y: auto;">${heldInvoicesHtml}</div>`,
                showConfirmButton: false,
                width: '90%', // Make the modal wider to accommodate the table
                willOpen: () => {
                    document.addEventListener('keydown', handleEscapeKey);
                },
                willClose: () => {
                    document.removeEventListener('keydown', handleEscapeKey);
                }
            });
        })
        .catch(error => {
            console.error('Error fetching held invoices:', error);
            Swal.fire('Error', 'Could not load held invoices. Please try again later.', 'error');
        });
}

// Escape key handler to close the modal
function handleEscapeKey(event) {
    if (event.key === 'Escape') {
        Swal.close();
    }
}





function retrieveHeldInvoice(invoiceId) {
    // Close the held invoices modal
    Swal.close();

    fetch(`/inc/hold_invoices/retrieve_held_invoice.php?id=${invoiceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the productList with the retrieved items
                productList = data.items;

                // Set discount type and value from retrieved data
                discountType = data.discount_type || 'flat';
                // Ensure discount value is a number
                discountValue = parseFloat(data.discount_value) || 0;

                // Set customer name and number from retrieved data
                const customerName = data.customer_name || 'Walk-in Customer';
                const customerNumber = data.customer_number || '0';

                // Update the customer name and number fields in the UI
                document.getElementById('name').value = customerName;
                document.getElementById('tele').value = customerNumber;

                // Save the retrieved items, discount data, and customer info to localStorage
                localStorage.setItem('productList', JSON.stringify(productList));
                localStorage.setItem('discountType', discountType);
                localStorage.setItem('discountValue', discountValue);
                localStorage.setItem('customerName', customerName);
                localStorage.setItem('customerPhone', customerNumber);

                // Re-render the product list in the cart, apply discount, and calculate totals
                renderProductList();
                applyDiscount(); // Reapply the discount
                calculateInvoiceTotal();

                // Optionally, delete or mark the held invoice as completed after retrieval
                deleteHeldInvoice(invoiceId);
            } else {
                Swal.fire('Error', 'Failed to retrieve the invoice.', 'error');
            }
        })
        .catch(error => {
            console.error('Error retrieving held invoice:', error);
            Swal.fire('Error', 'Could not retrieve the invoice. Please try again later.', 'error');
        });
}


function deleteHeldInvoice(invoiceId) {
    fetch(`/inc/hold_invoices/delete_held_invoice.php?id=${invoiceId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                Swal.fire('Error', 'Failed to delete the invoice.', 'error');
            }
        });
}