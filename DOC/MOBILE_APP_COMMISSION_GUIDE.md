# Mobile App Integration Guide: Employee Commission Feature

## Overview
The Employee Commission feature allows sales employees (cashiers) to earn a commission based on the profit of specific products they sell. This guide outlines how to integrate this feature into the mobile app.

## 1. Feature Availability Check
Before displaying any commission-related UI, check if the feature is enabled globally.

**Endpoint:** `GET /api/v1/settings/get.php`

**Response:**
```json
{
  "success": true,
  "data": {
    "employee_commission_enabled": true, // Check this flag
    "sell_Insufficient_stock_item": true,
    "sell_Inactive_batch_products": true
  }
}
```
*   If `employee_commission_enabled` is `false`, hide all commission UI elements.

## 2. Displaying Potential Commission
When browsing products or adding them to the cart, valid products will now show their commission percentage.

**Endpoints:**
*   `GET /api/v1/products/list.php`
*   `GET /api/v1/products/search.php`
*   `GET /api/v1/products/details.php`

**New Field:** `commission_percentage` (float)

**Example Data:**
```json
{
  "id": 101,
  "name": "Luxury Item",
  "price": 5000.00,
  "cost_price": 4000.00,
  "commission_percentage": 5.00 // 5% of Profit
}
```

**Calculation for UI Display (Optional):**
You can estimate the commission amount for the user:
```
Profit = Price - Cost Price
Commission Amount = Profit * (Commission Percentage / 100)
```
*Note: The actual calculation happens on the server during invoice submission.*

## 3. Submitting the Invoice
No changes are needed to the request body of the invoice submission endpoint. The server automatically calculates and records commission based on the logged-in user.

**Endpoint:** `POST /api/v1/invoices/submit.php`

**Response:**
The response now includes a `commission` object if commission was earned.

```json
{
  "success": true,
  "message": "Invoice submitted successfully",
  "invoiceNumber": 1005,
  "commission": {
    "enabled": true,
    "total_commission": 150.50, // Amount earned on this invoice
    "employee_id": 5,
    "records_count": 2, // Number of items that had commission
    "details": [ ... ]
  }
}
```
**Action:** Show a success message like "Invoice Submitted! You earned Rs. 150.50 commission."

## 4. Viewing Commission History
Add a new screen or section for employees to view their commission history.

**Endpoint:** `GET /api/v1/employees/commission_history.php`

**Parameters:**
*   `page` (default: 1)
*   `per_page` (default: 20)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "invoice_number": 1005,
      "product_name": "Luxury Item",
      "product_profit": 1000.00,
      "commission_percentage": 5.00,
      "commission_amount": 50.00,
      "date": "2026-01-13 14:30:00"
    }
  ],
  "meta": {
    "pagination": { ... },
    "total_earnings": 1550.00 // Total lifetime earnings for this employee
  }
}
```
