# Employee Commission from Invoice Profit - Implementation Plan

## 📋 Overview

This document outlines the complete implementation plan for adding employee commission functionality based on product profit. The feature allows businesses to incentivize employees by giving them a percentage commission from the profit of each product sold.

---

## 🎯 Feature Requirements Summary

1. **Admin Toggle**: Enable/disable commission feature from Admin Panel Settings
2. **Per-Product Commission**: Each product can have its own commission percentage
3. **Initial Commission**: Default to 0% for all products
4. **Dual Input Mode**: Support both percentage and value input with auto-calculation
5. **Invoice Integration**: Automatically calculate and add commission during bill submission
6. **Salary Integration**: Commission added to employee salary with bill reference
7. **Minimal UI**: Clean, minimal interface for commission input

---

## � DEVELOPMENT PROGRESS

> **Started**: January 13, 2026 00:35 IST\n> **Status**: ✅ Implementation Complete - Awaiting User Testing\n> **Completed**: January 13, 2026 00:56 IST

### Phase 1: Database Migration
| Step | Task | Status | Completed |
|------|------|--------|-----------|
| 1.1 | Create `commission_migration.sql` file | ✅ Done | 00:36 |
| 1.2 | Add `employee_commission_percentage` column to products table | ✅ Done | 00:36 |
| 1.3 | Add `employee_commission_enabled` setting | ✅ Done | 00:36 |
| 1.4 | Create `employee_commission_history` table | ✅ Done | 00:36 |

### Phase 2: Admin Panel Settings\n| Step | Task | Status | Completed |\n|------|------|--------|-----------|\n| 2.1 | Add commission setting default in PHP initialization | ✅ Done | 00:40 |\n| 2.2 | Add commission toggle UI in settings.php | ✅ Done | 00:40 |\n| 2.3 | Add formData save for commission toggle | ✅ Done | 00:41 |

### Phase 3: Product Edit Page
| Step | Task | Status | Completed |
|------|------|--------|-----------|\n| 3.1 | Add CSS styles for commission input | ✅ Done | 00:43 |\n| 3.2 | Add commission HTML section to edit.php | ✅ Done | 00:43 |\n| 3.3 | Add JavaScript auto-calculation logic | ✅ Done | 00:45 |\n| 3.4 | Load commission in populateForm() | ✅ Done | 00:44 |

### Phase 4: Product API Updates\n| Step | Task | Status | Completed |\n|------|------|--------|-----------|\n| 4.1 | Update updateProduct.php to save commission | ✅ Done | 00:48 |\n| 4.2 | Update getProductDetails.php to include commission | ✅ Done | 00:49 |

### Phase 5: Invoice Submission (Web + API)
| Step | Task | Status | Completed |\n|------|------|--------|-----------|\n| 5.1 | Add commission setting load in submit-invoice.php | ✅ Done | 00:50 |\n| 5.2 | Add commission tracking variables | ✅ Done | 00:51 |\n| 5.3 | Calculate commission in product loop | ✅ Done | 00:52 |\n| 5.4 | Insert commission history records | ✅ Done | 00:55 |\n| 5.5 | Add to employee salary | ✅ Done | 00:55 |\n| 5.6 | Include commission in JSON response | ✅ Done | 00:55 |\n\n### Phase 6: Testing\n| Step | Task | Status | Completed |\n|------|------|--------|-----------|\n| 6.1 | Test commission toggle ON/OFF | ⏳ User Test | - |\n| 6.2 | Test product commission save/load | ⏳ User Test | - |\n| 6.3 | Test invoice with commission | ⏳ User Test | - |\n| 6.4 | Verify salary update | ⏳ User Test | - |

---

## �📁 Files to Create

### 1. Database Migration Script
**File**: `commission_migration.sql`
```
Purpose: Create necessary database columns and tables for commission feature
```

### 2. API Endpoints
**Files**:
- `api/v1/commission/settings.php` - Get/Update commission settings
- `api/v1/commission/calculate.php` - Helper to calculate commission amounts

---

## 📁 Files to Modify

### 1. Database Structure
**File**: `srijayalk_system_DB_Structure.sql`
- Add `employee_commission_percentage` column to `products` table
- Add `employee_commission_enabled` to `settings` table

### 2. Product Management
**Files**:
- `products/edit.php` - Add commission input field in UI
- `products/API/updateProduct.php` - Handle commission percentage update
- `products/API/getProductDetails.php` - Include commission in response

