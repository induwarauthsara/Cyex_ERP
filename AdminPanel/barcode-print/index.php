<!-- File: /AdminPanel/barcode-print/index.php -->
<?php
// require_once '../../inc/config.php';
include '../nav.php';

// Check session and authorization
if (!isset($_SESSION['employee_id'])) {
    header('Location: /login/');
    exit();
}

// Only admin users can access this page
if ($_SESSION['employee_role'] !== 'Admin') {
    header('Location: /');
    exit();
}

// Get template data if editing
$templateId = isset($_GET['template_id']) ? intval($_GET['template_id']) : null;
$template = null;

if ($templateId) {
    $stmt = mysqli_prepare($con, "SELECT * FROM barcode_templates WHERE template_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $templateId);
    mysqli_stmt_execute($stmt);
    $template = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// Get GRN ID if coming from GRN
$grnId = isset($_GET['grn_id']) ? intval($_GET['grn_id']) : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Printing - <?php echo $ERP_COMPANY_NAME; ?></title>

    <!-- Include required CSS and JS files -->
    <?php include '../includes/cdn_includes.php'; ?>

    <!-- Additional required libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <!-- Load our JavaScript after dependencies -->
    <script type="module" src="../js/config.js"></script>
    <!-- <script type="module" src="./js/barcode_print.js"></script> -->

    <style>
        /* Barcode Printing Styles */
        @media print {
            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }

        .barcode-preview {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* QR code styling */
        .qrcode {
            display: inline-block;
        }

        .qrcode img {
            width: 100%;
            height: 100%;
        }

        .barcode-preview {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .barcode-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2px 0;
        }

        .barcode-container svg {
            max-width: 100%;
            height: auto;
        }

        .price-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8em;
            margin-top: 2px;
        }

        .strike {
            text-decoration: line-through;
        }

        .promo-price {
            color: #10B981;
            font-weight: bold;
        }
    </style>
</head>

<body class="dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold dark:text-white">
                <i class="fas fa-barcode mr-2"></i> Barcode Printing
            </h1>
            <div class="flex gap-2">
                <button id="previewBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                    <i class="fas fa-eye mr-2"></i> Preview
                </button>
                <button id="printBtn" class="btn bg-green-500 hover:bg-green-600 text-white">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Settings -->
            <div class="md:col-span-1 space-y-6">
                <!-- Paper Settings -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="text-xl font-semibold">Label Settings</h2>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-3">Paper Settings</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="paperSize" class="block text-sm mb-1">Paper Size (mm):</label>
                                        <select id="paperSize" class="form-select block w-full">
                                            <option value="30">30×15 mm (Small Label)</option>
                                            <option value="36">36×13 mm</option>
                                            <option value="24">24×12 mm</option>
                                            <option value="18">18×10 mm</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="margin" class="block text-sm mb-1">Margin (mm):</label>
                                        <input type="number" id="margin" class="form-input block w-full" value="1" min="0" step="0.5">
                                    </div>
                                    <div>
                                        <label for="gapBetween" class="block text-sm mb-1">Gap Between Labels (mm):</label>
                                        <input type="number" id="gapBetween" class="form-input block w-full" value="2.5" min="0" step="0.5">
                                    </div>
                                    <div>
                                        <label for="fontSize" class="block text-sm mb-1">Font Size (pt):</label>
                                        <input type="number" id="fontSize" class="form-input block w-full" value="9" min="6" max="12">
                                    </div>
                                    <div>
                                        <label for="barcodeHeight" class="block text-sm mb-1">Barcode Height (mm):</label>
                                        <input type="number" id="barcodeHeight" class="form-input block w-full" value="15" min="5" max="30">
                                    </div>
                                </div>

                                <h3 class="text-lg font-semibold mb-3 mt-6">Content Settings</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="showShopName" class="form-checkbox">
                                        <label for="showShopName">Shop Name</label>
                                    </div>
                                    <div id="shopNameField" class="ml-6 hidden">
                                        <input type="text" id="shopName" class="form-input w-full hidden"
                                            value="<?php echo $ERP_COMPANY_NAME; ?>"
                                            placeholder="Enter shop name">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="showProductName" class="form-checkbox" checked>
                                        <label for="showProductName">Product Name</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="showPrice" class="form-checkbox" checked>
                                        <label for="showPrice">Price</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="showPromoPrice" class="form-checkbox">
                                        <label for="showPromoPrice">Promotional Price</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="showCategory" class="form-checkbox">
                                        <label for="showCategory">Category</label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" id="showUnit" class="form-checkbox">
                                        <label for="showUnit">Unit</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Items -->
            <div class="md:col-span-2 space-y-6">
                <!-- Search and Add -->
                <div class="card">
                    <div class="flex gap-4">
                        <div class="flex-grow">
                            <select id="productSearch" class="form-input w-full" placeholder="Search products..."></select>
                        </div>
                        <button id="addProductBtn" class="btn bg-blue-500 hover:bg-blue-600 text-white">
                            <i class="fas fa-plus mr-2"></i> Add
                        </button>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card">
                    <div class="overflow-x-auto">
                        <table id="itemsTable" class="w-full">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Batch</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Items will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Template Actions -->
                <div class="card">
                    <div class="flex justify-between items-center">
                        <div>
                            <label class="form-label" for="templateName">Save as Template</label>
                            <div class="flex gap-2">
                                <input type="text" id="templateName" class="form-input"
                                    placeholder="Enter template name">
                                <button id="saveTemplateBtn" class="btn bg-purple-500 hover:bg-purple-600 text-white">
                                    <i class="fas fa-save mr-2"></i> Save
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Load Template</label>
                            <select id="loadTemplate" class="form-input">
                                <option value="">Select Template</option>
                                <?php
                                $query = "SELECT template_id, template_name FROM barcode_templates ORDER BY template_name";
                                $result = mysqli_query($con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$row['template_id']}'>{$row['template_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="relative w-full max-w-4xl mx-auto my-16">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                <div class="p-4 border-b dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-xl font-semibold dark:text-white">Preview</h3>
                    <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-4">
                    <div id="previewContent" class="bg-white overflow-auto" style="min-height: 400px;">
                        <!-- Preview content will be loaded here -->
                    </div>
                </div>
                <div class="p-4 border-t dark:border-gray-700 flex justify-end gap-2">
                    <button onclick="closePreviewModal()" class="btn bg-gray-500 hover:bg-gray-600 text-white">
                        Close
                    </button>
                    <button onclick="printBarcodes()" class="btn bg-green-500 hover:bg-green-600 text-white">
                        <i class="fas fa-print mr-2"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="grnId" value="<?php echo $grnId; ?>">
    <script type="module" src="./js/barcode_print.js"></script>

    <!-- Direct script to fix product search functionality -->
    <script>
        // Global functions
        function getSettings() {
            return {
                paper_size: parseFloat($('#paperSize').val()),
                margin: parseFloat($('#margin').val()) || 1,
                gap_between: parseFloat($('#gapBetween').val()) || 2.5,
                font_size: parseFloat($('#fontSize').val()) || 9,
                barcode_height: parseFloat($('#barcodeHeight').val()) || 15,
                show_price: $('#showPrice').prop('checked'),
                show_unit: $('#showUnit').prop('checked'),
                show_category: $('#showCategory').prop('checked'),
                show_promo_price: $('#showPromoPrice').prop('checked'),
                show_shop_name: $('#showShopName').prop('checked'),
                shop_name: $('#shopName').val(),
                show_product_name: $('#showProductName').prop('checked')
            };
        }

        // Modal close function
        function closePreviewModal() {
            $('#previewModal').addClass('hidden');
        }

        // Generate preview HTML function 
        function generatePreviewHTML(items, settings) {
            let html = '<h3 class="text-xl font-semibold mb-4">Live Preview</h3>';
            html += `<div class="flex flex-wrap gap-2 justify-start">`;

            // Determine dimensions based on paper size
            let labelWidth, labelHeight;
            switch (parseFloat(settings.paper_size)) {
                case 36:
                    labelWidth = 36;
                    labelHeight = 13;
                    break;
                case 24:
                    labelWidth = 24;
                    labelHeight = 12;
                    break;
                case 18:
                    labelWidth = 18;
                    labelHeight = 10;
                    break;
                case 30:
                default:
                    labelWidth = 30;
                    labelHeight = 15;
            }

            // Calculate display size (scaled for preview)
            const scale = 3; // Scale for display purposes
            const displayWidth = labelWidth * scale;
            const displayHeight = labelHeight * scale;
            const margin = Math.max(1, settings.margin) || 1; // Ensure minimum margin

            // More compact spacing for barcode
            const compactSpacing = true;
            const lineSpacing = compactSpacing ? 0.8 : 1.2;

            // Generate a unique timestamp for all barcode IDs
            const timestamp = new Date().getTime();

            items.forEach((item, index) => {
                // Create a unique ID for each barcode
                const barcodeId = `barcode-preview-${index}-${timestamp}`;
                const barcode = item.barcode || `PROD${item.product_id}`;

                // Create a barcode preview card for each item
                html += `
                    <div class="barcode-preview border p-1 relative bg-white" 
                         style="width: ${displayWidth}px; height: ${displayHeight}px; margin: ${settings.gap_between || 0}px; page-break-inside: avoid;">
                        <div class="absolute inset-0 p-1" style="overflow: hidden;">
                    `;

                let contentHeight = 0;

                // Shop name (smaller size and less margin)
                if (settings.show_shop_name) {
                    const fontSize = Math.min(settings.font_size * 0.85, 6);
                    html += `<div class="text-center font-bold" style="font-size: ${fontSize}pt; line-height: ${lineSpacing};">
                            ${settings.shop_name || '<?php echo $ERP_COMPANY_NAME; ?>'}
                        </div>`;
                    contentHeight += fontSize * lineSpacing;
                }

                // Product name (slightly reduced size)
                if (settings.show_product_name) {
                    const fontSize = Math.min(settings.font_size * 0.9, 7);
                    html += `<div class="text-center overflow-hidden" style="font-size: ${fontSize}pt; line-height: ${lineSpacing}; 
                        white-space: nowrap; text-overflow: ellipsis;">
                            ${item.product_name}
                        </div>`;
                    contentHeight += fontSize * lineSpacing;
                }

                // Barcode (optimized height)
                const barcodeHeight = Math.min(settings.barcode_height, labelHeight - contentHeight - 5);
                html += `<div class="flex justify-center" style="height: ${barcodeHeight}px; margin: 1px 0;">
                        <svg id="${barcodeId}" class="w-full"></svg>
                    </div>`;

                // Price and additional info (more compact)
                html += `<div class="flex justify-between items-center text-xs" style="font-size: ${settings.font_size * 0.65}pt; line-height: ${lineSpacing};">`;

                // Left column (category)
                if (settings.show_category && item.category) {
                    html += `<div class="text-left truncate" style="max-width: 30%;">${item.category}</div>`;
                } else {
                    html += '<div></div>';
                }

                // Middle column (price)
                html += `<div class="text-center flex-grow">`;
                if (settings.show_price) {
                    html += `<div class="font-bold ${settings.show_promo_price && item.discount_price ? 'line-through' : ''}">
                            Rs. ${parseFloat(item.price).toFixed(0)}
                        </div>`;

                    if (settings.show_promo_price && item.discount_price) {
                        html += `<div class="font-bold text-green-600">
                                Rs. ${parseFloat(item.discount_price).toFixed(0)}
                            </div>`;
                    }
                }
                html += `</div>`;

                // Right column (unit)
                if (settings.show_unit) {
                    html += `<div class="text-right" style="max-width: 20%;">${item.unit || 'pcs'}</div>`;
                } else {
                    html += '<div></div>';
                }

                html += `</div>`;

                // Close label container
                html += `
                            </div>
                        </div>
                    `;
            });

            html += '</div>';

            // Add script to render barcodes after HTML is added to DOM
            html += `
                <script>
                    function initializeBarcodePreview() {
                        try {
                            ${items.map((item, index) => {
                                const barcode = item.barcode || `PROD${item.product_id}`;
                                
                                return `
                                    try {
                                        const barcodeElement = document.getElementById("barcode-preview-${index}-${timestamp}");
                                        if (barcodeElement) {
                                            JsBarcode(barcodeElement, "${barcode}", {
                                                format: "CODE128",
                                                width: 1,
                                                height: ${settings.barcode_height},
                                                displayValue: true,
                                                fontSize: ${settings.font_size * 1.2},
                                                textMargin: 1,
                                                margin: 0,
                                                background: "#ffffff"
                                            });
                                        } else {
                                            console.error("Barcode element not found for index ${index}");
                                        }
                                    } catch(e) {
                                        console.error("Error rendering barcode for ${item.product_name}:", e);
                                    }
                                `;
                            }).join('')}
                        } catch(e) {
                            console.error("Error in barcode initialization:", e);
                        }
                    }

                    // Initialize barcodes after a short delay to ensure DOM is ready
                    setTimeout(initializeBarcodePreview, 100);
                <\/script>`;

            return html;
        }

        // Print barcodes function 
        async function printBarcodes() {
            // Get all items from the table
            const items = [];
            $('#itemsTable tbody tr').each(function() {
                const $row = $(this);
                const qty = parseInt($row.find('.item-qty').val()) || 1;

                // For each quantity, push item to the items array - this expands items by quantity
                for (let i = 0; i < qty; i++) {
                    items.push({
                        product_id: $row.data('product-id'),
                        product_name: $row.data('product-name'),
                        barcode: $row.data('barcode'),
                        price: $row.data('price'),
                        discount_price: $row.data('discount-price'),
                        category: $row.data('category'),
                        unit: $row.data('unit'),
                        batch_id: $row.data('batch-id'),
                        batch_number: $row.find('td:eq(1)').text().trim(),
                        quantity: 1 // Each item has quantity 1 since we're expanding them
                    });
                }
            });

            if (items.length === 0) {
                Swal.fire({
                    title: 'No Items',
                    text: 'Please add items to print',
                    icon: 'warning'
                });
                return;
            }

            // Get the print format
            const printFormat = $('#printerType').val();

            try {
                // Get settings and ensure paper size is numeric
                const settings = getSettings();
                settings.paper_size = parseFloat(settings.paper_size);

                // Show loading message
                $('#loadingOverlay').removeClass('hidden').addClass('flex');

                // For thermal printing
                if (printFormat === 'network') {
                    // Get printer settings
                    const printerIp = $('#printerIp').val();
                    const printerPort = parseInt($('#printerPort').val()) || 9100;

                    if (!printerIp) {
                        $('#loadingOverlay').removeClass('flex').addClass('hidden');
                        Swal.fire({
                            title: 'Missing Information',
                            text: 'Printer IP address is required for thermal printing',
                            icon: 'warning'
                        });
                        return;
                    }

                    // Send to thermal printer
                    const response = await fetch('./api/thermal_print.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'thermal_print',
                            printer_ip: printerIp,
                            printer_port: printerPort,
                            settings: settings,
                            items: items
                        })
                    });

                    $('#loadingOverlay').removeClass('flex').addClass('hidden');

                    if (!response.ok) {
                        throw new Error('Failed to send to thermal printer');
                    }

                    const result = await response.json();

                    if (!result.success) {
                        throw new Error(result.message || 'Failed to send to thermal printer');
                    }

                    // Show success message
                    Swal.fire({
                        title: 'Success',
                        text: 'Labels sent to thermal printer',
                        icon: 'success'
                    });

                    return;
                }

                // For regular PDF printing
                const response = await fetch('./api/barcode.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'print',
                        settings: settings,
                        items: items
                    })
                });

                $('#loadingOverlay').removeClass('flex').addClass('hidden');

                if (!response.ok) {
                    throw new Error('Failed to generate barcodes');
                }

                const blob = await response.blob();
                const url = URL.createObjectURL(blob);

                // Open in new window for printing
                const printWindow = window.open(url);

                if (printWindow) {
                    printWindow.onload = function() {
                        printWindow.print();
                        setTimeout(() => {
                            URL.revokeObjectURL(url);
                        }, 2000);
                    };
                } else {
                    // If popup blocked, show direct link
                    Swal.fire({
                        title: 'Popup Blocked',
                        text: 'Unable to open print window. Please allow popups or click below to open PDF.',
                        icon: 'warning',
                        confirmButtonText: 'Open PDF',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(url, '_blank');
                        }
                    });
                }
            } catch (error) {
                $('#loadingOverlay').removeClass('flex').addClass('hidden');
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'An error occurred while generating barcodes',
                    icon: 'error'
                });
            }
        }

        $(document).ready(function() {
            // Add loading overlay
            const loadingOverlay = $('<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center"><div class="bg-white p-4 rounded shadow-lg"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto"></div><div class="mt-2 text-center">Loading...</div></div></div>');
            $('body').append(loadingOverlay);

            // Toggle shop name field visibility
            $('#showShopName').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#shopNameField').removeClass('hidden');
                    $('#shopName').removeClass('hidden');
                } else {
                    $('#shopNameField').addClass('hidden');
                    $('#shopName').addClass('hidden');
                }
            });

            // Trigger shop name field visibility on initial load
            $('#showShopName').trigger('change');

            // Show/hide thermal printer settings based on print format
            $('#printerType').on('change', function() {
                const printerType = $(this).val();

                // Hide all printer settings first
                $('.network-printer-settings, .windows-printer-settings').hide();

                // Show the relevant settings based on selection
                if (printerType === 'network') {
                    $('.network-printer-settings').show();
                } else if (printerType === 'windows') {
                    $('.windows-printer-settings').show();
                }
            });

            // Trigger print format change on initial load
            $('#printerType').trigger('change');

            // Function to show loading overlay
            function showLoading() {
                $('#loadingOverlay').removeClass('hidden').addClass('flex');
            }

            // Function to hide loading overlay
            function hideLoading() {
                $('#loadingOverlay').removeClass('flex').addClass('hidden');
            }

            // Initialize Select2 for product search with proper width and display
            setTimeout(function() {
                $('#productSearch').select2({
                    placeholder: 'Search products...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('body'),
                    ajax: {
                        url: '../../inc/fetch_product.php',
                        dataType: 'json',
                        delay: 250,
                        beforeSend: function() {
                            showLoading();
                        },
                        data: function(params) {
                            return {
                                search: params.term
                            };
                        },
                        processResults: function(data) {
                            hideLoading();
                            if (data && data.products && Array.isArray(data.products)) {
                                return {
                                    results: data.products.map(product => ({
                                        id: product.product_id,
                                        text: product.product_name,
                                        sku: product.sku,
                                        barcode: product.barcode,
                                        price: product.rate,
                                        category: product.category_name,
                                        product: product
                                    }))
                                };
                            } else {
                                console.error('Unexpected data format:', data);
                                return {
                                    results: []
                                };
                            }
                        },
                        error: function() {
                            hideLoading();
                        },
                        cache: true
                    },
                    minimumInputLength: 2
                });
            }, 100);

            // Initialize content settings
            initializeContentSettings();

            // Handle product selection
            $('#productSearch').on('select2:select', function(e) {
                const product = e.params.data;
                fetchBatches(product);
            });

            // Handle add product button
            $('#addProductBtn').on('click', function() {
                const selectedProduct = $('#productSearch').select2('data')[0];
                if (selectedProduct) {
                    fetchBatches(selectedProduct);
                } else {
                    Swal.fire({
                        title: 'No Product Selected',
                        text: 'Please select a product first',
                        icon: 'warning'
                    });
                }
            });

            // Function to initialize content settings
            function initializeContentSettings() {
                // Remove disabled attribute from all checkboxes
                $('#showShopName, #showProductName, #showPrice, #showUnit, #showCategory, #showPromoPrice').prop('disabled', false);

                // Add change event listeners to all content setting checkboxes and inputs
                $('#showShopName, #showProductName, #showPrice, #showUnit, #showCategory, #showPromoPrice').on('change', function() {
                    updatePreview();
                });

                // Add input event for shop name text field
                $('#shopName').on('input', function() {
                    updatePreview();
                });

                // Add change event listeners to paper settings
                $('#paperSize, #margin, #gapBetween, #fontSize, #barcodeHeight').on('change input', function() {
                    updatePreview();
                });
            }

            // Function to fetch batches for a product
            function fetchBatches(product) {
                if (!product || !product.id) return;

                showLoading();
                $.ajax({
                    url: '../../inc/fetch_product.php',
                    method: 'GET',
                    data: {
                        search: product.text // Use product name for exact match
                    },
                    success: function(response) {
                        hideLoading();
                        if (response && response.batches && Array.isArray(response.batches)) {
                            displayBatchModal(product, response.batches);
                        } else {
                            Swal.fire({
                                title: 'No Batches Found',
                                text: 'No batches available for this product',
                                icon: 'warning'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('Error fetching batches:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to fetch batch information',
                            icon: 'error'
                        });
                    }
                });
            }

            // Function to display batch selection modal
            function displayBatchModal(product, batches) {
                if (!batches || batches.length === 0) {
                    Swal.fire({
                        title: 'No Batches',
                        text: 'No batches found for this product',
                        icon: 'warning'
                    });
                    return;
                }

                // Get barcode for display
                const barcode = product.barcode || product.sku || '';
                const barcodeInfo = barcode ? `<div class="mb-3 text-center">
                        <span class="font-semibold">Barcode:</span> ${barcode}
                    </div>` : '';

                // Prepare HTML for batch selection
                let batchesHtml = `
                        ${barcodeInfo}
                    <table class="w-full mt-4 mb-2">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 border">Batch #</th>
                                <th class="p-2 border">Price</th>
                                <th class="p-2 border">Quantity</th>
                                <th class="p-2 border">Expiry</th>
                                <th class="p-2 border">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                batches.forEach(batch => {
                    const expiryDate = batch.expiry_date ? new Date(batch.expiry_date).toLocaleDateString() : 'N/A';
                    const hasDiscountPrice = batch.discount_price && parseFloat(batch.discount_price) > 0 && parseFloat(batch.discount_price) < parseFloat(batch.selling_price);

                    batchesHtml += `
                        <tr>
                            <td class="p-2 border">${batch.batch_number}</td>
                            <td class="p-2 border">Rs. ${parseFloat(batch.selling_price).toFixed(2)}
                                ${hasDiscountPrice ? `<br><span class="text-green-600">Promo: Rs. ${parseFloat(batch.discount_price).toFixed(2)}</span>` : ''}
                            </td>
                            <td class="p-2 border">${batch.batch_quantity}</td>
                            <td class="p-2 border">${expiryDate}</td>
                            <td class="p-2 border">
                                <button
                                    class="select-batch bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded"
                                    data-batch='${JSON.stringify(batch)}'
                                    data-product='${JSON.stringify(product)}'>
                                    Select
                                </button>
                            </td>
                        </tr>
                    `;
                });

                batchesHtml += `</tbody></table>`;

                Swal.fire({
                    title: `Select Batch for ${product.text}`,
                    html: batchesHtml,
                    showCancelButton: true,
                    showConfirmButton: false,
                    width: '800px'
                });
            }

            // Event delegation for batch selection
            $(document).on('click', '.select-batch', function() {
                const batch = JSON.parse($(this).attr('data-batch'));
                const product = JSON.parse($(this).attr('data-product'));

                // Add the selected product and batch to the items table
                const tableBody = $('#itemsTable tbody');
                const quantity = 1; // Default quantity

                // Check if this batch already exists in the table
                const existingRow = tableBody.find(`tr[data-batch-id="${batch.batch_id}"]`);

                if (existingRow.length) {
                    // Update existing row quantity
                    const qtyInput = existingRow.find('.item-qty');
                    const currentQty = parseInt(qtyInput.val()) || 0;
                    qtyInput.val(currentQty + 1);
                    qtyInput.trigger('change'); // Trigger any change handlers
                } else {
                    // Store additional needed data as data attributes
                    const hasDiscountPrice = batch.discount_price && parseFloat(batch.discount_price) > 0 && parseFloat(batch.discount_price) < parseFloat(batch.selling_price);
                    const unit = product.unit || 'pcs'; // Default to 'pcs' if no unit specified
                    const barcode = product.barcode || product.sku || ''; // Use barcode or SKU if available

                    // Add new row
                    const newRow = `
                        <tr 
                            data-batch-id="${batch.batch_id}" 
                            data-product-id="${batch.product_id}"
                            data-product-name="${product.text}"
                                data-barcode="${barcode}"
                            data-category="${product.category || ''}"
                            data-unit="${unit}"
                            data-price="${parseFloat(batch.selling_price).toFixed(2)}"
                            data-discount-price="${hasDiscountPrice ? parseFloat(batch.discount_price).toFixed(2) : ''}"
                        >
                            <td>${product.text}</td>
                            <td>${batch.batch_number}</td>
                            <td>Rs. ${parseFloat(batch.selling_price).toFixed(2)}
                                ${hasDiscountPrice ? `<br><span class="text-green-600">Promo: Rs. ${parseFloat(batch.discount_price).toFixed(2)}</span>` : ''}
                            </td>
                            <td>
                                <input type="number" class="form-input w-24 item-qty" value="${quantity}" min="1">
                            </td>
                            <td>
                                <button class="text-red-600 hover:text-red-800 delete-item">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                    tableBody.append(newRow);
                }

                // Close the modal
                Swal.close();

                // Clear the select2 search
                $('#productSearch').val(null).trigger('change');

                // Update preview and item count
                updateItemCount();
                updatePreview();
            });

            // Function to update item count
            function updateItemCount() {
                // You can implement any counting or summarizing logic here
                // Example: counting the number of rows in the table
                const itemCount = $('#itemsTable tbody tr').length;
                console.log('Items in table:', itemCount);
            }

            // Handle delete items
            $(document).on('click', '.delete-item', function() {
                $(this).closest('tr').remove();
                updateItemCount();
                updatePreview(); // Update preview when an item is deleted
            });

            // Handle quantity changes
            $(document).on('change', '.item-qty', function() {
                updateItemCount();
                updatePreview(); // Update preview when quantity changes
            });

            // Function to update the preview
            function updatePreview() {
                // Get all items from the table
                const items = [];
                $('#itemsTable tbody tr').each(function() {
                    const $row = $(this);
                    const qty = parseInt($row.find('.item-qty').val()) || 1;

                    // For each quantity, add an item entry
                    for (let i = 0; i < qty; i++) {
                        items.push({
                            product_id: $row.data('product-id'),
                            product_name: $row.data('product-name'),
                            barcode: $row.data('barcode') || '',
                            price: $row.data('price'),
                            discount_price: $row.data('discount-price'),
                            category: $row.data('category'),
                            unit: $row.data('unit')
                        });
                    }
                });

                // If no items, don't update preview
                if (items.length === 0) {
                    return;
                }

                // Get settings
                const settings = getSettings();

                // Generate HTML preview
                const previewHtml = generatePreviewHTML(items, settings);

                // Remove any existing preview
                $('#livePreview').remove();

                // Create a new container for the preview
                const previewContainer = $('<div id="livePreview" class="mt-4 p-4 border rounded bg-white"></div>');
                previewContainer.html(previewHtml);

                // Add the new preview after the Template Actions card
                $('.card:last').after(previewContainer);
            }

            // Add event handler for preview button
            $('#previewBtn').on('click', function() {
                showDetailedPreview();
            });

            // Function to show detailed preview in modal
            function showDetailedPreview() {
                // Similar to updatePreview but shows in the modal
                const items = [];
                $('#itemsTable tbody tr').each(function() {
                    const $row = $(this);
                    const qty = parseInt($row.find('.item-qty').val()) || 1;

                    for (let i = 0; i < qty; i++) {
                        items.push({
                            product_id: $row.data('product-id'),
                            product_name: $row.data('product-name'),
                            barcode: $row.data('barcode'),
                            price: $row.data('price'),
                            discount_price: $row.data('discount-price'),
                            category: $row.data('category'),
                            unit: $row.data('unit')
                        });
                    }
                });

                if (items.length === 0) {
                    Swal.fire({
                        title: 'No Items',
                        text: 'Please add items to preview',
                        icon: 'warning'
                    });
                    return;
                }

                const settings = getSettings();

                // Use the modal for detailed preview
                const previewHtml = generatePreviewHTML(items, settings);
                $('#previewContent').html(previewHtml);
                $('#previewModal').removeClass('hidden');
            }

            // Initialize the Print button
            $('#printBtn').on('click', function() {
                // Similar to preview but send to print endpoint
                printBarcodes();
            });

            // Load template event handler
            $('#loadTemplate').on('change', function() {
                const templateId = $(this).val();
                if (!templateId) return;

                // Load template data from server
                $.ajax({
                    url: './api/get_template.php',
                    method: 'GET',
                    data: {
                        id: templateId
                    },
                    success: function(response) {
                        if (response && response.success) {
                            loadTemplateData(response.template);
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to load template',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading template:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to load template',
                            icon: 'error'
                        });
                    }
                });
            });

            // Save template event handler
            $('#saveTemplateBtn').on('click', function() {
                const templateName = $('#templateName').val().trim();
                if (!templateName) {
                    Swal.fire({
                        title: 'Missing Name',
                        text: 'Please enter a template name',
                        icon: 'warning'
                    });
                    return;
                }

                // Get all items from the table
                const items = [];
                $('#itemsTable tbody tr').each(function() {
                    const $row = $(this);
                    items.push({
                        product_id: $row.data('product-id'),
                        product_name: $row.data('product-name'),
                        barcode: $row.data('barcode'),
                        price: $row.data('price'),
                        discount_price: $row.data('discount-price'),
                        category: $row.data('category'),
                        unit: $row.data('unit') || 'pcs',
                        batch_id: $row.data('batch-id'),
                        quantity: parseInt($row.find('.item-qty').val()) || 1
                    });
                });

                if (items.length === 0) {
                    Swal.fire({
                        title: 'No Items',
                        text: 'Please add items to save as a template',
                        icon: 'warning'
                    });
                    return;
                }

                // Get settings
                const settings = getSettings();

                // Save template data to server
                $.ajax({
                    url: './api/save_template.php',
                    method: 'POST',
                    data: JSON.stringify({
                        name: templateName,
                        settings: settings,
                        items: items
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        if (response && response.success) {
                            Swal.fire({
                                title: 'Success',
                                text: 'Template saved successfully',
                                icon: 'success'
                            });

                            // Add to template dropdown if it's not already there
                            if (response.template_id && $('#loadTemplate option[value="' + response.template_id + '"]').length === 0) {
                                $('#loadTemplate').append(`<option value="${response.template_id}">${templateName}</option>`);
                            }

                            // Clear template name field
                            $('#templateName').val('');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'Failed to save template',
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving template:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to save template',
                            icon: 'error'
                        });
                    }
                });
            });

            // Function to load template data
            function loadTemplateData(template) {
                // Clear existing items
                $('#itemsTable tbody').empty();

                // Load settings
                if (template.settings) {
                    const settings = template.settings;

                    // Set paper settings
                    $('#paperSize').val(settings.paper_size || '36').trigger('change');
                    $('#margin').val(settings.margin || 1);
                    $('#gapBetween').val(settings.gap_between || 2.5);
                    $('#fontSize').val(settings.font_size || 9);
                    $('#barcodeHeight').val(settings.barcode_height || 15);

                    // Set content settings
                    $('#showShopName').prop('checked', settings.show_shop_name !== false).trigger('change');
                    $('#shopName').val(settings.shop_name || '<?php echo $ERP_COMPANY_NAME; ?>');
                    $('#showProductName').prop('checked', settings.show_product_name !== false).trigger('change');
                    $('#showPrice').prop('checked', settings.show_price !== false).trigger('change');
                    $('#showUnit').prop('checked', settings.show_unit === true).trigger('change');
                    $('#showCategory').prop('checked', settings.show_category === true).trigger('change');
                    $('#showPromoPrice').prop('checked', settings.show_promo_price === true).trigger('change');
                }

                // Load items
                if (template.items && Array.isArray(template.items)) {
                    template.items.forEach(item => {
                        const hasDiscountPrice = item.discount_price && parseFloat(item.discount_price) > 0 && parseFloat(item.discount_price) < parseFloat(item.price);

                        // Add row for each item
                        const newRow = `
                            <tr 
                                data-batch-id="${item.batch_id || ''}" 
                                data-product-id="${item.product_id}"
                                data-product-name="${item.product_name}"
                                data-barcode="${item.barcode || ''}"
                                data-category="${item.category || ''}"
                                data-unit="${item.unit || 'pcs'}"
                                data-price="${parseFloat(item.price).toFixed(2)}"
                                data-discount-price="${hasDiscountPrice ? parseFloat(item.discount_price).toFixed(2) : ''}"
                            >
                                <td>${item.product_name}</td>
                                <td>${item.batch_name || 'N/A'}</td>
                                <td>Rs. ${parseFloat(item.price).toFixed(2)}
                                    ${hasDiscountPrice ? `<br><span class="text-green-600">Promo: Rs. ${parseFloat(item.discount_price).toFixed(2)}</span>` : ''}
                                </td>
                                <td>
                                    <input type="number" class="form-input w-24 item-qty" value="${item.quantity || 1}" min="1">
                                </td>
                                <td>
                                    <button class="text-red-600 hover:text-red-800 delete-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        $('#itemsTable tbody').append(newRow);
                    });

                    // Update preview
                    updatePreview();
                }
            }

            // Show/hide shop name input based on checkbox
            $('#showShopName').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.shop-name-container').show();
                } else {
                    $('.shop-name-container').hide();
                }
            });

            // Focus product search when addProductBtn is clicked
            $(document).on('keydown', function(event) {
                if (event.key === "Insert") {
                    // After adding product, focus on the search field for quick entry of the next product
                    setTimeout(function() {
                        $('#productSearch').select2('open');
                    }, 1);
                }
            });
        });
    </script>
    </div>
</body>

</html>