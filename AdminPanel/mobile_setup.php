<?php
include 'nav.php';

// Fetch Company Details
$company_name = $GLOBALS['ERP_COMPANY_NAME'] ?? 'Srijaya POS';
$base_url = $GLOBALS['ERP_COMPANY_BASE_URL'] ?? 'https://pos.srijaya.lk';
$api_url = rtrim($base_url, '/') . '/api/v1';

// QR Payload
$payload = [
    'name' => $company_name,
    'apiUrl' => $api_url
];
$jsonPayload = json_encode($payload);
?>

<div class="mobile-setup-container">
    <div class="setup-card">
        <div class="card-header-custom">
            <h2><i class="fas fa-mobile-alt"></i> Mobile App Setup</h2>
            <p>Scan to connect your POS App</p>
        </div>
        
        <div class="card-body-custom">
            <div class="instruction-box">
                <i class="fas fa-qrcode fa-3x mb-3 text-white"></i>
                <h5>Scan QR Code</h5>
                <p>Open the Srijaya POS Mobile App and scan this code to automatically configure the server connection.</p>
            </div>

            <!-- QR Code Container -->
            <div class="qr-wrapper">
                <div id="qrcode" class="qr-box"></div>
            </div>

            <div class="connection-info">
                <h6>Connection Details</h6>
                <div class="info-row">
                    <span class="label">Company:</span>
                    <span class="value"><?php echo htmlspecialchars($company_name); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">API URL:</span>
                    <span class="value highlight"><?php echo htmlspecialchars($api_url); ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="badge-active">Active</span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="settings.php" class="btn-custom">
                    <i class="fas fa-cog"></i> Update Company Settings
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Include QRCode.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script type="text/javascript">
    // Generate QR Code
    var payload = <?php echo json_encode($jsonPayload); ?>;
    
    // Clear previous if any
    document.getElementById("qrcode").innerHTML = "";
    
    // Create QR Code
    new QRCode(document.getElementById("qrcode"), {
        text: payload,
        width: 220,
        height: 220,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
</script>

<style>
    /* Scoped Styles for Mobile Setup Page */
    .mobile-setup-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 80vh;
        padding: 20px;
    }

    .setup-card {
        background-color: #202231;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        width: 100%;
        max-width: 500px;
        overflow: hidden;
        border: 1px solid #343E59;
    }

    .card-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 25px;
        text-align: center;
        color: white;
    }

    .card-header-custom h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .card-header-custom p {
        margin-top: 5px;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .card-body-custom {
        padding: 30px;
        color: white;
        text-align: center;
    }

    .instruction-box {
        margin-bottom: 25px;
    }

    .instruction-box p {
        color: #a0aec0;
        font-size: 0.9rem;
        margin-top: 10px;
        line-height: 1.5;
    }

    .qr-wrapper {
        background: white;
        padding: 15px;
        border-radius: 12px;
        display: inline-block;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .qr-box {
        display: flex;
        justify-content: center;
    }

    .connection-info {
        background-color: #2d3748;
        border-radius: 10px;
        padding: 20px;
        text-align: left;
        margin-bottom: 25px;
        border: 1px solid #4a5568;
    }

    .connection-info h6 {
        margin-bottom: 15px;
        font-size: 1rem;
        border-bottom: 1px solid #4a5568;
        padding-bottom: 10px;
        color: #e2e8f0;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .label {
        color: #a0aec0;
    }

    .value {
        font-weight: 500;
        color: white;
    }

    .value.highlight {
        color: #63b3ed;
        word-break: break-all;
        margin-left: 10px;
        text-align: right;
    }

    .badge-active {
        background-color: #48bb78;
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .btn-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background-color: transparent;
        border: 2px solid #a0aec0;
        color: #a0aec0;
        padding: 10px 25px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .btn-custom:hover {
        background-color: #a0aec0;
        color: #1a202c;
        transform: translateY(-2px);
    }

    /* Override any global h2 styles from acp.css if needed */
    .setup-card h2 {
        color: white;
    }
</style>

<?php include '../inc/footer.php'; ?>
