<!-- File: /AdminPanel/includes/cdn_includes.php -->

<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Popper.js -->
<script src="https://unpkg.com/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>

<!-- Custom Config -->
<script type="module" src="/AdminPanel/js/config.js"></script>

<script>
    // Tailwind CSS Configuration
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                colors: {
                    primary: '#1E40AF',
                    secondary: '#6B7280',
                    success: '#10B981',
                    danger: '#EF4444',
                    warning: '#F59E0B',
                    info: '#3B82F6'
                }
            }
        }
    }
</script>

<style>
    /* Dark theme overrides */
    .dark {
        background-color: #111827;
        color: white;
    }

    .dark .dataTables_wrapper {
        background-color: #1F2937;
        color: #F3F4F6;
    }

    .dark .select2-container--default .select2-selection--single {
        background-color: #374151;
        border-color: #4B5563;
        color: white;
    }

    /* Common styles */
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: colors 200ms;
    }

    .btn-primary {
        background-color: #1E40AF;
        color: white;
    }
    .btn-primary:hover {
        background-color: #1E3A8A;
    }

    .btn-danger {
        background-color: #EF4444;
        color: white;
    }
    .btn-danger:hover {
        background-color: #DC2626;
    }

    /* Form styles */
    .form-input {
        width: 100%;
        border-radius: 0.5rem;
        border-color: #D1D5DB;
        outline-width: 2px;
        outline-color: #1E40AF;
    }
    .dark .form-input {
        border-color: #374151;
        background-color: #1F2937;
        color: white;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
    }
    .dark .form-label {
        color: #E5E7EB;
    }

    /* Table styles */
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Card styles */
    .card {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
    }
    .dark .card {
        background-color: #1F2937;
    }
</style>