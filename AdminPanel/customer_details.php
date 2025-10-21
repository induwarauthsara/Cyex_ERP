<?php
include 'nav.php';

// Get customer ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: /AdminPanel/customers.php');
    exit;
}

$customerId = (int)$_GET['id'];
?>
<title>Customer Details - Admin Panel</title>

<style>
    .customer-details-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    .back-button {
        display: inline-block;
        padding: 10px 20px;
        background: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .back-button:hover {
        background: #5a6268;
    }

    .customer-header {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .customer-header h1 {
        margin: 0 0 20px 0;
        color: #333;
    }

    .customer-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .info-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 18px;
        color: #333;
        font-weight: 500;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .stat-label {
        font-size: 14px;
        color: #666;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #333;
    }

    .stat-value.positive {
        color: #28a745;
    }

    .stat-value.negative {
        color: #dc3545;
    }

    .invoices-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .section-header {
        padding: 20px;
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .section-header h2 {
        margin: 0;
        color: #333;
    }

    .invoices-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoices-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #333;
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .invoices-table td {
        padding: 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .invoices-table tbody tr:hover {
        background: #f8f9fa;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-paid {
        background: #d4edda;
        color: #155724;
    }

    .status-unpaid {
        background: #f8d7da;
        color: #721c24;
    }

    .status-partial {
        background: #fff3cd;
        color: #856404;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #666;
    }
</style>

<div class="customer-details-container">
    <a href="/AdminPanel/customers.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Customers
    </a>

    <div id="customerContent">
        <div class="loading">
            <i class="fas fa-spinner fa-spin"></i> Loading customer details...
        </div>
    </div>
</div>

<script>
    const customerId = <?php echo $customerId; ?>;

    // Load customer details on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadCustomerDetails();
    });

    function loadCustomerDetails() {
        fetch(`/inc/api/customer_invoices.php?id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderCustomerDetails(data);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Failed to load customer details');
            });
    }

    function renderCustomerDetails(data) {
        const customer = data.customer;
        const totals = data.totals;
        const invoices = data.invoices;

        let html = `
        <div class="customer-header">
            <h1><i class="fas fa-user"></i> ${customer.name}</h1>
            <div class="customer-info-grid">
                <div class="info-item">
                    <div class="info-label">Mobile Number</div>
                    <div class="info-value">${customer.mobile}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Customer Type</div>
                    <div class="info-value">${customer.type.toUpperCase()}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Extra Fund</div>
                    <div class="info-value">Rs. ${parseFloat(customer.extra_fund).toFixed(2)}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Customer ID</div>
                    <div class="info-value">#${customer.id}</div>
                </div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Sales</div>
                <div class="stat-value positive">Rs. ${parseFloat(totals.total_sales).toFixed(2)}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Paid</div>
                <div class="stat-value">Rs. ${parseFloat(totals.total_paid).toFixed(2)}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Outstanding Balance</div>
                <div class="stat-value ${totals.total_outstanding > 0 ? 'negative' : 'positive'}">
                    Rs. ${parseFloat(totals.total_outstanding).toFixed(2)}
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Profit</div>
                <div class="stat-value positive">Rs. ${parseFloat(totals.total_profit).toFixed(2)}</div>
            </div>
        </div>

        <div class="invoices-section">
            <div class="section-header">
                <h2><i class="fas fa-file-invoice"></i> Invoice History (${invoices.length})</h2>
            </div>
    `;

        if (invoices.length > 0) {
            html += `
            <table class="invoices-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Profit</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
        `;

            invoices.forEach(invoice => {
                const statusClass = invoice.full_paid ? 'status-paid' : (invoice.advance > 0 ? 'status-partial' : 'status-unpaid');

                html += `
                <tr>
                    <td><strong>#${invoice.invoice_number}</strong></td>
                    <td>${invoice.date} ${invoice.time}</td>
                    <td>Rs. ${parseFloat(invoice.total).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(invoice.advance).toFixed(2)}</td>
                    <td>Rs. ${parseFloat(invoice.balance).toFixed(2)}</td>
                    <td class="positive">Rs. ${parseFloat(invoice.profit).toFixed(2)}</td>
                    <td>${invoice.payment_method}</td>
                    <td><span class="status-badge ${statusClass}">${invoice.status}</span></td>
                    <td>
                        <a href="/invoice/print.php?printType=standard&id=${invoice.invoice_number}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View
                        </a>
                    </td>
                </tr>
            `;
            });

            html += `
                </tbody>
            </table>
        `;
        } else {
            html += `
            <div style="padding: 40px; text-align: center; color: #666;">
                <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                <p>No invoices found for this customer</p>
            </div>
        `;
        }

        html += `</div>`;

        document.getElementById('customerContent').innerHTML = html;
    }

    function showError(message) {
        document.getElementById('customerContent').innerHTML = `
        <div style="background: white; padding: 40px; border-radius: 8px; text-align: center;">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 20px;"></i>
            <h3>Error</h3>
            <p>${message}</p>
            <a href="/AdminPanel/customers.php" class="btn btn-primary" style="margin-top: 20px;">
                Back to Customers
            </a>
        </div>
    `;
    }
</script>