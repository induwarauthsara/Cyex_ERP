# Bill Summary Logic & Patterns

This document outlines the various display patterns for the Bill Summary section in the invoice print layout (`print.php`). The layout adapts based on the presence of discounts, the discount mode (Individual vs. Global/Simple), and advance payments.

## Key Variables

*   **`$sub_total`**: Sum of (Price × Quantity) for all items.
*   **`$discount`**: Total value of discounts applied (from database).
*   **`$advance`**: Advance payment amount.
*   **`$total`** / **`$display_total`**: Final payable amount (Sub Total - Discount). Recalculated as `$sub_total - $display_discount` to ensure accuracy.
*   **`$individual_discount_mode`**:
    *   `1`: Individual Discount Mode (Discounts shown per item).
    *   `0`: Standard/Global Discount Mode (Discount shown in summary).
*   **`$total_discount`**: Calculated sum of all individual item discounts.
*   **`$display_discount`**: The discount amount to display (uses `$total_discount` in individual mode, otherwise `$discount` from database).

---

## Patterns

### 1. Simple Bill (No Discount, No Advance)
**Condition:** `$discount == 0` AND `$advance == 0`

*   **Display:**
    *   **Total**: `$total`

*   **Example:**
    *   **Total: 5,000.00**

---

### 2. Bill with Standard Discount (No Advance)
**Condition:** `$discount > 0` AND `$individual_discount_mode != 1` AND `$advance == 0`

*   **Display:**
    *   **Sub Total**: `$sub_total`
    *   **Discount**: `- $discount`
    *   **Total**: `$total`

*   **Example:**
    *   Sub Total: 5,000.00
    *   Discount: -500.00
    *   **Total: 4,500.00**

---

### 3. Bill with Advance Only (No Discount)
**Condition:** `$discount == 0` AND `$advance > 0` AND `$advance < $sub_total`

*   **Display:**
    *   **Total**: `$total` (Sub Total not shown since total = sub_total)
    *   **Advance**: `$advance`
    *   **Balance**: `($total - $advance)`

*   **Example:**
    *   Total: 5,000.00
    *   Advance: 2,000.00
    *   **Balance: 3,000.00**

---

### 4. Bill with Standard Discount + Advance
**Condition:** `$discount > 0` AND `$individual_discount_mode != 1` AND `$advance > 0` AND `$advance < $total`

*   **Display:**
    *   **Sub Total**: `$sub_total`
    *   **Discount**: `- $discount`
    *   **Total**: `$total`
    *   **Advance**: `$advance`
    *   **Balance**: `($total - $advance)`

*   **Example:**
    *   Sub Total: 5,000.00
    *   Discount: -500.00
    *   **Total: 4,500.00**
    *   Advance: 2,000.00
    *   **Balance: 2,500.00**

*   **Note:** Sub Total shown only when `$display_total != $sub_total` (i.e., discount exists)

---

### 5. Bill with Individual Discount Mode (No Advance)
**Condition:** `$discount > 0` AND `$individual_discount_mode == 1` AND `$advance == 0`

*   **Special Feature:** A "🎉 You total saved: Rs.XX" message is displayed separately.
*   **Display:**
    *   **Sub Total**: `$sub_total`
    *   **Discount**: `$total_discount` (Calculated from individual item discounts)
    *   **Total**: `$total`

*   **Example:**
    *   *(Message: 🎉 You total saved: Rs.500.00)*
    *   Sub Total: 5,000.00
    *   Discount: -500.00
    *   **Total: 4,500.00**

*   **Note:** Discount displayed uses `$total_discount` (sum of item discounts) instead of database `$discount`

---

### 6. Bill with Individual Discount Mode + Advance
**Condition:** `$discount > 0` AND `$individual_discount_mode == 1` AND `$advance > 0` AND `$advance < $total`

*   **Special Feature:** "🎉 You total saved" message displayed.
*   **Display:**
    *   **Sub Total**: `$sub_total`
    *   **Discount**: `$total_discount` (Calculated from individual item discounts)
    *   **Total**: `$total`
    *   **Advance**: `$advance`
    *   **Balance**: `($total - $advance)`

*   **Example:**
    *   *(Message: 🎉 You total saved: Rs.500.00)*
    *   Sub Total: 5,000.00
    *   Discount: -500.00
    *   **Total: 4,500.00**
    *   Advance: 2,000.00
    *   **Balance: 2,500.00**

---

## Display Logic Summary

### Visibility Rules:
1. **Sub Total**: Only shown when `$display_total != $sub_total` (i.e., when there's a discount)
2. **Discount**: Shown when `$discount > 0` OR `($individual_discount_mode == 1 && $total_discount > 0)`
3. **Total**: Always shown
4. **Advance**: Shown when `$advance > 0` AND `$advance != $display_total`
5. **Balance**: Shown when `$advance > 0` AND `$calculated_balance > 0`

### Key Calculations:
```php
// Determine which discount to display
$display_discount = ($individual_discount_mode == 1 && $total_discount > 0) ? $total_discount : $discount;

// Recalculate total for accuracy
$display_total = $sub_total - $display_discount;

// Calculate balance
$calculated_balance = $display_total - $advance;

// Visibility conditions
$show_sub_total = ($display_total != $sub_total);
$show_discount = ($discount > 0 || ($individual_discount_mode == 1 && $total_discount > 0));
$show_advance = ($advance > 0 && $advance != $display_total);
$show_balance = ($advance > 0 && $calculated_balance > 0);
```

---

## Edge Cases

### Fully Paid by Advance (Advance >= Sub Total)
**Condition:** `$advance >= $sub_total`
*   **Behavior:** Reverts to **Pattern 1 (Simple Bill)**.
*   **Display:**
    *   **Total**: `$total`
    *   *(Sub Total, Advance, and Balance are hidden)*

*   **Example:**
    *   Sub Total: 2,000.00
    *   Advance: 2,000.00
    *   **Total: 2,000.00**
