<?php
include 'nav.php';

// Fetch current settings from database
$current_settings = [];
try {
    $settings_query = "SELECT setting_name, setting_value, setting_description FROM settings WHERE setting_name IN ('sell_Insufficient_stock_item', 'sell_Inactive_batch_products', 'invoice_print_type')";
    $settings_result = mysqli_query($con, $settings_query);

    if ($settings_result) {
        while ($setting = mysqli_fetch_assoc($settings_result)) {
            $current_settings[$setting['setting_name']] = [
                'value' => $setting['setting_value'],
                'description' => $setting['setting_description']
            ];
        }
        mysqli_free_result($settings_result);
    }
} catch (Exception $e) {
    error_log("Error fetching settings: " . $e->getMessage());
}

// Set default values if not found in database
if (!isset($current_settings['sell_Insufficient_stock_item'])) {
    $current_settings['sell_Insufficient_stock_item'] = ['value' => '1', 'description' => 'Allow selling out-of-stock items'];
}
if (!isset($current_settings['sell_Inactive_batch_products'])) {
    $current_settings['sell_Inactive_batch_products'] = ['value' => '1', 'description' => 'Allow selling from inactive batches'];
}
if (!isset($current_settings['invoice_print_type'])) {
    $current_settings['invoice_print_type'] = ['value' => 'standard', 'description' => 'Default invoice print type'];
}
?>

