# Bill Edit and Delete Feature Specification & Implementation Plan

## 1. Feature Overview
**Feature name**: Bill Edit & Delete
**Purpose**:
- Allow correction of mistakes in bills
- Maintain financial & stock accuracy
**Applies to**:
- POS Web
- POS Mobile App

## 2. Best Practice Recommendation
> [!IMPORTANT]
> **Data Integrity & Safety**
> *   ❌ **Never hard delete bills**
> *   ✅ **Always soft delete + audit log**

## 3. Allowed Actions & Requirements

### 3.1. Stock Adjustment Logic
-   **User Prompt**: The system must **ask the user** whether to **restock** (return items to inventory) or **remove** items (write off) while showing the changes of stock.
-   **Mandatory Preview**: Before any commit, the system **MUST** display a summary of:
    -   Exact Stock Quantity Changes (+/-) per item.
    -   Exact Commission Value Changes (+/-) for Biller/Worker.
    -   Net Financial Impact.
-   **Confirmation**: User must explicitly click "Confirm" after reviewing these changes.

### 3.2. Audit Log (Must Have)
Store the following for every Edit/Delete action:
-   **Store**: `Bill ID`
-   **Action**: `EDIT` or `DELETE`
-   **Old values**: Snapshot of previous state
-   **New values**: Snapshot of new state
-   **User who did it**: User ID/Name
-   **Date & time**: Timestamp
-   **Reason**: Mandatory explanation

## 4. Database Changes
To support the audit log, a new table is required.

**Table**: `invoice_audit_logs`
| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | INT | Primary Key |
| `invoice_id` | INT | Linked Bill ID |
| `action_type` | ENUM | 'EDIT', 'DELETE' |
| `old_payload` | JSON | Previous Data |
| `new_payload` | JSON | New Data |
| `reason` | TEXT | User Reason |
| `user_id` | INT | User ID |
| `created_at` | DATETIME | Timestamp |

## 5. UI / UX Requirements
### 5.1. POS Web
-   **Edit Modal/Page**:
    -   Input field for **Reason**.
    -   Stock adjustment toggle (Restock vs Write-off).
    -   **Preview Modal**:
        -   Calculated Diff Table: Shows Old vs New values for Stock and Commission.
        -   "Confirm Changes" button.
-   **Delete Action**:
    -   **Preview Modal**: Shows impact of deletion (Stock return count, Commission reversal amount).
    -   Confirmation requiring **Reason**.

### 5.2. POS Mobile App
-   **Interface**:
    -   **Preview Screen**: Before final save/delete, show "Impact Summary" (e.g., "Stock: +5 Items, Commission: -$10").
    -   Simple "Confirm" button.

---

## 6. Impacted Files & Locations

| Component | File Path | Status | Action |
| :--- | :--- | :--- | :--- |
| **API** | `api/v1/invoices/update.php` | **New** | Create API to handle invoice updates (Smart Diff logic). |
| **API** | `api/v1/invoices/delete.php` | **New** | Create API to handle invoice deletion (Stock/Commission Rollback). |
| **API** | `api/v1/services/InvoiceService.php` | **New** | Create shared service class for core invoice logic (Stock, Commission calc). |
| **Web UI** | `invoice/edit-bill.php` | **Modify** | Front-end changes to call `api/v1/invoices/update.php` via AJAX/Fetch. |
| **Web UI** | `invoice/invoice-delete-submit.php` | **Deprecate** | Logic moved to API. Web UI should call `api/v1/invoices/delete.php` via AJAX. |
| **Web UI** | `invoice/edit-bill.php` | **Modify** | Update UI to trigger API calls instead of form submission. |
| **Web UI** | `AdminPanel/invoices/history.php` | **Modify** | Connect "Delete" button to `api/v1/invoices/delete.php`. |

## 7. Code Logic & Algorithms

