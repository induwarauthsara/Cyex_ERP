<?php
require_once('../nav.php');

// Get GRN ID from URL
$grn_id = isset($_GET['grn_id']) ? (int)$_GET['grn_id'] : 0;

if (!$grn_id) {
    die("Invalid GRN ID");
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit GRN - Goods Received Note</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            "50": "#eff6ff",
                            "100": "#dbeafe",
                            "200": "#bfdbfe",
                            "300": "#93c5fd",
                            "400": "#60a5fa",
                            "500": "#3b82f6",
                            "600": "#2563eb",
                            "700": "#1d4ed8",
                            "800": "#1e40af",
                            "900": "#1e3a8a",
                            "950": "#172554"
                        }
                    }
                }
            }
        }
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-900 text-white">
    <div class="min-h-screen">
        <!-- Header -->
        <nav class="bg-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-white">
                                <i class="fas fa-edit mr-2"></i>Edit GRN
                            </h1>
                        </div>
                        <div class="ml-10 flex items-baseline space-x-4">
                            <a href="index.php" class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                                <i class="fas fa-arrow-left mr-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-6xl mx-auto py-6 px-4">
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-20">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                <p class="text-gray-400">Loading GRN details...</p>
            </div>

            <!-- Edit Form (Hidden until data loads) -->
            <form id="editGrnForm" class="space-y-6" style="display: none;">
                
                <!-- Basic Information Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                        Basic Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">GRN Number</label>
                            <input type="text" id="grnNumber" disabled
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-gray-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Receipt Date <span class="text-red-500">*</span></label>
                            <input type="date" id="receiptDate"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Supplier</label>
                            <input type="text" id="supplierName" disabled
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-gray-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Invoice Number</label>
                            <input type="text" id="invoiceNumber"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500"
                                placeholder="Supplier invoice number">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Invoice Date</label>
                            <input type="date" id="invoiceDate"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Items Card (Read-only) -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-boxes mr-2 text-green-500"></i>
                        Items Received
                        <span class="ml-2 text-sm text-gray-400">(Cannot be modified)</span>
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-gray-400 border-b border-gray-700">
                                    <th class="pb-3 px-2">Product</th>
                                    <th class="pb-3 px-2 w-24">Batch</th>
                                    <th class="pb-3 px-2 w-24">Quantity</th>
                                    <th class="pb-3 px-2 w-32">Cost</th>
                                    <th class="pb-3 px-2 w-32">Selling Price</th>
                                    <th class="pb-3 px-2 w-32">Total</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody" class="divide-y divide-gray-700">
                                <!-- Items will be loaded here -->
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-gray-600">
                                    <td colspan="5" class="pt-4 px-2 text-right font-semibold">Total Amount:</td>
                                    <td class="pt-4 px-2 font-bold text-green-400" id="totalAmount">Rs. 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Payment Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-yellow-500"></i>
                        Payment Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Payment Status</label>
                            <select id="paymentStatus" onchange="handlePaymentStatusChange()"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                                <option value="unpaid">Unpaid (Credit)</option>
                                <option value="partial">Partial Payment</option>
                                <option value="paid">Fully Paid</option>
                            </select>
                        </div>

                        <div id="paidAmountDiv" style="display: none;">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Paid Amount</label>
                            <input type="number" id="paidAmount" step="0.01" min="0"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                                placeholder="0.00" onchange="updateOutstanding()">
                        </div>

                        <div id="paymentMethodDiv" style="display: none;">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                            <select id="paymentMethod"
                                class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white">
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>
                    </div>

                    <div id="paymentReferenceDiv" style="display: none;" class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Reference</label>
                        <input type="text" id="paymentReference"
                            class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white"
                            placeholder="Transaction ID, Cheque No, etc.">
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Outstanding Balance:</span>
                            <span id="outstandingAmount" class="text-xl font-bold text-yellow-400">Rs. 0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-gray-800 rounded-lg p-6 shadow-lg">
                    <h2 class="text-lg font-semibold mb-4 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-purple-500"></i>
                        Notes
                    </h2>
                    <textarea id="notes" rows="3"
                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:ring-2 focus:ring-blue-500"
                        placeholder="Add any additional notes or comments..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3">
                    <a href="index.php"
                        class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="button" onclick="updateGRN()"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-semibold">
                        <i class="fas fa-save mr-2"></i>Update GRN
                    </button>
                </div>
            </form>
        </main>
    </div>

    <script>
        const grnId = <?= $grn_id ?>;
        const authToken = localStorage.getItem('auth_token');
        let grnData = null;

        // Check authentication
        if (!authToken) {
            Swal.fire({
                icon: 'error',
                title: 'Authentication Required',
                text: 'Please log in to continue',
            }).then(() => {
                window.location.href = '/login/';
            });
        }

        // Load GRN data on page load
        $(document).ready(function() {
            loadGRNData();
        });

        async function loadGRNData() {
            try {
                const response = await fetch(`/api/v1/grn/details.php?id=${grnId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + authToken
                    }
                });

                const data = await response.json();

                if (data.success && data.data) {
                    grnData = data.data;
                    populateForm(grnData);
                    document.getElementById('loadingState').style.display = 'none';
                    document.getElementById('editGrnForm').style.display = 'block';
                } else {
                    throw new Error(data.message || 'Failed to load GRN');
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load GRN details: ' + error.message
                }).then(() => {
                    window.location.href = 'index.php';
                });
            }
        }

        function populateForm(data) {
            // Basic info
            document.getElementById('grnNumber').value = data.grn_number || '';
            document.getElementById('receiptDate').value = data.receipt_date || '';
            document.getElementById('supplierName').value = data.supplier?.name || '';
            document.getElementById('invoiceNumber').value = data.invoice_number || '';
            document.getElementById('invoiceDate').value = data.invoice_date || '';
            document.getElementById('notes').value = data.notes || '';

            // Payment info (nested in payment object)
            document.getElementById('paymentStatus').value = data.payment?.payment_status || 'unpaid';
            document.getElementById('paidAmount').value = data.payment?.paid_amount || '0';
            document.getElementById('paymentMethod').value = data.payment?.payment_method || '';
            document.getElementById('paymentReference').value = data.payment?.payment_reference || '';

            // Populate items
            const itemsBody = document.getElementById('itemsBody');
            itemsBody.innerHTML = '';
            let total = 0;

            if (data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    const itemTotal = item.received_qty * item.cost;
                    total += itemTotal;

                    const row = `
                        <tr>
                            <td class="py-3 px-2">
                                <div class="font-medium">${item.product?.name || 'Unknown Product'}</div>
                                ${item.expiry_date ? '<small class="text-gray-500">Exp: ' + item.expiry_date + '</small>' : ''}
                            </td>
                            <td class="py-3 px-2">
                                <span class="text-gray-400">${item.batch_number || 'N/A'}</span>
                            </td>
                            <td class="py-3 px-2 text-white">${item.received_qty}</td>
                            <td class="py-3 px-2 text-white">Rs. ${parseFloat(item.cost).toFixed(2)}</td>
                            <td class="py-3 px-2 text-white">Rs. ${parseFloat(item.selling_price).toFixed(2)}</td>
                            <td class="py-3 px-2 font-semibold">Rs. ${itemTotal.toFixed(2)}</td>
                        </tr>
                    `;
                    itemsBody.insertAdjacentHTML('beforeend', row);
                });
            }

            // Use total from payment object or calculated total
            const totalAmount = data.payment?.total_amount || total;
            document.getElementById('totalAmount').textContent = 'Rs. ' + totalAmount.toFixed(2);
            
            handlePaymentStatusChange();
            updateOutstanding();
        }

        function handlePaymentStatusChange() {
            const status = document.getElementById('paymentStatus').value;
            const paidAmountDiv = document.getElementById('paidAmountDiv');
            const paymentMethodDiv = document.getElementById('paymentMethodDiv');
            const paymentReferenceDiv = document.getElementById('paymentReferenceDiv');

            if (status === 'paid') {
                paidAmountDiv.style.display = 'none';
                paymentMethodDiv.style.display = 'block';
                paymentReferenceDiv.style.display = 'block';
                const totalText = document.getElementById('totalAmount').textContent;
                const total = parseFloat(totalText.replace('Rs. ', '').replace(',', ''));
                document.getElementById('paidAmount').value = total.toFixed(2);
            } else if (status === 'partial') {
                paidAmountDiv.style.display = 'block';
                paymentMethodDiv.style.display = 'block';
                paymentReferenceDiv.style.display = 'block';
            } else {
                paidAmountDiv.style.display = 'none';
                paymentMethodDiv.style.display = 'none';
                paymentReferenceDiv.style.display = 'none';
                document.getElementById('paidAmount').value = '0';
            }
            updateOutstanding();
        }

        function updateOutstanding() {
            const totalText = document.getElementById('totalAmount').textContent;
            const total = parseFloat(totalText.replace('Rs. ', '').replace(',', ''));
            const status = document.getElementById('paymentStatus').value;
            let paid = 0;

            if (status === 'paid') {
                paid = total;
            } else if (status === 'partial') {
                paid = parseFloat(document.getElementById('paidAmount').value) || 0;
            }

            const outstanding = total - paid;
            document.getElementById('outstandingAmount').textContent = 'Rs. ' + outstanding.toFixed(2);
        }

        async function updateGRN() {
            // Collect data
            const updateData = {
                grn_id: grnId,
                receipt_date: document.getElementById('receiptDate').value,
                invoice_number: document.getElementById('invoiceNumber').value,
                invoice_date: document.getElementById('invoiceDate').value,
                notes: document.getElementById('notes').value,
                payment_data: {
                    payment_status: document.getElementById('paymentStatus').value,
                    paid_amount: parseFloat(document.getElementById('paidAmount').value) || 0,
                    payment_method: document.getElementById('paymentMethod').value,
                    payment_reference: document.getElementById('paymentReference').value
                }
            };

            // Validate
            if (!updateData.receipt_date) {
                Swal.fire('Error', 'Please select a receipt date', 'error');
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Updating GRN...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch('/api/v1/grn/update.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + authToken
                    },
                    body: JSON.stringify(updateData)
                });

                const data = await response.json();

                Swal.close();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'GRN updated successfully',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'index.php?updated=1';
                    });
                } else {
                    Swal.fire('Error', data.message || 'Failed to update GRN', 'error');
                }
            } catch (error) {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Connection error. Please try again.', 'error');
            }
        }
    </script>
</body>

</html>
