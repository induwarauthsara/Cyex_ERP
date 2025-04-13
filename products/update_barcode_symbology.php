<?php require_once '../inc/config.php'; ?>
<?php require '../inc/header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Barcode Symbology - Advanced POS System</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
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
            padding: 20px;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #4a90e2;
            border-color: #4a90e2;
        }

        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h2 class="mb-4 text-center">Update Barcode Symbology</h2>
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <p><strong>What does this do?</strong> This tool will automatically detect and update the barcode symbology for all products based on their barcodes.</p>
                        <p>The system will analyze each barcode and determine if it should use:</p>
                        <ul>
                            <li>EAN-13 (13-digit numeric barcodes)</li>
                            <li>EAN-8 (8-digit numeric barcodes)</li>
                            <li>UPC-A (12-digit numeric barcodes)</li>
                            <li>CODE39 (alphanumeric barcodes with certain special characters)</li>
                            <li>CODE128 (default fallback for any other format)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mx-auto text-center">
                    <button id="updateButton" class="btn btn-primary btn-lg">
                        <i class="fas fa-sync-alt"></i> Update All Barcode Symbologies
                    </button>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div id="resultContainer" class="card p-3 d-none">
                        <h4>Results</h4>
                        <div id="resultContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loader -->
    <div id="loader" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-50" style="z-index: 9999; display: none;">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- JavaScript (at the end of document for faster loading) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Handle update button click
            $('#updateButton').on('click', function() {
                // Show confirmation dialog
                Swal.fire({
                    title: 'Update Barcode Symbologies?',
                    text: 'This will update all product barcode symbologies based on their barcodes. Continue?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, update them',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateBarcodeSymbologies();
                    }
                });
            });

            // Function to update barcode symbologies
            function updateBarcodeSymbologies() {
                // Show loader
                $('#loader').show();
                $('#updateButton').prop('disabled', true);

                // Send AJAX request
                $.ajax({
                    url: 'API/update_barcode_symbology.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        // Hide loader
                        $('#loader').hide();
                        $('#updateButton').prop('disabled', false);

                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success'
                            });

                            // Display results
                            const details = response.details;
                            let resultHTML = `
                                <div class="alert alert-success">
                                    <p><strong>Operation Complete!</strong></p>
                                    <ul>
                                        <li><strong>Updated:</strong> ${details.updated} products</li>
                                        <li><strong>Skipped:</strong> ${details.skipped} products (already had correct symbology)</li>
                                        <li><strong>Failed:</strong> ${details.failed} products</li>
                                    </ul>
                                </div>
                            `;

                            // Add errors if any
                            if (details.errors && details.errors.length > 0) {
                                resultHTML += `
                                    <div class="alert alert-warning mt-3">
                                        <p><strong>Errors:</strong></p>
                                        <ul>
                                            ${details.errors.map(error => `<li>${error}</li>`).join('')}
                                        </ul>
                                    </div>
                                `;
                            }

                            $('#resultContent').html(resultHTML);
                            $('#resultContainer').removeClass('d-none');
                        } else {
                            // Show error message
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        // Hide loader
                        $('#loader').hide();
                        $('#updateButton').prop('disabled', false);

                        // Show error message
                        Swal.fire({
                            title: 'Error',
                            text: 'An error occurred while connecting to the server.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    </script>
</body>

</html> 