### 3. Invoice Submission (Web)
**File**: `submit-invoice.php` - Calculate and distribute commissions (main logic)

### 4. Invoice Submission (API)
**File**: `api/v1/invoices/submit.php` - API wrapper that includes commission data in response

### 5. Admin Settings
**File**: `AdminPanel/settings.php` - Add commission toggle switch

### 6. HRM/Salary
**File**: `AdminPanel/hrm/paySalary.php` - Ensure salary table supports commission entries

---

## 📊 Database Changes

### 1. Add Column to `products` Table
```sql
ALTER TABLE `products` 
ADD COLUMN `employee_commission_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00 
COMMENT 'Employee commission percentage from product profit (0-100)';
```

### 2. Add Setting to `settings` Table
```sql
INSERT INTO `settings` (`setting_name`, `setting_description`, `setting_value`) 
VALUES ('employee_commission_enabled', 'Enable employee commission from invoice profit', '0');
```

### 3. Create Commission History Table
```sql
CREATE TABLE `employee_commission_history` (
  `commission_id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` INT(10) NOT NULL,
  `sales_id` INT(5) NOT NULL,
  `employee_id` INT(4) NOT NULL,
  `product_id` INT(10) NOT NULL,
  `product_profit` DECIMAL(15,2) NOT NULL,
  `commission_percentage` DECIMAL(5,2) NOT NULL,
  `commission_amount` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commission_id`),
  KEY `idx_invoice` (`invoice_number`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_product` (`product_id`),
  FOREIGN KEY (`invoice_number`) REFERENCES `invoice`(`invoice_number`) ON DELETE CASCADE,
  FOREIGN KEY (`employee_id`) REFERENCES `employees`(`employ_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

---

## 💻 Implementation Details

### Phase 1: Database Migration

#### File: `commission_migration.sql`
```sql
-- Employee Commission Feature Migration
-- Run this script to add commission support

-- 1. Add commission percentage to products table
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `employee_commission_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00 
COMMENT 'Employee commission percentage from product profit (0-100)';

-- 2. Add commission setting
INSERT IGNORE INTO `settings` (`setting_name`, `setting_description`, `setting_value`) 
VALUES ('employee_commission_enabled', 'Enable employee commission from invoice profit', '0');

-- 3. Create commission history table
CREATE TABLE IF NOT EXISTS `employee_commission_history` (
  `commission_id` INT(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` INT(10) NOT NULL,
  `sales_id` INT(5) DEFAULT NULL,
  `employee_id` INT(4) NOT NULL,
  `product_id` INT(10) DEFAULT NULL,
  `product_name` VARCHAR(100) NOT NULL,
  `product_profit` DECIMAL(15,2) NOT NULL,
  `commission_percentage` DECIMAL(5,2) NOT NULL,
  `commission_amount` DECIMAL(15,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commission_id`),
  KEY `idx_invoice` (`invoice_number`),
  KEY `idx_employee` (`employee_id`),
  KEY `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Add index for faster commission queries
CREATE INDEX IF NOT EXISTS `idx_commission_date` ON `employee_commission_history` (`created_at`);
```

---

### Phase 2: Admin Panel Settings

#### File: `AdminPanel/settings.php`
**Location**: Add after Quotation Configuration section (~line 788)

```php
<!-- Employee Commission Configuration Section -->
<div class="settings-section">
    <h2 class="section-title">
        <i class="fas fa-hand-holding-usd"></i>
        Employee Commission
    </h2>
    <p class="section-description">
        Configure employee commission from product profit on each sale
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
                    Enable employee commission from product profit. When enabled, the biller will receive commission based on each product's commission percentage.
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
    </div>
</div>
```

**Also add to PHP initialization** (~line 62):
```php
if (!isset($current_settings['employee_commission_enabled'])) {
    $current_settings['employee_commission_enabled'] = ['value' => '0', 'description' => 'Enable employee commission from invoice profit'];
}
```

---

### Phase 3: Product Edit Page UI

#### File: `products/edit.php`
**Location**: Add after Alert Quantity field in the variant/batch section

**CSS to add** (in `<style>` section):
```css
/* Commission Input Styles */
.commission-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.commission-container .input-group {
    max-width: 300px;
}

.commission-container label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.commission-container label i {
    color: #28a745;
}

.commission-container .form-text {
    font-size: 0.8rem;
    color: #6c757d;
}

.commission-sync-indicator {
    font-size: 0.75rem;
    color: #17a2b8;
    margin-top: 5px;
}
```

**HTML to add** (after Alert Quantity input, ~line 270):
```html
<!-- Employee Commission Section -->
<div class="commission-container mt-3" id="commissionSection">
    <label for="employeeCommission" class="form-label">
        <i class="fas fa-hand-holding-usd"></i> Employee Commission
    </label>
    <div class="row g-2 align-items-end">
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Percentage (%)</label>
            <div class="input-group input-group-sm" style="width: 120px;">
                <input type="number" class="form-control" id="commissionPercentage" 
                    name="commissionPercentage" min="0" max="100" step="0.01" 
                    placeholder="0.00" value="0">
                <span class="input-group-text">%</span>
            </div>
        </div>
        <div class="col-auto">
            <span class="text-muted">or</span>
        </div>
        <div class="col-auto">
            <label class="form-label small text-muted mb-1">Value (Rs.)</label>
            <div class="input-group input-group-sm" style="width: 150px;">
                <span class="input-group-text">Rs.</span>
                <input type="number" class="form-control" id="commissionValue" 
                    name="commissionValue" min="0" step="0.01" 
                    placeholder="0.00" value="0" readonly>
            </div>
        </div>
    </div>
    <div class="commission-sync-indicator" id="commissionCalculation">
        <i class="fas fa-info-circle"></i>
        <span id="commissionCalcText">Based on product profit, enter commission %</span>
    </div>
    <small class="form-text text-muted mt-1">
        Commission given to the biller from product profit when sold.
    </small>
</div>
```

**JavaScript to add** (in `<script>` section):
```javascript
// Commission Auto-Calculation Logic
$(document).ready(function() {
    // Calculate commission value from percentage
    function calculateCommissionValue() {
        const cost = parseFloat($('#cost').val()) || 0;
        const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
        const profit = sellingPrice - cost;
        const percentage = parseFloat($('#commissionPercentage').val()) || 0;
        
        if (profit > 0 && percentage > 0) {
            const commissionValue = (profit * percentage / 100).toFixed(2);
            $('#commissionValue').val(commissionValue);
            $('#commissionCalcText').html(`
                Profit: Rs.${profit.toFixed(2)} × ${percentage}% = Rs.${commissionValue} commission per unit
            `);
        } else {
            $('#commissionValue').val('0.00');
            $('#commissionCalcText').text('Based on product profit, enter commission %');
        }
    }
    
    // Listen for changes
    $('#commissionPercentage, #cost, #sellingPrice').on('input change', function() {
        calculateCommissionValue();
    });
    
    // Enable value input for reverse calculation
    $('#commissionValue').on('focus', function() {
        $(this).prop('readonly', false);
    }).on('blur', function() {
        $(this).prop('readonly', true);
    }).on('input', function() {
        const cost = parseFloat($('#cost').val()) || 0;
        const sellingPrice = parseFloat($('#sellingPrice').val()) || 0;
        const profit = sellingPrice - cost;
        const value = parseFloat($(this).val()) || 0;
        
        if (profit > 0 && value > 0) {
            const percentage = ((value / profit) * 100).toFixed(2);
            $('#commissionPercentage').val(percentage);
            $('#commissionCalcText').html(`
                Rs.${value} ÷ Rs.${profit.toFixed(2)} profit = ${percentage}% commission
            `);
        }
    });
});
```

---

### Phase 4: Product API Update

#### File: `products/API/updateProduct.php`
**Location**: Add commission handling in the product update section (~line 203)

```php
// Get commission percentage - add after line 206
$commissionPercentage = isset($productData['commissionPercentage']) ? 
    floatval($productData['commissionPercentage']) : 0.00;

// Validate commission percentage
if ($commissionPercentage < 0 || $commissionPercentage > 100) {
    throw new ProductException("Commission percentage must be between 0 and 100", "commissionPercentage");
}
```

**Modify the UPDATE SQL** (~line 234):
```php
// Update products table - add employee_commission_percentage
$sql = "UPDATE products SET 
        product_name = ?, 
        product_type = ?, 
        barcode = ?, 
        barcode_symbology = ?, 
        sku = ?, 
        show_in_landing_page = ?, 
        category_id = ?, 
        brand_id = ?, 
        active_status = ?, 
        image = ?,
        employee_commission_percentage = ?
        WHERE product_id = ?";

$stmt = $con->prepare($sql);
if (!$stmt) {
    throw new ProductException("Database error: " . $con->error);
}

$stmt->bind_param(
    "sssssiiisisdi",
    $productName,
    $productType,
    $productCode,
    $barcodeSymbology,
    $sku,
    $showInEcommerce,
    $categoryId,
    $brandId,
    $activeStatus,
    $imagePath,
    $commissionPercentage,
    $productId
);
```

#### File: `products/API/getProductDetails.php`
**Modify**: Include commission percentage in the SELECT query and response

```php
// Add to SELECT query
$query = "SELECT p.*, ..., p.employee_commission_percentage, ...";

// Include in response
$productData['commission_percentage'] = $row['employee_commission_percentage'] ?? 0;
```

---

### Phase 5: Invoice Submission with Commission

#### File: `submit-invoice.php`
**Location**: After the product loop where profit is calculated (~line 726)

**Add at beginning** (after settings load, ~line 20):
```php
// Load commission setting
$employee_commission_enabled = 0;
try {
    $setting_query = "SELECT setting_value FROM settings WHERE setting_name = 'employee_commission_enabled'";
    $setting_result = mysqli_query($con, $setting_query);
    if ($row = mysqli_fetch_assoc($setting_result)) {
        $employee_commission_enabled = intval($row['setting_value']);
    }
} catch (Exception $e) {
    error_log("Warning: Could not load commission setting: " . $e->getMessage());
}
```

**Add commission tracking array** (~line 178):
```php
// Initialize commission tracking
$totalCommission = 0;
$commissionRecords = [];
```

**Inside the product foreach loop** (after line 516 where profit is calculated):
```php
// Calculate employee commission if enabled
if ($employee_commission_enabled == 1 && $profit > 0) {
    // Get product's commission percentage
    $commQuery = "SELECT product_id, employee_commission_percentage FROM products WHERE product_name = ?";
    $commStmt = mysqli_prepare($con, $commQuery);
    mysqli_stmt_bind_param($commStmt, 's', $productName);
    mysqli_stmt_execute($commStmt);
    $commResult = mysqli_stmt_get_result($commStmt);
    
    if ($commRow = mysqli_fetch_assoc($commResult)) {
        $productCommissionPercent = floatval($commRow['employee_commission_percentage']);
        
        if ($productCommissionPercent > 0) {
            $commissionAmount = ($profit * $productCommissionPercent / 100);
            $totalCommission += $commissionAmount;
            
            // Store for later insertion
            $commissionRecords[] = [
                'product_id' => $commRow['product_id'],
                'product_name' => $productName,
                'product_profit' => $profit,
                'commission_percentage' => $productCommissionPercent,
                'commission_amount' => $commissionAmount
            ];
        }
    }
    mysqli_stmt_close($commStmt);
}
```

**After all products are processed** (before commit, ~line 835):
```php
// Process accumulated commissions
if ($employee_commission_enabled == 1 && $totalCommission > 0 && $biller) {
    // 1. Insert commission history records
    foreach ($commissionRecords as $commRecord) {
        $commInsertQuery = "INSERT INTO employee_commission_history 
            (invoice_number, employee_id, product_id, product_name, product_profit, 
             commission_percentage, commission_amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $commStmt = mysqli_prepare($con, $commInsertQuery);
        mysqli_stmt_bind_param($commStmt, 'iiisddd', 
            $invoiceNumber,
            $biller,
            $commRecord['product_id'],
            $commRecord['product_name'],
            $commRecord['product_profit'],
            $commRecord['commission_percentage'],
            $commRecord['commission_amount']
        );
        mysqli_stmt_execute($commStmt);
        mysqli_stmt_close($commStmt);
    }
    
    // 2. Add commission to employee salary
    $commissionDescription = "Commission from Bill #" . $invoiceNumber;
    $salaryInsertQuery = "INSERT INTO salary (emp_id, amount, description) VALUES (?, ?, ?)";
    $salaryStmt = mysqli_prepare($con, $salaryInsertQuery);
    mysqli_stmt_bind_param($salaryStmt, 'ids', $biller, $totalCommission, $commissionDescription);
    mysqli_stmt_execute($salaryStmt);
    mysqli_stmt_close($salaryStmt);
    
    // 3. Update employee salary balance (add to pending salary)
    $updateEmpQuery = "UPDATE employees SET salary = salary + ? WHERE employ_id = ?";
    $updateStmt = mysqli_prepare($con, $updateEmpQuery);
    mysqli_stmt_bind_param($updateStmt, 'di', $totalCommission, $biller);
    mysqli_stmt_execute($updateStmt);
    mysqli_stmt_close($updateStmt);
    
    // 4. Log the commission transaction
    $actionLog = "Commission Added";
    $logMsg = "Commission of Rs." . number_format($totalCommission, 2) . " added from Invoice #" . $invoiceNumber;
    $transLogQuery = "INSERT INTO transaction_log (transaction_type, description, amount, employ_id) 
                      VALUES (?, ?, ?, ?)";
    $transStmt = mysqli_prepare($con, $transLogQuery);
    mysqli_stmt_bind_param($transStmt, 'ssdi', $actionLog, $logMsg, $totalCommission, $biller);
    mysqli_stmt_execute($transStmt);
    mysqli_stmt_close($transStmt);
    
    error_log("COMMISSION: Rs." . $totalCommission . " added to Employee ID: " . $biller . " from Invoice #" . $invoiceNumber);
}
```

---

### Phase 6: API Invoice Submit Endpoint

#### File: `api/v1/invoices/submit.php`
**Purpose**: This is an API wrapper that calls `submit-invoice.php`. We need to enhance the response to include commission information.

**Current Architecture**:
```
API Request → api/v1/invoices/submit.php → includes submit-invoice.php → Response
```

**Modifications Required**:

**1. Add commission data to the response** (after line 63):
```php
// Return the result maintaining the existing response format
if (isset($result['success']) && $result['success']) {
    // Build response data
    $responseData = isset($result['data']) ? $result['data'] : $result;
    
    // Add commission info if available
    if (isset($result['commission'])) {
        $responseData['commission'] = $result['commission'];
    }
    
    ApiResponse::success(
        $responseData,
        isset($result['message']) ? $result['message'] : 'Invoice submitted successfully',
        201
    );
}
```

**2. Update submit-invoice.php to include commission in response**:
In `submit-invoice.php`, modify the success response (~line 839) to include commission data:

```php
// Build commission summary for response
$commissionSummary = null;
if ($employee_commission_enabled == 1 && $totalCommission > 0) {
    $commissionSummary = [
        'enabled' => true,
        'total_commission' => $totalCommission,
        'employee_id' => $biller,
        'records_count' => count($commissionRecords),
        'details' => $commissionRecords
    ];
}

// Send success response with commission data
echo json_encode([
    'success' => true,
    'message' => 'Invoice submitted successfully',
    'invoiceNumber' => $invoiceNumber,
    'printReceipt' => $printReceipt,
    'printType' => $printType,
    'commission' => $commissionSummary  // NEW: Add commission info
]);
```

---

### Phase 7: Product View/Index Updates

#### File: `products/index.php`
**Add commission column to DataTable if needed** (optional enhancement)

---

## 📈 Data Flow Diagram

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Admin Panel    │────▶│   Settings      │────▶│   Database      │
│  Settings       │     │   Table         │     │   Toggle        │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │
         ▼
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Product Edit   │────▶│   Products      │────▶│   Commission %  │
│  Page           │     │   Table         │     │   Per Product   │
└─────────────────┘     └─────────────────┘     └─────────────────┘
         │
         ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         Invoice Submission                               │
│                                                                         │
│  ┌───────────────────┐              ┌───────────────────┐              │
│  │ Web POS Interface │              │ Mobile App / API  │              │
│  │ (Direct POST)     │              │ (REST API Client) │              │
│  └─────────┬─────────┘              └─────────┬─────────┘              │
│            │                                  │                         │
│            │                                  ▼                         │
│            │                    ┌─────────────────────────┐            │
│            │                    │ api/v1/invoices/submit  │            │
│            │                    │ (API Wrapper)           │            │
│            │                    └───────────┬─────────────┘            │
│            │                                │                          │
│            └────────────────┬───────────────┘                          │
│                             ▼                                          │
│                 ┌───────────────────────┐                              │
│                 │   submit-invoice.php   │                              │
│                 │   (Main Logic)         │                              │
│                 │                        │                              │
│                 │ ┌────────────────────┐ │                              │
│                 │ │ Commission Logic   │ │                              │
│                 │ │ - Check Setting    │ │                              │
│                 │ │ - Calculate %      │ │                              │
│                 │ │ - Update Salary    │ │                              │
│                 │ │ - Log History      │ │                              │
│                 │ └────────────────────┘ │                              │
│                 └───────────┬────────────┘                              │
│                             │                                          │
│         ┌───────────────────┼───────────────────┐                      │
│         ▼                   ▼                   ▼                      │
│  ┌─────────────┐     ┌─────────────┐     ┌─────────────┐              │
│  │ Commission  │     │ Salary      │     │ Employee    │              │
│  │ History     │     │ Table       │     │ Table       │              │
│  │ Table       │     │ (Add Entry) │     │ (Update $)  │              │
│  └─────────────┘     └─────────────┘     └─────────────┘              │
│                             │                                          │
│                             ▼                                          │
│                 ┌───────────────────────┐                              │
│                 │ JSON Response with    │                              │
│                 │ Commission Summary    │                              │
│                 └───────────────────────┘                              │
└─────────────────────────────────────────────────────────────────────────┘
```

### API Response Structure (with Commission)
```json
{
  "success": true,
  "message": "Invoice submitted successfully",
  "data": {
    "invoiceNumber": 12345,
    "printReceipt": true,
    "printType": "receipt",
    "commission": {
      "enabled": true,
      "total_commission": 150.00,
      "employee_id": 3,
      "records_count": 2,
      "details": [
        {
          "product_id": 101,
          "product_name": "Product A",
          "product_profit": 500.00,
          "commission_percentage": 10.00,
          "commission_amount": 50.00
        },
        {
          "product_id": 102,
          "product_name": "Product B",
          "product_profit": 1000.00,
          "commission_percentage": 10.00,
          "commission_amount": 100.00
        }
      ]
    }
  }
}
```

---

## ✅ Testing Checklist

### Admin Settings
- [ ] Commission toggle appears in settings page
- [ ] Toggle saves correctly to database
- [ ] Toggle state persists after page refresh

### Product Management
- [ ] Commission field appears in product edit page
- [ ] Percentage input works correctly (0-100)
- [ ] Value auto-calculates based on profit and percentage
- [ ] Reverse calculation (value to percentage) works
- [ ] Commission saves to products table
- [ ] Commission loads when editing existing product

### Invoice Submission (Commission Disabled)
- [ ] No commission calculated when setting is OFF
- [ ] Invoice submits normally without commission

### Invoice Submission (Commission Enabled)
- [ ] Commission calculated correctly for each product
- [ ] Commission history records created
- [ ] Salary entry added for biller with bill reference
- [ ] Employee salary balance updated
- [ ] Transaction log entry created
- [ ] Products with 0% commission skipped correctly

### Edge Cases
- [ ] Products with 0 profit (no commission)
- [ ] Products with 0% commission
- [ ] Walk-in customer invoices
- [ ] One-time products handling
- [ ] Multiple products in single invoice

### API Invoice Submit (`api/v1/invoices/submit.php`)
- [ ] API authentication works correctly
- [ ] Commission calculated when submitting via API
- [ ] Response includes commission data when enabled
- [ ] Response does NOT include commission data when disabled
- [ ] Commission summary shows correct total amount
- [ ] Commission details array contains correct product breakdowns
- [ ] Employee salary updated when submitting via API
- [ ] Commission history records created for API submissions
- [ ] Error responses handled properly
- [ ] Mobile app receives commission info in response

---

## 🔧 Rollback Plan

If issues arise, execute:
```sql
-- Remove commission column from products
ALTER TABLE `products` DROP COLUMN `employee_commission_percentage`;

-- Remove commission setting
DELETE FROM `settings` WHERE `setting_name` = 'employee_commission_enabled';

-- Drop commission history table
DROP TABLE IF EXISTS `employee_commission_history`;
```

---

## 📝 Notes for Development

1. **Minimal UI**: Keep the commission input simple and unobtrusive
2. **Real-time Calculation**: Use JavaScript for instant feedback
3. **Database Optimization**: Index commission-related columns for faster queries
4. **Error Handling**: Gracefully handle missing commission data
5. **Backward Compatibility**: Existing products default to 0% commission
6. **Logging**: Comprehensive logging for debugging commission calculations

---

## 🗓️ Implementation Order

1. **Day 1**: Database migration + Admin settings toggle
2. **Day 2**: Product edit page UI + API updates
3. **Day 3**: Invoice submission commission logic
4. **Day 4**: Testing + Bug fixes
5. **Day 5**: Documentation + Deployment

---

## 📚 Related Documentation

- `API_DOCUMENTATION.md` - API endpoint details
- `EXPENSES_ARCHITECTURE.md` - Salary/expense system integration
- `srijayalk_system_DB_Structure.sql` - Complete database schema

---

*Created: January 13, 2026*
*Last Updated: January 13, 2026*
*Author: System Development Team*
