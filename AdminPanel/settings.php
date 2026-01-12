<?php
include 'nav.php';

// Fetch current settings from database
$current_settings = [];
try {
    $settings_query = "SELECT setting_name, setting_value, setting_description FROM settings WHERE setting_name IN ('sell_Insufficient_stock_item', 'sell_Inactive_batch_products', 'invoice_print_type', 'quotation_validity_days', 'quotation_prefix', 'quotation_auto_generate', 'employee_commission_enabled')";
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

// Fetch Telegram Settings
$tg_config = ['bot_token' => '', 'master_chat_id' => '', 'bot_enabled' => '0', 'allow_dm' => '0'];
$tg_topics = [];
$tg_schedules = [];

try {
    // Check if tables exist first
    $tableCheck = mysqli_query($con, "SHOW TABLES LIKE 'telegram_config'");
    if (mysqli_num_rows($tableCheck) > 0) {
        // Config
        $res = mysqli_query($con, "SELECT setting_key, setting_value FROM telegram_config");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $tg_config[$row['setting_key']] = $row['setting_value'];
            }
        }

        // Topics
        $res = mysqli_query($con, "SELECT * FROM telegram_topics ORDER BY id ASC");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) $tg_topics[] = $row;
        }

        // Schedules
        $res = mysqli_query($con, "SELECT ts.*, tt.topic_name FROM telegram_schedules ts LEFT JOIN telegram_topics tt ON ts.target_topic_key = tt.topic_key");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) $tg_schedules[] = $row;
        }
    }
} catch (Exception $e) {
    // Silent fail or log
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
if (!isset($current_settings['quotation_validity_days'])) {
    $current_settings['quotation_validity_days'] = ['value' => '30', 'description' => 'Default validity period for quotations in days'];
}
if (!isset($current_settings['quotation_prefix'])) {
    $current_settings['quotation_prefix'] = ['value' => 'QT', 'description' => 'Prefix for quotation numbers'];
}
if (!isset($current_settings['quotation_auto_generate'])) {
    $current_settings['quotation_auto_generate'] = ['value' => '1', 'description' => 'Auto generate quotation numbers'];
}
// Employee Commission Setting
if (!isset($current_settings['employee_commission_enabled'])) {
    $current_settings['employee_commission_enabled'] = ['value' => '0', 'description' => 'Enable employee commission from invoice profit'];
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

    /* Tab Navigation Styles */
    .settings-tabs {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
        background: white;
        padding: 0.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        gap: 0.5rem;
    }

    .tab-btn {
        padding: 0.8rem 2rem;
        border: none;
        background: transparent;
        font-weight: 600;
        color: #6c757d;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .tab-btn:hover {
        background: #f8f9fa;
        color: #2c3e50;
    }

    .tab-btn.active {
        background: #e7f1ff;
        color: #007bff;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.1);
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.4s ease-out;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="settings-container">
    <!-- Header Section -->
    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> System Settings</h1>
        <p>Configure system behavior and preferences</p>
    </div>

    <!-- Tab Navigation -->
    <div class="settings-tabs">
        <button class="tab-btn active" data-tab="general">
            <i class="fas fa-sliders-h"></i> General
        </button>
        <button class="tab-btn" data-tab="telegram">
            <i class="fab fa-telegram"></i> Telegram Bot
        </button>
    </div>

    <!-- TAB 1: General Settings -->
    <div id="tab-general" class="tab-content active">

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

                    <!-- Quotation Configuration Section -->
                    <div class="settings-section">
                        <h2 class="section-title">
                            <i class="fas fa-file-invoice"></i>
                            Quotation Configuration
                        </h2>
                        <p class="section-description">
                            Manage quotation numbering and printing preferences
                        </p>

                        <div class="row">
                            <!-- Quotation Validity Days -->
                            <div class="col-md-4 col-12">
                                <div class="setting-item active" id="setting_quotation_validity_days">
                                    <div class="setting-header">
                                        <h3 class="setting-title">
                                            <i class="fas fa-calendar-days text-success"></i>
                                            Validity Period
                                        </h3>
                                    </div>
                                    <p class="setting-description">
                                        Default number of days a quotation remains valid
                                    </p>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="quotation_validity_days"
                                                name="quotation_validity_days"
                                                value="<?php echo $current_settings['quotation_validity_days']['value']; ?>"
                                                min="1" max="365" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">days</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quotation Prefix -->
                            <div class="col-md-4 col-12">
                                <div class="setting-item active" id="setting_quotation_prefix">
                                    <div class="setting-header">
                                        <h3 class="setting-title">
                                            <i class="fas fa-hashtag text-info"></i>
                                            Number Prefix
                                        </h3>
                                    </div>
                                    <p class="setting-description">
                                        Prefix for quotation numbers (e.g., QT, QUOT)
                                    </p>
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="quotation_prefix"
                                            name="quotation_prefix"
                                            value="<?php echo $current_settings['quotation_prefix']['value']; ?>"
                                            maxlength="10" required
                                            placeholder="QT">
                                        <small class="form-text text-muted">Format: <?php echo $current_settings['quotation_prefix']['value']; ?>000001</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Auto Generate Numbers -->
                            <div class="col-md-4 col-12">
                                <div class="setting-item active" id="setting_quotation_auto_generate">
                                    <div class="setting-header">
                                        <h3 class="setting-title">
                                            <i class="fas fa-robot text-warning"></i>
                                            Auto Generate
                                        </h3>
                                    </div>
                                    <p class="setting-description">
                                        Automatically generate quotation numbers
                                    </p>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch" style="padding-top: 10px;">
                                            <input type="checkbox" class="custom-control-input"
                                                id="quotation_auto_generate"
                                                name="quotation_auto_generate"
                                                <?php echo ($current_settings['quotation_auto_generate']['value'] == '1') ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="quotation_auto_generate">
                                                <?php echo ($current_settings['quotation_auto_generate']['value'] == '1') ? 'Enabled' : 'Disabled'; ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Employee Commission Configuration Section -->
        <div class="settings-section">
            <h2 class="section-title">
                <i class="fas fa-hand-holding-usd"></i>
                Employee Commission
            </h2>
            <p class="section-description">
                Configure employee commission from product profit on each sale. Commission is given to the biller (cashier).
            </p>

            <div class="row">
                <!-- Commission Enable/Disable Toggle -->
                <div class="col-lg-6 col-md-12">
                    <div class="setting-item <?php echo ($current_settings['employee_commission_enabled']['value'] == '1') ? 'active' : 'inactive'; ?>" id="setting_employee_commission_enabled">
                        <div class="setting-header">
                            <h3 class="setting-title">
                                <i class="fas fa-percentage text-success"></i>
                                Invoice Commission
                            </h3>
                            <label class="toggle-switch">
                                <input type="checkbox" id="employee_commission_enabled"
                                    name="employee_commission_enabled" value="1"
                                    <?php echo ($current_settings['employee_commission_enabled']['value'] == '1') ? 'checked' : ''; ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <p class="setting-description">
                            Enable employee commission from product profit. When enabled, the biller (cashier) will receive commission based on each product's commission percentage setting.
                        </p>
                        <div class="setting-status">
                            <span class="status-badge <?php echo ($current_settings['employee_commission_enabled']['value'] == '1') ? 'enabled' : 'disabled'; ?>"
                                id="status_employee_commission_enabled">
                                <i class="fas <?php echo ($current_settings['employee_commission_enabled']['value'] == '1') ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i>
                                <?php echo ($current_settings['employee_commission_enabled']['value'] == '1') ? 'Enabled' : 'Disabled'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Commission Info -->
                <div class="col-lg-6 col-md-12">
                    <div class="setting-item active">
                        <div class="setting-header">
                            <h3 class="setting-title">
                                <i class="fas fa-info-circle text-info"></i>
                                How It Works
                            </h3>
                        </div>
                        <p class="setting-description">
                            <strong>1.</strong> Set commission % for each product in Product Edit page<br>
                            <strong>2.</strong> When invoice is submitted, commission is calculated from profit<br>
                            <strong>3.</strong> Commission is added to the biller's salary automatically<br>
                            <strong>4.</strong> Salary description shows "Commission from Bill #XXX"
                        </p>
                    </div>
                </div>
            </div>
        </div>



        <!-- Save Section (General) -->
        <div class="save-section">
            <div class="button-group">
                <button type="button" id="resetBtn" class="btn-reset">
                    <i class="fas fa-undo"></i> &nbsp; Reset to Default
                </button>
                <button type="submit" form="billingSettingsForm" class="btn-save">
                    <i class="fas fa-save"></i> &nbsp; Save General Settings
                </button>
            </div>
            <div class="text-muted mt-2" style="font-size: 0.8rem; margin-top: 10px;">
                Press Ctrl+S to quick save | Ctrl+R to reset
            </div>
        </div>

    </div> <!-- End General Tab -->

    <!-- TAB 2: Telegram Integration -->
    <div id="tab-telegram" class="tab-content">

        <!-- Telegram Bot Configuration Section -->
        <div class="settings-section">
            <h2 class="section-title">
                <i class="fab fa-telegram text-primary"></i>
                Telegram Bot Integration
            </h2>
            <p class="section-description">
                Manage your Telegram Bot connection, topics, and automated reporting schedules.
            </p>

            <form id="telegramSettingsForm">
                <!-- 1. Connection Settings -->
                <div class="setting-item active">
                    <div class="setting-header">
                        <h3 class="setting-title"><i class="fas fa-link"></i> Connection Details</h3>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnTestTelegram">
                            <i class="fas fa-paper-plane"></i> Test Connection
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Bot Token</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="bot_token" id="tg_bot_token" value="<?php echo htmlspecialchars($tg_config['bot_token']); ?>" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleTokenVisibility" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Master Chat ID</label>
                                <input type="text" class="form-control" name="master_chat_id" id="tg_chat_id" value="<?php echo htmlspecialchars($tg_config['master_chat_id']); ?>" placeholder="-1001234567890">
                                <small class="text-muted">ID of the Supergroup</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-2">
                                <label style="display:flex; justify-content:space-between; align-items:center;">
                                    Bot Status
                                    <label class="toggle-switch mb-0">
                                        <input type="checkbox" name="bot_enabled" value="1" <?php echo ($tg_config['bot_enabled'] == '1') ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </label>
                            </div>
                            <div class="form-group">
                                <label style="display:flex; justify-content:space-between; align-items:center;">
                                    Allow DMs
                                    <label class="toggle-switch mb-0">
                                        <input type="checkbox" name="allow_dm" value="1" <?php echo ($tg_config['allow_dm'] == '1') ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Topics Manager -->
                <div class="setting-item active">
                    <div class="setting-header">
                        <h3 class="setting-title"><i class="fas fa-list-ul"></i> Topic Mapping</h3>
                    </div>
                    <p class="setting-description">Map system features to specific Telegram Topics (Threads).</p>

                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th>Function</th>
                                    <th>Topic ID</th>
                                    <th>Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tg_topics as $topic): ?>
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <strong><?php echo $topic['topic_name']; ?></strong> <br>
                                            <small class="text-muted"><?php echo $topic['description']; ?></small>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm" name="topic_id[<?php echo $topic['id']; ?>]" value="<?php echo $topic['topic_id']; ?>" placeholder="e.g. 2">
                                        </td>
                                        <td>
                                            <label class="toggle-switch" style="width: 40px; height: 20px;">
                                                <input type="checkbox" name="topic_active[<?php echo $topic['id']; ?>]" value="1" <?php echo ($topic['is_active'] == 1) ? 'checked' : ''; ?>>
                                                <span class="toggle-slider" style="border-radius: 20px;"></span>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. Schedule Manager -->
                <div class="setting-item active">
                    <div class="setting-header">
                        <h3 class="setting-title"><i class="fas fa-clock"></i> Reporting Schedule</h3>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <thead>
                                <tr>
                                    <th>Report</th>
                                    <th>Schedule Time (Hour)</th>
                                    <th>Target Topic</th>
                                    <th>Active</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tg_schedules as $sched): ?>
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <?php echo ucwords(str_replace('_', ' ', $sched['report_type'])); ?>
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm" name="sched_hour[<?php echo $sched['id']; ?>]">
                                                <?php for ($i = 0; $i < 24; $i++): ?>
                                                    <option value="<?php echo $i; ?>" <?php echo ($sched['schedule_hour'] == $i) ? 'selected' : ''; ?>>
                                                        <?php echo sprintf("%02d:00", $i); ?> (<?php echo date("g A", strtotime("$i:00")); ?>)
                                                    </option>
                                                <?php endfor; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <span class="badge badge-light"><?php echo $sched['topic_name'] ?? 'Unknown'; ?></span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch" style="width: 40px; height: 20px;">
                                                <input type="checkbox" name="sched_active[<?php echo $sched['id']; ?>]" value="1" <?php echo ($sched['is_active'] == 1) ? 'checked' : ''; ?>>
                                                <span class="toggle-slider" style="border-radius: 20px;"></span>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" class="btn btn-success btn-save-telegram" onclick="$('#telegramSettingsForm').submit()">
                        <i class="fas fa-save"></i> Save Telegram Settings
                    </button>
                </div>
            </form>
        </div>
    </div> <!-- End Telegram Tab -->

</div> <!-- End Settings Container -->

<script>
    /**
     * Tab Switching System - Explicit Display Control
     * Handles both CSS classes and inline display properties
     */
    (function() {
        'use strict';

        // Tab switching function
        function switchToTab(tabId) {
            console.log('=== Switching to tab:', tabId, '===');

            // Get all tab buttons and contents
            const allButtons = document.querySelectorAll('.tab-btn');
            const allContents = document.querySelectorAll('.tab-content');

            console.log('Found elements:', {
                buttons: allButtons.length,
                contents: allContents.length
            });

            // 1. Hide ALL tab contents and deactivate ALL buttons
            allButtons.forEach(function(btn) {
                btn.classList.remove('active');
                console.log('Deactivated button:', btn.getAttribute('data-tab'));
            });

            allContents.forEach(function(content) {
                content.classList.remove('active');
                content.style.display = 'none';
                console.log('Hidden content:', content.id);
            });

            // 2. Show the target tab content
            const targetContent = document.getElementById('tab-' + tabId);
            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.style.display = 'block';
                console.log('✓ Showed content:', targetContent.id);
            } else {
                console.error('✗ Tab content NOT found:', 'tab-' + tabId);
                return false;
            }

            // 3. Activate the target button
            const targetButton = document.querySelector('.tab-btn[data-tab="' + tabId + '"]');
            if (targetButton) {
                targetButton.classList.add('active');
                console.log('✓ Activated button:', tabId);
            } else {
                console.error('✗ Tab button NOT found for:', tabId);
            }

            console.log('=== Tab switch complete ===');
            return true;
        }

        // Initialize tab system
        function initTabSystem() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            console.log('🚀 Tab System Initializing...');
            console.log('Buttons found:', tabButtons.length);
            console.log('Contents found:', tabContents.length);

            // Ensure initial state is correct
            tabContents.forEach(function(content) {
                if (!content.classList.contains('active')) {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });

            // Add click handlers to all tab buttons
            tabButtons.forEach(function(button) {
                const tabId = button.getAttribute('data-tab');
                console.log('Attaching click handler to:', tabId);

                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    switchToTab(tabId);
                });
            });

            // Expose globally
            window.switchTab = switchToTab;

            console.log('✓ Tab System Ready');
        }

        // Auto-initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTabSystem);
        } else {
            // DOM already loaded
            initTabSystem();
        }
    })();
