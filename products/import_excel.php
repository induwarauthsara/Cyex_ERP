<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Products (CSV) - Advanced POS System</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .card-header {
            background: linear-gradient(to right, #4a90e2, #5a9de2);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 15px 20px;
        }

        .step-container {
            display: none;
        }

        .step-container.active {
            display: block;
        }

        .preview-table {
            font-size: 0.9rem;
        }
        
        .preview-table th {
            background-color: #f8f9fa;
        }

        .variant-row {
            background-color: #fcfcfc;
        }

        .product-header-row {
            background-color: #e3f2fd;
            font-weight: bold;
        }
        
        .badge-generated {
            background-color: #ffc107;
            color: #000;
        }

        .variant-indent {
            padding-left: 20px;
            border-left: 3px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        
        <!-- STEP 1: Upload -->
        <div id="step1" class="step-container active">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-file-csv me-2"></i> Import Products from CSV</h4>
                </div>
                <div class="card-body p-5 text-center">
                    
                    <div class="mb-4">
                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                        <h5>Upload your CSV File</h5>
                        <p class="text-muted">Ensure your file matches the recommended structure.</p>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <form id="uploadForm">
                                <div class="input-group mb-3">
                                    <input type="file" class="form-control" id="csvFile" name="csvFile" accept=".csv" required>
                                    <button class="btn btn-primary" type="submit">
                                        Preview Data <i class="fas fa-arrow-right list-inline-item"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mt-4 text-start">
                        <h6><i class="fas fa-info-circle"></i> CSV Tips:</h6>
                        <ul class="text-muted small">
                            <li>Required Columns: <strong>Product Name, Cost, Selling Price</strong></li>
                            <li><strong>Product Name</strong> is used to group variants. (Same Name = Same Product)</li>
                            <li><strong>Discount Price</strong> is optional. Leave empty if none.</li>
                            <li>If <strong>Barcode</strong> is empty, the system will auto-generate one.</li>
                            <li>If <strong>SKU</strong> is empty, it will default to 'SKU-[Barcode]'.</li>
                            <li>Enter each variant on a new row with the <strong>same Product Name</strong>.</li>
                        </ul>
                        <div class="mt-2 text-center">
                             <a href="sample_import.csv" download class="btn btn-outline-secondary btn-sm"><i class="fas fa-download"></i> Download Sample CSV</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- STEP 2: Preview -->
        <div id="step2" class="step-container">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Preview Import Data</h4>
                    <button class="btn btn-light btn-sm" onclick="location.reload()">Cancel</button>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-check-circle"></i> File processed. Please review the structure below before saving.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered preview-table">
                            <thead>
                                <tr>
                                    <th width="30%">Product / Variant Info</th>
                                    <th>Ref (Barcode/SKU)</th>
                                    <th>Category / Brand</th>
                                    <th class="text-end">Cost</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-center">Stock</th>
                                </tr>
                            </thead>
                            <tbody id="previewBody">
                                <!-- Dynamic Content -->
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3 gap-2">
                        <button class="btn btn-secondary" onclick="location.reload()">Back</button>
                        <button class="btn btn-success px-4" id="confirmImportBtn">
                            <i class="fas fa-save me-2"></i> Confirm & Import
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let parsedData = [];

        $(document).ready(function() {
            
            // Handle File Upload
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();
                
                let formData = new FormData(this);
                
                Swal.fire({
                    title: 'Processing...',
                    text: 'Reading CSV file',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.ajax({
                    url: 'API/preview_import.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        if(response.success) {
                            parsedData = response.products;
                            renderPreview(parsedData);
                            $('#step1').removeClass('active');
                            $('#step2').addClass('active');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Failed to read file', 'error');
                    }
                });
            });

            // Handle Confirm Import
            $('#confirmImportBtn').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to import ${parsedData.length} products.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Import!',
                    confirmButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performImport();
                    }
                });
            });

        });

        function renderPreview(products) {
            let html = '';
            
            if(products.length === 0) {
                html = '<tr><td colspan="7" class="text-center text-danger">No valid data found in CSV</td></tr>';
            }

            products.forEach((prod, index) => {
                // Product Header Row
                let badge = prod.is_barcode_generated ? '<span class="badge badge-generated ms-1">Auto-Gen</span>' : '';
                
                html += `
                    <tr class="product-header-row">
                        <td>
                            <div class="fw-bold text-primary">${prod.name}</div>
                        </td>
                        <td>
                            <div><small class="text-muted">Barcode:</small> ${prod.barcode} ${badge}</div>
                            <div><small class="text-muted">SKU:</small> ${prod.sku}</div>
                        </td>
                        <td>
                            ${prod.category ? '<span class="badge bg-secondary me-1">'+prod.category+'</span>' : '-'}
                            ${prod.brand ? '<span class="badge bg-light text-dark border">'+prod.brand+'</span>' : '-'}
                        </td>
                        <td colspan="4" class="text-center text-muted fst-italic">
                            <small>${prod.variants.length} Variant(s)</small>
                        </td>
                    </tr>
                `;

                // Variant Rows
                prod.variants.forEach(variant => {
                    let discountDisplay = (variant.discount_price && variant.discount_price > 0) 
                                          ? variant.discount_price.toFixed(2) 
                                          : '-';

                    html += `
                        <tr class="variant-row">
                            <td>
                                <div class="variant-indent">
                                    <i class="fas fa-long-arrow-alt-right text-muted me-2"></i>
                                    ${variant.is_default ? '<span class="text-muted fst-italic">Default Batch</span>' : '<strong>'+variant.name+'</strong>'}
                                    <div class="small text-muted ps-4">${variant.note}</div>
                                </div>
                            </td>
                            <td></td>
                            <td></td>
                            <td class="text-end">${variant.cost.toFixed(2)}</td>
                            <td class="text-end fw-bold">${variant.price.toFixed(2)}</td>
                            <td class="text-end text-success">${discountDisplay}</td>
                            <td class="text-center">
                                <span class="badge ${variant.stock > 0 ? 'bg-success' : 'bg-danger'}">
                                    ${variant.stock}
                                </span>
                            </td>
                        </tr>
                    `;
                });
            });

            $('#previewBody').html(html);
        }

        async function performImport() {
            const BATCH_SIZE = 50; // Process 50 products per request
            const totalProducts = parsedData.length;
            let processedCount = 0;
            let successCount = 0;
            let failCount = 0;
            let errors = [];

            // Initialize Progress Modal
            Swal.fire({
                title: 'Importing...',
                html: `
                    <div class="mb-2">Processing <span id="processed-count">0</span> / ${totalProducts} products</div>
                    <div class="progress" style="height: 20px;">
                        <div id="import-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="import-status-text" class="text-muted small mt-2">Starting...</div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false
            });

            // Process Batch Function
            const runBatch = async (startIndex) => {
                const batch = parsedData.slice(startIndex, startIndex + BATCH_SIZE);
                
                if (batch.length === 0) {
                    // All Done
                    finishImport(successCount, failCount, errors);
                    return;
                }

                try {
                    // Send Batch
                    const response = await $.ajax({
                        url: 'API/process_import.php',
                        type: 'POST',
                        data: JSON.stringify({ products: batch }),
                        contentType: 'application/json'
                    });

                    if (response.success) {
                        successCount += response.summary.added;
                        failCount += response.summary.failed;
                        if(response.summary.errors){
                            errors = [...errors, ...response.summary.errors];
                        }
                    } else {
                        // Entire batch failed logic fallback (shouldn't happen with our API check)
                         failCount += batch.length;
                         errors.push("Batch failed unknown error");
                    }

                } catch (error) {
                    console.error(error);
                    failCount += batch.length;
                    errors.push(`Network/Server Error on batch starting at ${startIndex}: ${error.statusText || 'Unknown'}`);
                }

                // Update Progress
                processedCount += batch.length;
                const percent = Math.min(100, Math.round((processedCount / totalProducts) * 100));
                
                $('#import-progress-bar').css('width', percent + '%').text(percent + '%');
                $('#processed-count').text(Math.min(processedCount, totalProducts));
                $('#import-status-text').text(`Processed batch... (${processedCount}/${totalProducts})`);

                // Next Batch (recursive)
                // Small delay to let UI breathe
                setTimeout(() => runBatch(startIndex + BATCH_SIZE), 100); 
            };

            // Start First Batch
            runBatch(0);
        }

        function finishImport(success, failed, errorList) {
            let msg = `Successfully added ${success} products.`;
            let icon = 'success';
            
            if (failed > 0) {
                msg += `<br><span class="text-danger fw-bold">Failed: ${failed}</span>`;
                icon = 'warning';
                
                if(errorList.length > 0) {
                    // Format errors as a clean list
                    const errorHtml = errorList.map(err => `<li>${err}</li>`).join('');
                    
                    msg += `
                        <div class="mt-3 text-start">
                            <label class="fw-bold mb-1">Error Details:</label>
                            <div class="border rounded p-2 bg-light text-danger small" style="max-height: 300px; overflow-y: auto; text-align: left;">
                                <ul class="mb-0 ps-3">
                                    ${errorHtml}
                                </ul>
                            </div>
                        </div>
                    `;
                }
            } else if (success === 0 && failed > 0) {
                icon = 'error';
            }

            Swal.fire({
                title: failed > 0 ? 'Import Completed with Issues' : 'Import Complete!',
                html: msg,
                icon: icon,
                width: '600px', // Wider modal to see errors better
                confirmButtonText: 'OK, Go to List'
            }).then(() => {
                window.location.href = 'index.php';
            });
        }
    </script>
</body>
</html>