### 7.1. General Principles
-   **Stock Integrity**: When a bill is edited or deleted, stock movements must be exactly reversed or adjusted.
    -   **Product Stock**: Direct +/- adjustment (subject to user's "Restock" choice).
    -   **Raw Materials**: If product uses `makeProduct` recipe, adjust raw material `items` quantity accordingly.
-   **Commission Integrity**: Employee commissions are tied to "Profit".
    -   **Policy**: To prevent "ghost" commissions, the system will **Reverse** the original commission (deduct from salary) and **Apply** the new commission (add to salary). This ensures the `employees.salary` balance is always accurate to the current state of valid invoices.

### 7.2. Bill Delete Logic
1.  **Validation**: Check if invoice exists.
2.  **Preview Mode (Dry Run)**:
    -   Calculate potential Stock Rollback and Commission Reversal.
    -   Return these values to UI for User Confirmation.
3.  **Retrieve Data**: Get all Sales items and One-Time sales items.
3.  **Audit Logging**: Capture `old_data` (current state) and `reason`.
4.  **Stock Rollback** (If user selects Restock):
    -   For each `sales` item: `UPDATE products SET stock_qty = stock_qty + qty`.
    -   If applicable, restore raw materials in `items` table.
5.  **Commission Rollback**:
    -   Calculate previous Profit.
    -   Deduct shared profit from Biller (`salary`) and Worker (`salary`).
    -   Deduct from Company Account (`amount`).
6.  **Soft Delete**: Mark invoice as deleted in `invoices` table (set status/flag), or move to archive if preferred. (Do not hard delete per best practice).
7.  **Log**: Insert into `invoice_audit_logs`.

### 7.3. Bill Edit Logic
The "Edit" is treated as a **Modified Smart Diff**:

1.  **Sales Item Comparison**:
    -   **Removed items**: Trigger "Stock Rollback" (based on user preference).
    -   **Added Items**: Trigger "Stock Deduct".
    -   **Modified Items** (Qty changes):
        -   Diff = `New Qty` - `Old Qty`.
        -   Adjust Stock by `Diff` (inverse operation).
2.  **Commission Recalculation**:
    -   `Profit Diff` = `New Profit` - `Old Profit`.
    -   Adjust Salaries and Company Account based on `Profit Diff`.
    -   *Example*: If Profit increases by 1000, Biller gets +50 (5%), Company gets +850 (85%).
3.  **Audit**: Log `old_data` (pre-edit) and `new_data` (post-edit) with `reason`.

## 8. API Endpoints

### 8.1. Update Invoice (`POST /api/v1/invoices/update.php`)
**Payload:**
```json
{
    "invoice_number": 1001,
    "items": [...],
    "discount": 50,
    "payment_method": "Cash",
    "biller_name": "John",
    "worker_name": "Doe",
    "reason": "Customer returned item",
    "restock_items": true,
    "preview_only": false  // Set to true for Dry Run/Impact Analysis
}
```
**Response (Preview Mode):**
```json
{
    "status": "preview",
    "stock_changes": [
        {"product": "A", "change": "+2", "new_stock": 50}
    ],
    "commission_changes": {
        "biller": {"old": 100, "new": 105, "diff": "+5"},
        "worker": {"old": 50, "new": 52.5, "diff": "+2.5"}
    }
}
```

### 8.2. Delete Invoice (`POST /api/v1/invoices/delete.php`)
**Payload:**
```json
{ 
    "invoice_number": 1001,
    "reason": "Duplicate entry",
    "restock_items": true,
    "preview_only": false
}
```

## 9. Verification Plan & Test Cases

### 9.1. Automated / Manual API Tests
| ID | Scenario | Expected Outcome |
| :--- | :--- | :--- |
| **TC01** | **Delete Bill** | Bill soft-deleted, Audit logged, Stock restored, Commission recovered. |
| **TC02** | **Edit: Add Item** | Invoice Total up, Stock down, Commission up, Audit logged. |
| **TC03** | **Edit: Remove Item** | Invoice Total down, Stock restored, Commission down, Audit logged. |
| **TC04** | **Edit: Increase Qty** | Stock decreases further, Commission increases. |
| **TC05** | **Edit: Decrease Qty** | Stock partially restored, Commission decreases. |
| **TC06** | **Edit: Change Discount** | Invoice Profit changes. Commission adjusts. |
| **TC07** | **Edit: Change Biller** | Old Biller commission reversed. New Biller commission applied. |

### 9.2. UI Verification
1.  **Web**: Go to `invoice/edit-bill.php?id=XXXX`. Modify quantities. Save. Check DB for accurate Stock/Salary/Audit updates.
2.  **Mobile**: Verify "Delete" option works (via API) and restricts complicated Edits if necessary.

## 10. Detailed Implementation Steps

1.  **[DB] Audit Table**: Create `invoice_audit_logs` table.
2.  **[API] Create InvoiceService**: Implement `api/v1/services/InvoiceService.php` with methods:
    -   `deleteInvoice($id, $reason, $restock)`
    -   `updateInvoice($id, $data, $reason, $restock)`
    -   `logAudit($action, $old, $new, $reason)`
    -   `rollbackStock($items)`
    -   `adjustCommission($profitDiff)`
3.  **[API] Create Endpoints**:
    -   Create `api/v1/invoices/delete.php`.
    -   Create `api/v1/invoices/update.php`.
4.  **[Web] Refactor to API Client**:
    -   Modify `invoice/edit-bill.php` (JS) to collect form data and `POST` to `api/v1/invoices/update.php`.
    -   Modify `AdminPanel/invoices/history.php` (JS) to `POST` to `api/v1/invoices/delete.php` on delete action.
    -   *Note*: Ensure session cookies/auth headers are passed correctly to the API.
5.  **[Web] Cleanup**:
    -   Deprecate/Remove `invoice/edit-submit.php` and `invoice/invoice-delete-submit.php` as logic is now in API.
6.  **[Verify]**: Run Test Cases TC01-TC07.
