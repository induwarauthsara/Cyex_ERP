/**
 * invoice-edit-actions.js
 * Handles Bill Edit actions with API integration and Preview Modal
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editInvoiceForm');
    if (form) {
        form.addEventListener('submit', handleEditSubmit);
    }
});

async function handleEditSubmit(e) {
    e.preventDefault();

    // 1. Ask for Reason
    // 1. Ask for Reason
    const result = await Swal.fire({
        title: 'Save Changes?',
        input: 'text',
        inputLabel: 'Reason for modification (Optional)',
        inputPlaceholder: 'e.g., Customer return, Wrong Item... (Default: edit)',
        showCancelButton: true
    });
    
    if (result.isDismissed) return;
    const reason = result.value;

    // 2. Collect Form Data
    const formData = new FormData(e.target);
    const invoiceNumber = formData.get('bill-no');
    
    // Parse Items
    const items = [];
    const itemCount = parseInt(formData.get('no') || 0); // "no" is the row count input

    for (let i = 0; i < 500; i++) { // Loop safely to check all potential indices or use "no"
         // Logic based on existing 'invoice_main_functions.jsx' naming convention
         // IDs are like: product_0, qty_0, rate_0...
         const productVal = document.getElementById('product_' + i)?.value;
         if (productVal) {
             items.push({
                 product: productVal,
                 qty: parseFloat(document.getElementById('qty_' + i)?.value || 0),
                 rate: parseFloat(document.getElementById('rate_' + i)?.value || 0),
                 worker: document.getElementById('biller')?.value,
                 isOneTimeProduct: document.getElementById('is_otp_' + i)?.value == '1'
             });
         }
    }

    const payload = {
        invoice_number: invoiceNumber,
        items: items,
        discount: parseFloat(formData.get('discount') || 0),
        payment_method: formData.get('PaymentMethod'),
        biller: formData.get('biller'),
        // default_worker removed, using biller for all worker contexts
        reason: reason,
        restock_items: document.getElementById('ChangeStock')?.checked || false,
        update_commission: document.getElementById('UpdateCommission')?.checked || false,
        preview_only: true
    };

    // 3. Dry Run (Preview)
    Swal.fire({ title: 'Calculating Impact...', didOpen: () => Swal.showLoading() });

    try {
        const previewRes = await fetch('/api/v1/invoices/update.php', {
            method: 'POST',
            headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
            },
            body: JSON.stringify(payload)
        });
        
        const previewData = await previewRes.json();
        if (!previewData.success) throw new Error(previewData.message);

        const data = previewData.data;

        // 4. Build Preview HTML
        let diffHtml = '<ul>';
        if (data.item_diffs && data.item_diffs.length > 0) {
            data.item_diffs.forEach(d => {
                let color = d.type === 'added' ? 'green' : (d.type === 'removed' ? 'red' : 'orange');
                diffHtml += `<li style="color:${color}">${d.type.toUpperCase()}: ${d.product} (Diff: ${d.diff})</li>`;
            });
        } else {
            diffHtml += '<li>No item changes</li>';
        }
        diffHtml += '</ul>';

        let financeHtml = `
            <p>Old Total: ${data.financial_changes.old_total}</p>
            <p>New Total: ${data.financial_changes.new_total}</p>
            <p><strong>Profit Diff: ${data.financial_changes.profit_diff}</strong></p>
        `;

        // 5. Confirm
        const { isConfirmed } = await Swal.fire({
            title: 'Confirm Changes?',
            html: `
                <div style="text-align:left; font-size: 0.9em;">
                    <p><strong>Item Changes:</strong></p>
                    ${diffHtml}
                    <hr>
                    <p><strong>Financial Impact:</strong></p>
                    ${financeHtml}
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Save Changes'
        });

        if (isConfirmed) {
             // 6. Execute Update
             payload.preview_only = false;
             Swal.fire({ title: 'Saving...', didOpen: () => Swal.showLoading() });

             const updateRes = await fetch('/api/v1/invoices/update.php', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
                },
                body: JSON.stringify(payload)
            });

            const updateData = await updateRes.json();
            if (updateData.success) {
                Swal.fire('Saved!', 'Invoice updated successfully.', 'success')
                    .then(() => window.location.href = '/AdminPanel/invoices/'); // Redirect
            } else {
                throw new Error(updateData.message);
            }
        }

    } catch (error) {
        Swal.fire('Error', error.message, 'error');
    }
}
