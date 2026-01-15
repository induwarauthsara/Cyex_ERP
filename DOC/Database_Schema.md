# Database Schema Documentation

**Database Name:** `srijayapos_new` (Default)
**Engine:** InnoDB
**Charset:** utf8mb4

---

## Key Tables

### 1. Products & Inventory
*   **`products`**: Central product catalog.
    *   `product_id` (PK)
    *   `has_stock`: '1' or '0' (Service vs Good).
    *   `barcode_symbology`: Barcode format (e.g., CODE128).
*   **`product_batch`**: Manages specific batches of products (expiry, cost price variation).
    *   `batch_id` (PK)
    *   `expiry_date`: Crucial for perishables.
    *   `selling_price` & `cost`: Allows tracking profit margin per batch.
*   **`categories`** & **`brands`**: Classification tables.
*   **`combo_products`**: For bundle sales.

### 2. POS & Sales
*   **`invoice`**: Sales headers.
    *   `invoice_number` (PK)
    *   `items`: JSON stored items (in some versions) or linked via `oneTimeProducts_sales`/log tables. Note: The schema shows legacy `items` table usage might be mixed or deprecated in favor of JSON in `held_invoices`.
*   **`held_invoices`**: Temporary storage for suspended bills.
    *   `items`: JSON column storing the cart state.
*   **`cash_register`**: Tracks daily float, opening/closing balance.

### 3. Expenses & Finance
*   **`expenses`**: Business expenses.
    *   `status`: 'paid', 'partial', 'unpaid'.
    *   `recurring_ref_id`: Link to `recurring_expenses`.
*   **`expense_payments`**: Payment history for split payments.
    *   *Trigger*: `update_expense_status_after_payment` auto-updates parent expense status.
*   **`salary`**: Records of salary payouts. Linked to expenses logic.
*   **`accounts`**: Cash books (e.g., 'Cash', 'Bank').

### 4. Human Resources (HRM)
*   **`employees`**: Staff details.
    *   `role`: 'Admin', 'Employee'.
    *   `password`: Authentication credential (check Security section).
*   **`attendance`**: Clock-in/Clock-out logs.

### 5. Procurement
*   **`purchase_orders`** (PO): Orders sent to suppliers.
*   **`goods_receipt_notes`** (GRN): what actually arrived.
    *   Links to `product_batch` to auto-increment stock upon arrival.

---

## Database Triggers
*   `update_expense_status_after_payment`: Automatically calculates if an expense is 'paid' or 'partial' based on `expense_payments` total.

## Notes & Issues
1.  **Legacy Tables**: `items` seems simple compared to `product_batch`. Ensure `items` isn't being used for active stock logic if `product_batch` is the source of truth.
2.  **JSON Usage**: `held_invoices` uses JSON for items, offering flexibility but harder SQL reporting compared to a normalized `invoice_items` table.
