<?php include '../nav.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Orders - <?php echo $ERP_COMPANY_NAME; ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Additional CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style type="text/tailwindcss">
        @layer components {
            .btn {
                @apply px-4 py-2 rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50;
            }
            .btn-primary {
                @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500;
            }
            .btn-success {
                @apply bg-green-600 text-white hover:bg-green-700 focus:ring-green-500;
            }
            .btn-warning {
                @apply bg-yellow-600 text-white hover:bg-yellow-700 focus:ring-yellow-500;
            }
            .btn-danger {
                @apply bg-red-600 text-white hover:bg-red-700 focus:ring-red-500;
            }
            .table-responsive {
                @apply overflow-x-auto rounded-lg shadow border-gray-200 border;
            }
            .status-badge {
                @apply px-2 py-1 rounded-full text-xs font-medium;
            }
            .status-draft {
                @apply bg-gray-100 text-gray-800;
            }
            .status-pending {
                @apply bg-yellow-100 text-yellow-800;
            }
            .status-approved {
                @apply bg-green-100 text-green-800;
            }
            .status-ordered {
                @apply bg-blue-100 text-blue-800;
            }
            .status-received {
                @apply bg-purple-100 text-purple-800;
            }
            .status-cancelled {
                @apply bg-red-100 text-red-800;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white p-4 rounded-lg shadow-lg flex items-center space-x-3">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
            <span class="text-gray-700">Loading...</span>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        <i class="fas fa-file-invoice mr-2"></i> Purchase Orders
                    </h2>
                </div>
                <div class="mt-4 md:mt-0 flex flex-col sm:flex-row sm:space-x-3 space-y-2 sm:space-y-0">
                    <a href="create_po.php" class="btn btn-primary">
                        <i class="fas fa-plus mr-2"></i> Create Purchase Order
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <!-- Filters -->
                <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="statusFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="ordered">Ordered</option>
                            <option value="received">Received</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                        <select id="supplierFilter" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Suppliers</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                        <input type="date" id="dateFrom" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                        <input type="date" id="dateTo" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="purchaseOrdersTable" class="w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View PO Modal -->
    <div id="viewPoModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 w-full max-w-4xl">
            <div class="bg-white rounded-lg shadow-xl">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900" id="poDetailsTitle">Purchase Order Details</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closePoModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">PO Number:</p>
                            <p class="font-medium" id="poNumber"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Supplier:</p>
                            <p class="font-medium" id="poSupplier"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Order Date:</p>
                            <p class="font-medium" id="poDate"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Delivery Date:</p>
                            <p class="font-medium" id="poDeliveryDate"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            <p class="font-medium" id="poStatus"></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Created By:</p>
                            <p class="font-medium" id="poCreatedBy"></p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-lg font-medium mb-3">Items</h4>
                        <div class="table-responsive">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Product</th>
                                        <th class="px-4 py-2 text-right">Quantity</th>
                                        <th class="px-4 py-2 text-right">Unit Cost</th>
                                        <th class="px-4 py-2 text-right">Total</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="poItemsTable"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Notes:</p>
                            <p class="font-medium" id="poNotes"></p>
                        </div>
                        <div class="text-right space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span class="font-medium" id="poSubtotal"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span class="font-medium" id="poDiscount"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Tax:</span>
                                <span class="font-medium" id="poTax"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Shipping:</span>
                                <span class="font-medium" id="poShipping"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-base font-semibold text-gray-600">Total:</span>
                                <span class="text-base font-bold" id="poTotal"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end space-x-3 rounded-b-lg">
                    <button type="button" id="printPoBtn" class="btn btn-primary">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                    <button type="button" class="btn bg-gray-500 text-white hover:bg-gray-600" onclick="closePoModal()">
                        <i class="fas fa-times mr-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>

    <script>
        $(document).ready(function() {
            // Show loading overlay
            function showLoading() {
                $('#loadingOverlay').removeClass('hidden');
            }

            // Hide loading overlay
            function hideLoading() {
                $('#loadingOverlay').addClass('hidden');
            }

            // Format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'LKR'
                }).format(amount);
            }

            // Initialize Select2
            $('#supplierFilter').select2({
                placeholder: 'Select supplier',
                allowClear: true
            });

            // Initialize DataTable
            const poTable = $('#purchaseOrdersTable').DataTable({
                ajax: {
                    url: 'purchase.php?action=get_po_list',
                    dataSrc: 'data',
                    error: function(xhr, error, thrown) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Loading Data',
                            text: error
                        });
                    }
                },
                columns: [{
                        data: 'po_number',
                        className: 'px-4 py-3'
                    },
                    {
                        data: 'order_date',
                        className: 'px-4 py-3',
                        render: function(data) {
                            return moment(data).format('YYYY-MM-DD');
                        }
                    },
                    {
                        data: 'supplier_name',
                        className: 'px-4 py-3'
                    },
                    {
                        data: 'total_amount',
                        className: 'px-4 py-3',
                        render: function(data) {
                            return formatCurrency(data);
                        }
                    },
                    {
                        data: 'status',
                        className: 'px-4 py-3',
                        render: function(data) {
                            return `<span class="status-badge status-${data.toLowerCase()}">${
                        data.charAt(0).toUpperCase() + data.slice(1)
                    }</span>`;
                        }
                    },
                    {
                        data: 'created_by_name',
                        className: 'px-4 py-3'
                    },
                    {
                        data: null,
                        className: 'px-4 py-3 text-right space-x-2',
                        orderable: false,
                        render: function(data) {
                            const btns = [];

                            // View button
                            btns.push(`
                        <button type="button" class="btn-view-po text-blue-600 hover:text-blue-800" title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                    `);

                            // Edit button for draft status
                            if (data.status === 'draft') {
                                btns.push(`
                            <a href="edit_po.php?id=${data.po_id}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        `);
                            }

                            // Create GRN button for ordered status
                            if (data.status === 'ordered') {
                                btns.push(`
                            <a href="create_grn.php?po_id=${data.po_id}" class="text-green-600 hover:text-green-800" title="Create GRN">
                                <i class="fas fa-truck"></i>
                            </a>
                        `);
                            }

                            // Print button
                            btns.push(`
                        <button type="button" class="btn-print-po text-gray-600 hover:text-gray-800" title="Print">
                            <i class="fas fa-print"></i>
                        </button>
                    `);

                            return btns.join('');
                        }
                    }
                ],
                dom: 'lBfrtip',
                buttons: [{
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel mr-2"></i>Export Excel',
                        className: 'btn btn-success mb-2',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf mr-2"></i>Export PDF',
                        className: 'btn btn-danger mb-2',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    }
                ],
                order: [
                    [1, 'desc']
                ],
                pageLength: 25,
                responsive: true,
                select: true
            });

            // Apply filters
            $('#statusFilter, #supplierFilter').on('change', function() {
                poTable.draw();
            });

            $('#dateFrom, #dateTo').on('change', function() {
                poTable.draw();
            });

            // Custom filtering function
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const status = $('#statusFilter').val();
                const supplier = $('#supplierFilter').val();
                const dateFrom = $('#dateFrom').val();
                const dateTo = $('#dateTo').val();

                // Status filter
                if (status && data[4].toLowerCase().indexOf(status.toLowerCase()) === -1) {
                    return false;
                }

                // Supplier filter
                if (supplier && data[2] !== supplier) {
                    return false;
                }

                // Date range filter
                if (dateFrom || dateTo) {
                    const date = moment(data[1], 'YYYY-MM-DD');

                    if (dateFrom && date.isBefore(dateFrom)) {
                        return false;
                    }
                    if (dateTo && date.isAfter(dateTo)) {
                        return false;
                    }
                }

                return true;
            });

            // View PO Details
            $('#purchaseOrdersTable').on('click', '.btn-view-po', async function() {
                const data = poTable.row($(this).closest('tr')).data();

                try {
                    showLoading();
                    const response = await $.get('purchase.php', {
                        action: 'get_po_details',
                        po_id: data.po_id
                    });

                    if (response.status === 'success') {
                        const po = response.data.po;
                        const items = response.data.items;

                        // Fill PO details
                        $('#poNumber').text(po.po_number);
                        $('#poSupplier').text(po.supplier_name);
                        $('#poDate').text(moment(po.order_date).format('YYYY-MM-DD'));
                        $('#poDeliveryDate').text(moment(po.delivery_date).format('YYYY-MM-DD'));
                        $('#poStatus').html(`<span class="status-badge status-${po.status.toLowerCase()}">${
                    po.status.charAt(0).toUpperCase() + po.status.slice(1)
                }</span>`);
                        $('#poCreatedBy').text(po.created_by_name);
                        $('#poNotes').text(po.notes || 'N/A');

                        // Fill items table
                        $('#poItemsTable').empty();
                        items.forEach(item => {
                            $('#poItemsTable').append(`
                        <tr class="border-b">
                            <td class="px-4 py-2">
                                <div>${item.product_name}</div>
                                <div class="text-xs text-gray-500">SKU: ${item.sku}</div>
                            </td>
                            <td class="px-4 py-2 text-right">
                                <div>${item.quantity}</div>
                                <div class="text-xs text-gray-500">
                                    Received: ${item.total_received_qty}
                                </div>
                            </td>
                            <td class="px-4 py-2 text-right">${formatCurrency(item.unit_cost)}</td>
                            <td class="px-4 py-2 text-right">${formatCurrency(item.total_cost)}</td>
                            <td class="px-4 py-2 text-center">
                                <span class="status-badge status-${item.status.toLowerCase()}">
                                    ${item.status.charAt(0).toUpperCase() + item.status.slice(1)}
                                </span>
                            </td>
                        </tr>
                    `);
                        });

                        // Fill totals
                        $('#poSubtotal').text(formatCurrency(po.subtotal));
                        $('#poDiscount').text(formatCurrency(po.total_discount));
                        $('#poTax').text(formatCurrency(po.total_tax));
                        $('#poShipping').text(formatCurrency(po.shipping_fee));
                        $('#poTotal').text(formatCurrency(po.total_amount));

                        // Show modal
                        $('#viewPoModal').removeClass('hidden');
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Loading Details',
                        text: error.message
                    });
                } finally {
                    hideLoading();
                }
            });

            // Print PO
            async function printPO(poId) {
                try {
                    showLoading();
                    const response = await $.get('purchase.php', {
                        action: 'get_po_details',
                        po_id: poId
                    });

                    if (response.status === 'success') {
                        const po = response.data.po;
                        const items = response.data.items;

                        // Create print window content
                        let printContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Purchase Order - ${po.po_number}</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            .header { text-align: center; margin-bottom: 20px; }
                            .info { margin-bottom: 20px; }
                            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
                            table { width: 100%; border-collapse: collapse; }
                            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                            th { background-color: #f8f9fa; }
                            .totals { margin-top: 20px; text-align: right; }
                            @media print {
                                @page { margin: 0.5cm; }
                                body { margin: 1cm; }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>${ERP_COMPANY_NAME}</h2>
                            <h3>Purchase Order</h3>
                        </div>
                        
                        <div class="info-grid">
                            <div>
                                <p><strong>PO Number:</strong> ${po.po_number}</p>
                                <p><strong>Date:</strong> ${moment(po.order_date).format('YYYY-MM-DD')}</p>
                                <p><strong>Delivery Date:</strong> ${moment(po.delivery_date).format('YYYY-MM-DD')}</p>
                            </div>
                            <div>
                                <p><strong>Supplier:</strong> ${po.supplier_name}</p>
                                <p><strong>Status:</strong> ${po.status}</p>
                                <p><strong>Created By:</strong> ${po.created_by_name}</p>
                            </div>
                        </div>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th style="text-align: right">Quantity</th>
                                    <th style="text-align: right">Unit Cost</th>
                                    <th style="text-align: right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                        items.forEach(item => {
                            printContent += `
                        <tr>
                            <td>${item.product_name}</td>
                            <td>${item.sku}</td>
                            <td style="text-align: right">${item.quantity}</td>
                            <td style="text-align: right">${formatCurrency(item.unit_cost)}</td>
                            <td style="text-align: right">${formatCurrency(item.total_cost)}</td>
                        </tr>
                    `;
                        });

                        printContent += `
                            </tbody>
                        </table>
                        
                        <div class="totals">
                            <p><strong>Subtotal:</strong> ${formatCurrency(po.subtotal)}</p>
                            <p><strong>Discount:</strong> ${formatCurrency(po.total_discount)}</p>
                            <p><strong>Tax:</strong> ${formatCurrency(po.total_tax)}</p>
                            <p><strong>Shipping:</strong> ${formatCurrency(po.shipping_fee)}</p>
                            <p><strong>Total Amount:</strong> ${formatCurrency(po.total_amount)}</p>
                        </div>
                        
                        <div style="margin-top: 40px;">
                            <p><strong>Notes:</strong> ${po.notes || 'N/A'}</p>
                        </div>
                        
                        <div style="margin-top: 60px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                            <div style="text-align: center;">
                                <p>_______________________</p>
                                <p>Authorized Signature</p>
                            </div>
                            <div style="text-align: center;">
                                <p>_______________________</p>
                                <p>Supplier Signature</p>
                            </div>
                        </div>
                    </body>
                    </html>
                `;

                        // Open print window
                        const printWindow = window.open('', '', 'width=800,height=600');
                        printWindow.document.write(printContent);
                        printWindow.document.close();
                        printWindow.focus();

                        // Print after images are loaded
                        setTimeout(() => {
                            printWindow.print();
                            printWindow.close();
                        }, 250);
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Printing PO',
                        text: error.message
                    });
                } finally {
                    hideLoading();
                }
            }

            // Print button click handler
            $('#purchaseOrdersTable').on('click', '.btn-print-po', function() {
                const data = poTable.row($(this).closest('tr')).data();
                printPO(data.po_id);
            });

            // Print button in modal
            $('#printPoBtn').click(function() {
                const poId = poTable.row('.selected').data().po_id;
                printPO(poId);
            });

            // Close modal
            window.closePoModal = function() {
                $('#viewPoModal').addClass('hidden');
            };

            // Close modal on backdrop click
            $('#viewPoModal').click(function(e) {
                if (e.target === this) {
                    closePoModal();
                }
            });
        });
    </script>

</body>

</html>