<!-- Custom CSS for Settings Page -->
<style>
    /* General Settings Styles */
    .settings-container {
        max-width: 1200px;
        margin: 10px auto;
        padding: 0 10px;
    }

    .settings-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem;
        border-radius: 12px;
        margin-bottom: 1rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .settings-header h1 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 400;
    }

    .settings-header p {
        margin: 0.3rem 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    /* Section Styles */
    .settings-section {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        border: 1px solid #e9ecef;
    }

    .section-title {
        color: #2c3e50;
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.4rem;
        border-bottom: 2px solid #3498db;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .section-description {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 1.25rem;
        line-height: 1.4;
    }

    /* Setting Item Styles */
    .setting-item {
        background: #f8f9fa;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .setting-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border-color: #3498db;
    }

    .setting-item.active {
        border-color: #28a745;
        background: #f8fff9;
    }

    .setting-item.inactive {
        border-color: #dc3545;
        background: #fff8f8;
    }

    .setting-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .setting-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .setting-description {
        color: #6c757d;
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 0.75rem;
    }

    /* Toggle Switch Styles */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 26px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    input:checked+.toggle-slider {
        background-color: #28a745;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(24px);
    }

    /* Status Badge */
    .setting-status {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.75rem;
    }

    .status-badge {
        padding: 0.3rem 0.8rem;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .status-badge.enabled {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .status-badge.disabled {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* Save Button */
    .save-section {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        text-align: center;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.06);
        margin-top: 1rem;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
    }

    .btn-save,
    .btn-reset {
        border: none;
        padding: 0.8rem 2.5rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 25px;
        color: white;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 180px;
    }

    .btn-save {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        box-shadow: 0 3px 12px rgba(40, 167, 69, 0.3);
    }

    .btn-reset {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        box-shadow: 0 3px 12px rgba(108, 117, 125, 0.3);
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-reset:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .btn-save:active,
    .btn-reset:active {
        transform: translateY(0);
    }

    .btn-save:disabled,
    .btn-reset:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .settings-container {
            padding: 0 8px;
            margin: 8px auto;
        }

        .settings-header {
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .settings-header h1 {
            font-size: 1.5rem;
        }

        .settings-section {
            padding: 1rem;
            margin-bottom: 0.75rem;
        }

        .setting-item {
            padding: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .setting-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .setting-title {
            font-size: 1rem;
        }

        .btn-save {
            padding: 0.8rem 1.5rem;
            font-size: 0.95rem;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .btn-reset {
            padding: 0.8rem 1.5rem;
            font-size: 0.95rem;
            width: 100%;
        }

        .button-group {
            flex-direction: column;
            gap: 0.5rem;
        }

        .save-section {
            padding: 1rem;
            margin-top: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .settings-header h1 {
            font-size: 1.3rem;
        }

        .section-title {
            font-size: 1.1rem;
            flex-direction: column;
            align-items: flex-start;
            text-align: center;
        }

        .setting-item {
            padding: 0.75rem;
        }

        .toggle-switch {
            width: 45px;
            height: 24px;
        }

        .toggle-slider:before {
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(21px);
        }
    }

    .setting-item.updating {
        transform: scale(1.02);
        border-color: #007bff;
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
    }

    .btn-save.btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    /* Additional responsive fixes */
    @media (max-width: 576px) {
        .settings-header h1 {
            font-size: 1.5rem;
        }

        .section-title {
            font-size: 1.2rem;
            text-align: center;
        }

        .toggle-switch {
            width: 50px;
            height: 25px;
        }

        .toggle-switch:before {
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(25px);
        }
    }

    /* Improved animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .settings-section {
        animation: fadeInUp 0.6s ease-out;
    }

    .setting-item {
        animation: fadeInUp 0.6s ease-out;
        animation-delay: 0.1s;
        animation-fill-mode: both;
    }

    /* Enhanced focus states for accessibility */
    .toggle-switch input:focus+.toggle-slider {
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .btn-save:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.25);
    }

    /* Loading state improvements */
    .btn-save:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Toast notification positioning for mobile */
    @media (max-width: 768px) {
        .swal2-container.swal2-top-end {
            top: 80px !important;
            right: 10px !important;
        }
    }
</style>

<div class="settings-container">
    <!-- Header Section -->
    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> System Settings</h1>
        <p>Configure system behavior and preferences</p>
    </div>

    <!-- Billing Configuration Section -->
    <div class="settings-section">
        <h2 class="section-title">
            <i class="fas fa-file-invoice-dollar"></i>
            Billing Configuration
        </h2>
        <p class="section-description">
            Control billing system inventory and stock management behavior
        </p>

        <form id="billingSettingsForm">
            <div class="row">
                <!-- Out-of-Stock Sales Setting -->
                <div class="col-lg-6 col-md-12">
                    <div class="setting-item <?php echo ($current_settings['sell_Insufficient_stock_item']['value'] == '1') ? 'active' : 'inactive'; ?>" id="setting_sell_Insufficient_stock_item">
                        <div class="setting-header">
                            <h3 class="setting-title">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                Out-of-Stock Sales
                            </h3>
                            <label class="toggle-switch">
                                <input type="checkbox" id="sell_Insufficient_stock_item"
                                    name="sell_Insufficient_stock_item" value="1"
                                    <?php echo ($current_settings['sell_Insufficient_stock_item']['value'] == '1') ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <p class="setting-description">
                            Allow selling products when stock is insufficient. Stock can go negative for urgent sales.
                        </p>
                        <div class="setting-status">
                            <span class="status-badge <?php echo ($current_settings['sell_Insufficient_stock_item']['value'] == '1') ? 'enabled' : 'disabled'; ?>"
                                id="status_sell_Insufficient_stock_item">
                                <i class="fas <?php echo ($current_settings['sell_Insufficient_stock_item']['value'] == '1') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                <?php echo ($current_settings['sell_Insufficient_stock_item']['value'] == '1') ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Inactive Batch Sales Setting -->
                <div class="col-lg-6 col-md-12">
                    <div class="setting-item <?php echo ($current_settings['sell_Inactive_batch_products']['value'] == '1') ? 'active' : 'inactive'; ?>" id="setting_sell_Inactive_batch_products">
                        <div class="setting-header">
                            <h3 class="setting-title">
                                <i class="fas fa-ban text-info"></i>
                                Inactive Batch Sales
                            </h3>
                            <label class="toggle-switch">
                                <input type="checkbox" id="sell_Inactive_batch_products"
                                    name="sell_Inactive_batch_products" value="1"
                                    <?php echo ($current_settings['sell_Inactive_batch_products']['value'] == '1') ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <p class="setting-description">
                            Allow selling from inactive or expired batches. Useful for clearing old inventory.
                        </p>
                        <div class="setting-status">
                            <span class="status-badge <?php echo ($current_settings['sell_Inactive_batch_products']['value'] == '1') ? 'enabled' : 'disabled'; ?>"
                                id="status_sell_Inactive_batch_products">
                                <i class="fas <?php echo ($current_settings['sell_Inactive_batch_products']['value'] == '1') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                <?php echo ($current_settings['sell_Inactive_batch_products']['value'] == '1') ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                    </div>
                </div>

    <!-- Invoice Configuration Section -->
    <div class="settings-section">
        <h2 class="section-title">
            <i class="fas fa-print"></i>
            Invoice Configuration
        </h2>
        <p class="section-description">
            Manage invoice printing and display preferences
        </p>

        <div class="row">
            <!-- Print Type Setting -->
            <div class="col-12">
                <div class="setting-item active" id="setting_invoice_print_type">
                    <div class="setting-header">
                        <h3 class="setting-title">
                            <i class="fas fa-receipt text-primary"></i>
                            Default Print Type
                        </h3>
                    </div>
                    <p class="setting-description">
                        Choose how invoices should be printed by default.
                    </p>
                    
                    <div class="print-type-options mt-3">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="print_type_receipt" name="invoice_print_type" class="custom-control-input" value="receipt" 
                                <?php echo ($current_settings['invoice_print_type']['value'] == 'receipt') ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="print_type_receipt">Receipt Print Only</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="print_type_standard" name="invoice_print_type" class="custom-control-input" value="standard" 
                                <?php echo ($current_settings['invoice_print_type']['value'] == 'standard') ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="print_type_standard">Standard Invoice Only</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="print_type_both" name="invoice_print_type" class="custom-control-input" value="both" 
                                <?php echo ($current_settings['invoice_print_type']['value'] == 'both') ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="print_type_both">Ask Every Time (Both)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            </div>
        </form>
    </div>

    <!-- Save Section -->
    <div class="save-section">
        <div class="button-group">
            <button type="button" id="resetBtn" class="btn-reset">
                <i class="fas fa-undo"></i> &nbsp;  Reset to Default
            </button>
            <button type="submit" form="billingSettingsForm" class="btn-save">
                <i class="fas fa-save"></i>  &nbsp; Save Settings
            </button>
        </div>
        <div class="text-muted mt-2" style="font-size: 0.8rem; margin-top: 10px;">
            Press Ctrl+S to quick save | Ctrl+R to reset
        </div>
    </div>
</div>

<!-- JavaScript for Settings Form -->
<script>
    $(document).ready(function() {
        // Update status badges and visual states when switches are toggled
        $('input[type="checkbox"]').change(function() {
            const settingName = $(this).attr('name');
            const isChecked = $(this).is(':checked');
            const statusBadge = $('#status_' + settingName);
            const settingItem = $('#setting_' + settingName);

            // Update status badge
            if (isChecked) {
                statusBadge.removeClass('disabled').addClass('enabled')
                    .html('<i class="fas fa-check-circle"></i> Enabled');
                settingItem.removeClass('inactive').addClass('active');
            } else {
                statusBadge.removeClass('enabled').addClass('disabled')
                    .html('<i class="fas fa-times-circle"></i> Disabled');
                settingItem.removeClass('active').addClass('inactive');
            }

            // Add visual feedback
            settingItem.addClass('updating');
            setTimeout(() => settingItem.removeClass('updating'), 300);
        });

        // Reset button functionality
        $('#resetBtn').click(function() {
            Swal.fire({
                title: 'Reset Settings?',
                text: 'This will reset all settings to their default values. Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Reset',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Reset to default values (both enabled)
                    $('#sell_Insufficient_stock_item').prop('checked', true).trigger('change');
                    $('#sell_Inactive_batch_products').prop('checked', true).trigger('change');
                    $('#print_type_standard').prop('checked', true);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Settings Reset!',
                        text: 'All settings have been reset to default values.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            });
        });

        // Handle form submission
        $('#billingSettingsForm').submit(function(e) {
            e.preventDefault();

            const formData = {
                sell_Insufficient_stock_item: $('#sell_Insufficient_stock_item').is(':checked') ? '1' : '0',
                sell_Inactive_batch_products: $('#sell_Inactive_batch_products').is(':checked') ? '1' : '0',
                invoice_print_type: $('input[name="invoice_print_type"]:checked').val()
            };

            // Show loading state
            const submitBtn = $('.btn-save');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fas fa-spinner loading-spinner"></i> Saving...').prop('disabled', true);

            $.ajax({
                url: 'api/update_settings.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Settings Saved!',
                            text: response.message,
                            timer: 2500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        // Add success animation
                        submitBtn.html('<i class="fas fa-check"></i> Saved!').addClass('btn-success');
                        setTimeout(() => {
                            submitBtn.html(originalText).removeClass('btn-success');
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Error!',
                        text: 'Failed to update settings. Please check your connection and try again.',
                        confirmButtonColor: '#dc3545'
                    });
                    console.error('AJAX Error:', error);
                },
                complete: function() {
                    // Restore button state after delay
                    setTimeout(() => {
                        submitBtn.html(originalText).prop('disabled', false);
                    }, 2000);
                }
            });
        });

        // Add keyboard shortcuts
        $(document).keydown(function(e) {
            // Ctrl+S to save
            if (e.ctrlKey && e.which === 83) {
                e.preventDefault();
                $('#billingSettingsForm').submit();
            }
            // Ctrl+R to reset
            if (e.ctrlKey && e.which === 82) {
                e.preventDefault();
                $('#resetBtn').click();
            }
        });

        // Add tooltips for better UX
        $('[data-toggle="tooltip"]').tooltip();

        // Loading animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            .loading-spinner {
                animation: spin 1s linear infinite;
            }
        `;
        document.head.appendChild(style);
    });
</script>

