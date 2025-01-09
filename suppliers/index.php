<?php
// Only Admin can access this page
session_start();
$employee_role = $_SESSION['employee_role'];
if ($employee_role !== "Admin") {
    header("Location: /index.php");
}
require '../inc/header.php';
require_once '../inc/config.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management - <?php echo $ERP_COMPANY_NAME; ?></title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Add after existing DataTables CSS -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" rel="stylesheet">

    <style>
        .help-icon {
            color: #6c757d;
            cursor: pointer;
        }

        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-color: #86b7fe;
        }

        .note-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .note-cell:hover {
            white-space: normal;
            overflow: visible;
            cursor: pointer;
        }

        .action-btn {
            min-width: 38px;
        }

        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
        }

        .error-details {
            max-height: 200px;
            overflow-y: auto;
            font-family: monospace;
            white-space: pre-wrap;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
        }
    </style>
</head>

<body>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="position-fixed top-0 start-0 w-100 h-100 d-none"
        style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="position-absolute top-50 start-50 translate-middle text-white text-center">
            <div class="spinner-border mb-2" role="status"></div>
            <div>Processing...</div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h2 class="mb-3">
                    <i class="fas fa-truck me-2"></i>
                    Supplier Management
                </h2>
                <button type="button" class="btn btn-primary" id="addSupplierBtn">
                    <i class="fas fa-plus me-2"></i> Add New Supplier
                </button>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <table id="suppliersTable" class="table table-striped table-bordered w-100">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Telephone</th>
                            <th>Address</th>
                            <th>Note</th>
                            <th>Credit Balance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Supplier Form Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm" class="needs-validation" novalidate>
                        <input type="hidden" id="supplierId">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                                <div class="input-group has-validation">
                                    <input type="text" class="form-control" id="supplierName" required
                                        data-bs-toggle="tooltip" title="Enter the complete business name of the supplier">
                                    <div class="invalid-feedback">Please enter supplier name</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telephone</label>
                                <div class="input-group has-validation">
                                    <input type="tel" class="form-control" id="supplierTel"
                                        pattern="[0-9]{10}" data-bs-toggle="tooltip"
                                        title="Enter a 10-digit telephone number">
                                    <div class="invalid-feedback">Please enter a valid 10-digit phone number</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" id="supplierAddress" rows="2"
                                data-bs-toggle="tooltip" title="Enter the complete business address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" id="note" rows="2"
                                data-bs-toggle="tooltip" title="Add any additional notes about the supplier"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="saveSupplierBtn">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" class="needs-validation" novalidate>
                        <input type="hidden" id="paymentSupplierId">
                        <div class="mb-3">
                            <label class="form-label">Amount <span class="text-danger">*</span></label>
                            <div class="input-group has-validation">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" id="paymentAmount"
                                    required min="0" step="0.01"
                                    data-bs-toggle="tooltip" title="Enter payment amount">
                                <div class="invalid-feedback">Please enter a valid amount</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="paymentMethod" required
                                data-bs-toggle="tooltip" title="Select payment method">
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="credit_card">Credit Card</option>
                            </select>
                            <div class="invalid-feedback">Please select a payment method</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference No.</label>
                            <input type="text" class="form-control" id="referenceNo"
                                data-bs-toggle="tooltip" title="Enter reference number (e.g., cheque number, transaction ID)">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" id="paymentNote" rows="2"
                                data-bs-toggle="tooltip" title="Add any additional notes about the payment"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary" id="savePaymentBtn">
                        <i class="fas fa-save me-2"></i>Save Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Modal -->
    <div class="modal fade" id="paymentHistoryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Payment History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="paymentHistoryTable" class="table table-striped w-100">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Reference</th>
                                    <th>Note</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <!-- Add after existing script imports -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

    <script>
        $(document).ready(function() {
            // Show loading overlay
            function showLoading() {
                $('#loadingOverlay').removeClass('d-none');
            }

            // Hide loading overlay
            function hideLoading() {
                $('#loadingOverlay').addClass('d-none');
            }

            // Error handler function
            function handleError(error, title = 'Error') {
                console.error(error);
                let errorMessage = error.message || error;
                if (error.responseJSON) {
                    errorMessage = error.responseJSON.message || errorMessage;
                }

                Swal.fire({
                    icon: 'error',
                    title: title,
                    html: `
                <div>
                    <p class="text-danger">${errorMessage}</p>
                    <div class="error-details">
                        ${JSON.stringify(error, null, 2)}
                    </div>
                </div>
            `,
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                });
            }

            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));

            // Initialize DataTable
            const suppliersTable = $('#suppliersTable').DataTable({
                ajax: {
                    url: 'suppliers.php?action=get_suppliers',
                    dataSrc: 'data',
                    error: function(xhr, error, thrown) {
                        handleError(error, 'Failed to load suppliers');
                    }
                },
                dom: 'lBfrtipQ',
                select: true,
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-secondary btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5] // Exclude actions column
                        }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info btn-sm',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        }
                    }
                ],
                columns: [{
                        data: 'supplier_id'
                    },
                    {
                        data: 'supplier_name'
                    },
                    {
                        data: 'supplier_tel'
                    },
                    {
                        data: 'supplier_address'
                    },
                    {
                        data: 'note',
                        render: function(data) {
                            if (!data) return '';
                            return `<div class="note-cell" title="${data}">${data}</div>`;
                        }
                    },
                    {
                        data: 'credit_balance',
                        render: function(data) {
                            const amount = parseFloat(data);
                            const formattedAmount = amount.toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'LKR'
                            });
                            const textClass = amount < 0 ? 'text-danger' : 'text-success';
                            return `<span class="${textClass}">${formattedAmount}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-info action-btn view-payments" 
                                    title="View Payment History">
                                <i class="fas fa-history"></i>
                            </button>
                            <button class="btn btn-success action-btn add-payment" 
                                    title="Add New Payment">
                                <i class="fas fa-dollar-sign"></i>
                            </button>
                            <button class="btn btn-primary action-btn edit-supplier" 
                                    title="Edit Supplier Details">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger action-btn delete-supplier" 
                                    title="Delete Supplier">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                        }
                    }
                ],
                order: [
                    [1, 'asc']
                ],
                responsive: true,
                pageLength: 25,
                drawCallback: function() {
                    // Reinitialize tooltips after table redraw
                    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    tooltips.forEach(el => new bootstrap.Tooltip(el));
                }
            });

            // Form validation function
            function validateForm(formId) {
                const form = document.getElementById(formId);
                form.classList.add('was-validated');
                return form.checkValidity();
            }

            // Reset form validation
            function resetFormValidation(formId) {
                const form = document.getElementById(formId);
                form.classList.remove('was-validated');
                form.reset();
            }

            // Add Supplier Button Click
            $('#addSupplierBtn').click(function() {
                $('#supplierModal .modal-title').html('<i class="fas fa-plus me-2"></i>Add New Supplier');
                resetFormValidation('supplierForm');
                $('#supplierId').val('');
                $('#supplierModal').modal('show');
            });

            // Save Supplier
            $('#saveSupplierBtn').click(async function() {
                if (!validateForm('supplierForm')) {
                    return;
                }

                const supplierId = $('#supplierId').val();
                const data = {
                    supplier_name: $('#supplierName').val().trim(),
                    supplier_tel: $('#supplierTel').val().trim(),
                    supplier_address: $('#supplierAddress').val().trim(),
                    note: $('#note').val().trim(),
                    action: supplierId ? 'edit_supplier' : 'add_supplier'
                };

                if (supplierId) {
                    data.supplier_id = supplierId;
                }

                const button = $(this);
                const originalHtml = button.html();

                try {
                    button.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');
                    showLoading();

                    const response = await $.post('suppliers.php', data);

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#supplierModal').modal('hide');
                        suppliersTable.ajax.reload();
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    handleError(error, 'Save Failed');
                } finally {
                    button.prop('disabled', false).html(originalHtml);
                    hideLoading();
                }
            });

            // Edit Supplier
            $('#suppliersTable').on('click', '.edit-supplier', function() {
                const data = suppliersTable.row($(this).closest('tr')).data();
                $('#supplierModal .modal-title').html('<i class="fas fa-edit me-2"></i>Edit Supplier');
                resetFormValidation('supplierForm');

                $('#supplierId').val(data.supplier_id);
                $('#supplierName').val(data.supplier_name);
                $('#supplierTel').val(data.supplier_tel);
                $('#supplierAddress').val(data.supplier_address);
                $('#note').val(data.note);

                $('#supplierModal').modal('show');
            });

            // Delete Supplier
            $('#suppliersTable').on('click', '.delete-supplier', async function() {
                const data = suppliersTable.row($(this).closest('tr')).data();

                try {
                    const result = await Swal.fire({
                        title: 'Delete Supplier?',
                        html: `
                    <div class="text-start">
                        <p>Are you sure you want to delete supplier:</p>
                        <ul>
                            <li><strong>Name:</strong> ${data.supplier_name}</li>
                            <li><strong>Phone:</strong> ${data.supplier_tel || 'N/A'}</li>
                            <li><strong>Balance:</strong> ${parseFloat(data.credit_balance).toLocaleString('en-US', {
                                style: 'currency',
                                currency: 'LKR'
                            })}</li>
                        </ul>
                        <p class="text-danger">This action cannot be undone!</p>
                    </div>
                `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash me-2"></i>Yes, delete it!',
                        cancelButtonText: '<i class="fas fa-times me-2"></i>Cancel'
                    });

                    if (result.isConfirmed) {
                        showLoading();
                        const response = await $.post('suppliers.php', {
                            action: 'delete_supplier',
                            supplier_id: data.supplier_id
                        });

                        if (response.status === 'success') {
                            await Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            suppliersTable.ajax.reload();
                        } else {
                            throw new Error(response.message);
                        }
                    }
                } catch (error) {
                    handleError(error, 'Delete Failed');
                } finally {
                    hideLoading();
                }
            });

            // Initialize Payment History DataTable
            const paymentHistoryTable = $('#paymentHistoryTable').DataTable({
                dom: 'lBfrtipQ',
                select: true,
                buttons: [{
                        extend: 'copy',
                        text: '<i class="fas fa-copy"></i> Copy',
                        className: 'btn btn-secondary btn-sm'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fas fa-file-pdf"></i> PDF',
                        className: 'btn btn-danger btn-sm'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print"></i> Print',
                        className: 'btn btn-info btn-sm'
                    }
                ],
                columns: [{
                        data: 'date',
                        render: function(data) {
                            return moment(data).format('YYYY-MM-DD');
                        }
                    },
                    {
                        data: 'amount',
                        render: function(data) {
                            const amount = parseFloat(data);
                            return `<span class="text-success">
                        ${amount.toLocaleString('en-US', {
                            style: 'currency',
                            currency: 'LKR'
                        })}
                    </span>`;
                        }
                    },
                    {
                        data: 'method',
                        render: function(data) {
                            return data.split('_')
                                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                                .join(' ');
                        }
                    },
                    {
                        data: 'reference_no',
                        defaultContent: '<span class="text-muted">-</span>'
                    },
                    {
                        data: 'note',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            return `<div class="note-cell" title="${data}">${data}</div>`;
                        }
                    },
                    {
                        data: 'created_by_name',
                        defaultContent: '<span class="text-muted">System</span>'
                    }
                ],
                order: [ [0, 'desc'] ],
                responsive: true,
                pageLength: 10
            });

            // View Payment History
            $('#suppliersTable').on('click', '.view-payments', async function() {
                const data = suppliersTable.row($(this).closest('tr')).data();

                try {
                    showLoading();
                    const response = await $.get('suppliers.php', {
                        action: 'get_payment_history',
                        supplier_id: data.supplier_id
                    });

                    if (response.status === 'success') {
                        $('#paymentHistoryModal .modal-title').html(
                            `<i class="fas fa-history me-2"></i>Payment History - ${data.supplier_name}`
                        );
                        paymentHistoryTable.clear().rows.add(response.data).draw();
                        $('#paymentHistoryModal').modal('show');
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    handleError(error, 'Failed to Load Payment History');
                } finally {
                    hideLoading();
                }
            });

            // Add Payment
            $('#suppliersTable').on('click', '.add-payment', function() {
                const data = suppliersTable.row($(this).closest('tr')).data();
                $('#paymentModal .modal-title').html(
                    `<i class="fas fa-dollar-sign me-2"></i>Add Payment - ${data.supplier_name}`
                );
                $('#paymentSupplierId').val(data.supplier_id);
                resetFormValidation('paymentForm');
                $('#paymentModal').modal('show');
            });

            // Save Payment
            $('#savePaymentBtn').click(async function() {
                if (!validateForm('paymentForm')) {
                    return;
                }

                const button = $(this);
                const originalHtml = button.html();

                try {
                    button.prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                    showLoading();

                    const response = await $.post('suppliers.php', {
                        action: 'add_payment',
                        supplier_id: $('#paymentSupplierId').val(),
                        amount: $('#paymentAmount').val(),
                        method: $('#paymentMethod').val(),
                        reference_no: $('#referenceNo').val().trim(),
                        note: $('#paymentNote').val().trim()
                    });

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Successful',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        $('#paymentModal').modal('hide');
                        suppliersTable.ajax.reload();
                    } else {
                        throw new Error(response.message);
                    }
                } catch (error) {
                    handleError(error, 'Payment Failed');
                } finally {
                    button.prop('disabled', false).html(originalHtml);
                    hideLoading();
                }
            });

            // Handle modal closing
            $('.modal').on('hidden.bs.modal', function() {
                resetFormValidation(this.querySelector('form')?.id);
            });

            // Initialize all tooltips
            document.querySelectorAll('[title]').forEach(el => {
                new bootstrap.Tooltip(el);
            });
        });
    </script>

</body>

</html>