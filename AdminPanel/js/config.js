// File: /AdminPanel/js/config.js

const CONFIG = {
    DATE_FORMAT: 'YYYY-MM-DD',
    DATETIME_FORMAT: 'YYYY-MM-DD HH:mm:ss',
    CURRENCY_SYMBOL: 'Rs.',
    DECIMAL_PLACES: 2,
    API_ENDPOINTS: {
        SUPPLIERS: '/AdminPanel/api/suppliers.php',
        PRODUCTS: '/AdminPanel/api/products.php',
        PURCHASE_ORDERS: '/AdminPanel/purchase/api/purchase_orders.php',
        GRN: '/AdminPanel/purchase3/api/grn.php',
        BARCODE: '/AdminPanel/barcode-print/api/barcode.php'
    },
    STATUSES: {
        PO: {
            DRAFT: 'draft',
            PENDING: 'pending',
            APPROVED: 'approved',
            ORDERED: 'ordered',
            RECEIVED: 'received',
            CANCELLED: 'cancelled'
        },
        GRN: {
            DRAFT: 'draft',
            COMPLETED: 'completed',
            CANCELLED: 'cancelled'
        }
    },
    ALERTS: {
        SUCCESS: {
            icon: 'success',
            confirmButtonColor: '#10B981'
        },
        ERROR: {
            icon: 'error',
            confirmButtonColor: '#EF4444'
        },
        CONFIRM: {
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#EF4444'
        }
    }
};

// Utility functions
const formatCurrency = (amount) => {
    return `${CONFIG.CURRENCY_SYMBOL} ${parseFloat(amount).toFixed(CONFIG.DECIMAL_PLACES)}`;
};

const formatDate = (date) => {
    return moment(date).format(CONFIG.DATE_FORMAT);
};

const formatDateTime = (datetime) => {
    return moment(datetime).format(CONFIG.DATETIME_FORMAT);
};

// Error handler
const handleError = (error) => {
    console.error('Error:', error);
    Swal.fire({
        title: 'Error!',
        text: error.message || 'Something went wrong. Please try again.',
        ...CONFIG.ALERTS.ERROR
    });
};

// Success handler
const showSuccess = (message) => {
    Swal.fire({
        title: 'Success!',
        text: message,
        ...CONFIG.ALERTS.SUCCESS
    });
};

// Confirmation dialog
const confirmAction = async(message) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: message,
        ...CONFIG.ALERTS.CONFIRM
    });
    return result.isConfirmed;
};

// Export configuration and utilities
export {
    CONFIG,
    formatCurrency,
    formatDate,
    formatDateTime,
    handleError,
    showSuccess,
    confirmAction
};