</script>

<!-- Main Settings Script -->
<script>
    /* Tab system initialized above */

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

        // General Form Submission (Existing)
        $('#billingSettingsForm').submit(function(e) {
            e.preventDefault();

            const formData = {
                sell_Insufficient_stock_item: $('#sell_Insufficient_stock_item').is(':checked') ? '1' : '0',
                sell_Inactive_batch_products: $('#sell_Inactive_batch_products').is(':checked') ? '1' : '0',
                invoice_print_type: $('input[name="invoice_print_type"]:checked').val(),
                quotation_validity_days: $('#quotation_validity_days').val(),
                quotation_prefix: $('#quotation_prefix').val(),
                quotation_auto_generate: $('#quotation_auto_generate').is(':checked') ? '1' : '0',
                employee_commission_enabled: $('#employee_commission_enabled').is(':checked') ? '1' : '0'
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
                // Determine which tab is active and submit appropriate form
                if ($('#tab-general').hasClass('active')) {
                    $('#billingSettingsForm').submit();
                } else {
                    $('#telegramSettingsForm').submit();
                }
            }
        });

        // Add tooltips for better UX
        // Add tooltips for better UX
        if ($().tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }

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

        // --- Telegram Bot Scripts ---

        // Handle Telegram Form Submission
        $('#telegramSettingsForm').submit(function(e) {
            e.preventDefault();
            const btn = $('.btn-save-telegram');
            const originalText = btn.html();

            btn.html('<i class="fas fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

            $.ajax({
                url: 'api/update_telegram_settings.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Telegram settings updated successfully.',
                            timer: 2000,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Connection failed', 'error');
                },
                complete: function() {
                    btn.html(originalText).prop('disabled', false);
                }
            });
        });

        // Test Telegram Connection
        $('#btnTestTelegram').click(function() {
            const token = $('#tg_bot_token').val();
            const chatId = $('#tg_chat_id').val();

            if (!token || !chatId) {
                Swal.fire('Missing Info', 'Please enter Bot Token and Chat ID first.', 'warning');
                return;
            }

            const btn = $(this);
            const originalIcon = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Testing...');

            $.ajax({
                url: 'api/test_telegram_connection.php',
                method: 'POST',
                data: {
                    token: token,
                    chat_id: chatId
                },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Connected!', `Successfully connected to bot: <b>${res.bot_name}</b>`, 'success');
                    } else {
                        Swal.fire('Connection Failed', res.message || 'Invalid Token or Chat ID', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server error while testing', 'error');
                },
                complete: function() {
                    btn.html('<i class="fas fa-paper-plane"></i> Test Connection');
                }
            });
        });

        // Toggle Bot Token Visibility
        $('#toggleTokenVisibility').click(function() {
            const input = $('#tg_bot_token');
            const icon = $(this).find('i');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

    });
</script>