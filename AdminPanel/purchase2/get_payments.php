<?php

require_once('../nav.php');

if (!isset($_GET['po_id'])) {
    die('Purchase order ID is required');
}

$po_id = (int)$_GET['po_id'];

// Get PO details
$po_sql = "
    SELECT 
        po.*,
        s.supplier_name,
        COALESCE(SUM(sp.amount), 0) as paid_amount
    FROM purchase_orders po
    LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
    LEFT JOIN supplier_payments sp ON po.po_id = sp.po_id
    WHERE po.po_id = ?
    GROUP BY po.po_id
";

$po_stmt = mysqli_prepare($con, $po_sql);
mysqli_stmt_bind_param($po_stmt, 'i', $po_id);
mysqli_stmt_execute($po_stmt);
$po_result = mysqli_stmt_get_result($po_stmt);
$po_data = mysqli_fetch_assoc($po_result);

if (!$po_data) {
    die('Purchase order not found');
}

// Get payment history
$payments_sql = "
    SELECT 
        sp.*,
        e.emp_name as created_by_name
    FROM supplier_payments sp
    LEFT JOIN employees e ON sp.created_by = e.employ_id
    WHERE sp.po_id = ?
    ORDER BY sp.created_at DESC
";

$payments_stmt = mysqli_prepare($con, $payments_sql);
mysqli_stmt_bind_param($payments_stmt, 'i', $po_id);
mysqli_stmt_execute($payments_stmt);
$payments_result = mysqli_stmt_get_result($payments_stmt);

// Calculate remaining balance
$balance = $po_data['total_amount'] - $po_data['paid_amount'];
?>

<div class="p-4">
    <!-- PO Summary -->
    <div class="mb-6 grid grid-cols-2 gap-4">
        <div>
            <p class="text-gray-400">PO Number</p>
            <p class="text-white font-semibold"><?= htmlspecialchars($po_data['po_number']) ?></p>
        </div>
        <div>
            <p class="text-gray-400">Supplier</p>
            <p class="text-white font-semibold"><?= htmlspecialchars($po_data['supplier_name']) ?></p>
        </div>
        <div>
            <p class="text-gray-400">Total Amount</p>
            <p class="text-white font-semibold">
                <?= number_format($po_data['total_amount'], 2) ?>
            </p>
        </div>
        <div>
            <p class="text-gray-400">Balance</p>
            <p class="text-white font-semibold <?= $balance > 0 ? 'text-red-500' : 'text-green-500' ?>">
                <?= number_format($balance, 2) ?>
            </p>
        </div>
    </div>

    <!-- Add Payment Button -->
    <?php if ($balance > 0): ?>
        <div class="mb-6">
            <button onclick="showAddPaymentModal(<?= $po_id ?>)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-plus mr-2"></i>Add Payment
            </button>
        </div>
    <?php endif; ?>

    <!-- Payment History -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Reference</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Amount</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Method</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Created By</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-400 uppercase">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                <?php while ($payment = mysqli_fetch_assoc($payments_result)): ?>
                    <tr>
                        <td class="px-4 py-2 text-white">
                            <?= date('Y-m-d', strtotime($payment['date'])) ?>
                        </td>
                        <td class="px-4 py-2 text-white">
                            <?= htmlspecialchars($payment['reference_no']) ?>
                        </td>
                        <td class="px-4 py-2 text-white">
                            <?= number_format($payment['amount'], 2) ?>
                        </td>
                        <td class="px-4 py-2 text-white">
                            <?= ucfirst($payment['method']) ?>
                        </td>
                        <td class="px-4 py-2 text-white">
                            <?= htmlspecialchars($payment['created_by_name']) ?>
                        </td>
                        <td class="px-4 py-2 text-white">
                            <?= htmlspecialchars($payment['note']) ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php if (mysqli_num_rows($payments_result) === 0): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-gray-400 text-center">
                            No payments recorded
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function showAddPaymentModal(poId) {
        Swal.fire({
            title: 'Add Payment',
            html: `
            <form id="paymentForm" class="text-left">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Amount</label>
                    <input type="number" id="amount" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white" 
                           step="0.01" required min="0.01" max="${<?= $balance ?>}">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Payment Method</label>
                    <select id="method" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white" required>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="credit_card">Credit Card</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Reference No.</label>
                    <input type="text" id="reference" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-400">Note</label>
                    <textarea id="note" class="mt-1 block w-full rounded-md bg-gray-700 border-gray-600 text-white" rows="2"></textarea>
                </div>
            </form>
        `,
            showCancelButton: true,
            confirmButtonText: 'Add Payment',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            preConfirm: () => {
                return {
                    amount: document.getElementById('amount').value,
                    method: document.getElementById('method').value,
                    reference: document.getElementById('reference').value,
                    note: document.getElementById('note').value,
                    po_id: poId
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit payment
                $.ajax({
                    url: 'add_payment.php',
                    method: 'POST',
                    data: result.value,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Success!',
                                'Payment added successfully',
                                'success'
                            ).then(() => {
                                // Refresh payment history
                                viewPayments(poId);
                                // Reload main table
                                $('#purchaseOrdersTable').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Failed to add payment',
                            'error'
                        );
                    }
                });
            }
        });
    }
</script>