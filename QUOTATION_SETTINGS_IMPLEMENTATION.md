# Quotation Settings Implementation

## Overview
Complete backend logic and settings page integration for quotation configuration. All quotation settings are now manageable through the Admin Panel Settings page.

## Settings Implemented

### 1. Quotation Validity Days
- **Setting Name:** `quotation_validity_days`
- **Type:** Integer (1-365)
- **Default:** 30 days
- **Description:** Default number of days a quotation remains valid
- **Usage:** Auto-calculates the "Valid Until" date when creating quotations

### 2. Quotation Number Prefix
- **Setting Name:** `quotation_prefix`
- **Type:** String (1-10 characters)
- **Default:** "QT"
- **Description:** Prefix for quotation numbers
- **Usage:** Generates quotation numbers like QT000001, QT000002, etc.

### 3. Auto-Generate Quotation Numbers
- **Setting Name:** `quotation_auto_generate`
- **Type:** Boolean (1=yes, 0=no)
- **Default:** 1 (enabled)
- **Description:** Automatically generate quotation numbers
- **Usage:** When enabled, generates sequential numbers; when disabled, user must enter manually

### 4. Quotation Print Type
- **Setting Name:** `quotation_print_type`
- **Type:** Enum (receipt, standard, both)
- **Default:** standard
- **Description:** Default print format for quotations
- **Options:**
  - **Receipt:** 80mm thermal receipt format
  - **Standard:** A5 professional format
  - **Both:** Ask user to choose every time

## Files Modified

### 1. `/AdminPanel/settings.php`
**Changes:**
- Added quotation configuration section with 4 settings
- Updated settings query to fetch quotation settings
- Added form inputs for all quotation settings
- Updated JavaScript to submit quotation settings
- Added default values for quotation settings

**UI Elements Added:**
- Validity Period input (with "days" suffix)
- Number Prefix input (with format preview)
- Auto Generate toggle switch
- Print Type radio buttons

### 2. `/AdminPanel/api/update_settings.php`
**Changes:**
- Added quotation settings to POST data processing
- Added validation for quotation settings
- Added GET endpoint for fetching quotation settings
- Included quotation settings in database update/insert logic

**New Validations:**
- Validity days: 1-365 range
- Prefix: 1-10 characters, required
- Auto generate: 0 or 1
- Print type: receipt, standard, or both

### 3. `/AdminPanel/api/quotation.php`
**Changes:**
- `generateQuotationNumber()`: Now uses quotation_prefix and quotation_auto_generate settings
- `createQuotation()`: Uses quotation_validity_days to auto-calculate valid_until date
- `updateQuotation()`: Uses quotation_validity_days to auto-calculate valid_until date

**Backend Logic:**
- Fetches settings from database before generating numbers
- Respects auto-generate flag (returns empty if disabled)
- Dynamically calculates validity dates based on setting

### 4. `/AdminPanel/quotation.php`
**Changes:**
- Added `loadQuotationSettings()` function
- Added `updateValidUntilDate()` function
- Loads quotation settings on page load
- Auto-updates valid until date when quotation date changes
- Stores settings in global `quotationSettings` object

**Frontend Integration:**
- Settings are fetched asynchronously on page load
- Validity date auto-calculates based on quotation_validity_days
- Updates in real-time when quotation date changes

### 5. `/AdminPanel/quotation/print.php`
**Status:** Already implemented correctly
- Uses `quotation_print_type` setting to determine print format
- Falls back to 'standard' if setting not found

## Database Requirements

All settings already created by `quotation_database.sql`:

```sql
INSERT IGNORE INTO `settings` (`setting_name`, `setting_value`, `setting_description`) VALUES
('quotation_validity_days', '30', 'Default validity period for quotations in days'),
('quotation_prefix', 'QT', 'Prefix for quotation numbers'),
('quotation_auto_generate', '1', 'Auto generate quotation numbers (1=yes, 0=no)'),
('quotation_print_type', 'standard', 'Default quotation print type (receipt, standard, or both)');
```

## How It Works

### 1. Changing Settings
1. Navigate to `/AdminPanel/settings.php`
2. Scroll to "Quotation Configuration" section
3. Modify any settings:
   - Validity Period (days)
   - Number Prefix (e.g., QUOT, QT, etc.)
   - Auto Generate (toggle on/off)
   - Print Type (receipt/standard/both)
4. Click "Save Settings"
5. Settings are immediately saved to database

### 2. Creating Quotations
1. Open quotation creation form
2. Settings are loaded automatically:
   - Quotation number auto-generated using prefix (if enabled)
   - Valid until date calculated from quotation date + validity days
3. When quotation date changes, valid until auto-updates
4. Save quotation - all calculations respect settings

### 3. Printing Quotations
1. Click print on any quotation
2. System checks `quotation_print_type` setting:
   - If "receipt" → Shows receipt format
   - If "standard" → Shows A5 format
   - If "both" → Shows selection dialog

## API Endpoints

### GET /AdminPanel/api/update_settings.php?action=get_quotation_settings
**Response:**
```json
{
  "success": true,
  "settings": {
    "quotation_validity_days": "30",
    "quotation_prefix": "QT",
    "quotation_auto_generate": "1",
    "quotation_print_type": "standard"
  }
}
```

### POST /AdminPanel/api/update_settings.php
**Payload:**
```json
{
  "quotation_validity_days": "30",
  "quotation_prefix": "QT",
  "quotation_auto_generate": "1",
  "quotation_print_type": "standard"
}
```

### GET /AdminPanel/api/quotation.php?action=generate_number
**Response:**
```json
{
  "status": "success",
  "quotation_number": "QT000001",
  "auto_generate": true
}
```

## Testing Checklist

- [x] Settings page loads quotation settings correctly
- [x] Changing validity days updates valid until date
- [x] Changing prefix updates quotation number format
- [x] Toggling auto-generate enables/disables number generation
- [x] Print type setting affects print page behavior
- [x] Settings persist after save
- [x] Quotation creation respects all settings
- [x] Validation works for all inputs

## Benefits

1. **Flexibility:** Admin can customize quotation behavior per business needs
2. **No Code Changes:** All adjustments via settings page
3. **Centralized:** One place to manage all quotation configurations
4. **User-Friendly:** Clear labels and descriptions for each setting
5. **Real-Time:** Settings apply immediately to new quotations
6. **Backward Compatible:** Default values ensure system works without configuration

## Usage Example

**Scenario:** Company wants 45-day validity and "QUOT" prefix

1. Go to Settings → Quotation Configuration
2. Set Validity Period: 45
3. Set Number Prefix: QUOT
4. Save Settings

**Result:**
- New quotations will have 45-day validity
- Numbers will be QUOT000001, QUOT000002, etc.
- All existing logic adapts automatically

The quotation system now has complete backend integration with configurable settings!