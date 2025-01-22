<?php
require_once('../nav.php');

// Check if user is logged in
if (!isset($_SESSION['employee_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order & GRN Management - <?php echo $ERP_COMPANY_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- Custom CSS -->
    <style>
        /* Dark theme overrides for DataTables */
        .dataTables_wrapper {
            color: #e5e7eb;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_processing,
        .dataTables_wrapper .dataTables_paginate {
            color: #e5e7eb;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e5e7eb !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #6b7280 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            color: #111827 !important;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100">
    <!-- Header Section -->
    <header class="bg-gray-800 shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">Purchase Order & GRN Management</h1>
                <div class="flex space-x-4">
                    <button onclick="location.href='create_po.php'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i> New Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <?php
            // Get summary statistics
            $total_po = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM purchase_orders"))['count'];
            $pending_po = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM purchase_orders WHERE status = 'pending'"))['count'];
            $ordered_po = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM purchase_orders WHERE status = 'ordered'"))['count'];
            $received_po = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as count FROM purchase_orders WHERE status = 'received'"))['count'];
            ?>
            
            <!-- Total POs Card -->
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-600 rounded-full">
                        <i class="fas fa-file-invoice text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Total POs</h3>
                        <p class="text-2xl font-bold"><?php echo $total_po; ?></p>
                    </div>
                </div>
            </div>

            <!-- Pending POs Card -->
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-600 rounded-full">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Pending</h3>
                        <p class="text-2xl font-bold"><?php echo $pending_po; ?></p>
                    </div>
                </div>
            </div>

            <!-- Ordered POs Card -->
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-green-600 rounded-full">
                        <i class="fas fa-truck text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Ordered</h3>
                        <p class="text-2xl font-bold"><?php echo $ordered_po; ?></p>
                    </div>
                </div>
            </div>

            <!-- Received POs Card -->
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-600 rounded-full">
                        <i class="fas fa-box-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Received</h3>
                        <p class="text-2xl font-bold"><?php echo $received_po; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Orders Table -->
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Purchase Orders</h2>
            <div class="overflow-x-auto">
                <table id="purchaseOrdersTable" class="w-full text-left">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Date</th>
                            <th class="px-4 py-2">PO Number</th>
                            <th class="px-4 py-2">Supplier</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Total Amount</th>
                            <th class="px-4 py-2">Paid</th>
                            <th class="px-4 py-2">Balance</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </main>

    <!-- Add Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-gray-800 rounded-lg shadow-lg w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Add Payment</h3>
                    <form id="paymentForm">
                        <input type="hidden" id="po_id" name="po_id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Date</label>
                            <input type="date" id="payment_date" name="payment_date" 
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Amount</label>
                            <input type="number" step="0.01" id="payment_amount" name="payment_amount" 
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Payment Method</label>
                            <select id="payment_method" name="payment_method" 
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Reference Number</label>
                            <input type="text" id="reference_no" name="reference_no" 
                                   class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Note</label>
                            <textarea id="payment_note" name="payment_note" rows="3" 
                                      class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2"></textarea>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closePaymentModal()" 
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg">Cancel</button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg">Submit Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#purchaseOrdersTable').DataTable({
                ajax: {
                    url: 'ajax/get_purchase_orders.php',
                    dataSrc: ''
                },
                columns: [
                    { data: 'order_date' },
                    { data: 'po_number' },
                    { data: 'supplier_name' },
                    { 
                        data: 'status',
                        render: function(data) {
                            let statusClass = '';
                            switch(data) {
                                case 'draft': statusClass = 'bg-gray-600'; break;
                                case 'pending': statusClass = 'bg-yellow-600'; break;
                                case 'ordered': statusClass = 'bg-blue-600'; break;
                                case 'received': statusClass = 'bg-green-600'; break;
                                default: statusClass = 'bg-gray-600';
                            }
                            return `<span class="${statusClass} px-2 py-1 rounded-full text-xs">${data}</span>`;
                        }
                    },
                    { 
                        data: 'total_amount',
                        render: function(data) {
                            return parseFloat(data).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                        }
                    },
                    {
                        data: 'paid_amount',
                        render: function(data) {
                            return parseFloat(data || 0).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            const balance = parseFloat(data.total_amount) - parseFloat(data.paid_amount || 0);
                            return balance.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'USD'
                            });
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            let buttons = `
                                <button onclick="viewPO(${data.po_id})" class="text-blue-400 hover:text-blue-600 mr-2">
                                    <i class="fas fa-eye"></i>
                                </button>`;
                            
                            if (data.status !== 'received') {
                                buttons += `
                                    <button onclick="editPO(${data.po_id})" class="text-yellow-400 hover:text-yellow-600 mr-2">
                                        <i class="fas fa-edit"></i>
                                    </button>`;
                            }

                            if (data.status === 'ordered') {
                                buttons += `
                                    <button onclick="createGRN(${data.po_id})" class="text-green-400 hover:text-green-600 mr-2">
                                        <i class="fas fa-truck-loading"></i>
                                    </button>`;
                            }

                            const balance = parseFloat(data.total_amount) - parseFloat(data.paid_amount || 0);
                            if (balance > 0 && data.status === 'received') {
                                buttons += `
                                    <button onclick="showPaymentModal(${data.po_id}, ${balance})" class="text-purple-400 hover:text-purple-600 mr-2">
                                        <i class="fas fa-money-bill"></i>
                                    </button>`;
                            }

                            buttons += `
                                <button onclick="exportPDF(${data.po_id})" class="text-red-400 hover:text-red-600 mr-2">
                                    <i class="fas fa-file-pdf"></i>
                                </button>`;

                            if (data.status === 'draft') {
                                buttons += `
                                    <button onclick="deletePO(${data.po_id})" class="text-red-400 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>`;
                            }

                            return buttons;
                        }
                    }
                ],
                responsive: true,
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'classic',
                width: '100%'
            });

            // Set default date in payment form
            $('#payment_date').val(moment().format('YYYY-MM-DD'));
        });

        // Function to view PO details
        function viewPO(poId) {
            window.location.href = `view_po.php?id=${poId}`;
        }

        // Function to edit PO
        function editPO(poId) {
            window.location.href = `edit_po.php?id=${poId}`;
        }

        // Function to create GRN
        function createGRN(poId) {
            window.location.href = `create_grn.php?po_id=${poId}`;
        }

        // Function to show payment modal
        function showPaymentModal(poId, balance) {
            $('#po_id').val(poId);
            $('#payment_amount').val(balance);
            $('#payment_amount').attr('max', balance);
            $('#paymentModal').removeClass('hidden');
        }

        // Function to close payment modal
        function closePaymentModal() {
            $('#paymentModal').addClass('hidden');
            $('#paymentForm')[0].reset();
        }

        // Function to export PDF
        function exportPDF(poId) {
            window.location.href = `export_po.php?id=${poId}`;
        }

        // Function to delete PO
        function deletePO(poId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax/delete_po.php',
                        type: 'POST',
                        data: { po_id: poId },
                        success: function(response) {
                            const data = JSON.parse(response);
                            if (data.success) {
                                Swal.fire(
                                    'Deleted!',
                                    'Purchase order has been deleted.',
                                    'success'
                                );
                                $('#purchaseOrdersTable').DataTable().ajax.reload();
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message,
                                    'error'
                                );
                            }
                        },
                        error: function() {
                            Swal.fire(
                                'Error!',
                                'Something went wrong.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Handle payment form submission
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'ajax/add_payment.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire(
                            'Success!',
                            'Payment has been added successfully.',
                            'success'
                        );
                        closePaymentModal();
                        $('#purchaseOrdersTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire(
                            'Error!',
                            data.message,
                            'error'
                        );
                    }
                },
                error: function() {
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                }
            });
        });
    </script>
</body>
</html>