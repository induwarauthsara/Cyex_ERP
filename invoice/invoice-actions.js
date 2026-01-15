/**
 * invoice-actions.js
 * Handles Bill Delete actions with API integration and Preview Modal
 */

async function confirmDeleteInvoice(invoiceNumber) {
    // 1. Ask for Reason
    const { value: reasonInput } = await Swal.fire({
        title: 'Delete Invoice #' + invoiceNumber,
        input: 'text',
        inputLabel: 'Reason for deletion (Optional)',
        inputPlaceholder: 'e.g., Duplicate entry, Returns...',
        showCancelButton: true
    });

    if (reasonInput !== undefined) { // Check if not cancelled (value is undefined on cancel)
        const reason = reasonInput || "delete invoice"; // Default value if empty
        // 2. Ask for Restock Preference
        const { value: restock } = await Swal.fire({
            title: 'Stock Adjustment',
            text: "Do you want to restock items to inventory?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Restock',
            cancelButtonText: 'No, Write-off',
            reverseButtons: true
        });

        // 3. Show Loading & Fetch Preview
        Swal.fire({ title: 'Calculating Impact...', didOpen: () => Swal.showLoading() });

        try {
            const previewResponse = await fetch('/api/v1/invoices/delete.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token') // Assuming auth
                },
                body: JSON.stringify({
                    invoice_number: invoiceNumber,
                    reason: reason,
                    restock_items: restock,
                    preview_only: true
                })
            });
            
            const previewData = await previewResponse.json();

            if (!previewData.success) {
                throw new Error(previewData.message || 'Error calculating preview');
            }

            const data = previewData.data;
            
            // 4. Show Impact Summary (Html)
            let stockHtml = '<ul>';
            if (data.stock_changes.length > 0) {
                data.stock_changes.forEach(item => {
                    stockHtml += `<li><b>${item.name}</b>: ${item.change > 0 ? '+' : ''}${item.change} <small>(${item.type})</small></li>`;
                });
            } else {
                stockHtml += '<li>No stock changes</li>';
            }
            stockHtml += '</ul>';

            let commHtml = '<ul>';
            const comm = data.commission_changes;
            if (comm.biller) commHtml += `<li>Biller: ${comm.biller.amount}</li>`;
            if (comm.worker) commHtml += `<li>Worker: ${comm.worker.amount}</li>`;
            if (comm.company) commHtml += `<li>Company: ${comm.company.amount}</li>`;
            commHtml += '</ul>';

            const { isConfirmed } = await Swal.fire({
                title: 'Confirm Deletion?',
                html: `
                    <div style="text-align:left; font-size: 0.9em;">
                        <p><strong>Impact Summary:</strong></p>
                        <p><u>Stock Changes:</u></p>
                        ${stockHtml}
                        <p><u>Financial Reversals:</u></p>
                        ${commHtml}
                        <hr>
                        <p style="color:red"><b>This action cannot be undone!</b></p>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete it!'
            });

            if (isConfirmed) {
                // 5. Execute Delete
                Swal.fire({ title: 'Deleting...', didOpen: () => Swal.showLoading() });
                
                const deleteResponse = await fetch('/api/v1/invoices/delete.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + localStorage.getItem('jwt_token') },
                    body: JSON.stringify({
                        invoice_number: invoiceNumber,
                        reason: reason,
                        restock_items: restock,
                        preview_only: false
                    })
                });

                const deleteData = await deleteResponse.json();
                
                if (deleteData.success) {
                    Swal.fire('Deleted!', 'Invoice has been deleted.', 'success')
                        .then(() => location.reload());
                } else {
                    throw new Error(deleteData.message);
                }
            }

        } catch (error) {
            Swal.fire('Error', error.message, 'error');
        }
    }
}
